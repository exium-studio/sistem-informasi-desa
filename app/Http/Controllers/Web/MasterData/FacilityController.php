<?php

namespace App\Http\Controllers\Web\MasterData;

use App\Helpers\DocumentHelper;
use App\Helpers\StorageServerHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Web\MasterData\CreateFacilityRequest;
use App\Http\Requests\Web\MasterData\UpdateFacilityRequest;
use App\Http\Resources\Templates\Response\WithDataResource;
use App\Http\Resources\Templates\Response\WithoutDataResource;
use App\Http\Resources\Web\Gens\FacilitiesResource;
use App\Models\Document;
use App\Models\Facilities;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class FacilityController extends Controller
{
    public function index()
    {
        try {
            if (!Gate::allows('masterdata.view')) {
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

            $facilities = Facilities::withTrashed()->get();
            if ($facilities->isEmpty()) {
                return response()->json(
                    new WithoutDataResource(
                        Response::HTTP_OK,
                        'SUCCESS_GET_DATA',
                        'Berhasil Mengambil Data',
                        'Belum ada data fasilitas yang tersedia.'
                    ),
                    Response::HTTP_OK
                );
            }

            return response()->json(
                new WithDataResource(
                    Response::HTTP_OK,
                    'SUCCESS_GET_DATA',
                    'Berhasil Mengambil Data',
                    'Data fasilitas berhasil didapatkan.',
                    FacilitiesResource::collection($facilities)
                ),
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::error('| Facility Index | - Error : ' . $e->getMessage());
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

    public function store(CreateFacilityRequest $request)
    {
        try {
            if (!Gate::allows('masterdata.create')) {
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

            DB::beginTransaction();

            $documentIds = [];

            if ($request->hasFile('documents') && is_array($request->file('documents'))) {
                $documentIds = DocumentHelper::uploadDocuments($request->file('documents'));
            }

            $facility = Facilities::create([
                'name'        => $request->name,
                'description' => $request->description,
                'location'    => $request->location,
                'document_id' => $documentIds ?: null,
            ]);

            DB::commit();

            return response()->json(
                new WithoutDataResource(
                    Response::HTTP_CREATED,
                    'SUCCESS_CREATE_DATA',
                    'Berhasil Menyimpan Data',
                    "Data fasilitas '{$facility->name}' berhasil ditambahkan."
                ),
                Response::HTTP_CREATED
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('| Facility Store | - Error : ' . $e->getMessage());
            return response()->json(
                new WithoutDataResource(
                    Response::HTTP_INTERNAL_SERVER_ERROR,
                    'ERROR_CREATE_DATA',
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
            if (!Gate::allows('masterdata.view')) {
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

            $facility = Facilities::withTrashed()->find($id);
            if (!$facility) {
                return response()->json(
                    new WithoutDataResource(
                        Response::HTTP_NOT_FOUND,
                        'DATA_NOT_FOUND',
                        'Data Tidak Ditemukan',
                        'Data fasilitas tidak ditemukan.'
                    ),
                    Response::HTTP_NOT_FOUND
                );
            }

            return response()->json(
                new WithDataResource(
                    Response::HTTP_OK,
                    'SUCCESS_GET_DATA',
                    'Berhasil Mengambil Data',
                    "Data fasilitas '{$facility->name}' berhasil didapatkan.",
                    new FacilitiesResource($facility)
                ),
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::error('| Facility Show | - Error : ' . $e->getMessage());
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

    public function update(UpdateFacilityRequest $request, $id)
    {
        try {
            if (!Gate::allows('masterdata.edit')) {
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

            $facility = Facilities::withTrashed()->find($id);
            if (!$facility) {
                return response()->json(
                    new WithoutDataResource(
                        Response::HTTP_NOT_FOUND,
                        'DATA_NOT_FOUND',
                        'Data Tidak Ditemukan',
                        'Data fasilitas tidak ditemukan.'
                    ),
                    Response::HTTP_NOT_FOUND
                );
            }

            $data = $request->validated();

            $existingDocumentIds = $facility->document_id ?? [];
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

            // ✅ Update data facility
            $facility->update([
                'name'        => $data['name'] ?? $facility->name,
                'description' => $data['description'] ?? $facility->description,
                'location'    => $data['location'] ?? $facility->location,
                'document_id' => $finalDocumentIds ?: null,
            ]);

            DB::commit();

            return response()->json(
                new WithoutDataResource(
                    Response::HTTP_OK,
                    'SUCCESS_UPDATE_DATA',
                    'Berhasil Memperbarui Data',
                    "Data fasilitas '{$facility->name}' berhasil diperbarui."
                ),
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error('| Facility Update | - Error : ' . $e->getMessage());
            return response()->json(
                new WithoutDataResource(
                    Response::HTTP_INTERNAL_SERVER_ERROR,
                    'ERROR_UPDATE_DATA',
                    'Gagal Memperbarui Data',
                    'Terjadi kesalahan pada sistem, silahkan coba lagi nanti atau hubungi admin. ' . $e->getMessage(),
                ),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function destroy($id)
    {
        try {
            if (!Gate::allows('masterdata.delete')) {
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

            DB::beginTransaction();

            $facility = Facilities::find($id);
            if (!$facility) {
                return response()->json(
                    new WithoutDataResource(
                        Response::HTTP_NOT_FOUND,
                        'DATA_NOT_FOUND',
                        'Data Tidak Ditemukan',
                        'Data fasilitas tidak ditemukan.'
                    ),
                    Response::HTTP_NOT_FOUND
                );
            }

            $facility->delete();

            DB::commit();

            return response()->json(
                new WithoutDataResource(
                    Response::HTTP_OK,
                    'SUCCESS_DELETE_DATA',
                    'Fasilitas berhasil dihapus',
                    "Data fasilitas '{$facility->name}' berhasil dihapus."
                ),
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('| Facility Destroy | - Error : ' . $e->getMessage());
            return response()->json(
                new WithoutDataResource(
                    Response::HTTP_INTERNAL_SERVER_ERROR,
                    'ERROR_DELETE_DATA',
                    'Gagal Menghapus Data',
                    'Terjadi kesalahan pada sistem, silahkan coba lagi nanti atau hubungi admin.',
                ),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function restore($id)
    {
        try {
            if (!Gate::allows('masterdata.restore')) {
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

            DB::beginTransaction();

            $facility = Facilities::onlyTrashed()->find($id);
            if (!$facility) {
                return response()->json(
                    new WithoutDataResource(
                        Response::HTTP_NOT_FOUND,
                        'DATA_NOT_FOUND',
                        'Data Tidak Ditemukan',
                        'Data fasilitas tidak ditemukan atau belum dihapus.'
                    ),
                    Response::HTTP_NOT_FOUND
                );
            }

            $facility->restore();

            DB::commit();

            return response()->json(
                new WithDataResource(
                    Response::HTTP_OK,
                    'SUCCESS_RESTORE_DATA',
                    'Berhasil Mengembalikan Data',
                    "Data fasilitas '{$facility->name}' berhasil dipulihkan.",
                    $facility
                ),
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('| Facility Restore | - Error : ' . $e->getMessage());
            return response()->json(
                new WithoutDataResource(
                    Response::HTTP_INTERNAL_SERVER_ERROR,
                    'ERROR_RESTORE_DATA',
                    'Gagal Merestorasi Data',
                    'Terjadi kesalahan pada sistem, silahkan coba lagi nanti atau hubungi admin.',
                ),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
