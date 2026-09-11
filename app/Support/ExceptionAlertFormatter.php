<?php

namespace App\Support;

use Illuminate\Http\Request;
use Throwable;

final class ExceptionAlertFormatter
{
    public static function format(Throwable $exception): string
    {
        $escape = static fn ($value): string => htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        // Allowlisted diagnostics only. Exception messages, SQL, bindings,
        // request URLs/parameters/headers and trace arguments can contain secrets.
        $context = [
            'Application' => config('app.name'),
            'Environment' => app()->environment(),
            'Exception' => get_class($exception),
            'Source' => basename($exception->getFile()).':'.$exception->getLine(),
            'Time' => now()->toDateTimeString(),
        ];
        if (app()->bound('request') && ($request = app('request')) instanceof Request) {
            $context['Method'] = $request->method();
            // A route template identifies the endpoint without including customer
            // identifiers or payment tokens from the actual request path.
            $context['Route'] = $request->route()?->uri() ?? '(unmatched)';
        }
        $message = '<b>Application exception</b>'."\n";
        foreach ($context as $label => $value) {
            $message .= '<b>'.$label.':</b> '.$escape($value)."\n";
        }

        return $message;
    }
}
