<?php

use Core\Notification\Services\TelegramNotificationService;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Auth\AuthenticationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withExceptions(function (Exceptions $exceptions) {
        // Report all exceptions to Telegram automatically (including caught exceptions)
        $exceptions->report(function (Throwable $e) {
            // Skip specific exception types that we don't want to report
            $skipExceptions = [
                \Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class,
                \Illuminate\Database\Eloquent\ModelNotFoundException::class,
                \Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException::class,
                \Illuminate\Auth\AuthenticationException::class,
                \Illuminate\Auth\Access\AuthorizationException::class,
                \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException::class,
            ];

            foreach ($skipExceptions as $exceptionClass) {
                if ($e instanceof $exceptionClass) {
                    return;
                }
            }

            // Skip specific URLs
            if (request()->fullUrl() == "https://cleanstation.app/storage/storage/system/user.png") {
                return;
            }

            // Send directly to Telegram
            try {
                $telegramService = app(TelegramNotificationService::class);
                $formattedMessage = $telegramService->formatExceptionMessage($e);
                $telegramService->sendMessage('@itcleanstation', $formattedMessage);
            } catch (\Throwable $telegramError) {
                // Silently fail to prevent infinite loops
                \Illuminate\Support\Facades\Log::error('Failed to send telegram notification: ' . $telegramError->getMessage());
            }
        });

        $exceptions->shouldRenderJsonWhen(function (Request $request, Throwable $e) {
            return $request->expectsJson();
        });
    })
    ->withRouting()
    ->withCommands([
        \Core\Notification\Console\Commands\SendWelcomeNotification::class,
        \Core\Notification\Console\Commands\SendInactiveNewUsersNotification::class,
        \Core\Notification\Console\Commands\SendInactiveAfterOrderNotification::class,
        \Core\Notification\Console\Commands\SendFeedBackNotification::class,
        \Core\Notification\Console\Commands\SendAbandonedCartNotification::class,
        \Core\Notification\Console\Commands\RemoveLastWeekNotification::class,
        \Core\Notification\Console\Commands\SendCelebrateBirthdayNotification::class,
        \Core\Notification\Console\Commands\SendNoOrderPeriodNotification::class,
        \Core\Wallet\Console\Commands\HandleExpiredWalletTransaction::class,
        \Core\Users\Commands\HandleExpiredPoints::class,
        \Core\Users\Commands\HandleExpiredContract::class,
        \Core\Orders\Commands\OrderIsPendingPaymentForTenMinutes::class,
        \Core\Orders\Commands\OrderIsLateForPickup::class,
        \Core\Orders\Commands\OrderIsLateForDelivery::class,
        \Core\Orders\Commands\CartHasBeenLeftForMoreThanTenMinutes::class,
        \Core\Orders\Commands\ShortPerformanceReport::class,
        \Core\Orders\Commands\OrderStartsPickupInOneHour::class,
        \Core\Orders\Commands\OrderStartsDeliveryInOneHour::class,
        \Core\Orders\Commands\DailySummaryOffAddedOrRemovedItems::class,
        \Core\Orders\Commands\CreateScheduledOrders::class,
    ])
    ->withMiddleware(function (Middleware $middleware) {
        // Add route-record to the API middleware group
        $middleware->group('api', [
            \Core\Admin\Http\Middleware\RouteRecordMiddleware::class,
        ]);

        $middleware->alias([
            'auth' => \App\Http\Middleware\Authenticate::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'csrf' => \App\Http\Middleware\VerifyCsrfToken::class,

            /**** OTHER MIDDLEWARE ALIASES ****/
            'localize'                => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRoutes::class,
            'localizationRedirect'    => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
            'localeSessionRedirect'   => \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
            'localeCookieRedirect'    => \Mcamara\LaravelLocalization\Middleware\LocaleCookieRedirect::class,
            'localeViewPath'          => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationViewPath::class,

            //my middlewares
            'active'            => \Core\Users\Middleware\ActiveUser::class,
            'checkPermission'   => \Core\Users\Middleware\CheckPermissions::class,
            'route-record'      => \Core\Admin\Http\Middleware\RouteRecordMiddleware::class,

            //Spatie Permission
            'role'              => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission'        => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // ✅ Add your custom handler here:
        $exceptions->renderable(function (AuthenticationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => trans('auth.unauthenticated'),
                ], 401);
            }

            return null; // fallback to Laravel default for web routes
        });
    })
    ->create();
