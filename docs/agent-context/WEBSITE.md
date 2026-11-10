# Website and CMS map

Source inspected: 2026-11-10 at `5ef4bfa33270d936d9a6550428cb8452ac047174`.

| Concern | Source |
| --- | --- |
| Public localized URLs | `routes/web.php` |
| Public data loading | `app/Http/Controllers/PageController.php` |
| Main landing layout and social/SEO tags | `resources/views/layouts/landing.blade.php` |
| Page wrappers | `resources/views/pages/` |
| Rendered page sections | `resources/views/landing/sections/` |
| CMS models, validation and persistence | `packages/core/pages/src/Models`, `Requests`, `Services` |
| Admin page editor | `packages/core/pages/src/resources/views/pages/pages/edit.blade.php` |
| CMS defaults | `database/seeders/WebsitePageCmsSeeder.php` |
| Navigation | `resources/views/layouts/partials/navbar.blade.php`, `footer.blade.php` |
| Translations | `lang/ar.json`, `lang/en.json`, and translatable database models |

Pricing is `/pricing` (route `pricing`); coverage is `/riyadh` (route `coverage`), under the selected language prefix. CMS lookup uses `pricing`, and `coverage` or `riyadh`, respectively. Never infer the public URL from the CMS slug alone.

CMS translations include `title`, `description`, `meta_title`, `meta_description`, `seo_image`, `social_share_title`, `social_share_description`, and `social_share_image`. The landing layout resolves social fallbacks from SEO/page metadata. Read that resolution before changing editor defaults or declaring why a live preview differs.

The dedicated pricing/coverage hero sections can use section translations. Price tables are backed by categories/products; coverage lists are backed by city/district queries. These operational data sources are separate from CMS hero text and metadata.

The CMS seeder fills missing records/translations and section slots. Read its current implementation before running it; creating code for a seeder does not establish that a particular database has run it. A saved form, generated HTML tags, and a third-party cached social preview are three different evidence sources.

Arabic terminal text: use PowerShell `Get-Content -Encoding UTF8` or an explicit UTF-8 reader. A misdecoded terminal display does not justify rewriting stored Arabic strings.
