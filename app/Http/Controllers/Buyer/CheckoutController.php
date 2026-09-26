<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Buyer\Address;
use App\Services\Marketplace\CartService;
use App\Services\Marketplace\CheckoutService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly CartService $cartService,
        private readonly CheckoutService $checkoutService,
    ) {}

    public function show(Request $request): View|RedirectResponse
    {
        $selectedIds = array_map('intval', (array) $request->session()->get('checkout_cart_item_ids', []));
        $items = $this->cartService->selectedItems($request->user(), $selectedIds);

        if ($items->isEmpty()) {
            return redirect()->route('buyer.cart')->with('buyer_notice', 'Your cart is empty.');
        }

        $voucherCodes = (array) $request->session()->get('checkout_voucher_codes', []);
        $preview = $this->checkoutService->preview($items, $voucherCodes);

        return view('Buyer.checkout', [
            'cartItems' => $items,
            'addresses' => $request->user()->addresses()->orderByDesc('is_default')->latest()->get(),
            'preview' => $preview,
            'voucherCodes' => $voucherCodes,
        ]);
    }

    public function select(Request $request): RedirectResponse
    {
        $ids = array_values(array_filter(array_map('intval', (array) $request->input('cart_item_ids', []))));
        $voucherCodes = collect((array) $request->input('voucher_codes', []))
            ->mapWithKeys(fn ($value, $key) => [(int) $key => mb_strtoupper(trim((string) $value))])
            ->filter()
            ->all();

        $request->session()->put('checkout_cart_item_ids', $ids);
        $request->session()->put('checkout_voucher_codes', $voucherCodes);

        return redirect()->route('buyer.checkout');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'address_id' => ['required', 'integer', Rule::exists('addresses', 'id')->where('user_id', $request->user()->id)],
            'payment_method' => ['required', Rule::in(['COD', 'ONLINE'])],
            'voucher_codes' => ['nullable', 'array'],
            'voucher_codes.*' => ['nullable', 'string', 'max:80'],
        ]);

        $selectedIds = array_map('intval', (array) $request->session()->get('checkout_cart_item_ids', []));
        $items = $this->cartService->selectedItems($request->user(), $selectedIds);
        $address = Address::query()->where('user_id', $request->user()->id)->findOrFail((int) $data['address_id']);
        $voucherCodes = collect((array) $request->input('voucher_codes', []))
            ->mapWithKeys(fn ($value, $key) => [(int) $key => mb_strtoupper(trim((string) $value))])
            ->filter()
            ->all();

        $order = $this->checkoutService->placeOrder($request->user(), $address, $items, $data['payment_method'], $voucherCodes);

        $request->session()->forget(['checkout_cart_item_ids', 'checkout_voucher_codes']);

        return redirect()
            ->route('buyer.orders.success', ['order' => $order->order_number])
            ->with('buyer_notice', 'Order placed successfully.');
    }
}
