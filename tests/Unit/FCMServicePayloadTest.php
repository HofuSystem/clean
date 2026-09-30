<?php

namespace Tests\Unit;

use Core\Notification\Services\FCMService;
use PHPUnit\Framework\TestCase;

class FCMServicePayloadTest extends TestCase
{
    /**
     * Test that convertIntegersToStrings converts all values to primitive strings
     * and correctly serializes nested arrays and objects into JSON strings,
     * fulfilling the Google FCM HTTP v1 map<string, string> contract.
     */
    public function test_fcm_payload_converts_nested_arrays_and_objects_to_strings(): void
    {
        $service = FCMService::getInstance();

        $nestedSenderData = [
            'id' => 5,
            'fullname' => 'محمد السائق',
            'phone' => '0501234567',
            'image' => 'https://cleanstation.app/storage/avatars/driver.jpg',
        ];

        $payload = [
            'key' => 'order',
            'key_id' => 123,
            'status' => 'receiving_driver_accepted',
            'title' => 'تم قبول طلبك',
            'body' => 'السائق في الطريق لاستلام الملابس',
            'order_id' => 123,
            'order_driver_type' => 'receipt',
            'sender_data' => $nestedSenderData,
            'reference' => 'CS-10023',
            'is_urgent' => true,
            'notes' => null,
        ];

        $sanitized = $service->convertIntegersToStrings($payload);

        $this->assertIsArray($sanitized);

        // Every key and every value must be a primitive string for Google FCM Protobuf map<string, string>
        foreach ($sanitized as $k => $v) {
            $this->assertIsString($k, "Key '{$k}' must be a string");
            $this->assertIsString($v, "Value for key '{$k}' must be a primitive string, found " . gettype($v));
        }

        // Specifically check sender_data
        $this->assertArrayHasKey('sender_data', $sanitized);
        $this->assertIsString($sanitized['sender_data']);

        // Check that sender_data can be decoded back to the original array
        $decodedSender = json_decode($sanitized['sender_data'], true);
        $this->assertIsArray($decodedSender);
        $this->assertEquals(5, $decodedSender['id']);
        $this->assertEquals('محمد السائق', $decodedSender['fullname']);
        $this->assertEquals('0501234567', $decodedSender['phone']);
        $this->assertEquals('https://cleanstation.app/storage/avatars/driver.jpg', $decodedSender['image']);

        // Check scalar conversions
        $this->assertSame('123', $sanitized['key_id']);
        $this->assertSame('123', $sanitized['order_id']);
        $this->assertSame('1', $sanitized['is_urgent']);
        $this->assertSame('', $sanitized['notes']);
    }

    /**
     * Test that convertIntegersToStrings handles string JSON payloads gracefully.
     */
    public function test_fcm_payload_handles_json_encoded_input(): void
    {
        $service = FCMService::getInstance();

        $jsonPayload = json_encode([
            'order_id' => 999,
            'driver' => ['id' => 10, 'name' => 'Driver 10'],
        ]);

        $sanitized = $service->convertIntegersToStrings($jsonPayload);

        $this->assertIsArray($sanitized);
        $this->assertSame('999', $sanitized['order_id']);
        $this->assertIsString($sanitized['driver']);
        $this->assertEquals(['id' => 10, 'name' => 'Driver 10'], json_decode($sanitized['driver'], true));
    }

    /**
     * Test empty or non-array inputs.
     */
    public function test_fcm_payload_handles_empty_or_non_array_inputs(): void
    {
        $service = FCMService::getInstance();

        $this->assertSame([], $service->convertIntegersToStrings([]));
        $this->assertSame('plain-string', $service->convertIntegersToStrings('plain-string'));
    }
}
