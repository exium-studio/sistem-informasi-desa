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
                    'Password atau username/email yang anda masukkan tidak valid, silahkan periksa kembali dan pastikan akun anda sudah terdaftar.'
                ),
                Response::HTTP_UNAUTHORIZED
            );
        }

        $user = Auth::user();

        // cek user aktif
        if (in_array($user->account_status, [1, 3])) {
            Auth::logout();
            Log::info("| Auth | - Login failed for email: {$credential['email']}, User is not active since {$user->deactivate_at}");

            $message = ($user->account_status == 1)
                ? "Kami mendeteksi bahwa akun anda belum diaktifkan sejak " . DateHelper::formatTanggalIndonesia($user->created_at, 1) . ", silahkan hubungi admin untuk melakukan aktivasi."
                : "Kami mendeteksi bahwa akun anda telah dinonaktifkan sejak " . DateHelper::formatTanggalIndonesia($user->deactivate_at, 1) . ", silahkan hubungi admin untuk melakukan aktivasi kembali.";

            return new WithoutDataResource(Response::HTTP_UNAUTHORIZED, 'Akun Tidak Aktif', $message);
        }

        $user->update(['last_login' => now('Asia/Jakarta')]);

        // login success
        Log::info("| Auth | - Login success for email: {$credential['email']}, at {$user->last_login}");

        $token = $user->createToken('create_token_' . Str::uuid())->plainTextToken;
        $filteredUser = $user->makeHidden(['password', 'remember_token', 'roles']);
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
                ]
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
                    'Maaf, akun pengguna terkait tidak ditemukan.'
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
                ]
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
                    'Anda tidak memiliki sesi login yang aktif.'
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
                'Anda berhasil melakukan logout.'
            ),
            Response::HTTP_OK
        );
    }
}
