<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\LoginNeedsVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class LoginController extends Controller
{
    public function submit(Request $request)
    {
        // validate the phone number
        $request->validate([
            'phone' => 'required|numeric|min:10'
        ]);

        // find or create a user model
        $user = User::firstOrCreate([
            'phone' => $request->phone
        ]);

        if (!$user) {
            return response()->json(['message' => 'Could not process a user with that phone number.'], 401);
        }

        // send the user a one-time use code
        // $user->notify(new LoginNeedsVerification());
        $otp = rand(100000, 999999);
        $ret = $this->sendOtp($request, $otp);

        $user->update([
            'login_code' => $otp
        ]);

        // return back a response
        return response()->json(['message' => 'Text message notification sent.']);
    }

    public function sendOtp($request, $otp)
    {
        // Validate request
        $request->validate([
            'phone' => 'required|numeric|min:10'
        ]);

        $message = "Your OTP is: $otp. Please do not share it with anyone.";
        $phone = $request->phone;

        // Initialize cURL
        $ch = curl_init();

        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, "http://localhost:3001/send-message");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'number' => $phone . '@c.us',
            'message' => $message
        ]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Execute cURL request
        $response = curl_exec($ch);
        curl_close($ch);

        // Decode and return response
        $responseData = json_decode($response, true);

        return response()->json([
            'otp' => $otp, // For testing purposes (you may remove this in production)
            'whatsapp_response' => $responseData
        ]);
    }

    public function verify(Request $request)
    {
        // validate the incoming request
        $request->validate([
            'phone' => 'required|numeric|min:10',
            'login_code' => 'required|numeric|between:111111,999999'
        ]);

        // find the user
        $user = User::where('phone', $request->phone)
            ->where('login_code', $request->login_code)
            ->first();

        // is the code provided the same one saved?
        // if so, return back an auth token
        if ($user) {
            $user->update([
                'login_code' => null
            ]);

            return $user->createToken($request->login_code)->plainTextToken;
        }

        // if not, return back a message
        return response()->json(['message' => 'Invalid verification code.'], 401);
    }

    public function logout(Request $request)
    {
        // Revoke the user's current authentication token
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Successfully logged out.']);
    }
}
