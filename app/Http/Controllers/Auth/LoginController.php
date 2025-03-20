<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\DateHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginDashboardRequest;
use App\Http\Resources\Templates\Response\WithDataResource;
use App\Http\Resources\Templates\Response\WithoutDataResource;
use Illuminate\Support\Str;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    public function login(LoginDashboardRequest $request)
    {
        $credential = $request->validated();

        // check auth
        $loginSuccess = Auth::attempt([
            'email' => $credential['email'],
            'password' => $credential['password']
        ]) || Auth::attempt([
            'username' => $credential['email'],
            'password' => $credential['password']
        ]);
        if (!$loginSuccess) {
            Log::info("| Auth | - Login failed for email/username: {$credential['email']}, Invalid credentials");
            return response()->json(
                new WithoutDataResource(
                    Response::HTTP_UNAUTHORIZED,
                    'Login Gagal',
                    'Password atau username/email yang anda masukkan tidak valid, silahkan periksa kembali dan pastikan akun anda sudah terdaftar.',
                    'INVALID_CREDENTIALS'
                ),
                Response::HTTP_UNAUTHORIZED
            );
        }

        $user = Auth::user();

        // cek user aktif
        if (in_array($user->account_status, [1, 3])) {
            Auth::logout();
            Log::info("| Auth | - Login failed for email: {$credential['email']}, User is not active since {$user->deactivate_at}");

            if ($user->account_status == 1) {
                return response()->json(
                    new WithoutDataResource(
                        Response::HTTP_UNAUTHORIZED,
                        'Akun Belum Aktif',
                        "Kami mendeteksi bahwa akun anda belum diaktifkan sejak " . DateHelper::formatTanggalIndonesia($user->created_at, 1) .
                            ", silahkan hubungi admin untuk melakukan aktivasi.",
                        'ACCOUNT_NOT_ACTIVATED'
                    ),
                    Response::HTTP_UNAUTHORIZED
                );
            }

            return response()->json(
                new WithoutDataResource(
                    Response::HTTP_UNAUTHORIZED,
                    'Akun Nonaktif',
                    "Kami mendeteksi bahwa akun anda telah dinonaktifkan sejak " . DateHelper::formatTanggalIndonesia($user->deactivate_at, 1) .
                        ", silahkan hubungi admin untuk melakukan aktivasi kembali.",
                    'ACCOUNT_DEACTIVATED'
                ),
                Response::HTTP_UNAUTHORIZED
            );
        }

        $user->update(['last_login' => now()]);

        // login success
        Log::info("| Auth | - Login success for email: {$credential['email']}, at {$user->last_login}");

        $token = $user->createToken('create_token_' . Str::uuid())->plainTextToken;
        $filteredUser = $user->makeHidden(['password', 'remember_token']);
        $roles = $user->roles->first();
        $filteredRoles = $roles ? $roles->makeHidden(['permissions']) : null;

        return response()->json(
            new WithDataResource(
                Response::HTTP_OK,
                'Login Berhasil',
                'Selamat datang, ' . $user->name . '!',
                [
                    'user' => $filteredUser,
                    'role' => $filteredRoles,
                    'permission' => $filteredRoles ? $roles->permissions->pluck('id') : [],
                    'token' => $token
                ],
                'LOGIN_SUCCESS'
            ),
            Response::HTTP_OK
        );
    }

    public function getUserInfo()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(
                new WithoutDataResource(
                    Response::HTTP_UNAUTHORIZED,
                    'Akses Ditolak',
                    'Maaf, akun pengguna terkait tidak ditemukan.',
                    'ACCOUNT_NOT_FOUND'
                ),
                Response::HTTP_UNAUTHORIZED
            );
        }

        // Sembunyikan atribut sensitif
        // $filteredUser = $user->makeHidden('roles');
        $roles = $user->roles->first();
        $filteredRoles = $roles ? $roles->makeHidden(['permissions']) : null;

        return response()->json(
            new WithDataResource(
                Response::HTTP_OK,
                'Berhasil Medapatkan Data',
                'Data pengguna ' . $user->name . ' , berhasil didapatkan.',
                [
                    'user' => $user,
                    'roles' => $filteredRoles,
                    'permissions' => $filteredRoles ? $roles->permissions->pluck('id') : []
                ],
                'SUCCESS_GET_USER_INFO'
            ),
            Response::HTTP_OK
        );
    }

    public function logout()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(
                new WithoutDataResource(
                    Response::HTTP_UNAUTHORIZED,
                    'Logout Gagal',
                    'Anda tidak memiliki sesi login yang aktif.',
                    'NO_ACTIVE_SESSION'
                ),
                Response::HTTP_UNAUTHORIZED
            );
        }

        // Hapus token akses saat ini jika ada
        if (method_exists($user->currentAccessToken(), 'delete')) {
            $user->currentAccessToken()->delete();
        }

        Auth::guard('web')->logout();

        Log::info("| Auth | - Logout success for email: " . $user->email);

        return response()->json(
            new WithoutDataResource(
                Response::HTTP_OK,
                'Logout Berhasil',
                'Anda berhasil melakukan logout.',
                'LOGOUT_SUCCESS'
            ),
            Response::HTTP_OK
        );
    }
}
