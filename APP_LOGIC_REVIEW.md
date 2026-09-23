Application logic review — September 22, 2026

This review covers the current local checkout: registered routes, authentication and role middleware, onboarding, catalog and cart behavior, checkout, payments, cancellation, delivery, returns, refunds, payouts, messaging, settings, and deployment assumptions. Application source code was not changed. A production asset build was run, and all database-backed experiments used temporary `likhae_audit_*` schemas that were removed after testing.

P1 means a core workflow, financial correctness, access restriction, or deployment is broken. P2 means a narrower malfunction or an incomplete supporting workflow. “Reproduced” means the behavior was exercised using the actual application's HTTP handlers or services; “source-confirmed” identifies a concrete code mismatch that was not exercised in a real browser or Linux runtime.

1. **P1 — Cart rows use product IDs and minimum prices instead of cart-item IDs and selected-variant prices. Reproduced.**

   [app/Http/Controllers/BuyerController.php:479](C:/xampp/htdocs/railway-likhae/Likhae/app/Http/Controllers/BuyerController.php:479) uses PHP's array union, `$p + [...]`. Existing keys in the product array win, so `id`, `price`, and `stock` are not replaced. The cart template builds update/remove URLs and checkout selections from this wrong ID. A selected PHP 250 variant was displayed as PHP 100; independently, the displayed row ID differed from the actual cart-item ID. With multiple rows, actions may target another item in the buyer's cart or fail authorization/lookup.

   Use an overriding merge or an explicit cart-row structure, and consistently use the cart-item ID for cart operations. See [resources/views/Buyer/cart.blade.php:270](C:/xampp/htdocs/railway-likhae/Likhae/resources/views/Buyer/cart.blade.php:270).

2. **P1 — Buy Now does not add the selected product. Reproduced.**

   [resources/js/Buyer/buyer.js:154](C:/xampp/htdocs/railway-likhae/Likhae/resources/js/Buyer/buyer.js:154) navigates to `/buyer/checkout?buy=...`. [app/Http/Controllers/CheckoutController.php:35](C:/xampp/htdocs/railway-likhae/Likhae/app/Http/Controllers/CheckoutController.php:35) reads existing selected cart rows and never processes `buy`, `quantity`, or `product_variant_id`. An empty-cart buyer is redirected back to the cart; a buyer with selected cart rows sees those rows instead.

   Connect Buy Now to a validated server action that selects the requested variant and quantity before checkout. The existing add-to-cart GET flow also mutates state and should use the existing POST route.

3. **P1 — The normal product-review form produces a server error. Reproduced.**

   [resources/views/Buyer/orders.blade.php:25](C:/xampp/htdocs/railway-likhae/Likhae/resources/views/Buyer/orders.blade.php:25) does not send `order_item_id`. [app/Http/Controllers/BuyerOrderController.php:82](C:/xampp/htdocs/railway-likhae/Likhae/app/Http/Controllers/BuyerOrderController.php:82) declares it nullable but accesses `$data['order_item_id']` directly. Submitting the form without that field returned HTTP 500. Orders containing multiple items additionally need an item selector. Repeating a review with an explicit item ID also returned HTTP 500 from the unique constraint instead of a validation response.

   Supply/select the purchased item, handle missing optional keys, and enforce one review per item at validation and database levels.

4. **P1 — Logistics views have incompatible capitalization for Linux deployment. Source-confirmed.**

   Git tracks `resources/views/Logistics/`, but [routes/logistics.php:20](C:/xampp/htdocs/railway-likhae/Likhae/routes/logistics.php:20) loads `logistics.landing`; [app/Http/Controllers/LogisticsPortalController.php:62](C:/xampp/htdocs/railway-likhae/Likhae/app/Http/Controllers/LogisticsPortalController.php:62) and many other handlers use lowercase names. Even correctly capitalized entry views extend lowercase `logistics.app`, for example [resources/views/Logistics/dashboard/index.blade.php:1](C:/xampp/htdocs/railway-likhae/Likhae/resources/views/Logistics/dashboard/index.blade.php:1). Windows resolves these paths, while a case-sensitive filesystem will report missing views.

   Normalize the tracked directory and all view references consistently. Windows route/page tests do not cover this deployment failure.

5. **P1 — Sellers can undo administrator product moderation. Reproduced.**

   The admin archive action and seller visibility control share the same `is_active` field. After an admin archived a product, the seller's toggle endpoint restored it successfully. See [app/Http/Controllers/AdminOperationsController.php:65](C:/xampp/htdocs/railway-likhae/Likhae/app/Http/Controllers/AdminOperationsController.php:65) and [app/Http/Controllers/SellerProductController.php:75](C:/xampp/htdocs/railway-likhae/Likhae/app/Http/Controllers/SellerProductController.php:75). Product edits can also write this field through `listing_status`.

   Separate administrator moderation status from seller publication status and require both to allow marketplace visibility and purchasing.

6. **P1 — Existing cart items can still be bought after the seller account is suspended. Reproduced.**

   Product discovery checks the owner's active status, but [app/Services/CheckoutService.php:89](C:/xampp/htdocs/railway-likhae/Likhae/app/Services/CheckoutService.php:89) checks only the shop's approval status, product/category state, and inventory. Suspending the owner after the item is added to a cart still allowed checkout. The resulting order belongs to a seller who cannot enter the workspace to fulfill it.

   Recheck the seller owner's active/suspension state inside the checkout transaction. Apply the same consistency check to the selected courier's owner.

7. **P1 — Address codes are silently discarded, and courier matching can select the wrong province. Reproduced.**

   Registration supplies `region_code`, `province_code`, `city_code`, and `barangay_code`, but [app/Models/Address.php:10](C:/xampp/htdocs/railway-likhae/Likhae/app/Models/Address.php:10) omits them from `$fillable`. A supplied city code was saved as null. [app/Services/CheckoutService.php:43](C:/xampp/htdocs/railway-likhae/Likhae/app/Services/CheckoutService.php:43) then falls back to matching the city name alone.

   A buyer address in San Jose, Occidental Mindoro accepted a courier covering San Jose, Nueva Ecija. Preserve and validate location codes; make any legacy fallback include province/region. The account address form/controller also needs to preserve these identifiers.

8. **P1 — Seller cancellation loses reserved inventory and leaves shipments actionable. Reproduced.**

   [app/Http/Controllers/SellerOrderController.php:45](C:/xampp/htdocs/railway-likhae/Likhae/app/Http/Controllers/SellerOrderController.php:45) calls the status transition without restoring stock. Starting with five units, ordering two and cancelling as the seller left three units instead of five. Cancelling after Ready to Ship also left the shipment available for successful rider assignment.

   Centralize cancellation in a transaction that restores inventory once and prevents further shipment operations. [app/Models/SellerOrder.php:67](C:/xampp/htdocs/railway-likhae/Likhae/app/Models/SellerOrder.php:67) and [app/Models/Shipment.php:42](C:/xampp/htdocs/railway-likhae/Likhae/app/Models/Shipment.php:42) currently do not coordinate those effects. The buyer cancellation handler additionally returns HTTP 500 if its optional `note` field is omitted because it accesses the key directly.

9. **P1 — Online Payment is selectable without an implemented payment flow. Source-confirmed.**

   [app/Http/Controllers/CheckoutController.php:68](C:/xampp/htdocs/railway-likhae/Likhae/app/Http/Controllers/CheckoutController.php:68) accepts `online`, and [app/Services/CheckoutService.php:83](C:/xampp/htdocs/railway-likhae/Likhae/app/Services/CheckoutService.php:83) creates a pending payment. There is no gateway session, payment confirmation/webhook handler, or payment route, and seller fulfillment does not require a successful online payment. Receipt confirmation can credit seller earnings through [app/Services/LedgerService.php:16](C:/xampp/htdocs/railway-likhae/Likhae/app/Services/LedgerService.php:16) without a payment check.

   Implement payment collection and verified payment transitions, or remove the online option until available.

10. **P1 — One delivered COD parcel marks a multi-seller order fully paid. Reproduced.**

    [app/Models/Shipment.php:103](C:/xampp/htdocs/railway-likhae/Likhae/app/Models/Shipment.php:103) updates every payment of the parent order and sets its payment status to paid when one COD shipment is delivered. In a two-seller checkout, delivering only the first seller's parcel marked the full order paid while the other seller order remained pending.

    Track collections per shipment/seller order and derive the parent's paid status from the collected total. The existing checkout test actually asserts this incorrect early-paid state, so a green suite does not establish correctness here.

11. **P1 — New return requests cannot reach successful admin refunds through the UI. Reproduced.**

    Buyer requests start as `requested` in [app/Http/Controllers/BuyerOrderController.php:96](C:/xampp/htdocs/railway-likhae/Likhae/app/Http/Controllers/BuyerOrderController.php:96). The admin action permits only `refunded` or `rejected`, but [app/Services/LedgerService.php:59](C:/xampp/htdocs/railway-likhae/Likhae/app/Services/LedgerService.php:59) permits refunds only from `approved` or `disputed`. No active route advances a new request into either accepted state. A buyer-created return submitted to the admin refund action failed with “This return cannot be refunded.”

    Define and wire the approval/dispute transitions, or make the admin resolution action perform the appropriate transition atomically. See [app/Http/Controllers/AdminOperationsController.php:168](C:/xampp/htdocs/railway-likhae/Likhae/app/Http/Controllers/AdminOperationsController.php:168).

12. **P1 — Refund accounting can debit earnings that were never credited. Reproduced.**

    Earnings are credited when a buyer confirms receipt, but returns are permitted immediately after delivery. [app/Services/LedgerService.php:65](C:/xampp/htdocs/railway-likhae/Likhae/app/Services/LedgerService.php:65) always adds a negative seller/platform reversal. Refunding a delivered order before receipt confirmation produced a seller balance of -18,000 centavos despite the seller having received no sale credit.

    Reverse only posted credits, with reference-linked, idempotent entries. Refunding an order also updates `payments.status` without synchronizing `orders.payment_status`; the reproduced result was `refunded` versus `paid`. Handle both status representations and partial-order amounts in the same transaction.

13. **P1 — A newly approved courier has no application workflow for configuring coverage and fees. Source-confirmed.**

    Checkout requires an active `service_areas` record. Partner registration and approval create no such records, and [routes/logistics.php:135](C:/xampp/htdocs/railway-likhae/Likhae/routes/logistics.php:135) exposes only a GET delivery-areas page. [app/Http/Controllers/LogisticsPortalController.php:58](C:/xampp/htdocs/railway-likhae/Likhae/app/Http/Controllers/LogisticsPortalController.php:58) reads existing coverage; it does not generate it from buyer addresses or riders, despite the page wording.

    Add authorized create/update/deactivate operations for coverage and rates. Without external database setup, a newly approved courier cannot become a checkout option.

14. **P1 — Seller payout requests have no connected administrator decision workflow. Source-confirmed.**

    Sellers can request a payout via [routes/Seller.php:48](C:/xampp/htdocs/railway-likhae/Likhae/routes/Seller.php:48). The request reserves their balance. [app/Services/LedgerService.php:39](C:/xampp/htdocs/railway-likhae/Likhae/app/Services/LedgerService.php:39) can mark a payout paid/rejected and reverse a rejected reservation, but no controller or route calls it. The admin finance page lists payment transactions, not payout decisions.

    Add an authorized payout review/decision workflow and record the external payment reference. Until then, requests can remain pending with their balances reserved.

15. **P2 — Changing an email address preserves its verified status. Reproduced for buyers; the same omission exists in seller/admin profile handlers.**

    [app/Http/Controllers/BuyerController.php:380](C:/xampp/htdocs/railway-likhae/Likhae/app/Http/Controllers/BuyerController.php:380), [app/Http/Controllers/SellerAccountController.php:51](C:/xampp/htdocs/railway-likhae/Likhae/app/Http/Controllers/SellerAccountController.php:51), and [app/Http/Controllers/AdminOperationsController.php:417](C:/xampp/htdocs/railway-likhae/Likhae/app/Http/Controllers/AdminOperationsController.php:417) change `email` without clearing `email_verified_at` or sending a new verification notification. A previously verified buyer remained verified after switching to an unverified address.

    Normalize email changes, reset verification, and send a new verification link. Decide whether sensitive account changes should also require password confirmation.

16. **P2 — The administrator creation command omits a required verification step. Reproduced.**

    [app/Console/Commands/CreateAdmin.php:43](C:/xampp/htdocs/railway-likhae/Likhae/app/Console/Commands/CreateAdmin.php:43) creates an active admin but neither verifies the address nor sends verification mail. [routes/Admin.php:10](C:/xampp/htdocs/railway-likhae/Likhae/routes/Admin.php:10) requires `verified`. The new account is therefore redirected to email verification instead of the dashboard. The local mailer is still `log`, so resending writes a message to the log rather than delivering it.

    Make the bootstrap command and its instructions consistent with the desired verification policy. The earlier guidance to create an admin and immediately access the dashboard omitted this requirement.

17. **P2 — Recipient edits are ignored, and rider delivery details use incorrect recipient and amount fields. Reproduced.**

    Checkout displays editable `recipient_name` and `contact_number`, but [app/Http/Controllers/CheckoutController.php:65](C:/xampp/htdocs/railway-likhae/Likhae/app/Http/Controllers/CheckoutController.php:65) ignores them and copies the original saved address. [app/Http/Controllers/RiderController.php:128](C:/xampp/htdocs/railway-likhae/Likhae/app/Http/Controllers/RiderController.php:128) displays the account owner's current name/contact rather than the shipping recipient snapshot.

    The same rider page displayed PHP 200.00 while the actual shipment COD amount was PHP 210.00 including shipping. Save validated checkout recipient changes into the snapshot and show the snapshot plus `cod_amount_minor` on delivery screens.

18. **P2 — Store controls and marketing campaigns are saved without enforcing their advertised effects. Vacation behavior reproduced; other controls source-confirmed.**

    [app/Http/Controllers/SellerAccountController.php:24](C:/xampp/htdocs/railway-likhae/Likhae/app/Http/Controllers/SellerAccountController.php:24) saves vacation mode, visibility, and automatic acceptance. Marketplace discovery and checkout do not consume these settings. A shop on vacation still accepted a new order even though the UI describes vacation mode as pausing orders.

    [app/Http/Controllers/SellerEngagementController.php:65](C:/xampp/htdocs/railway-likhae/Likhae/app/Http/Controllers/SellerEngagementController.php:65) saves discount/voucher campaigns, but checkout never evaluates them or increments usage, and product mapping hardcodes zero discount. The cart's claim that seller discounts are already reflected in prices is not supported by this flow.

19. **P2 — Messaging streams have timing and reconnection defects. Elapsed-time calculation reproduced; reconnect behavior source-confirmed.**

    [app/Http/Controllers/BuyerController.php:214](C:/xampp/htdocs/railway-likhae/Likhae/app/Http/Controllers/BuyerController.php:214) checks `now()->diffInSeconds($started) < 20`. With the installed Carbon version, 30 seconds after the start the result is -30, so the timeout condition remains true. This can occupy a PHP worker until the client disconnects or another timeout intervenes.

    The seller stream returns at most 50 messages and closes. [resources/views/Seller/messages.blade.php:186](C:/xampp/htdocs/railway-likhae/Likhae/resources/views/Seller/messages.blade.php:186) opens EventSource with a fixed initial `after` query, and the server does not emit SSE event IDs. Automatic reconnections repeat the first batch; after 50 new messages, later messages cannot reach that open chat through this stream. Use correct elapsed-time direction and an advancing reconnect cursor.

20. **P2 — A shipment returned after three failures does not resolve the seller order. Source-confirmed.**

    [app/Models/Shipment.php:87](C:/xampp/htdocs/railway-likhae/Likhae/app/Models/Shipment.php:87) makes the shipment terminal `returned`, but its seller order remains `shipped`. Buyer cancellation is unavailable in that state, receipt requires delivery, and returns require a delivered order. Reserved stock and order resolution have no connected completion path.

    Define a returned-to-sender lifecycle, including physical receipt, inventory restoration, and payment resolution. Do not restore stock merely because a parcel was marked returned if it has not arrived back.

21. **P2 — Deployment cleanup relies on a prebuild hook that npm is configured to skip. Source-confirmed by configuration and build output.**

    [.npmrc:1](C:/xampp/htdocs/railway-likhae/Likhae/.npmrc:1) sets `ignore-scripts=true`. [package.json:6](C:/xampp/htdocs/railway-likhae/Likhae/package.json:6) puts hot-marker cleanup in `prebuild`, while the explicit build script only runs Vite. The observed `npm run build` ran Vite without the prebuild cleanup. Consequently, the README's claim that `composer run deploy` removes a development `public/hot` file is not guaranteed by the actual script.

    Invoke the cleanup explicitly from the build/deploy command and verify that a deployment cannot retain the local Vite marker.

A policy decision is needed for courier suspension: suspending a provider blocks its logistics workspace, but its active riders can still update assigned shipments. This was reproduced through the rider HTTP endpoint. If suspension is intended to stop all operations, enforce provider state for riders too; if existing deliveries should continue, document and test that exception. This is kept separate from confirmed defects because the intended rule is not stated.

Other implementation limits found: rider earnings are explicitly displayed as unimplemented; COD remittance has a table but no connected application workflow; admin reporting reads a different commission configuration key from the one used for new seller commissions. The old `routes/Courier.php` and legacy controller/mapping code are not part of the active routing path, so missing legacy views were not counted as active route failures.

Verification and limits:

- All 213 registered routes had existing controller methods and unique route names.
- The existing suite ran 86 tests: 85 passed initially and one stopped because GD was disabled. That remaining test passed with `php -d extension=gd`. No permanent PHP configuration change was made.
- Twenty-three additional focused checks exercised behavior missing from the existing suite. Their expected-correct assertions failed on the current code; several checks describe the same underlying defect, and one concerns the courier-suspension policy above.
- The production frontend build succeeded: 258 modules transformed. The first attempt failed because the execution sandbox blocked Vite's Windows helper process; the authorized retry succeeded.
- No interactive browser, real payment gateway, real email delivery, or Linux runtime was used. Concurrency and deployment persistence need separate verification.
- The active application database was only inspected. Each test schema was created with a random audit-only name and removed afterwards.

The local reproduction suite is [storage/framework/testing/LogicAuditTest.php](C:/xampp/htdocs/railway-likhae/Likhae/storage/framework/testing/LogicAuditTest.php). Its runner is [storage/framework/testing/audit-runner.php](C:/xampp/htdocs/railway-likhae/Likhae/storage/framework/testing/audit-runner.php); run it from the application directory with `php -d extension=gd storage/framework/testing/audit-runner.php storage/framework/testing/LogicAuditTest.php`. It intentionally fails until the corresponding behaviors are corrected and is outside the regular PHPUnit test directories. Existing-suite results and focused-check logs are retained in `storage/framework/testing/audit-*.xml` and `audit-*.txt`.

Repair the cart/Buy Now/review path and Linux view casing first, then the authorization and shipping checks, followed by cancellation, payment/refund accounting, and the missing coverage/payout workflows. Add regression assertions for the intended outcomes rather than preserving the current incorrect behavior.

