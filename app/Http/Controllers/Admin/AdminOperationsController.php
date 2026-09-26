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
use App\Models\Buyer\Order;
use App\Models\Buyer\Payment;
use App\Models\Seller\Category;
use App\Models\Seller\Product;
use App\Services\Communication\ConversationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminOperationsController extends Controller
{
    public function products(Request $request): View
    {
        $products = Product::query()
            ->with(['sellerProfile.user', 'category', 'variants'])
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

    public function reports(): View
    {
        return view('Admin.reports', [
            'ordersByStatus' => Order::selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status'),
            'paymentsByStatus' => Payment::selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status'),
            'commissionTotal' => (float) CommissionTransaction::sum('commission_amount'),
        ]);
    }

    public function messages(Request $request, ConversationService $conversationService): View
    {
        return view('Admin.messages', ['conversations' => $conversationService->listFor($request->user())]);
    }

    public function settings(): View
    {
        return view('Admin.settings', [
            'settings' => PlatformSetting::orderBy('key')->get(),
            'policies' => PlatformPolicy::latest()->get(),
            'announcements' => Announcement::latest()->get(),
        ]);
    }

    public function account(Request $request): View
    {
        return view('Admin.account', ['user' => $request->user()]);
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
        foreach ($request->except(['_token', '_method']) as $key => $value) {
            PlatformSetting::put($key, $value, is_numeric($value) ? 'decimal' : 'string', $request->user()->id);
        }

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

    public function updateRefund(): RedirectResponse
    {
        return back()->with('status', 'Refund module is not part of the final 57-table schema.');
    }

    public function updatePreferences(): RedirectResponse
    {
        return back()->with('status', 'Admin preferences table is not part of the final 57-table schema.');
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
