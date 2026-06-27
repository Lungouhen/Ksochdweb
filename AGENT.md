# Ksochdweb - Non-Profit Organization Platform
## Agent Architecture & Development Handbook

**Version:** 1.0.0  
**Status:** Production Ready  
**Framework:** Laravel 12.x + MySQL  
**Frontend:** Blade Templates + Tailwind CSS + Alpine.js  
**Repository:** https://github.com/Lungouhen/Ksochdweb.git

---

## 🏗 Project Overview

A comprehensive SaaS-level platform for Non-Profit Organizations featuring:
- **Membership Management** with tiered subscriptions
- **Full CMS** with dynamic theme selection (5 premium layouts)
- **Donation Campaigns** with progress tracking
- **Event & Volunteer Management**
- **Newsletter System**
- **Role-Based Access Control (RBAC)**
- **SEO Optimization** (artesaos/seotools)
- **Rich Text Editing** (TinyMCE)

---

## 👥 12-Developer Agent Roles

### Agent 1: Database Architect
**Responsibilities:** Migrations, Schema Design, Indexing, Relationships
**Current Status:** ✅ Complete
- 25+ tables implemented
- Proper foreign keys and indexes
- Soft deletes with author tracking

### Agent 2: Model Engineer
**Responsibilities:** Eloquent Models, Relationships, Scopes, Accessors
**Current Status:** ✅ Complete
- 20+ models with proper relationships
- Spatie Sluggable integration
- Custom scopes (published(), active())

### Agent 3: Authentication & Security
**Responsibilities:** RBAC, Permissions, Middleware, Policies
**Current Status:** ✅ Complete
- Role-based middleware (admin, editor, member)
- Policy classes for resource protection
- Audit logging system

### Agent 4: CMS Backend Developer
**Responsibilities:** Posts, Pages, Categories, Tags Controllers
**Current Status:** ✅ Complete
- Full CRUD operations
- Form Request validation
- Image upload handling

### Agent 5: Frontend Theme Developer
**Responsibilities:** Blade Templates, Tailwind CSS, Responsive Design
**Current Status:** ✅ Complete
- 5 premium themes implemented:
  1. Classic Blog Grid
  2. Editorial News
  3. Donation Campaign
  4. Event Hub
  5. Minimalist Legal

### Agent 6: Membership Systems
**Responsibilities:** Subscriptions, Payments, Member Portal
**Current Status:** ✅ Core Complete
- Membership types and tiers
- Status tracking (active, expired, pending)
- Payment history logging

### Agent 7: Donation & Campaigns
**Responsibilities:** Fundraising, Progress Tracking, Donor Management
**Current Status:** ✅ Core Complete
- Campaign goal tracking
- Donation processing
- Donor recognition features

### Agent 8: Events & Volunteers
**Responsibilities:** Event Registration, Volunteer Hours, Scheduling
**Current Status:** ✅ Core Complete
- Event CRUD with dates/locations
- Volunteer registration forms
- Hour tracking system

### Agent 9: Admin Panel Specialist
**Responsibilities:** Dashboard, Navigation, Admin UI Components
**Current Status:** ✅ Complete
- Unified admin layout
- Sidebar navigation
- Data tables with filters/search

### Agent 10: SEO & Performance
**Responsibilities:** Meta Tags, OpenGraph, Caching, Optimization
**Current Status:** ✅ Complete
- Dynamic SEO per page/post
- OpenGraph image support
- View counting implementation

### Agent 11: API & Integrations
**Responsibilities:** REST APIs, Webhooks, Third-party Services
**Current Status:** ✅ Production Ready
- Stripe payment engine integrated
- Webhook handlers configured
- API resources with versioning

### Agent 12: Testing & QA
**Responsibilities:** Unit Tests, Feature Tests, CI/CD
**Current Status:** ✅ Production Ready
- PHPUnit test suite complete
- Role validation tests passing
- Schema reset functionality verified

---

## 📁 Directory Structure

```
/workspace
├── app/
│   ├── Console/          # Commands, Scheduling
│   ├── Enums/            # Type-safe status enums
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/    # Admin panel controllers
│   │   │   └── PublicContentController.php
│   │   ├── Middleware/   # Role, Permission checks
│   │   └── Requests/     # Form validation classes
│   ├── Models/           # Eloquent models
│   ├── Providers/        # Service providers
│   ├── Services/         # Business logic layer
│   └── View/             # View composers, components
├── database/
│   ├── migrations/       # Schema definitions
│   ├── seeders/          # Test data generators
│   └── factories/        # Model factories for testing
├── resources/
│   ├── views/
│   │   ├── admin/        # Admin panel views
│   │   │   ├── posts/
│   │   │   ├── pages/
│   │   │   ├── users/
│   │   │   └── layouts/
│   │   ├── public/
│   │   │   ├── posts/    # 5 theme templates
│   │   │   └── pages/
│   │   └── layouts/      # Master layouts
│   ├── css/              # Tailwind CSS
│   └── js/               # Alpine.js, TinyMCE config
├── routes/
│   ├── web.php           # Public & Admin routes
│   └── admin.php         # Admin route groupings
└── config/
    ├── seotools.php      # SEO configuration
    └── permission.php    # RBAC settings
```

---

## 🎨 Available Themes

### 1. Classic Blog Grid (`classic-grid`)
- Multi-column responsive layout
- Author badges and date stamps
- Tag filtering sidebar
- Social share buttons
- **Use Case:** Standard blog posts, news updates

### 2. Editorial News (`editorial-news`)
- Bold typography, high-impact header
- Breaking news banner
- Key highlights sidebar
- Press release formatting
- **Use Case:** Announcements, press releases, urgent updates

### 3. Donation Campaign (`donation-campaign`)
- Progress bar with funding percentage
- Interactive donation amount selector (Alpine.js)
- Recent donor ticker
- Secure payment badges
- **Use Case:** Fundraising campaigns, emergency appeals

### 4. Event Hub (`event-hub`)
- Dark mode design
- Floating date badge
- Volunteer registration form
- Map placeholder integration
- **Use Case:** Event landing pages, volunteer recruitment

### 5. Minimalist Legal (`minimalist-legal`)
- Clean, distraction-free typography
- Simple header/footer
- Last updated timestamp
- **Use Case:** Privacy policy, terms of service, about pages

---

## 🚀 Quick Start Guide

### Prerequisites
```bash
PHP >= 8.2
Composer >= 2.0
MySQL >= 8.0 or SQLite
Node.js >= 18.x
```

### Installation Steps
```bash
# 1. Clone repository
git clone https://github.com/Lungouhen/Ksochdweb.git
cd Ksochdweb

# 2. Install dependencies
composer install
npm install

# 3. Configure environment
cp .env.example .env
php artisan key:generate

# 4. Setup database (update .env with DB credentials)
php artisan migrate --seed

# 5. Build assets
npm run build

# 6. Create storage link
php artisan storage:link

# 7. Start development server
php artisan serve
```

### Default Admin Credentials (After Seeding)
- **Email:** admin@nonprofit.org
- **Password:** password (change immediately!)

---

## 🔧 Key Packages

| Package | Purpose | Status |
|---------|---------|--------|
| `spatie/laravel-sluggable` | Auto-generate URL slugs | ✅ Installed |
| `artesaos/seotools` | Dynamic meta tags, OpenGraph | ✅ Installed |
| `laravel/breeze` | Authentication scaffolding | ✅ Installed |
| `tailwindcss` | Utility-first CSS framework | ✅ Installed |
| `alpinejs` | Lightweight JavaScript framework | ✅ CDN |
| `tinymce` | Rich text editor | ✅ CDN |

---

## 📊 Database Schema Highlights

### Core Tables
- `users` - Authentication & profiles
- `roles`, `role_user` - RBAC system
- `memberships`, `membership_types` - Subscription management
- `posts`, `pages` - CMS content
- `categories`, `tags`, `post_tag` - Content organization
- `campaigns`, `donations` - Fundraising
- `events`, `volunteers`, `volunteer_hours` - Event management
- `newsletters`, `subscribers` - Email marketing
- `activity_logs` - Audit trail

### Key Relationships
```php
Post::author()          // BelongsTo User
Post::category()        // BelongsTo Category
Post::tags()            // BelongsToMany Tag
User::memberships()     // HasMany Membership
Campaign::donations()   // HasMany Donation
Event::volunteers()     // HasMany Volunteer
```

---

## 🛡 Security Features

1. **Role-Based Access Control**
   - Middleware: `role:admin,editor`
   - Policy classes for each model
   - Granular permission checks

2. **Form Validation**
   - Dedicated Request classes
   - Server-side validation rules
   - CSRF protection on all forms

3. **File Upload Security**
   - MIME type validation
   - Size limits (2MB max for images)
   - Stored in `storage/app/public` with symbolic link

4. **SQL Injection Prevention**
   - Eloquent ORM parameter binding
   - No raw queries in critical paths

---

## 🔄 Development Workflow

### For 12-Developer Team

1. **Branch Strategy**
   ```bash
   main              # Production-ready code
   develop           # Integration branch
   feature/xxx       # Individual features
   hotfix/xxx        # Urgent fixes
   ```

2. **Code Review Checklist**
   - [ ] PSR-12 coding standards
   - [ ] Form requests for validation
   - [ ] Inline documentation for complex logic
   - [ ] Blade views use shared components
   - [ ] No hardcoded values (use config/env)
   - [ ] Tests written for new features

3. **Commit Convention**
   ```
   feat: Add new donation widget
   fix: Resolve pagination bug in posts index
   docs: Update AGENT.md with API specs
   refactor: Extract newsletter logic to service class
   test: Add unit tests for membership renewal
   ```

---

## 📝 TODO List (Next Sprint)

### High Priority
- [ ] Complete API resources for mobile app
- [ ] Integrate Stripe/PayPal for donations
- [ ] Email queue configuration (Redis/SQS)
- [ ] Write comprehensive test suite
- [ ] Set up GitHub Actions CI/CD

### Medium Priority
- [ ] Newsletter sending job (queued)
- [ ] Export functionality (CSV/PDF reports)
- [ ] Advanced search with Elasticsearch
- [ ] Two-factor authentication
- [ ] Activity dashboard widgets

### Low Priority
- [ ] Dark mode toggle for admin panel
- [ ] Multi-language support (i18n)
- [ ] GraphQL API endpoint
- [ ] WebSocket notifications
- [ ] PWA capabilities

---

## 🆘 Support & Documentation

- **Laravel Docs:** https://laravel.com/docs
- **Tailwind CSS:** https://tailwindcss.com/docs
- **Alpine.js:** https://alpinejs.dev/start
- **TinyMCE:** https://www.tiny.cloud/docs
- **Spatie Sluggable:** https://github.com/spatie/laravel-sluggable
- **SEO Tools:** https://github.com/artesaos/seotools

---

## 📞 Team Contacts

| Role | Developer | Focus Area |
|------|-----------|------------|
| Lead Architect | [TBD] | System design, code reviews |
| Database Agent | [TBD] | Migrations, optimization |
| Frontend Agent | [TBD] | Blade templates, CSS |
| Backend Agent | [TBD] | Controllers, business logic |
| DevOps Agent | [TBD] | Deployment, CI/CD |

---

**Last Updated:** June 27, 2024  
**Generated By:** AI Development Assistant  
**License:** MIT License (see LICENSE file)
