# CleanStation context index

Source inspected: 2026-11-10, revision `5ef4bfa33270d936d9a6550428cb8452ac047174`.
Scope: repository structure and selected entry points; not an exhaustive audit or a production verification.

Read this index first, then one relevant reference. Refresh changed areas using Git and source inspection; do not reread the repository by default.

## Stack and layout

- Laravel 11 (`composer.json` constraint `^11.31`), PHP `^8.2`; Composer path repositories under `packages/core/*`.
- Packages expose `Core\...` namespaces, service providers, routes, controllers, requests, services, models and views. Example: `packages/core/orders/composer.json`.
- Laravel Sanctum and Spatie Permission provide authentication/role infrastructure. Arabic/English localization uses Mcamara Laravel Localization and Astrotomic translatable models.
- Web UI: Blade, existing JavaScript including jQuery in the B2B portal, Tailwind 3 and Vite 6. Inspect the actual screen before selecting a UI stack.
- PHPUnit 11 is the existing test framework. MyFatoorah handles payment integration; Redis/Predis and application queues are present in dependencies. Deployment configuration must be verified separately.
- No tracked Flutter/Dart source was found in the repository scan. Mobile API resources are not proof of which fields the mobile UI displays.

## Where to look

| Area | Entry points |
| --- | --- |
| Orders, scheduling, driver/technical APIs | [ORDERS.md](ORDERS.md), `packages/core/orders/src` |
| Business portal, companies and contracts | `packages/core/b2b/src`; order flow in [ORDERS.md](ORDERS.md) |
| Public website, CMS, SEO and sharing | [WEBSITE.md](WEBSITE.md) |
| Products, variants, categories and prices | `packages/core/products/src`, `packages/core/categories/src` |
| Wallet, payment gateway and financial records | `packages/core/wallet/src`, `packages/core/payment-gateways/src`, `packages/core/financials/src` |
| Users, roles, addresses, cities and districts | `packages/core/users/src`, `packages/core/info/src` |
| Notifications and tracking | `packages/core/notification/src`, order observers and analytics services |
| Shared settings, media and caching | `packages/core/settings/src`, `packages/core/media-center/src`, `packages/core/cache/src` |
| Build and tests | [VERIFICATION.md](VERIFICATION.md) |
| Installed skills and context maintenance | [SKILLS.md](SKILLS.md) |

Other domain packages include admin, blog, comments, coupons, logs and workers.
Root `routes/` is not the complete routing inventory: package providers register package routes.

## Memory boundaries

Keep this map about durable navigation and confirmed contracts. Do not store credentials, customer examples, phone numbers, tokens or copied production logs here. Link large existing reports only for the task that needs them.

A new checkout can read these tracked files without global skills installed. Global skills live on the developer's machine; pulling the repository on the server does not install them there.
