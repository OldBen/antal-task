<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SmsControllerTest extends TestCase
{
    private array $request;

    protected function setUp(): void
    {
        $this->request = [
            'from' => 'Test',
            'to' => '123456789',
            'message' => 'Hello, this is a test message.',
        ];
        parent::setUp();
    }
    
    public function test_message_sent_correctly(): void
    {
        Http::fake([
            env('SMSAPI_URL') => Http::response(['list' => [['id' => 123]]], 200)
        ]);
        $response = $this->postJson('/api/sms', $this->request);
        $response->assertStatus(200);
        Http::assertSent(function (Request $request) {
            return $request->url() === env('SMSAPI_URL') &&
                $request->hasHeader('Authorization', 'Bearer ' . env('SMSAPI_KEY'));
        });
    }

    public function test_failed_validation(): void
    {
        $this->request['to'] = 'invalid_number';
        $response = $this->postJson('/api/sms', $this->request);

        $response->assertStatus(422);
        Http::assertNotSent(function (Request $request) {
            return $request->url() === env('SMSAPI_URL');
        });
    }

    public function test_api_error_handling(): void
    {
        Http::fake([
            env('SMSAPI_URL') => Http::response(['error' => true, 'message' => 'API error'], 400)
        ]);
        $response = $this->postJson('/api/sms', $this->request);
        $response->assertStatus(400);
        $response->assertJson(['error' => 'API error']);
        Http::assertSent(function (Request $request) {
            return $request->url() === env('SMSAPI_URL') &&
                $request->hasHeader('Authorization', 'Bearer ' . env('SMSAPI_KEY'));
        });
    }
}
