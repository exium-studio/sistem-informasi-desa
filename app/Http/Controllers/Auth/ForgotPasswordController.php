<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SendOTPRequest;
use App\Http\Requests\Auth\VerifyOTPRequest;
use App\Http\Resources\Templates\Response\WithoutDataResource;
use App\Mail\Auth\SendingOTPMail;
use App\Models\Otp;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordController extends Controller
{
    public function sendOTP(SendOTPRequest $request)
    {
        $credentials = $request->validated();

        $user = User::where('email', $credentials['email'])->first();
        if (!$user) {
            return response()->json(
                new WithoutDataResource(
                    Response::HTTP_NOT_FOUND,
                    'Akun Tidak Ditemukan',
                    "Akun dengan email '{$credentials['email']}' tidak ditemukan, pastikan anda sudah melakukan registrasi akun kedalam sistem kami dengan email tersebut."
                ),
                Response::HTTP_NOT_FOUND
            );
        }

        $otp = rand(100000, 999999); // Generate OTP 6 digit
        $expiresAt = Carbon::now('Asia/Jakarta')->addMinutes(5); // OTP berlaku 10 menit

        Otp::updateOrCreate(
            ['user_id' => $user->id],
            [
                'otp' => Hash::make($otp), // Hash OTP untuk keamanan
                'expired_date' => $expiresAt,
            ]
        );

        Mail::to($user->email)->send(new SendingOTPMail($user->name, $otp));
        Log::info('| Auth | - Send OTP success for email: ' . $user->email);

        return response()->json(
            new WithoutDataResource(
                Response::HTTP_OK,
                'Berhasil Mengirim Kode OTP',
                'Kode OTP berhasil dikirim, silahkan cek inbox atau spam di email anda.'
            ),
            Response::HTTP_OK
        );
    }

    public function verifyOTP(VerifyOTPRequest $request)
    {
        $credentials = $request->validated();

        $user = User::where('email', $credentials['email'])->first();
        if (!$user) {
            return response()->json(
                new WithoutDataResource(
                    Response::HTTP_NOT_FOUND,
                    'Akun Tidak Ditemukan',
                    "Akun dengan email '{$credentials['email']}' tidak ditemukan, pastikan anda sudah melakukan registrasi akun kedalam sistem kami dengan email tersebut."
                ),
                Response::HTTP_NOT_FOUND
            );
        }

        $otpRecord = Otp::where('user_id', $user->id)->latest()->first();
        if (!$otpRecord) {
            Log::info('| Auth | - Verify OTP failed: OTP not found for email: ' . $user->email);
            return response()->json(
                new WithoutDataResource(
                    Response::HTTP_BAD_REQUEST,
                    'OTP Tidak Ditemukan',
                    'Kode OTP tidak ditemukan. Silakan kirim ulang OTP.'
                ),
                Response::HTTP_BAD_REQUEST
            );
        }

        if (!Hash::check($credentials['otp'], $otpRecord->otp)) {
            Log::info('| Auth | - Verify OTP failed: Incorrect OTP for email: ' . $user->email);
            return response()->json(
                new WithoutDataResource(
                    Response::HTTP_UNAUTHORIZED,
                    'OTP Tidak Valid',
                    'Kode OTP yang anda masukkan tidak sesuai. Silakan coba lagi atau kirim ulang OTP.'
                ),
                Response::HTTP_UNAUTHORIZED
            );
        }

        if (!$otpRecord || Carbon::now('Asia/Jakarta')->greaterThan($otpRecord->expired_date)) {
            Log::info('| Auth | - Verify OTP failed: OTP expired for email: ' . $user->email);
            return response()->json(
                new WithoutDataResource(
                    Response::HTTP_UNAUTHORIZED,
                    'OTP Kadaluarsa',
                    'Kode OTP yang anda masukkan sudah kadaluarsa. Silakan kirim ulang kode OTP.'
                ),
                Response::HTTP_UNAUTHORIZED
            );
        }

        Log::info('| Auth | - Verify OTP success for email: ' . $user->email);

        return response()->json(
            new WithoutDataResource(
                Response::HTTP_OK,
                'OTP Berhasil Diverifikasi',
                'Kode OTP anda berhasil diverifikasi. Silahkan lakukan reset password anda.'
            ),
            Response::HTTP_OK
        );
    }
}
