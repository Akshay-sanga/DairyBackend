<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Otp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendOtpMail;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function create(Request $request)
    {
        // 🔹 Validation
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email',
            'mobile' => 'required|unique:admins,mobile|digits:10',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $email = $request->email;
            $otp = rand(100000, 999999);

            // 🔹 Save OTP with expiration (10 minutes from now)
            $model = Otp::updateOrCreate(
                ['email' => $email],
                ['otp' => $otp],
                ['expires_at' => now()->addMinutes(10)] // Add this field to the 'otps' table

            );

            // 🔹 Send OTP via mail
            Mail::to($email)->send(new SendOtpMail($otp));

            return response()->json([
                'status_code' => 200,
                'message' => 'Otp sent to your email address successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something Went Wrong.',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    public function verifyOtp(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email',
            'mobile' => 'required|unique:admins,mobile|digits:10',
            'password' => 'required',
            'otp' => 'required',
        ]);
        try {

            $otp = Otp::where('email', $request->email)->first();

            if (!$otp || $otp->otp !== $request->otp) {
                return response()->json([
                    'status_code' => '400',
                    'message' => 'Invalid OTP'
                ], 400);
            }

            if ($otp->otp == $request->otp) {
                $admin = Admin::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'mobile' => $request->mobile,
                    'password' => Hash::make($request->password)
                ]);
                $otp->delete();
                return response()->json([
                    'status_code' => '200',
                    'message' => 'Otp verified successfully and User Registered.'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something Went Wrong.', 'error' => $e->getMessage()], 500);
        }
    }




    public function SendForgetOtp(Request $request)
    {

        // 🔹 Validation
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }
        try {
            $email = $request->email;
            $admin = Admin::where('email', $email)->first();
            if (!$admin) {
                return response->json([
                    'status_code' => '404',
                    'message' => 'Admin Email Not Found'
                ]);
            }

            $otp = rand(100000, 999999);
            $model = Otp::updateOrCreate(
                ['email' => $email],
                ['otp' => $otp]
            );
            Mail::to($email)->send(new SendOtpMail($otp));

            return response()->json([
                'status_code' => '200',
                'message' => 'Otp Send to Your Email Address successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something Went Wrong.', 'error' => $e->getMessage()], 500);
        }
    }


    public function ForgetPassword(Request $request)
    {
        $request->validate([

            'email' => 'required',
            'otp' => 'required',
            'password' => 'required',
            'confirm_password' => 'required|same:password',

        ]);
        try {

            $otp = Otp::where('email', $request->email)->first();

            if (!$otp || $otp->otp !== $request->otp) {
                return response()->json(['message' => 'Invalid OTP'], 400);
            }

            if ($otp->otp == $request->otp) {
                $admin = Admin::where('email', $request->email)->first();
                if (!$admin) {
                    return response()->json([
                            'status_code' => '404',
                            'message' => 'Admin Email Not Found'
                        ]);
                }
                $admin->update([
                        'password' => Hash::make($request->password),
                    ]);
                $otp->delete();
                return response()->json([
                    'status_code' => '200',
                    'message' => 'Password Forget successfully'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something Went Wrong.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function login(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');

        $admin = Admin::where('email', $email)->first();
        // return $admin;

        if (!$admin) {
            return response()->json([
                'status_code' => 401,
                'message' => 'Invalid email or password'
            ], 401);
        }

        if (!Hash::check($password, $admin->password)) {
            return response()->json([
                'status_code' => 401,
                'message' => 'Invalid email or password'
            ], 401);
        }

        // ✅ If you want to generate token (optional):
        $token = $admin->createToken('API Token')->plainTextToken;

        return response()->json([
            'status_code' => 200,
            'message' => 'Login successful',
            'admin' => $admin,
            'token' => $token
        ]);
    }

    public function storage_link(Request $request)
    {
        try {

            $target = storage_path('app/public');
            $link = public_path('storage');


            if (!File::exists($link)) {
                File::link($target, $link);
            }
            return response()->json(['message' => 'Storage link created successfully.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to create storage link.', 'error' => $e->getMessage()], 500);
        }
    }

    public function logout()
    {
        Auth::user()->tokens()->delete();
        $response = [
            "status_code" => 200,
            "Message" => "Logout Successfully"
        ];
        return response($response);
    }
}