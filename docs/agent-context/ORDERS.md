# Order entry points and note handling

Source inspected: 2026-11-10 at `5ef4bfa33270d936d9a6550428cb8452ac047174`.
Source findings below are not a live-order verification.

## Choose the creation path first

| Origin | Route definition | Handler and validation |
| --- | --- | --- |
| Customer mobile API | `packages/core/orders/src/routes/api.php`, POST `/api/client/orders` | `Controllers/Api/Client/OrdersController.php::createOrder`, `Requests/Api/CreateOrderRequest.php` |
| B2B portal | `packages/core/b2b/src/routes/web.php`, localized POST `/business/order` | `Controllers/FrontEnd/DashboardController.php::orderStore`, `Requests/FrontEnd/OrderRequest.php` |
| Admin dashboard | `packages/core/orders/src/routes/dashboard.php` | `Controllers/Dashboard/OrdersController.php::store`, `Requests/OrdersRequest.php`, service `storeOrder` |
| Scheduled requests | `packages/core/orders/src/Commands/CreateScheduledOrders.php` | Read scheduler data preparation and its call into order services |

Paths in the handler column are relative to the package's `src/` directory.
The order package's `Providers/RouteServiceProvider.php` adds the API prefix and registers its own web/dashboard routes.

## Follow data through the boundary

1. API `CreateOrderRequest::prepareForValidation` normalizes incoming fields before the controller calls `$request->validated()`.
2. API controller calls `OrdersService::createOrder`. The B2B controller calls the same service but has different input preparation; changing B2B code does not fix mobile request normalization.
3. `OrdersService::createOrder` assembles `note` and creates the order. Inspect `Models/Order.php` for fillable/casts and `Observers/OrderObserver.php` for side effects; the service alone is not the full behavior.
4. Admin notes display comes from `packages/core/orders/src/resources/views/pages/orders/inc/summary.blade.php`, using `$order->note`.
5. `packages/core/orders/src/DataResources/Api/Client/Order/OrderDetailsResource.php` exposes `note` from `$this->note` and the legacy key `nots` from `$this->desc`. Verify model/schema behavior and actual response values before assuming both attributes are stored or equivalent. Flutter consumption is unknown in this repository.

## Fast-order regression to remember

Observed user report: service type appeared when the customer left notes empty, but disappeared when a note was supplied.
The relevant API condition formerly copied `service_type` into `desc` only when `desc` was empty. At the inspected revision it combines the mapped service label with customer `desc` and avoids duplication when the note equals the raw or mapped service type.

For future changes, cover the behavior with and without customer notes, missing service type, identical label/code, and any client that already supplies a combined string. Follow the final `note` through service and API output. Prior conversation claims about deployment caches or which mobile field is displayed were not established by runtime evidence.

The B2B portal has separate note composition in `DashboardController::orderStore`. Its form is `packages/core/b2b/src/resources/views/web/partials/modals.blade.php`; submission is in `public/client/js/main.js`. Inspect these only when the reported source is the business portal.

## Related sensitive flows

### Wallet transaction route validation (2026-11-11)

- `packages/core/wallet/src/routes/dashboard.php` constrains wallet transaction group `{id}` parameters to digits. Previously arbitrary text reached `WalletTransactionsController::show` and `WalletTransactionsService::get(int $id)`, producing TypeError instead of rejecting the URL.
- `tests/Feature/Audit/WalletTransactionRoutesTest.php` verifies numeric IDs, rejection of nonnumeric IDs and preservation of static routes such as stats/search/export in the isolated application. The reported production alert identifies the typed method but omits the actual ID and exception message; its exact triggering URL remains unknown.

### Dashboard mobile column order (2026-11-11)

- Dashboard tables are defined in `packages/core/admin/src/resources/views/pages/index.blade.php`. `arrangeMobileOrderColumns` in `public/control/js/custom/datatable/table.js` reorders headers before DataTables derives its data columns.
- Below 768px, dashboard pickup date/time follow `type`, immediately followed by delivery date; `actions` sorts after all other columns, including unlisted fields. The orders-list and desktop ordering are unchanged by this dashboard-specific adjustment.
- Local verification: JavaScript syntax and execution against all eight dashboard header definitions, including repeated initialization and desktop no-op. No live mobile-browser verification.

### Points conversion (2026-11-11, baseline `30a9fc6` plus working changes)

- The legacy setting `riyal_per_point_redeem` holds points per SAR: production output supplied by the user showed `100` in both database and cache, with minimum `1000`. Thus 1000 points redeem for SAR 10. Do not invert the setting or change its API key.
- `PointsRedemptionService::valueInRiyals` divides points by this setting, rounds to halalas and guards nonpositive rates. Both redemption validation and `OrdersService::fastPaymentCardAmount` use it. The previous multiplication rejected valid redemptions and omitted their value from card quotes.
- Local verification: `tests/Feature/Audit/PointsRedemptionTest.php` uses isolated in-memory SQLite and unsaved order/user models. Seven cases cover conversion, card quote with wallet, exact settlement, over-redemption, zero rate, insufficient balance and minimum points. Multiplication reproduced failures; division passed. This does not establish deployment or a successful customer checkout.

- Payments: `packages/core/payment-gateways/src` and `packages/core/wallet/src`; order payment math is in `packages/core/orders/src/Support/OrderPaymentMath.php`.
- Status transitions: inspect order service, observers and driver/technical controllers together.
- Event payloads: `Services/AnalyticsOutboxService.php` and `Services/PurchasePayloadBuilder.php` in the orders package.
- Ownership: API order-detail routes use `Middleware/EnsureClientOwnsOrder.php`; inspect authorization when changing query or lookup behavior.
