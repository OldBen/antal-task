<?php

namespace Tests\Unit;

use App\Http\Requests\SendSmsRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class SendSmsRequestTest extends TestCase
{
    private SendSmsRequest $request;

    protected function setUp(): void
    {
        $this->request = new SendSmsRequest();
        $this->request->merge([
            'from' => 'Test',
            'to' => '123456789',
            'message' => 'Hello, this is a test message.',
        ]);
        parent::setUp();
    }

    public function test_valid_request_passes(): void
    {
        $validator = Validator::make($this->request->all(), $this->request->rules());
        $this->assertTrue($validator->passes());
    }

    public function test_request_with_long_number_passes(): void
    {
        $this->request->merge(['to' => '48123456789']);
        $validator = Validator::make($this->request->all(), $this->request->rules());
        $this->assertTrue($validator->passes());
    }

    public function test_request_with_invalid_from_fails(): void
    {
        $this->request->merge(['from' => 'Invalid']);
        $validator = Validator::make($this->request->all(), $this->request->rules());
        $this->assertFalse($validator->passes());
    }

    public function test_requests_with_invalid_number_fail(): void
    {
        // Too short
        $this->request->merge(['to' => '12345678']);
        $validator = Validator::make($this->request->all(), $this->request->rules());
        $this->assertFalse($validator->passes());

        // Too long
        $this->request->merge(['to' => '1234567890123']);
        $validator = Validator::make($this->request->all(), $this->request->rules());
        $this->assertFalse($validator->passes());

        // Long number does not start with 48
        $this->request->merge(['to' => '49123456789']);
        $validator = Validator::make($this->request->all(), $this->request->rules());
        $this->assertFalse($validator->passes());

        // Invalid characters
        $this->request->merge(['to' => '12345abcd']);
        $validator = Validator::make($this->request->all(), $this->request->rules());
        $this->assertFalse($validator->passes());
    }

    public function test_request_with_long_message_fails(): void
    {
        $this->request->merge(['message' => str_repeat('A', 161)]);
        $validator = Validator::make($this->request->all(), $this->request->rules());
        $this->assertFalse($validator->passes());
    }
}
