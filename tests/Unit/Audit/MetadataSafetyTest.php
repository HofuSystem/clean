<?php

namespace Tests\Unit\Audit;

use App\Support\CanonicalUrl;
use App\Support\RequestLogSanitizer;
use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;

class MetadataSafetyTest extends TestCase
{
    public function test_request_logs_keep_operational_metadata_and_drop_secrets_and_payloads(): void
    {
        $attributes = RequestLogSanitizer::attributes([
            'order_id' => 10, 'page' => 2, 'status' => 'pending',
            'password' => 'secret', 'otp' => '123456', 'verified_code' => '123456',
            'phone' => 'private', 'email' => 'private', 'token' => 'secret',
            'payload' => ['access_token' => 'secret'], 'type' => ['password' => 'secret'],
        ]);
        $this->assertSame(['order_id' => 10, 'status' => 'pending', 'page' => 2], $attributes);
        $headers = RequestLogSanitizer::headers([
            'Authorization' => ['Bearer secret'], 'Cookie' => ['session=secret'],
            'X-Api-Key' => ['secret'], 'App-Version' => ['2.0'], 'Accept' => ['application/json'],
        ]);
        $this->assertSame(['accept' => ['application/json'], 'app-version' => ['2.0']], $headers);
    }

    public function test_canonical_removes_ad_tracking_but_preserves_pagination_and_filters(): void
    {
        $request = Request::create('https://untrusted.example/ar/blog?page=2&utm_source=ads&GCLID=secret&fbclid=secret&category=laundry');
        $this->assertSame('https://cleanstation.app/ar/blog?category=laundry&page=2', CanonicalUrl::fromRequest($request, 'https://cleanstation.app/'));
        $this->assertSame('https://cleanstation.app/en', CanonicalUrl::fromRequest(Request::create('/en?wbraid=abc&utm_campaign=test'), 'https://cleanstation.app'));
        $this->assertSame('https://cleanstation.app/ar/blog?page=3', CanonicalUrl::fromRequest(Request::create('/ar/blog?page=3'), 'https://cleanstation.app'));
    }
}
