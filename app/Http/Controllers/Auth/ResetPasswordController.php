<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Resources\Templates\Response\WithoutDataResource;
use App\Models\Otp;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    public function resetPassword(ResetPasswordRequest $request)
    {
        $credentials = $request->validated();

        $user = User::where('email', $credentials['email'])->first();
        if (!$user) {
            return response()->json(
                new WithoutDataResource(
                    Response::HTTP_NOT_FOUND,
                    'Akun Tidak Ditemukan',
                    "Akun dengan email '{$credentials['email']}' tidak ditemukan, pastikan anda sudah melakukan registrasi akun kedalam sistem kami dengan email tersebut.",
                    'ACCOUNT_NOT_FOUND'
                ),
                Response::HTTP_NOT_FOUND
            );
        }

        $otpRecord = Otp::where('user_id', $user->id)->latest()->first();
        if (!$otpRecord) {
            return response()->json(
                new WithoutDataResource(
                    Response::HTTP_BAD_REQUEST,
                    'OTP Tidak Ditemukan',
                    'Kode OTP tidak ditemukan. Silakan kirim ulang OTP.',
                    'OTP_NOT_FOUND'
                ),
                Response::HTTP_BAD_REQUEST
            );
        }

        if (!Hash::check($credentials['otp'], $otpRecord->otp)) {
            return response()->json(
                new WithoutDataResource(
                    Response::HTTP_UNAUTHORIZED,
                    'OTP Tidak Valid',
                    'Kode OTP yang anda masukkan salah. Silakan coba lagi atau kirim ulang OTP.',
                    'INVALID_OTP'
                ),
                Response::HTTP_UNAUTHORIZED
            );
        }

        // Ubah password user
        $user->update([
            'password' => Hash::make($credentials['password'])
        ]);

        // Hapus OTP setelah digunakan
        $otpRecord->delete();

        return response()->json(
            new WithoutDataResource(
                Response::HTTP_OK,
                'Password Berhasil Diubah',
                'Password anda berhasil diubah. Silahkan login menggunakan password baru anda.',
                'PASSWORD_RESET_SUCCESS'
            ),
            Response::HTTP_OK
        );
    }
}
