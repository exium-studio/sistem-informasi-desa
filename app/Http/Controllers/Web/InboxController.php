<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\Templates\Response\WithDataResource;
use App\Http\Resources\Templates\Response\WithoutDataResource;
use App\Http\Resources\Web\InboxResource;
use App\Models\Inbox;
use App\Models\InboxReads;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class InboxController extends Controller
{
    public function index()
    {
        try {
            if (!Gate::allows('inbox.view')) {
                return response()->json(
                    new WithoutDataResource(
                        Response::HTTP_FORBIDDEN,
                        'NO_ACCESS',
                        'Tidak Memiliki Akses',
                        'Anda tidak memiliki akses untuk mengakses halaman ini.',
                    ),
                    Response::HTTP_FORBIDDEN
                );
            }

            $userId = auth()->id();
            $user = auth()->user();

            $superAdminInboxes = [];

            if ($user?->hasRole('Super Admin')) {
                $superAdminInboxes = Inbox::with(['created_user', 'inbox_type'])
                    ->orderByDesc('created_at')
                    ->get();
            }

            // Subquery inbox yang sudah dibaca user
            $readInboxIds = InboxReads::where('user_id', $userId)
                ->where('is_read', true)
                ->pluck('inbox_id')
                ->toArray();

            // Function reusable untuk ambil inbox
            $getInboxes = function (bool $verified) use ($userId, $readInboxIds) {
                return Inbox::with(['created_user', 'inbox_type'])
                    ->whereRaw('received_by @> ?', [json_encode([$userId])])
                    ->where('is_verified', $verified)
                    ->orderByRaw(
                        "(CASE WHEN id IN (" . implode(',', $readInboxIds ?: [0]) . ") THEN 1 ELSE 0 END), created_at DESC"
                    )
                    ->get();
            };

            $nonVerified = $getInboxes(false);
            $verified = $getInboxes(true);

            if ($nonVerified->isEmpty() && $verified->isEmpty()) {
                return response()->json(
                    new WithoutDataResource(
                        Response::HTTP_OK,
                        'SUCCESS_GET_DATA',
                        'Berhasil Mengambil Data',
                        'Data inbox tidak ditemukan.',
                    ),
                    Response::HTTP_OK
                );
            }

            return response()->json(
                new WithDataResource(
                    Response::HTTP_OK,
                    'SUCCESS_GET_DATA',
                    'Berhasil Mengambil Data',
                    'Data inbox berhasil didapatkan.',
                    [
                        'super_admin_inboxes' => InboxResource::collection($superAdminInboxes),
                        'non_verified_inboxes' => InboxResource::collection($nonVerified),
                        'verified_inboxes' => InboxResource::collection($verified),
                    ]
                ),
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::error('| Inbox Index | - Error : ' . $e->getMessage());
            return response()->json(
                new WithoutDataResource(
                    Response::HTTP_INTERNAL_SERVER_ERROR,
                    'ERROR_GET_DATA',
                    'Gagal Mengambil Data',
                    'Terjadi kesalahan pada sistem, silahkan coba lagi nanti atau hubungi admin.',
                ),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function unreadCount()
    {
        try {
            if (!Gate::allows('inbox.view')) {
                return response()->json(
                    new WithoutDataResource(
                        Response::HTTP_FORBIDDEN,
                        'NO_ACCESS',
                        'Tidak Memiliki Akses',
                        'Anda tidak memiliki akses untuk mengakses halaman ini.',
                    ),
                    Response::HTTP_FORBIDDEN
                );
            }

            $userId = auth()->id();

            // Ambil semua inbox yang mengandung user ini
            $count = Inbox::whereRaw('received_by @> ?', [json_encode([$userId])])
                ->whereNotIn('id', function ($query) use ($userId) {
                    $query->select('inbox_id')
                        ->from('inbox_reads')
                        ->where('user_id', $userId)
                        ->where('is_read', true);
                })
                ->count();

            return response()->json(
                new WithDataResource(
                    Response::HTTP_OK,
                    'SUCCESS_GET_DATA',
                    'Berhasil Mengambil Data',
                    'Jumlah inbox yang belum dibaca berhasil didapatkan.',
                    [
                        'unread_count' => $count
                    ]
                ),
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::error('| Inbox Unread Count | - Error : ' . $e->getMessage());
            return response()->json(
                new WithoutDataResource(
                    Response::HTTP_INTERNAL_SERVER_ERROR,
                    'ERROR_GET_DATA',
                    'Gagal Mengambil Data',
                    'Terjadi kesalahan pada sistem, silahkan coba lagi nanti atau hubungi admin.',
                ),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function update(Request $request)
    {
        try {
            if (!Gate::allows('inbox.edit')) {
                return response()->json(
                    new WithoutDataResource(
                        Response::HTTP_FORBIDDEN,
                        'NO_ACCESS',
                        'Tidak Memiliki Akses',
                        'Anda tidak memiliki akses untuk mengakses halaman ini.',
                    ),
                    Response::HTTP_FORBIDDEN
                );
            }

            $request->validate([
                'inbox_ids' => 'required|array|min:1',
                'inbox_ids.*' => 'integer|exists:inboxes,id'
            ]);

            $userId = auth()->id();
            $now = now();

            DB::beginTransaction();

            foreach ($request->inbox_ids as $inboxId) {
                InboxReads::updateOrCreate(
                    [
                        'inbox_id' => $inboxId,
                        'user_id' => $userId
                    ],
                    [
                        'is_read' => true,
                        'read_at' => $now
                    ]
                );
            }

            DB::commit();

            return response()->json(
                new WithoutDataResource(
                    Response::HTTP_OK,
                    'SUCCESS_UPDATE_DATA',
                    'Berhasil Memperbarui Data',
                    'Semua inbox berhasil ditandai sebagai sudah dibaca.',
                ),
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('| Inbox Update | - Error : ' . $e->getMessage());
            return response()->json(
                new WithoutDataResource(
                    Response::HTTP_INTERNAL_SERVER_ERROR,
                    'ERROR_UPDATE_DATA',
                    'Gagal Memperbarui Data',
                    'Terjadi kesalahan pada sistem, silahkan coba lagi nanti atau hubungi admin.',
                ),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
