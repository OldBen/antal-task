<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendSmsRequest;
use Illuminate\Support\Facades\Http;

class SmsController extends Controller
{
    private array $headers;

    public function __construct()
    {
        $this->headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . env('SMSAPI_KEY'),
        ];
    }

    public function __invoke(SendSmsRequest $request)
    {
        $requestBody = array_merge($request->validated(), ['format' => 'json']);
        $url = env('SMSAPI_URL');
        $response = Http::withHeaders($this->headers)->post($url, $requestBody);

        $responseBody = $response->json();
        if (isset($responseBody['error'])) {
            return response()->json(['error' => $responseBody['message']], 400);
        } else {
            return response()->json($responseBody['list']);
        }
    }
}