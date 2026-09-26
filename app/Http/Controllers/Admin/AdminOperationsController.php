<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Announcement;
use App\Models\Admin\AuditLog;
use App\Models\Admin\CommissionTransaction;
use App\Models\Admin\Dispute;
use App\Models\Admin\Notification;
use App\Models\Admin\PlatformPolicy;
use App\Models\Admin\PlatformSetting;
use App\Models\Admin\SellerComplianceAction;
use App\Models\Admin\SellerComplianceCase;
use App\Models\Buyer\Payment;
use App\Models\Seller\Category;
use App\Models\Seller\Product;
use App\Models\Seller\SellerOrder;
use App\Models\User;
use App\Services\Communication\ConversationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminOperationsController extends Controller
{
    public function products(Request $request): View
    {
        $products = Product::query()
            ->with(['sellerProfile.user', 'sellerProfile.primaryCategory', 'category', 'variants'])
            ->when($request->filled('status'), fn ($query) => $query->where('status', strtoupper($request->query('status'))))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('Admin.products', ['products' => $products, 'categories' => Category::orderBy('name')->get()]);
    }

    public function compliance(): View
    {
        return view('Admin.compliance', [
            'cases' => SellerComplianceCase::with(['sellerProfile.user', 'product', 'actions.performedBy'])->latest()->paginate(20),
        ]);
    }

    public function complaints(): View
    {
        return view('Admin.complaints', [
            'disputes' => Dispute::with(['openedBy', 'assignedAdmin', 'order', 'sellerOrder', 'shipment', 'evidence'])->latest()->paginate(20),
        ]);
    }

    public function finance(): View
    {
        return view('Admin.finance', [
            'payments' => Payment::with('order.buyer')->latest()->paginate(15),
            'commissions' => CommissionTransaction::with('sellerOrder.sellerProfile.user')->latest()->paginate(15),
            'totals' => [
                'payments' => (float) Payment::where('status', 'PAID')->sum('amount'),
                'commission' => (float) CommissionTransaction::sum('commission_amount'),
                'pending_commission' => (float) CommissionTransaction::where('status', 'PENDING')->sum('commission_amount'),
            ],
        ]);
    }

    public function reports(Request $request): View
    {
        $data = $request->validate([
            'status' => ['nullable', 'string', 'in:PLACED,CONFIRMED,PREPARING,READY_FOR_PICKUP,PICKED_UP,COMPLETED,CANCELLED'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);
        $orders = SellerOrder::query()
            ->with(['order.buyer', 'order.payments', 'sellerProfile.user'])
            ->when($data['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($data['date_from'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '>=', $date))
            ->when($data['date_to'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '<=', $date));

        return view('Admin.reports', [
            'orderRecords' => (clone $orders)->latest()->paginate(20)->withQueryString(),
            'gross' => (float) (clone $orders)->sum('grand_total'),
            'commission' => (float) CommissionTransaction::query()->whereIn('seller_order_id', (clone $orders)->select('id'))->sum('commission_amount'),
            'completed' => (clone $orders)->where('status', 'COMPLETED')->count(),
            'cancelled' => (clone $orders)->where('status', 'CANCELLED')->count(),
        ]);
    }

    public function messages(Request $request, ConversationService $conversationService): View
    {
        return view('Admin.messages', [
            'conversations' => $conversationService->listFor($request->user()),
            'contacts' => User::query()->where('status', 'ACTIVE')->whereKeyNot($request->user()->id)->orderBy('first_name')->get(),
            'announcements' => Announcement::query()->latest()->paginate(10),
        ]);
    }

    public function settings(): View
    {
        return view('Admin.settings', [
            'settings' => [
                'platform_name' => PlatformSetting::valueOf('platform_name', 'LIKHAE'),
                'support_email' => PlatformSetting::valueOf('support_email', ''),
                'commission_rate' => PlatformSetting::commissionRate(),
                'registration_enabled' => (bool) PlatformSetting::valueOf('registration_enabled', true),
            ],
            'policies' => PlatformPolicy::latest()->get(),
            'announcements' => Announcement::latest()->get(),
        ]);
    }

    public function account(Request $request): View
    {
        $adminUser = $request->user();

        return view('Admin.account', [
            'adminUser' => $adminUser,
            'preferences' => $adminUser,
        ]);
    }

    public function notifications(Request $request): View
    {
        return view('Admin.notifications', [
            'notifications' => $request->user()->notifications()->latest()->paginate(20),
        ]);
    }

    public function markNotificationsRead(Request $request): RedirectResponse
    {
        $request->user()->notifications()->whereNull('read_at')->update(['read_at' => now()]);

        return back()->with('status', 'Notifications marked as read.');
    }

    public function markNotificationRead(Request $request, Notification $notification): RedirectResponse
    {
        abort_unless((int) $notification->user_id === (int) $request->user()->id, 403);
        $notification->update(['read_at' => now()]);

        return back()->with('status', 'Notification marked as read.');
    }

    public function moderateProduct(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'string', 'in:ACTIVE,SUSPENDED,ARCHIVED,DRAFT'],
            'reason' => ['nullable', 'string', 'max:1000'],
        ]);

        $product->update(['status' => $data['status']]);

        if ($data['status'] === 'SUSPENDED') {
            $case = SellerComplianceCase::create([
                'case_number' => 'CMP-'.now()->format('YmdHis').'-'.strtoupper(Str::random(4)),
                'seller_profile_id' => $product->seller_profile_id,
                'product_id' => $product->id,
                'opened_by_admin_user_id' => $request->user()->id,
                'violation_type' => 'PRODUCT_MODERATION',
                'description' => $data['reason'] ?? 'Product suspended by admin.',
                'status' => 'OPEN',
                'opened_at' => now(),
            ]);

            SellerComplianceAction::create([
                'seller_compliance_case_id' => $case->id,
                'performed_by_user_id' => $request->user()->id,
                'action_type' => 'PRODUCT_SUSPEND',
                'reason' => $data['reason'] ?? null,
                'performed_at' => now(),
            ]);
        }

        return back()->with('status', 'Product moderation updated.');
    }

    public function storeCategory(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'parent_id' => ['nullable', 'integer', 'exists:categories,id'],
            'description' => ['nullable', 'string'],
        ]);

        Category::create([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']).'-'.strtolower(Str::random(4)),
            'parent_id' => $data['parent_id'] ?? null,
            'description' => $data['description'] ?? null,
            'is_active' => true,
        ]);

        return back()->with('status', 'Category added.');
    }

    public function updateCategory(Request $request, Category $category): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $category->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('status', 'Category updated.');
    }

    public function sendMessage(Request $request, ConversationService $conversationService): RedirectResponse
    {
        $data = $request->validate([
            'recipient_user_id' => ['required', 'integer', 'exists:users,id'],
            'body' => ['required', 'string', 'max:5000'],
        ]);

        $conversationService->send($request->user(), (int) $data['recipient_user_id'], $data['body']);

        return back()->with('status', 'Message sent.');
    }

    public function storeAnnouncement(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'published_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date'],
        ]);

        Announcement::create($data + [
            'created_by_user_id' => $request->user()->id,
            'is_active' => true,
        ]);

        return back()->with('status', 'Announcement saved.');
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'platform_name' => ['required', 'string', 'max:100'],
            'support_email' => ['nullable', 'email', 'max:255'],
            'commission_rate' => ['required', 'numeric', 'in:0.10'],
            'registration_enabled' => ['required', 'boolean'],
        ]);
        PlatformSetting::put('platform_name', $data['platform_name'], 'string', $request->user()->id);
        PlatformSetting::put('support_email', $data['support_email'] ?? '', 'string', $request->user()->id);
        PlatformSetting::put('commission_rate', $data['commission_rate'], 'decimal', $request->user()->id);
        PlatformSetting::put('registration_enabled', (bool) $data['registration_enabled'], 'boolean', $request->user()->id);

        return back()->with('status', 'Settings updated.');
    }

    public function storePolicy(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'version' => ['required', 'string', 'max:50'],
            'body' => ['required', 'string'],
            'effective_at' => ['nullable', 'date'],
        ]);

        PlatformPolicy::create($data + [
            'published_by_user_id' => $request->user()->id,
            'published_at' => now(),
            'is_active' => true,
        ]);

        return back()->with('status', 'Policy saved.');
    }

    public function updatePolicy(Request $request, PlatformPolicy $policy): RedirectResponse
    {
        $policy->update($request->validate([
            'title' => ['required', 'string', 'max:255'],
            'version' => ['required', 'string', 'max:50'],
            'body' => ['required', 'string'],
            'effective_at' => ['nullable', 'date'],
            'is_active' => ['nullable', 'boolean'],
        ]));

        return back()->with('status', 'Policy updated.');
    }

    public function updateAccount(Request $request): RedirectResponse
    {
        $request->user()->update($request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'middle_initial' => ['nullable', 'string', 'max:10'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($request->user()->id)],
            'contact_number' => ['required', 'string', 'max:30'],
        ]));

        return back()->with('status', 'Account updated.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $request->user()->update(['password' => Hash::make($data['password'])]);

        return back()->with('status', 'Password updated.');
    }

    public function updateDispute(Request $request, Dispute $dispute): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'string', 'in:OPEN,UNDER_REVIEW,RESOLVED,REJECTED'],
            'resolution' => ['nullable', 'string', 'max:5000'],
        ]);
        $dispute->update([
            'assigned_admin_user_id' => $request->user()->id,
            'status' => $data['status'],
            'resolution' => $data['resolution'] ?? null,
            'resolved_at' => in_array($data['status'], ['RESOLVED', 'REJECTED'], true) ? now() : null,
        ]);

        return back()->with('status', 'Dispute updated.');
    }

    public function updatePreferences(Request $request): RedirectResponse
    {
        $keys = ['registrations', 'risk', 'finance', 'messages'];
        $data = $request->validate(collect($keys)->mapWithKeys(fn (string $key): array => [
            $key => ['nullable', 'boolean'],
        ])->all());
        $preferences = collect($keys)->mapWithKeys(fn (string $key): array => [
            $key => (bool) ($data[$key] ?? false),
        ])->all();

        $request->user()->forceFill(['notification_preferences' => $preferences])->save();

        return back()->with('status', 'Notification preferences saved.');
    }

    public function exportProducts()
    {
        return $this->csv('products.csv', Product::query()->select(['id', 'name', 'status', 'seller_profile_id'])->get()->toArray());
    }

    public function exportFinance()
    {
        return $this->csv('finance.csv', CommissionTransaction::query()->select(['id', 'seller_order_id', 'commission_amount', 'status'])->get()->toArray());
    }

    public function exportReport()
    {
        return $this->exportFinance();
    }

    public function exportAuditLogs()
    {
        return $this->csv('audit-logs.csv', AuditLog::query()->latest()->limit(1000)->get(['id', 'actor_user_id', 'event', 'auditable_type', 'auditable_id', 'created_at'])->toArray());
    }

    private function csv(string $filename, array $rows)
    {
        $content = '';
        if ($rows !== []) {
            $content .= implode(',', array_keys($rows[0]))."\n";
            foreach ($rows as $row) {
                $content .= implode(',', array_map(fn ($v) => '"'.str_replace('"', '""', (string) $v).'"', $row))."\n";
            }
        }

        return Response::make($content, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }
}
