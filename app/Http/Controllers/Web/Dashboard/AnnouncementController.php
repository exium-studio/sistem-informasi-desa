<?php

namespace App\Http\Controllers\Web\Dashboard;

use App\Helpers\DocumentHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Dashboard\CreateAnnouncementRequest;
use App\Http\Requests\Web\Dashboard\UpdateAnnouncementRequest;
use App\Http\Resources\Templates\Response\WithDataResource;
use App\Http\Resources\Templates\Response\WithoutDataResource;
use App\Http\Resources\Web\Dashboard\AnnouncementResource;
use App\Models\Announcement;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class AnnouncementController extends Controller
{
    public function index()
    {
        try {
            if (!Gate::allows('announcement.view')) {
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

            $pengumuman = Announcement::with('user')->orderBy('created_at', 'desc')->get();
            if ($pengumuman->isEmpty()) {
                return response()->json(
                    new WithoutDataResource(
                        Response::HTTP_OK,
                        'SUCCESS_GET_DATA',
                        'Berhasil Mengambil Data',
                        'Data pengumuman tidak ditemukan.',
                    ),
                    Response::HTTP_OK
                );
            }

            return response()->json(
                new WithDataResource(
                    Response::HTTP_OK,
                    'SUCCESS_GET_DATA',
                    'Berhasil Mengambil Data',
                    'Data pengumuman berhasil didapatkan.',
                    AnnouncementResource::collection($pengumuman),
                ),
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::error('| Announcement Index | - Error function index : ' . $e->getMessage());
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

    public function store(CreateAnnouncementRequest $request)
    {
        try {
            if (!Gate::allows('announcement.create')) {
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

            $data = $request->validated();

            DB::beginTransaction();

            $documentIds = [];

            if ($request->hasFile('documents') && is_array($request->file('documents'))) {
                $documentIds = DocumentHelper::uploadDocuments($request->file('documents'));
            }

            $announcement = Announcement::create([
                'created_by'   => auth()->user()->id,
                'title'        => $data['title'],
                'description'  => $data['description'],
                'document_id'  => $documentIds ?: null,
                'location'     => $data['location'] ?? null,
                'published_at' => $data['startDateTime'] ?? null,
                'expires_at'   => $data['endDateTime'] ?? null
            ]);

            DB::commit();

            return response()->json(
                new WithoutDataResource(
                    Response::HTTP_CREATED,
                    'SUCCESS_CREATE_ANNOUNCEMENT',
                    'Pengumuman berhasil dibuat',
                    "Data pengumuman '{$announcement->title}' telah disimpan dengan sukses.",
                ),
                Response::HTTP_CREATED
            );
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('| Announcement Store | - Error function store : ' . $e->getMessage());
            return response()->json(
                new WithoutDataResource(
                    Response::HTTP_INTERNAL_SERVER_ERROR,
                    'ERROR_CREATE_ANNOUNCEMENT',
                    'Gagal Menyimpan Data',
                    'Terjadi kesalahan pada sistem, silahkan coba lagi nanti atau hubungi admin.',
                ),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function show($id)
    {
        try {
            if (!Gate::allows('announcement.view')) {
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

            $pengumuman = Announcement::with('user')->find($id);
            if (!$pengumuman) {
                return response()->json(
                    new WithoutDataResource(
                        Response::HTTP_NOT_FOUND,
                        'DATA_NOT_FOUND',
                        'Berhasil Mengambil Detail Data',
                        'Data pengumuman tidak ditemukan.',
                    ),
                    Response::HTTP_NOT_FOUND
                );
            }

            return response()->json(
                new WithDataResource(
                    Response::HTTP_OK,
                    'SUCCESS_GET_DATA',
                    'Berhasil Mengambil Detail Data',
                    'Data pengumuman berhasil didapatkan.',
                    new AnnouncementResource($pengumuman),
                ),
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::error('| Announcement Show | - Error function show : ' . $e->getMessage());
            return response()->json(
                new WithoutDataResource(
                    Response::HTTP_INTERNAL_SERVER_ERROR,
                    'ERROR_GET_ANNOUNCEMENT',
                    'Gagal Mendapatkan Data',
                    'Terjadi kesalahan pada sistem, silahkan coba lagi nanti atau hubungi admin.',
                ),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function update(UpdateAnnouncementRequest $request, $id)
    {
        try {
            if (!Gate::allows('announcement.edit')) {
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

            $announcement = Announcement::findOrFail($id);
            if (!$announcement) {
                return response()->json(
                    new WithoutDataResource(
                        Response::HTTP_NOT_FOUND,
                        'DATA_NOT_FOUND',
                        'Berhasil Mengambil Detail Data',
                        'Data pengumuman tidak ditemukan.',
                    ),
                    Response::HTTP_NOT_FOUND
                );
            }

            $data = $request->validated();

            $existingDocumentIds = $announcement->document_id ?? [];
            $deleteIds = $data['delete_document_ids'] ?? [];
            $newUploads = $request->file('documents') ?? [];

            // ✅ Safety: jika delete kosong & dokumen baru full, asumsikan ingin overwrite semua
            if (empty($deleteIds) && count($newUploads) === 3 && !empty($existingDocumentIds)) {
                $deleteIds = $existingDocumentIds;
                $data['delete_document_ids'] = $deleteIds;
            }

            // ✅ Validasi jumlah total dokumen (existing - delete + new) ≤ 3
            $remainingDocs = array_values(array_diff($existingDocumentIds, $deleteIds));
            $totalAfter = count($remainingDocs) + count($newUploads);

            if ($totalAfter > 3) {
                return response()->json(
                    new WithoutDataResource(
                        Response::HTTP_BAD_REQUEST,
                        'TOO_MANY_DOCUMENTS',
                        'Terlalu Banyak Dokumen',
                        "Jumlah total dokumen setelah update melebihi batas maksimum (maksimal 3)."
                    ),
                    Response::HTTP_BAD_REQUEST
                );
            }

            DB::beginTransaction();

            // ✅ Hapus dokumen lama jika ada
            if (!empty($deleteIds)) {
                DocumentHelper::deleteDocuments($deleteIds);
                $existingDocumentIds = array_values(array_diff($existingDocumentIds, $deleteIds));
            }

            // ✅ Upload dokumen baru
            $newDocumentIds = [];
            if (!empty($newUploads)) {
                $newDocumentIds = DocumentHelper::uploadDocuments($newUploads);
            }

            $finalDocumentIds = array_merge($existingDocumentIds, $newDocumentIds);

            // ✅ Update data pengumuman
            $announcement->update([
                'title'        => $data['title'],
                'description'  => $data['description'],
                'location'     => $data['location'] ?? null,
                'published_at' => $data['startDateTime'] ?? null,
                'expires_at'   => $data['endDateTime'] ?? null,
                'document_id'  => $finalDocumentIds ?: null,
            ]);

            DB::commit();

            return response()->json(
                new WithoutDataResource(
                    Response::HTTP_OK,
                    'SUCCESS_UPDATE_ANNOUNCEMENT',
                    'Pengumuman berhasil diperbarui',
                    "Data pengumuman '{$announcement->title}' telah berhasil diperbarui."
                ),
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('| Announcement Update | - Error function update : ' . $e->getMessage());
            return response()->json(
                new WithoutDataResource(
                    Response::HTTP_INTERNAL_SERVER_ERROR,
                    'ERROR_UPDATE_ANNOUNCEMENT',
                    'Gagal Memperbarui Data',
                    'Terjadi kesalahan pada sistem, silahkan coba lagi nanti atau hubungi admin.',
                ),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function destroy($id)
    {
        try {
            if (!Gate::allows('announcement.delete')) {
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

            $announcement = Announcement::findOrFail($id);
            if (!$announcement) {
                return response()->json(
                    new WithoutDataResource(
                        Response::HTTP_NOT_FOUND,
                        'DATA_NOT_FOUND',
                        'Berhasil Mengambil Detail Data',
                        'Data pengumuman tidak ditemukan.',
                    ),
                    Response::HTTP_NOT_FOUND
                );
            }

            DB::beginTransaction();

            // ✅ Gunakan helper untuk hapus dokumen + nullify document_id
            DocumentHelper::deleteDocumentsAndNullify($announcement);

            $announcement->delete();

            DB::commit();

            return response()->json(
                new WithoutDataResource(
                    Response::HTTP_OK,
                    'SUCCESS_DELETE_ANNOUNCEMENT',
                    'Pengumuman berhasil dihapus',
                    'Data pengumuman dan dokumen terkait telah dihapus.'
                ),
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::error('| Announcement Delete | - Error function destroy : ' . $e->getMessage());
            return response()->json(
                new WithoutDataResource(
                    Response::HTTP_INTERNAL_SERVER_ERROR,
                    'ERROR_DELETE_ANNOUNCEMENT',
                    'Gagal Menghapus Data',
                    'Terjadi kesalahan pada sistem, silahkan coba lagi nanti atau hubungi admin.',
                ),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
