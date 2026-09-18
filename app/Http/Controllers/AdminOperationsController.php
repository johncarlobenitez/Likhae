<?php

namespace App\Http\Controllers;

use App\Models\AdminAnnouncement;
use App\Models\AdminAuditLog;
use App\Models\AdminPolicy;
use App\Models\AdminPreference;
use App\Models\Category;
use App\Models\Message;
use App\Models\Order;
use App\Models\PlatformSetting;
use App\Models\Product;
use App\Models\Refund;
use App\Models\Transaction;
use App\Models\User;
use App\Models\WorkspaceNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminOperationsController extends Controller
{
    public function products(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'admin_status' => ['nullable', Rule::in(['approved', 'flagged'])],
            'listing_status' => ['nullable', Rule::in(['active', 'inactive', 'archived'])],
        ]);

        $query = Product::with(['seller', 'category'])->withCount('orderItems');
        if ($search = trim((string) ($filters['q'] ?? ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhereHas('seller', fn ($seller) => $seller->where('name', 'like', "%{$search}%"));
            });
        }
        if (! empty($filters['admin_status'])) {
            $query->where('admin_status', $filters['admin_status']);
        }
        if (! empty($filters['listing_status'])) {
            $query->where('listing_status', $filters['listing_status']);
        }

        $productRecords = $query->latest()->paginate(20)->withQueryString();
        $categoryRecords = Category::whereNull('parent_id')->withCount([
            'products',
            'products as active_products_count' => fn ($q) => $q->where('listing_status', 'active'),
        ])->orderBy('name')->get();
        $flaggedRecords = Product::with('seller')->where('admin_status', 'flagged')->latest()->take(50)->get();
        $stats = [
            'total' => Product::count(),
            'active' => Product::where('listing_status', 'active')->count(),
            'flagged' => Product::where('admin_status', 'flagged')->count(),
            'archived' => Product::where('listing_status', 'archived')->count(),
        ];

        return view('Admin.products', compact('productRecords', 'categoryRecords', 'flaggedRecords', 'stats'));
    }

    public function moderateProduct(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'action' => ['required', Rule::in(['approve', 'flag', 'archive', 'restore'])],
            'reason' => ['nullable', 'string', 'max:1000'],
        ]);

        $before = ['admin_status' => $product->admin_status, 'listing_status' => $product->listing_status];
        match ($validated['action']) {
            'approve' => $product->forceFill(['admin_status' => 'approved'])->save(),
            'flag' => $product->forceFill(['admin_status' => 'flagged'])->save(),
            'archive' => $product->forceFill(['listing_status' => 'archived', 'status' => 'inactive'])->save(),
            'restore' => $product->forceFill(['listing_status' => 'active', 'status' => 'active'])->save(),
        };

        $this->audit($request, 'product.'.$validated['action'], $product, $validated['reason'] ?? null, ['before' => $before]);

        return back()->with('success', 'Product moderation status updated.');
    }

    public function storeCategory(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120', Rule::unique('categories', 'name')->whereNull('parent_id')],
            'description' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $category = Category::create([
            ...$validated,
            'slug' => $this->uniqueCategorySlug($validated['name']),
            'source' => 'admin',
            'created_by_user_id' => $request->user()->id,
        ]);
        $this->audit($request, 'category.created', $category, $category->name);

        return back()->with('success', 'Category created.');
    }

    public function updateCategory(Request $request, Category $category): RedirectResponse
    {
        abort_unless($category->parent_id === null, 404);
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120', Rule::unique('categories', 'name')->ignore($category->id)->whereNull('parent_id')],
            'description' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);
        $category->update([...$validated, 'slug' => $this->uniqueCategorySlug($validated['name'], $category->id)]);
        $this->audit($request, 'category.updated', $category, $category->name);

        return back()->with('success', 'Category updated.');
    }

    public function exportProducts(Request $request): StreamedResponse
    {
        $this->audit($request, 'products.exported', null, 'Admin exported the product catalog.');
        return response()->streamDownload(function (): void {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['ID', 'SKU', 'Product', 'Seller', 'Category', 'Price', 'Stock', 'Listing Status', 'Admin Status', 'Created']);
            Product::with(['seller', 'category'])->orderBy('id')->chunkById(500, function ($products) use ($out): void {
                foreach ($products as $product) {
                    fputcsv($out, [$product->id, $product->sku, $product->name, $product->seller?->name, $product->category?->name, $product->price, $product->stock, $product->listing_status, $product->admin_status, $product->created_at]);
                }
            });
            fclose($out);
        }, 'likhae-products-'.now()->format('Ymd-His').'.csv', ['Content-Type' => 'text/csv']);
    }

    public function compliance(Request $request): View
    {
        $sellerProductCounts = Product::selectRaw('seller_id, COUNT(*) AS total')->groupBy('seller_id')->pluck('total', 'seller_id');
        $sellerRecords = User::where('role', 'seller')->latest()->paginate(20)->withQueryString();
        $flaggedProducts = Product::with(['seller', 'category'])->where('admin_status', 'flagged')->latest()->take(50)->get();
        $complianceStats = [
            'active' => User::where('role', 'seller')->where('status', 'active')->count(),
            'pending' => User::where('role', 'seller')->where('status', 'pending')->count(),
            'flagged' => Product::where('admin_status', 'flagged')->count(),
            'suspended' => User::where('role', 'seller')->where('status', 'suspended')->count(),
        ];

        return view('Admin.compliance', compact('sellerRecords', 'flaggedProducts', 'sellerProductCounts', 'complianceStats'));
    }

    public function complaints(Request $request): View
    {
        $filters = $request->validate(['status' => ['nullable', 'string', 'max:30'], 'q' => ['nullable', 'string', 'max:100']]);
        $query = Refund::with(['order.buyer', 'order.seller', 'buyer']);
        if (! empty($filters['status'])) $query->where('status', $filters['status']);
        if ($search = trim((string) ($filters['q'] ?? ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('refund_number', 'like', "%{$search}%")
                    ->orWhereHas('order', fn ($order) => $order->where('order_number', 'like', "%{$search}%"));
            });
        }
        $refundRecords = $query->latest()->paginate(20)->withQueryString();
        $refundStats = [
            'open' => Refund::whereIn('status', ['open', 'processing'])->count(),
            'pending_amount' => (float) Refund::whereIn('status', ['open', 'processing'])->sum('amount'),
            'resolved_week' => Refund::whereIn('status', ['completed', 'rejected'])->where('updated_at', '>=', now()->startOfWeek())->count(),
            'total' => Refund::count(),
        ];

        return view('Admin.complaints', compact('refundRecords', 'refundStats'));
    }

    public function updateRefund(Request $request, Refund $refund): RedirectResponse
    {
        $validated = $request->validate(['status' => ['required', Rule::in(['open', 'processing', 'completed', 'rejected'])]]);
        $refund->status = $validated['status'];
        $refund->resolved_at = in_array($validated['status'], ['completed', 'rejected'], true) ? now() : null;
        $refund->save();
        $this->audit($request, 'refund.'.$validated['status'], $refund, $refund->refund_number);

        return back()->with('success', 'Refund status updated.');
    }

    public function finance(Request $request): View
    {
        $filters = $request->validate(['status' => ['nullable', 'string', 'max:30'], 'q' => ['nullable', 'string', 'max:100']]);
        $rate = PlatformSetting::commissionRate();
        $transactionQuery = Transaction::with(['order.seller', 'buyer']);
        if (! empty($filters['status'])) $transactionQuery->where('status', $filters['status']);
        if ($search = trim((string) ($filters['q'] ?? ''))) {
            $transactionQuery->where(function ($q) use ($search) {
                $q->where('transaction_number', 'like', "%{$search}%")
                    ->orWhereHas('order', fn ($order) => $order->where('order_number', 'like', "%{$search}%"));
            });
        }
        $transactionRecords = $transactionQuery->latest()->paginate(20)->withQueryString();
        $gross = (float) Transaction::whereIn('status', ['paid', 'completed'])->sum('amount');
        $commission = round($gross * $rate, 2);
        $pendingSettlement = (float) Transaction::whereIn('status', ['processing', 'pending'])->sum('amount');
        $pendingCount = Transaction::whereIn('status', ['processing', 'pending'])->count();
        $exampleTransaction = Transaction::with('order.seller')->latest()->first();

        return view('Admin.finance', compact('transactionRecords', 'rate', 'gross', 'commission', 'pendingSettlement', 'pendingCount', 'exampleTransaction') + ['net' => $gross - $commission]);
    }

    public function exportFinance(Request $request): StreamedResponse
    {
        $rate = PlatformSetting::commissionRate();
        $this->audit($request, 'finance.exported', null, 'Admin exported transaction data.');
        return response()->streamDownload(function () use ($rate): void {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Transaction', 'Order', 'Buyer', 'Seller', 'Amount', 'Commission', 'Net', 'Method', 'Status', 'Date']);
            Transaction::with(['order.seller', 'buyer'])->orderBy('id')->chunkById(500, function ($rows) use ($out, $rate): void {
                foreach ($rows as $row) {
                    $commission = round((float) $row->amount * $rate, 2);
                    fputcsv($out, [$row->transaction_number, $row->order?->order_number, $row->buyer?->name, $row->order?->seller?->name, $row->amount, $commission, (float) $row->amount - $commission, $row->method, $row->status, $row->created_at]);
                }
            });
            fclose($out);
        }, 'likhae-transactions-'.now()->format('Ymd-His').'.csv', ['Content-Type' => 'text/csv']);
    }

    public function reports(Request $request): View
    {
        $filters = $request->validate([
            'status' => ['nullable', 'string', 'max:40'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);
        $query = $this->reportOrderQuery($filters);
        $orderRecords = (clone $query)->with(['buyer', 'seller', 'transaction'])->latest()->paginate(20)->withQueryString();
        $gross = (float) (clone $query)->where('status', '!=', 'cancelled')->sum('total_amount');

        return view('Admin.reports', [
            'orderRecords' => $orderRecords,
            'gross' => $gross,
            'commission' => round($gross * PlatformSetting::commissionRate(), 2),
            'completed' => (clone $query)->where('status', 'completed')->count(),
            'cancelled' => (clone $query)->where('status', 'cancelled')->count(),
            'recentExports' => collect(),
            'filters' => $filters,
        ]);
    }

    public function exportReport(Request $request): StreamedResponse
    {
        $filters = $request->validate([
            'status' => ['nullable', 'string', 'max:40'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);
        $query = $this->reportOrderQuery($filters)->with(['buyer', 'seller', 'transaction']);
        $this->audit($request, 'reports.exported', null, 'Admin exported the orders report.', $filters);
        return response()->streamDownload(function () use ($query): void {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Order', 'Buyer', 'Seller', 'Amount', 'Payment Method', 'Payment Status', 'Order Status', 'Transaction', 'Created']);
            $query->orderBy('id')->chunkById(500, function ($orders) use ($out): void {
                foreach ($orders as $order) {
                    fputcsv($out, [$order->order_number, $order->buyer?->name, $order->seller?->name, $order->total_amount, $order->payment_method, $order->payment_status, $order->status, $order->transaction?->transaction_number, $order->created_at]);
                }
            });
            fclose($out);
        }, 'likhae-orders-report-'.now()->format('Ymd-His').'.csv', ['Content-Type' => 'text/csv']);
    }

    public function messages(Request $request): View
    {
        $admin = $request->user();
        $all = Message::with(['sender', 'recipient', 'order'])
            ->where(fn ($q) => $q->where('sender_id', $admin->id)->orWhere('recipient_id', $admin->id))
            ->latest()->get();
        $partnerIds = $all->map(fn (Message $message) => $message->sender_id === $admin->id ? $message->recipient_id : $message->sender_id)->unique()->values();
        $partners = User::whereIn('id', $partnerIds)->get()->keyBy('id');
        $conversations = $partnerIds->map(function ($partnerId) use ($all, $partners, $admin) {
            $latest = $all->first(fn (Message $message) => in_array($partnerId, [$message->sender_id, $message->recipient_id], true));
            $partner = $partners->get($partnerId);
            return ['user' => $partner, 'latest' => $latest, 'unread' => $all->where('sender_id', $partnerId)->where('recipient_id', $admin->id)->whereNull('read_at')->count()];
        })->filter(fn ($row) => $row['user']);

        $selectedPartnerId = (int) $request->query('partner', $partnerIds->first() ?? 0);
        $selectedPartner = $partners->get($selectedPartnerId);
        $thread = collect();
        if ($selectedPartner) {
            Message::where('sender_id', $selectedPartner->id)->where('recipient_id', $admin->id)->whereNull('read_at')->update(['read_at' => now()]);
            $thread = Message::with(['sender', 'recipient', 'order'])->where(function ($q) use ($admin, $selectedPartner) {
                $q->where(fn ($x) => $x->where('sender_id', $admin->id)->where('recipient_id', $selectedPartner->id))
                    ->orWhere(fn ($x) => $x->where('sender_id', $selectedPartner->id)->where('recipient_id', $admin->id));
            })->oldest()->get();
        }

        $messageRecipients = User::query()
            ->whereIn('role', User::PUBLIC_ROLES)
            ->where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'role']);

        if (! $selectedPartner && $request->filled('partner')) {
            $selectedPartner = $messageRecipients->firstWhere('id', $selectedPartnerId);
        }

        $announcementRecords = AdminAnnouncement::latest()->paginate(15, ['*'], 'announcements_page')->withQueryString();
        return view('Admin.messages', compact('conversations', 'selectedPartner', 'thread', 'announcementRecords', 'messageRecipients'));
    }

    public function sendMessage(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'recipient_id' => ['required', 'integer', 'exists:users,id'],
            'order_id' => ['nullable', 'integer', 'exists:orders,id'],
            'body' => ['required', 'string', 'max:5000'],
        ]);
        abort_if((int) $validated['recipient_id'] === $request->user()->id, 422);
        Message::create(['sender_id' => $request->user()->id, ...$validated]);
        $this->audit($request, 'message.sent', User::find($validated['recipient_id']), 'Admin sent a direct message.');
        return redirect()->route('admin.messages', ['partner' => $validated['recipient_id']])->with('success', 'Message sent.');
    }

    public function storeAnnouncement(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'body' => ['required', 'string', 'max:5000'],
            'audience' => ['required', Rule::in(['all', 'buyers', 'sellers', 'logistics', 'riders'])],
            'priority' => ['required', Rule::in(['standard', 'important', 'urgent'])],
            'publish_at' => ['nullable', 'date'],
            'intent' => ['required', Rule::in(['draft', 'publish'])],
        ]);
        $publishAt = filled($validated['publish_at'] ?? null) ? now()->parse($validated['publish_at']) : now();
        $status = $validated['intent'] === 'draft' ? 'draft' : ($publishAt->isFuture() ? 'scheduled' : 'draft');
        $announcement = AdminAnnouncement::create([
            ...collect($validated)->except('intent')->all(),
            'publish_at' => $publishAt,
            'status' => $status,
            'created_by' => $request->user()->id,
        ]);
        if ($validated['intent'] === 'publish' && ! $publishAt->isFuture()) $announcement->publish();
        $this->audit($request, 'announcement.'.$announcement->status, $announcement, $announcement->title);
        return back()->with('success', $announcement->status === 'draft' ? 'Announcement saved as draft.' : ($announcement->status === 'scheduled' ? 'Announcement scheduled.' : 'Announcement published.'));
    }

    public function settings(Request $request): View
    {
        $settings = [
            'marketplace_name' => PlatformSetting::valueOf('marketplace_name', config('app.name', 'LIKHAE')),
            'support_email' => PlatformSetting::valueOf('support_email', ''),
            'seller_commission_rate' => PlatformSetting::commissionRate(),
            'registration_enabled' => (bool) PlatformSetting::valueOf('registration_enabled', true),
            'automated_product_risk_signals' => (bool) PlatformSetting::valueOf('automated_product_risk_signals', true),
        ];
        $policies = AdminPolicy::latest('updated_at')->paginate(15, ['*'], 'policies_page')->withQueryString();
        $auditLogs = AdminAuditLog::with('actor')->latest()->paginate(25, ['*'], 'audit_page')->withQueryString();
        return view('Admin.settings', compact('settings', 'policies', 'auditLogs'));
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'marketplace_name' => ['required', 'string', 'max:120'],
            'support_email' => ['nullable', 'email', 'max:255'],
            'seller_commission_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'registration_enabled' => ['nullable', 'boolean'],
            'automated_product_risk_signals' => ['nullable', 'boolean'],
        ]);
        DB::transaction(function () use ($validated, $request): void {
            PlatformSetting::put('marketplace_name', $validated['marketplace_name'], 'string', $request->user()->id);
            PlatformSetting::put('support_email', $validated['support_email'] ?? '', 'string', $request->user()->id);
            PlatformSetting::put('seller_commission_rate', ((float) $validated['seller_commission_rate']) / 100, 'float', $request->user()->id);
            PlatformSetting::put('registration_enabled', $request->boolean('registration_enabled'), 'boolean', $request->user()->id);
            PlatformSetting::put('automated_product_risk_signals', $request->boolean('automated_product_risk_signals'), 'boolean', $request->user()->id);
        });
        $this->audit($request, 'settings.updated', null, 'Platform settings updated.');
        return back()->with('success', 'Platform settings saved.');
    }

    public function storePolicy(Request $request): RedirectResponse
    {
        $validated = $this->policyData($request);
        $policy = AdminPolicy::create([...$validated, 'slug' => $this->uniquePolicySlug($validated['title']), 'created_by' => $request->user()->id, 'updated_by' => $request->user()->id, 'published_at' => $validated['status'] === 'published' ? now() : null]);
        $this->audit($request, 'policy.created', $policy, $policy->title);
        return back()->with('success', 'Policy saved.');
    }

    public function updatePolicy(Request $request, AdminPolicy $policy): RedirectResponse
    {
        $validated = $this->policyData($request);
        $policy->update([...$validated, 'slug' => $this->uniquePolicySlug($validated['title'], $policy->id), 'updated_by' => $request->user()->id, 'published_at' => $validated['status'] === 'published' ? ($policy->published_at ?? now()) : null]);
        $this->audit($request, 'policy.updated', $policy, $policy->title);
        return back()->with('success', 'Policy updated.');
    }

    public function exportAuditLogs(Request $request): StreamedResponse
    {
        $this->audit($request, 'audit.exported', null, 'Audit log exported.');
        return response()->streamDownload(function (): void {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Timestamp', 'Actor', 'Action', 'Target Type', 'Target ID', 'Description', 'IP']);
            AdminAuditLog::with('actor')->orderBy('id')->chunkById(500, function ($logs) use ($out): void {
                foreach ($logs as $log) fputcsv($out, [$log->created_at, $log->actor?->email, $log->action, $log->target_type, $log->target_id, $log->description, $log->ip_address]);
            });
            fclose($out);
        }, 'likhae-audit-'.now()->format('Ymd-His').'.csv', ['Content-Type' => 'text/csv']);
    }

    public function account(Request $request): View
    {
        $adminUser = $request->user();
        $preferences = AdminPreference::firstOrCreate(['user_id' => $adminUser->id], ['notification_preferences' => ['registrations' => true, 'risk' => true, 'finance' => true, 'messages' => true]]);
        return view('Admin.account', compact('adminUser', 'preferences'));
    }

    public function updateAccount(Request $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:80'],
            'last_name' => ['required', 'string', 'max:80'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'contact_number' => ['nullable', 'string', 'max:30'],
        ]);
        $user->fill($validated);
        $user->name = trim($validated['first_name'].' '.$validated['last_name']);
        $user->save();
        $this->audit($request, 'account.updated', $user, 'Administrator profile updated.');
        return back()->with('success', 'Profile updated.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'min:8', 'max:72'],
        ]);
        $request->user()->update(['password' => Hash::make($validated['password'])]);
        $this->audit($request, 'account.password_changed', $request->user(), 'Administrator password changed.');
        return back()->with('success', 'Password changed.');
    }

    public function updatePreferences(Request $request): RedirectResponse
    {
        $prefs = ['registrations' => $request->boolean('registrations'), 'risk' => $request->boolean('risk'), 'finance' => $request->boolean('finance'), 'messages' => $request->boolean('messages')];
        AdminPreference::updateOrCreate(['user_id' => $request->user()->id], ['notification_preferences' => $prefs]);
        $this->audit($request, 'account.preferences_updated', $request->user(), 'Administrator notification preferences updated.');
        return back()->with('success', 'Notification preferences saved.');
    }

    public function notifications(Request $request): View
    {
        $notifications = WorkspaceNotification::where('user_id', $request->user()->id)->latest()->paginate(25)->withQueryString();
        return view('Admin.notifications', compact('notifications'));
    }

    public function markNotificationRead(Request $request, WorkspaceNotification $notification): RedirectResponse
    {
        abort_unless($notification->user_id === $request->user()->id, 403);
        $notification->update(['read_at' => now()]);
        return back();
    }

    public function markNotificationsRead(Request $request): RedirectResponse
    {
        WorkspaceNotification::where('user_id', $request->user()->id)->whereNull('read_at')->update(['read_at' => now()]);
        return back()->with('success', 'All notifications marked as read.');
    }

    private function reportOrderQuery(array $filters)
    {
        $query = Order::query();
        if (! empty($filters['status'])) $query->where('status', $filters['status']);
        if (! empty($filters['date_from'])) $query->whereDate('created_at', '>=', $filters['date_from']);
        if (! empty($filters['date_to'])) $query->whereDate('created_at', '<=', $filters['date_to']);
        return $query;
    }

    private function policyData(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'revision' => ['nullable', 'string', 'max:40'],
            'body' => ['required', 'string'],
            'status' => ['required', Rule::in(['draft', 'published'])],
        ]);
    }

    private function uniqueCategorySlug(string $name, ?int $ignore = null): string
    {
        return $this->uniqueSlug(Category::query(), $name, $ignore);
    }

    private function uniquePolicySlug(string $name, ?int $ignore = null): string
    {
        return $this->uniqueSlug(AdminPolicy::query(), $name, $ignore);
    }

    private function uniqueSlug($query, string $name, ?int $ignore = null): string
    {
        $base = Str::slug($name) ?: 'item';
        $slug = $base;
        $i = 2;
        while ((clone $query)->when($ignore, fn ($q) => $q->where('id', '!=', $ignore))->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }
        return $slug;
    }

    private function audit(Request $request, string $action, mixed $target = null, ?string $description = null, array $metadata = []): void
    {
        AdminAuditLog::create([
            'actor_id' => $request->user()?->id,
            'action' => $action,
            'target_type' => is_object($target) ? class_basename($target) : null,
            'target_id' => is_object($target) && method_exists($target, 'getKey') ? $target->getKey() : null,
            'description' => $description,
            'ip_address' => $request->ip(),
            'metadata' => $metadata ?: null,
        ]);
    }
}
