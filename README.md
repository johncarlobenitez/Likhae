## Registration and administrator approval

Registration at `/register` saves buyers, sellers, logistics operators, and riders to the configured database with hashed passwords and a `pending` status. All applicants need a valid ID; sellers and logistics operators also need a business permit, and riders need vehicle details, OR/CR, and a driver's license. Documents accept JPG, PNG, or PDF files up to 5 MB each.

An active administrator signs in at `/login` and opens `/admin/registrations` to download documents, approve an application, or reject it with a reason. Decisions save the reviewer and review time. Reviewed applications cannot be decided a second time. Only approved accounts can sign in or use protected workspaces. Buyers and sellers use `/login`; logistics operators and riders use `/logistics/login`.

For a fresh setup, configure the database in `.env`, then run:

```sh
php artisan migrate
php artisan app:create-admin
npm run build
```

For local development, run:

```sh
php artisan serve
```

This starts Laravel and the Vite development server together, so the site loads with CSS and JavaScript and reflects asset changes automatically. To open the development site on a phone, connect the phone and computer to the same Wi-Fi and browse to the computer's LAN address (for example, `http://192.168.1.52:8000`). Laravel and Vite bind to the local network for this development workflow. If Windows Firewall prompts, allow PHP/Node on your private network. `VITE_HMR_HOST` can be set in `.env` to override the automatically detected LAN address when the computer has multiple network interfaces. `composer run dev` remains an alternative.

Registration sends a six-digit email verification code before an application can be submitted. For Gmail SMTP, configure these values in the local `.env` using a Google App Password (with two-step verification enabled):

```dotenv
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_SCHEME=smtp
MAIL_USERNAME=your-address@gmail.com
MAIL_PASSWORD="your-new-google-app-password"
MAIL_FROM_ADDRESS=your-address@gmail.com
MAIL_FROM_NAME="LIKHAE"
```

Keep the password out of source control and use a newly generated app password if one has been exposed. Clear cached Laravel configuration after changing mail settings with `php artisan config:clear`.

For production or hosting uploads, always run:

```sh
composer run deploy
```

This rebuilds the Vite assets, removes the local Vite hot marker, clears stale Laravel caches, and refreshes the production caches. Do not upload `public/hot`; it is only for local Vite development and will make the live site try to load assets from `127.0.0.1`.

The admin command privately prompts for a password and never overwrites an existing account. `db:seed` provides demo accounts only in local/testing environments and preserves existing credentials. Registration does not create administrator accounts.

For an existing installation, run `php artisan app:secure-registration-documents` after migration to move old public uploads into private storage. New uploads are private automatically and can only be downloaded through an authenticated admin route.

Address dropdowns require `PSGC_API_TOKEN` in `.env`. Approval and rejection decision emails are sent to the applicant using the configured mail settings. PHP's `mbstring` extension must be enabled. For this XAMPP CLI, commands can also be run with `php -d extension=mbstring artisan ...`.

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
