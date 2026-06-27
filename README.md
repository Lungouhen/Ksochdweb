# Ksochdweb - Enterprise Non-Profit SaaS Platform

[![Status](https://img.shields.io/badge/status-production%20ready-green)](https://github.com/Lungouhen/Ksochdweb)
[![License](https://img.shields.io/badge/license-MIT-blue)](LICENSE)
[![Laravel](https://img.shields.io/badge/Laravel-12.x-red)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.3+-purple)](https://php.net)

> **The ultimate all-in-one digital operating system for non-profit organizations.**  
> Built for scalability, security, and ease of use. Fully controllable from a single Admin Panel.

---

## 🚀 Quick Start

```bash
# Clone the repository
git clone https://github.com/Lungouhen/Ksochdweb.git
cd Ksochdweb

# Install dependencies
composer install --optimize-autoloader --no-dev
npm install && npm run build

# Setup environment
cp .env.example .env
php artisan key:generate

# Configure your database in .env, then run:
php artisan migrate --seed
php artisan storage:link

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start queue worker (required for emails/backups)
php artisan queue:work --daemon
```

**Default Admin Login:**
- **URL:** `/admin`
- **Email:** `admin@ksochdweb.com`
- **Password:** `password` *(Change immediately!)*

---

## 🌟 Key Features

### 🏛️ Core Governance
- **Executive Term Management:** Scope all data (finance, events, members) to specific terms (e.g., 2025-2026).
- **Double-Entry Financial Ledger:** Professional accounting with budgets, audits, and balance sheets.
- **Grant Lifecycle Management:** From application to disbursement and reporting.

### 🎨 Advanced CMS & Builder
- **7 Premium Themes:** Classic Blog, Editorial, Donation Campaign, Event Hub, Legal, Executive Showcase, Landing Page.
- **Drag & Drop Page Builder:** No-code layout creation with 15+ blocks.
- **Multi-Language Support:** Built-in translation manager and URL prefixing.
- **Rich Text Editors:** Switch between TinyMCE, CKEditor, and Summernote globally.

### 👥 Membership & Community
- **Tiered Memberships:** Recurring billing, proration, and member directory.
- **Event & Ticketing System:** QR codes, seat mapping, and check-in apps.
- **Volunteer Hub:** Shift scheduling, hour tracking, and certificate generation.

### 💰 Fundraising & Payments
- **Multi-Gateway Support:** Stripe, Razorpay, Paytm, Cashfree, PhonePe.
- **Donation Engine:** Recurring gifts, tribute donations, and campaign progress bars.
- **Digital Store:** Sell products or digital downloads with inventory tracking.

### 🛡️ Enterprise Security & Ops
- **Two-Factor Authentication (2FA):** Enforceable for all admin staff.
- **Automated Backups:** Daily DB + File backups to S3/Local with retention policies.
- **Audit Logs:** Track every action taken by any user.
- **Role-Based Access Control (RBAC):** Granular permissions for 12+ developer roles.

### 📱 Modern UX
- **PWA Ready:** Installable on mobile devices with offline support.
- **Optimistic UI:** Instant interactions without page reloads.
- **Command Palette:** Press `Ctrl+K` to navigate anywhere instantly.
- **Dark Mode:** Auto-switching based on system preferences.

---

## 🛠️ Tech Stack

| Component | Technology |
| :--- | :--- |
| **Backend** | Laravel 12.x, PHP 8.3+ |
| **Frontend** | Blade Templates, Tailwind CSS, Alpine.js |
| **Database** | MySQL 8.0+ / PostgreSQL |
| **Assets** | Vite (No CDNs, all local) |
| **Payments** | Stripe, Razorpay, Paytm SDKs |
| **PDF/Excel** | DomPDF, PhpSpreadsheet |
| **Images** | Intervention Image, Spatie Media Library |
| **Security** | Google2FA, Spatie Backup, Activity Log |

---

## 📂 Project Structure

```text
app/
├── Models/             # Eloquent models (User, Post, Donation, etc.)
├── Services/           # Heavy business logic (Ledger, TermManager, PDF)
├── Http/
│   ├── Controllers/    # Admin & Public controllers
│   ├── Requests/       # Form validation classes
│   └── Middleware/     # 2FA, Role, Theme enforcement
├── Jobs/               # Queue jobs (Emails, Backups, Imports)
└── Rules/              # Custom validation rules

resources/
├── views/
│   ├── admin/          # Admin panel views (Dashboard, Settings, Modules)
│   ├── public/         # 7 Premium Themes + Components
│   └── layouts/        # Master layouts (App, Auth, Print)
├── js/                 # Alpine components, FilePond, Chart.js
└── css/                # Tailwind directives & Custom styles

database/
├── migrations/         # 45+ Tables schema
└── seeders/            # Default data (Admin, Roles, Settings)
```

---

## ⚙️ Configuration

### Environment Variables (.env)

```ini
APP_NAME="Ksochdweb"
APP_URL=https://your-domain.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=ksochdweb
DB_USERNAME=root
DB_PASSWORD=secret

# Mail (SMTP)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password

# Payment Gateways (Keys can also be set in Admin Panel)
STRIPE_KEY=sk_test_...
RAZORPAY_KEY=rzp_test_...

# Backup
BACKUP_DESTINATION=s3  # or 'local'
```

### Cron Jobs
Required for automated backups and scheduled tasks:
```bash
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

### Queue Worker
Required for emails, PDF generation, and imports:
```bash
php artisan queue:work --daemon --tries=3
```

---

## 📸 Screenshots & Demos

*(Note: Screenshots will be generated upon first login)*
- **Dashboard:** Real-time analytics, financial charts, recent activities.
- **Page Builder:** Drag-and-drop interface for creating landing pages.
- **Donation Campaign:** Interactive progress bars and donor tickers.
- **Admin Settings:** Comprehensive control over branding, security, and integrations.

---

## 🔒 Security Features

1.  **2FA Enforcement:** Optional global toggle for Google Authenticator.
2.  **Rate Limiting:** API and form submission throttling.
3.  **CSRF & XSS Protection:** Built-in Laravel defenses + HTML Purifier.
4.  **Encrypted Secrets:** Payment keys and 2FA secrets encrypted in DB.
5.  **Activity Logging:** Immutable logs of all admin actions.

---

## 🧪 Testing

Run the test suite to verify installation:
```bash
php artisan test
```

Specific tests available for:
- Payment Gateway Webhooks
- Financial Ledger Balancing
- Role Permissions
- Form Validation

---

## 🤝 Contributing

This project is designed for a **12-Developer Team**. See `AGENT.md` for detailed role assignments and module ownership.

1.  Fork the repository.
2.  Create a feature branch (`git checkout -b feature/AmazingFeature`).
3.  Commit your changes (`git commit -m 'Add AmazingFeature'`).
4.  Push to the branch (`git push origin feature/AmazingFeature`).
5.  Open a Pull Request.

---

## 📄 License

This project is open-sourced software licensed under the [MIT license](LICENSE).

---

## 🆘 Support & Documentation

-   **Full Documentation:** See `AGENT.md` for developer guides.
-   **Issues:** Report bugs via GitHub Issues.
-   **Contact:** support@ksochdweb.org

---

**Built with ❤️ for Non-Profits worldwide.**  
*Empowering organizations to focus on their mission, not their software.*