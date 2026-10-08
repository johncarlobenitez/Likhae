<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\SellerProductSuspended;
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
use App\Models\Communication\Conversation;
use App\Models\Seller\Category;
use App\Models\Seller\Product;
use App\Models\Seller\SellerOrder;
use App\Models\Seller\SellerProfile;
use App\Models\User;
use App\Services\Communication\ConversationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Illuminate\Database\Query\Builder as QueryBuilder;

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
        $filters = $this->reportFilters($request);
        $items = $this->reportItemsQuery($filters);
        $salesExpression = $this->reportItemSalesExpression();
        $commissionExpression = $this->reportAllocatedCommissionExpression();

        $summary = (clone $items)->selectRaw("\n            COALESCE(SUM($salesExpression), 0) AS sales,\n            COALESCE(SUM($commissionExpression), 0) AS commission,\n            COALESCE(SUM(oi.quantity), 0) AS units_sold,\n            COUNT(DISTINCT so.id) AS order_count\n        ")->first();

        $sortColumn = match ($filters['sort'] ?? 'sales') {
            'units' => 'units_sold',
            'commission' => 'commission',
            'orders' => 'order_count',
            default => 'sales',
        };

        $shops = (clone $items)
            ->selectRaw("sp.id, sp.business_name AS label, shop_category.name AS shop_category, COUNT(DISTINCT so.id) AS order_count, SUM(oi.quantity) AS units_sold, SUM($salesExpression) AS sales, SUM($commissionExpression) AS commission")
            ->groupBy('sp.id', 'sp.business_name', 'shop_category.name')
            ->orderByDesc($sortColumn)
            ->orderBy('label')
            ->limit(10)
            ->get();

        $categories = (clone $items)
            ->selectRaw("c.id, COALESCE(c.name, 'Uncategorized') AS category_name, parent_category.name AS parent_category_name, COUNT(DISTINCT so.id) AS order_count, SUM(oi.quantity) AS units_sold, SUM($salesExpression) AS sales, SUM($commissionExpression) AS commission")
            ->groupBy('c.id', 'c.name', 'parent_category.name')
            ->orderByDesc($sortColumn)
            ->orderBy('category_name')
            ->limit(10)
            ->get();

        $products = (clone $items)
            ->selectRaw("oi.product_id AS id, oi.product_name AS label, sp.business_name AS shop_name, COALESCE(c.name, 'Uncategorized') AS category_name, parent_category.name AS parent_category_name, COUNT(DISTINCT so.id) AS order_count, SUM(oi.quantity) AS units_sold, SUM($salesExpression) AS sales, SUM($commissionExpression) AS commission")
            ->groupBy('oi.product_id', 'oi.product_name', 'sp.business_name', 'c.name', 'parent_category.name')
            ->orderByDesc($sortColumn)
            ->orderBy('label')
            ->limit(10)
            ->get();

        $orderItemRecords = (clone $items)
            ->join('orders as o', 'o.id', '=', 'so.order_id')
            ->leftJoin('users as buyer', 'buyer.id', '=', 'o.buyer_user_id')
            ->select([
                'oi.id',
                'oi.product_name',
                'oi.sku',
                'oi.quantity',
                'so.seller_order_number',
                'so.status',
                'so.created_at',
                'sp.business_name',
                'c.name as category_name',
                'parent_category.name as parent_category_name',
                'buyer.first_name as buyer_first_name',
                'buyer.middle_initial as buyer_middle_initial',
                'buyer.last_name as buyer_last_name',
                'ct.status as commission_status',
            ])
            ->selectRaw("$salesExpression AS sales, $commissionExpression AS commission")
            ->orderByDesc('so.created_at')
            ->orderByDesc('oi.id')
            ->paginate(20)
            ->withQueryString();

        return view('Admin.reports', [
            'orderItemRecords' => $orderItemRecords,
            'summary' => $summary,
            'shops' => $shops,
            'categories' => $categories,
            'products' => $products,
            'shopOptions' => SellerProfile::query()->with('primaryCategory')->orderBy('business_name')->get(['id', 'business_name', 'primary_category_id']),
            'categoryOptions' => Category::query()->with('parent')->where('is_active', true)->orderBy('parent_id')->orderBy('name')->get(),
            'productOptions' => Product::query()->with('sellerProfile')->orderBy('name')->get(['id', 'name', 'seller_profile_id']),
            'filters' => $filters,
        ]);
    }

    /** @return array<string, mixed> */
    private function reportFilters(Request $request): array
    {
        return $request->validate([
            'status' => ['nullable', 'string', 'in:PLACED,CONFIRMED,PREPARING,READY_FOR_PICKUP,PICKED_UP,COMPLETED,CANCELLED'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'seller_profile_id' => ['nullable', 'integer', 'exists:seller_profiles,id'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'product_id' => ['nullable', 'integer', 'exists:products,id'],
            'sort' => ['nullable', 'string', 'in:sales,units,commission,orders'],
        ]);
    }

    /** @param array<string, mixed> $filters */
    private function reportItemsQuery(array $filters): QueryBuilder
    {
        return DB::table('order_items as oi')
            ->join('seller_orders as so', 'so.id', '=', 'oi.seller_order_id')
            ->join('seller_profiles as sp', 'sp.id', '=', 'so.seller_profile_id')
            ->leftJoin('products as p', 'p.id', '=', 'oi.product_id')
            ->leftJoin('categories as c', 'c.id', '=', 'p.category_id')
            ->leftJoin('categories as parent_category', 'parent_category.id', '=', 'c.parent_id')
            ->leftJoin('categories as shop_category', 'shop_category.id', '=', 'sp.primary_category_id')
            ->leftJoin('commission_transactions as ct', 'ct.seller_order_id', '=', 'so.id')
            ->when($filters['status'] ?? null, fn (QueryBuilder $query, string $status) => $query->where('so.status', $status))
            ->when($filters['date_from'] ?? null, fn (QueryBuilder $query, string $date) => $query->whereDate('so.created_at', '>=', $date))
            ->when($filters['date_to'] ?? null, fn (QueryBuilder $query, string $date) => $query->whereDate('so.created_at', '<=', $date))
            ->when($filters['seller_profile_id'] ?? null, fn (QueryBuilder $query, int $sellerId) => $query->where('so.seller_profile_id', $sellerId))
            ->when($filters['category_id'] ?? null, fn (QueryBuilder $query, int $categoryId) => $query->where(function (QueryBuilder $categoryQuery) use ($categoryId): void {
                $categoryQuery->where('p.category_id', $categoryId)->orWhere('c.parent_id', $categoryId);
            }))
            ->when($filters['product_id'] ?? null, fn (QueryBuilder $query, int $productId) => $query->where('oi.product_id', $productId));
    }

    private function reportItemSalesExpression(): string
    {
        return 'CASE WHEN so.item_subtotal > 0 THEN oi.line_total * GREATEST(so.item_subtotal - so.voucher_discount, 0) / so.item_subtotal ELSE 0 END';
    }

    private function reportAllocatedCommissionExpression(): string
    {
        return "CASE WHEN ct.status IS NOT NULL AND ct.status <> 'VOID' AND so.item_subtotal > 0 THEN ct.commission_amount * oi.line_total / so.item_subtotal ELSE 0 END";
    }

    public function messages(Request $request, ConversationService $conversationService): View
    {
        $conversations = $conversationService->listFor($request->user());
        $conversations->each(fn ($conversation) => $conversationService->markRead($conversation, $request->user()));

        return view('Admin.messages', [
            'conversations' => $conversations,
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
            'reason' => ['required_if:status,SUSPENDED', 'nullable', 'string', 'max:1000'],
        ]);

        $mailDetails = null;

        if ($data['status'] === 'SUSPENDED') {
            $product->loadMissing(['sellerProfile.user', 'category', 'variants']);
            $reason = trim((string) $data['reason']);
            $seller = $product->sellerProfile;
            $sellerUser = $seller?->user;
            $actionUrl = route('seller.products', ['mode' => 'edit', 'product' => $product->id]);

            $case = SellerComplianceCase::create([
                'case_number' => 'CMP-'.now()->format('YmdHis').'-'.strtoupper(Str::random(4)),
                'seller_profile_id' => $product->seller_profile_id,
                'product_id' => $product->id,
                'opened_by_admin_user_id' => $request->user()->id,
                'violation_type' => 'PRODUCT_MODERATION',
                'description' => $reason,
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

            $product->update(['status' => $data['status']]);

            if ($sellerUser) {
                $productName = $product->name;
                $caseNumber = $case->case_number;
                $notificationMessage = 'Your product "'.$productName.'" (Product ID #'.$product->id.') was suspended on '
                    .now()->format('M j, Y \\a\\t g:i A').'. Reason: '.$reason
                    .'. Case '.$caseNumber.'. The listing is unavailable to buyers while suspended. Review and correct the listing, then contact LIKHAE support with the case number if you need clarification or a review.';

                Notification::create([
                    'user_id' => $sellerUser->id,
                    'type' => 'PRODUCT_SUSPENDED',
                    'title' => 'Product suspended: '.$productName,
                    'message' => $notificationMessage,
                    'reference_type' => 'PRODUCT',
                    'reference_id' => $product->id,
                    'action_url' => $actionUrl,
                ]);

                $mailDetails = [
                    'sellerUser' => $sellerUser,
                    'product' => $product,
                    'case' => $case,
                    'reason' => $reason,
                    'actionUrl' => $actionUrl,
                ];
            }
        } else {
            $product->update(['status' => $data['status']]);
        }

        $emailSent = false;

        if ($mailDetails && filled($mailDetails['sellerUser']->email)) {
            try {
                Mail::to($mailDetails['sellerUser']->email)->send(new SellerProductSuspended(
                    $mailDetails['product'],
                    $mailDetails['case'],
                    $mailDetails['reason'],
                    $mailDetails['actionUrl'],
                ));
                $emailSent = true;
            } catch (\Throwable $exception) {
                Log::warning('Product suspension email could not be sent.', [
                    'product_id' => $mailDetails['product']->id,
                    'seller_user_id' => $mailDetails['sellerUser']->id,
                    'exception' => $exception->getMessage(),
                ]);
            }
        }

        $statusMessage = ! $mailDetails
            ? 'Product moderation updated.'
            : ($emailSent
                ? 'Product suspended. The seller was sent an in-app notification and email.'
                : 'Product suspended and in-app notification saved. The seller email could not be sent; check the mail configuration.');

        return back()->with('status', $statusMessage);
    }

    public function categories(Request $request): View
    {
        $lineOfBusiness = Category::whereNull('parent_id')->orderBy('name')->get();
        $subcategories = Category::whereNotNull('parent_id')->with('parent')->orderBy('name')->get();

        return view('Admin.category-management.index', [
            'lineOfBusiness' => $lineOfBusiness,
            'subcategories' => $subcategories,
        ]);
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

    public function updateCategoryStatus(Request $request, Category $category): RedirectResponse
    {
        $request->validate([
            'is_active' => ['required', 'boolean'],
        ]);

        $category->update(['is_active' => $request->boolean('is_active')]);

        return back()->with('status', 'Category status updated.');
    }

    public function storeSubcategory(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
        ]);

        Category::create([
            'parent_id' => $data['category_id'],
            'name' => $data['name'],
            'slug' => Str::slug($data['name']).'-'.strtolower(Str::random(4)),
            'description' => $data['description'] ?? null,
            'is_active' => true,
        ]);

        return back()->with('status', 'Subcategory added.');
    }

    public function updateSubcategory(Request $request, Category $category): RedirectResponse
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

        return back()->with('status', 'Subcategory updated.');
    }

    public function updateSubcategoryStatus(Request $request, Category $category): RedirectResponse
    {
        $request->validate([
            'is_active' => ['required', 'boolean'],
        ]);

        $category->update(['is_active' => $request->boolean('is_active')]);

        return back()->with('status', 'Subcategory status updated.');
    }

    public function sendMessage(Request $request, ConversationService $conversationService): RedirectResponse
    {
        $data = $request->validate([
            'recipient_user_id' => ['required', 'integer', 'exists:users,id'],
            'conversation_id' => ['nullable', 'integer', 'exists:conversations,id'],
            'body' => ['required', 'string', 'max:5000'],
        ]);

        $context = [];
        if (! empty($data['conversation_id'])) {
            $conversation = Conversation::query()
                ->whereKey((int) $data['conversation_id'])
                ->whereHas('participants', fn ($query) => $query->where('users.id', $request->user()->id))
                ->whereHas('participants', fn ($query) => $query->where('users.id', (int) $data['recipient_user_id']))
                ->firstOrFail();
            $context = $conversationService->contextFor($conversation);
        }

        $conversationService->send($request->user(), (int) $data['recipient_user_id'], $data['body'], $context);

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
            'commission_rate' => ['required', 'numeric', 'in:0.10'],
            'registration_enabled' => ['required', 'boolean'],
        ]);
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
            'resolution' => [in_array($request->input('status'), ['RESOLVED', 'REJECTED'], true) ? 'required' : 'nullable', 'string', 'max:5000'],
        ]);
        $dispute->update([
            'assigned_admin_user_id' => $request->user()->id,
            'status' => $data['status'],
            'resolution' => $data['resolution'] ?? null,
            'resolved_at' => in_array($data['status'], ['RESOLVED', 'REJECTED'], true) ? now() : null,
        ]);
        $dispute->load('returnRefundRequest');
        if ($dispute->returnRefundRequest) {
            $dispute->returnRefundRequest->update([
                'status' => match ($data['status']) {
                    'UNDER_REVIEW' => 'UNDER_REVIEW',
                    'RESOLVED' => 'RESOLVED',
                    'REJECTED' => 'REJECTED',
                    default => 'REQUEST_SUBMITTED',
                },
            ]);
        }

        if ($dispute->openedBy && in_array($data['status'], ['RESOLVED', 'REJECTED'], true)) {
            Notification::create([
                'user_id' => $dispute->opened_by_user_id,
                'type' => 'RETURN_REFUND',
                'title' => $data['status'] === 'RESOLVED' ? 'Return / refund request resolved' : 'Return / refund request declined',
                'message' => 'Request '.$dispute->dispute_number.' was '.$data['status'].'. '.($data['resolution'] ?? ''),
                'reference_type' => Dispute::class,
                'reference_id' => $dispute->id,
                'action_url' => $dispute->order ? route('buyer.orders.show', $dispute->order) : null,
            ]);
        }

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

    public function exportReport(Request $request)
    {
        $filters = $this->reportFilters($request);
        $salesExpression = $this->reportItemSalesExpression();
        $commissionExpression = $this->reportAllocatedCommissionExpression();
        $items = $this->reportItemsQuery($filters)
            ->join('orders as o', 'o.id', '=', 'so.order_id')
            ->leftJoin('users as buyer', 'buyer.id', '=', 'o.buyer_user_id')
            ->select([
                'so.seller_order_number',
                'so.status as order_status',
                'so.created_at as order_date',
                'sp.business_name as shop',
                'oi.product_name',
                'oi.sku',
                'c.name as category',
                'parent_category.name as parent_category',
                'oi.quantity',
                'ct.status as commission_status',
                'buyer.first_name as buyer_first_name',
                'buyer.middle_initial as buyer_middle_initial',
                'buyer.last_name as buyer_last_name',
            ])
            ->selectRaw("$salesExpression AS sales, $commissionExpression AS commission")
            ->orderByDesc('so.created_at')
            ->orderByDesc('oi.id');

        return response()->streamDownload(function () use ($items): void {
            $output = fopen('php://output', 'wb');
            fputcsv($output, ['Seller order', 'Order date', 'Order status', 'Buyer', 'Shop', 'Category', 'Product', 'SKU', 'Quantity', 'Item sales (PHP)', 'Allocated commission (PHP)', 'Commission status']);

            foreach ($items->cursor() as $item) {
                fputcsv($output, [
                    $item->seller_order_number,
                    $item->order_date,
                    $item->order_status,
                    collect([$item->buyer_first_name, $item->buyer_middle_initial, $item->buyer_last_name])->filter()->implode(' '),
                    $item->shop,
                    $item->parent_category ? $item->parent_category.' / '.($item->category ?: 'Uncategorized') : ($item->category ?: 'Uncategorized'),
                    $item->product_name,
                    $item->sku,
                    $item->quantity,
                    number_format((float) $item->sales, 2, '.', ''),
                    number_format((float) $item->commission, 2, '.', ''),
                    $item->commission_status ?: 'NOT_CALCULATED',
                ]);
            }

            fclose($output);
        }, 'sales-commission-report-'.now()->format('Ymd-His').'.csv');
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
