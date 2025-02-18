<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppController extends Controller
{
    public function sendOtp(Request $request, $otp)
    {
        // Validate request
        $request->validate([
            'number' => 'required|string',
        ]);
        // Remove the OTP generation since it's passed as parameter
        $message = "Your OTP is: $otp. Please do not share it with anyone.";

        // Send OTP via WhatsApp bot
        $response = Http::post('http://localhost:3001/send-otp', [
            'number' => $request->number,
            'message' => $message
        ]);

        // Log response
        Log::info('WhatsApp OTP Response:', $response->json());
        return response()->json([
            'otp' => $otp, // For testing purposes (you may remove this in production)
            'whatsapp_response' => $response->json()
        ]);
    }
}
