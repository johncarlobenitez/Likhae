## Registration and administrator approval

Registration at `/register` saves buyers, sellers, logistics operators, and riders to the configured database with hashed passwords and a `pending` status. All applicants need a valid ID; sellers and logistics operators also need a business permit, and riders need vehicle details, OR/CR, and a driver's license. Documents accept JPG, PNG, or PDF files up to 5 MB each.

An active administrator signs in at `/login` and opens `/admin/registrations` to download documents, approve an application, or reject it with a reason. Decisions save the reviewer and review time. Reviewed applications cannot be decided a second time. Only approved accounts can sign in or use protected workspaces. Buyers and sellers use `/login`; logistics operators and riders use `/logistics/login`.

For a fresh setup, configure the database in `.env`, then run:

```sh
php artisan migrate
php artisan app:create-admin
npm run build
```

The admin command privately prompts for a password and never overwrites an existing account. `db:seed` provides demo accounts only in local/testing environments and preserves existing credentials. Registration does not create administrator accounts.

For an existing installation, run `php artisan app:secure-registration-documents` after migration to move old public uploads into private storage. New uploads are private automatically and can only be downloaded through an authenticated admin route.

Address dropdowns require `PSGC_API_TOKEN` in `.env`. Decision emails are not sent; applicants sign in after approval. PHP's `mbstring` extension must be enabled. For this XAMPP CLI, commands can also be run with `php -d extension=mbstring artisan ...`.

Run the backend checks with `php -d extension=mbstring vendor/phpunit/phpunit/phpunit --columns=80` (tests use an isolated SQLite database).

## Platform User Flow

### Guest

Browse products, categories, stores, and product details. Registration or login is required to purchase, add to cart, save products, message sellers, or manage orders.

### Buyer

Browse products → Add to cart or buy now → Place order → Pay → Track shipment → Confirm receipt → Review product or request return/refund.

### Seller

Manage products and inventory → Receive order → Prepare package → Select courier → Request pickup → Track delivery → Receive order completion and earnings.

### Admin

Approve registrations → Manage users and marketplace activity → Monitor product violations and deliveries → Resolve disputes and refunds → Manage commissions, transactions, policies, and platform settings.

### Overall Flow

Guest registers as Buyer or Seller → Buyer places an order → Seller prepares it → Logistics delivers it → Buyer confirms receipt → Admin monitors and resolves platform issues.
