<?php

namespace App\Http\Controllers\Web\Dashboard;

use App\Helpers\StorageServerHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Dashboard\CreateAnnouncementRequest;
use App\Http\Requests\Web\Dashboard\UpdateAnnouncementRequest;
use App\Http\Resources\Templates\Response\WithDataResource;
use App\Http\Resources\Templates\Response\WithoutDataResource;
use App\Http\Resources\Web\Dashboard\AnnouncementResource;
use App\Models\Announcement;
use App\Models\Document;
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

            if ($request->hasFile('file') && is_array($request->file('file'))) {
                $uploadedFiles = StorageServerHelper::uploadToServer($request->file('file'));

                // Debugging struktur response
                // Log::info('Struktur response upload file:', $uploadedFiles);

                // Pastikan response berupa array berisi dokumen
                if (is_array($uploadedFiles) && count($uploadedFiles) > 0) {
                    foreach ($uploadedFiles as $uploadedFile) {
                        if (is_array($uploadedFile) && isset($uploadedFile['file_id'])) {
                            $document = Document::create([
                                'uploaded_by' => auth()->user()->id,
                                'document_status_id' => 2,
                                'verified_by' => auth()->user()->id,
                                'file_id' => $uploadedFile['file_id'],
                                'file_name' => $uploadedFile['filename'],
                                'file_path' => $uploadedFile['url'],
                                'file_mime_type' => $uploadedFile['mime_type'],
                                'file_size' => $uploadedFile['size'],
                                'reason' => null
                            ]);

                            $documentIds[] = $document->id;
                        } else {
                            Log::error('Gagal menyimpan dokumen. Tidak ada file_id dalam response.', [$uploadedFile]);
                        }
                    }
                } else {
                    Log::error('Format response upload file tidak sesuai.', [$uploadedFiles]);
                }
            }

            Announcement::create([
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
                    'Pengumuman telah disimpan dengan sukses.',
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

            // TODO: Alur berubah, tambah payload delete_document_ids[]
            // 1. lakukan upload dokumen baru dahulu
            // 2. lakukan delete dokumen lama berdasarkan delete_document_ids[]
            // 3. update request ('file') menjadi ('documents')

            $data = $request->validated();

            DB::beginTransaction();

            $documentIds = [];

            if ($request->hasFile('file') && is_array($request->file('file'))) {
                $uploadedFiles = StorageServerHelper::uploadToServer($request->file('file'));

                // Debugging struktur response
                // Log::info('Struktur response upload file:', $uploadedFiles);

                // Pastikan response berupa array berisi dokumen
                if (is_array($uploadedFiles) && count($uploadedFiles) > 0) {
                    foreach ($uploadedFiles as $uploadedFile) {
                        if (is_array($uploadedFile) && isset($uploadedFile['file_id'])) {
                            $document = Document::create([
                                'uploaded_by' => auth()->user()->id,
                                'document_status_id' => 2,
                                'verified_by' => auth()->user()->id,
                                'file_id' => $uploadedFile['file_id'],
                                'file_name' => $uploadedFile['filename'],
                                'file_path' => $uploadedFile['url'],
                                'file_mime_type' => $uploadedFile['mime_type'],
                                'file_size' => $uploadedFile['size'],
                                'reason' => null
                            ]);

                            $documentIds[] = $document->id;
                        } else {
                            Log::error('Gagal menyimpan dokumen. Tidak ada file_id dalam response.', [$uploadedFile]);
                        }
                    }
                } else {
                    Log::error('Format response upload file tidak sesuai.', [$uploadedFiles]);
                }

                $existingDocumentIds = $announcement->document_id ?? [];

                if (!empty($existingDocumentIds)) {
                    $fileIds = Document::whereIn('id', $existingDocumentIds)->pluck('file_id')->toArray();

                    if (!empty($fileIds)) {
                        StorageServerHelper::deleteFromServer($fileIds);
                    }

                    Document::whereIn('id', $existingDocumentIds)->delete();
                }
            }

            // Update pengumuman
            $announcement->update([
                'title'        => $data['title'],
                'description'  => $data['description'],
                'location'     => $data['location'] ?? null,
                'published_at' => $data['startDateTime'] ?? null,
                'expires_at'   => $data['endDateTime'] ?? null,
                'document_id'  => $documentIds ?: $announcement->document_id,
            ]);

            DB::commit();

            return response()->json(
                new WithoutDataResource(
                    Response::HTTP_OK,
                    'SUCCESS_UPDATE_ANNOUNCEMENT',
                    'Pengumuman berhasil diperbarui',
                    'Pengumuman telah berhasil diperbarui.'
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

            $documentIds = is_array($announcement->document_id)
                ? $announcement->document_id
                : json_decode($announcement->document_id, true);

            if (!empty($documentIds)) {
                $fileIds = Document::whereIn('id', $documentIds)->pluck('file_id')->toArray();

                $announcement->update([
                    'document_id' => null
                ]);

                if (!empty($fileIds)) {
                    StorageServerHelper::deleteFromServer($fileIds);
                }

                Document::whereIn('id', $documentIds)->delete();
            }

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
