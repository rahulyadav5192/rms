<?php 
namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class GreenApiHelper
{
    public static function sendWhatsAppMessage($phoneNumber, $message)
    {
        $instanceId = env('GREEN_API_INSTANCE_ID'); // Add to .env
        $token = env('GREEN_API_TOKEN');             // Add to .env

        $url = "https://api.green-api.com/waInstance{$instanceId}/sendMessage/{$token}";

        $response = Http::post($url, [
            'chatId' => $phoneNumber . '@c.us', // e.g., 919876543210@c.us
            'message' => $message
        ]);

        return $response->successful();
    }
}
