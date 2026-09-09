<?php

namespace Core\Orders\Middleware;

use Closure;
use Core\Orders\Models\Order;
use Core\Settings\Traits\ApiResponse;
use Illuminate\Http\Request;

class EnsureClientOwnsOrder
{
    use ApiResponse;

    public function handle(Request $request, Closure $next)
    {
        $id = $request->route('id');
        $user = $request->user();

        if (! $user || ! is_scalar($id) || ! Order::query()
            ->whereKey($id)
            ->where('client_id', $user->getAuthIdentifier())
            ->exists()) {
            // Match the existing order API error envelope without disclosing ownership.
            return $this->returnErrorMessage(
                trans('system Error please try again later'), [], ['status' => 'fail'], 422
            );
        }

        return $next($request);
    }
}
