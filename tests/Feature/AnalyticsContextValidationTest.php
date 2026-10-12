<?php

namespace Tests\Feature;

use Core\Orders\Requests\Api\CreateOrderRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class AnalyticsContextValidationTest extends TestCase
{
    public function test_analytics_context_is_optional_for_legacy_order_requests(): void
    {
        $request = CreateOrderRequest::create('/api/client/orders', 'POST', ['type' => 'clothes']);
        $analyticsRules = array_filter(
            $request->rules(),
            static fn (string $key): bool => str_starts_with($key, 'analytics_context'),
            ARRAY_FILTER_USE_KEY
        );

        $validator = Validator::make(['type' => 'clothes'], $analyticsRules);

        $this->assertTrue($validator->passes(), $validator->errors()->toJson());
    }

    public function test_analytics_context_v2_accepts_expected_attribution_shape(): void
    {
        $request = CreateOrderRequest::create('/api/client/orders', 'POST');
        $analyticsRules = array_filter(
            $request->rules(),
            static fn (string $key): bool => str_starts_with($key, 'analytics_context'),
            ARRAY_FILTER_USE_KEY
        );

        $validator = Validator::make([
            'analytics_context' => [
                'app_instance_id' => 'c4d5e6f7-test-instance',
                'session_id' => 1712345678,
                'platform' => 'ios',
                'branch_first' => [
                    'channel' => 'facebook',
                    'feature' => 'cpc',
                    'campaign' => 'ramadan_2026',
                    'click_id' => 'first-click',
                    'clicked_branch_link' => true,
                    'click_timestamp' => 1712345600,
                ],
                'branch_latest' => [
                    'channel' => 'snapchat',
                    'feature' => 'paid',
                    'campaign' => 'eid_offer',
                    'click_id' => 'latest-click',
                    'clicked_branch_link' => true,
                    'click_timestamp' => 1712345650,
                ],
            ],
        ], $analyticsRules);

        $this->assertTrue($validator->passes(), $validator->errors()->toJson());
    }
}
