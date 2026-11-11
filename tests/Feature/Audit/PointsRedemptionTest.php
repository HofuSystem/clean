<?php

namespace Tests\Feature\Audit;

use Core\Orders\Models\Order;
use Core\Orders\Services\OrdersService;
use Core\Orders\Services\PointsRedemptionService;
use Core\Settings\Services\SettingsService;
use Core\Users\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\Support\IsolatedAuditTestCase;

class PointsRedemptionTest extends IsolatedAuditTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });
        (new SettingsService())->saveSettings([
            'minium_points_to_use' => 1000,
            'riyal_per_point_redeem' => 100,
        ]);
    }

    protected function tearDown(): void
    {
        (new \ReflectionProperty(SettingsService::class, 'allSettings'))->setValue(null, null);
        parent::tearDown();
    }

    public function test_thousand_points_are_ten_riyals_at_production_rate(): void
    {
        $result = app(PointsRedemptionService::class)->calculate(
            new User(['points_balance' => 2000]),
            new Order(['total_price' => 143, 'paid' => 0]),
            1000
        );
        $this->assertSame(['points' => 1000, 'value' => 10.0], $result);
    }

    public function test_card_quote_uses_same_conversion_after_wallet(): void
    {
        $amount = app(OrdersService::class)->fastPaymentCardAmount(
            new Order(['total_price' => 143, 'paid' => 3]),
            ['points_used' => true, 'points_amount' => 1000,
                'wallet_used' => true, 'wallet_amount_used' => 20],
            new User(['points_balance' => 2000, 'wallet' => 20])
        );
        $this->assertSame(110.0, $amount);
    }

    public function test_redemption_still_rejects_value_above_remaining_due(): void
    {
        $this->expectException(ValidationException::class);
        app(PointsRedemptionService::class)->calculate(
            new User(['points_balance' => 2000]),
            new Order(['total_price' => 143, 'paid' => 134]),
            1000
        );
    }

    public function test_zero_rate_rejects_redemption_without_dividing_by_zero(): void
    {
        (new SettingsService())->saveSettings(['riyal_per_point_redeem' => 0]);
        $this->expectException(ValidationException::class);
        app(PointsRedemptionService::class)->calculate(
            new User(['points_balance' => 2000]),
            new Order(['total_price' => 143, 'paid' => 0]),
            1000
        );
    }

    public function test_exact_remaining_amount_can_be_settled_with_points(): void
    {
        $result = app(PointsRedemptionService::class)->calculate(
            new User(['points_balance' => 1000]),
            new Order(['total_price' => 143, 'paid' => 133]),
            1000
        );
        $this->assertSame(10.0, $result['value']);
    }

    public function test_insufficient_points_balance_is_rejected(): void
    {
        $this->expectException(ValidationException::class);
        app(PointsRedemptionService::class)->calculate(
            new User(['points_balance' => 999]),
            new Order(['total_price' => 143, 'paid' => 0]),
            1000
        );
    }

    public function test_redemption_below_minimum_is_rejected(): void
    {
        $this->expectException(ValidationException::class);
        app(PointsRedemptionService::class)->calculate(
            new User(['points_balance' => 2000]),
            new Order(['total_price' => 143, 'paid' => 0]),
            999
        );
    }
}
