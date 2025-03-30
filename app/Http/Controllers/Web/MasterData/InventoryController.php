<?php

namespace App\Http\Controllers\Web\MasterData;

use App\Helpers\DocumentHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Web\MasterData\CreateInventoryRequest;
use App\Http\Requests\Web\MasterData\UpdateInventoryRequest;
use App\Http\Resources\Templates\Response\WithDataResource;
use App\Http\Resources\Templates\Response\WithoutDataResource;
use App\Http\Resources\Web\Gens\InventoriesResource;
use App\Models\Inventory;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class InventoryController extends Controller
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

            $inventories = Inventory::withTrashed()->get();
            if ($inventories->isEmpty()) {
                return response()->json(
                    new WithoutDataResource(
                        Response::HTTP_OK,
                        'SUCCESS_GET_DATA',
                        'Berhasil Mengambil Data',
                        'Belum ada data inventaris yang tersedia.'
                    ),
                    Response::HTTP_OK
                );
            }

            return response()->json(
                new WithDataResource(
                    Response::HTTP_OK,
                    'SUCCESS_GET_DATA',
                    'Berhasil Mengambil Data',
                    'Data inventaris berhasil didapatkan.',
                    InventoriesResource::collection($inventories)
                ),
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::error('| Inventory Index | - Error: ' . $e->getMessage());
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

    public function store(CreateInventoryRequest $request)
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

            $inventory = Inventory::create([
                'name'           => $request->name,
                'description'    => $request->description,
                'amount'         => $request->amount,
                'amount_usage'   => 0,
                'document_id'    => $documentIds ?: null,
            ]);

            DB::commit();

            return response()->json(
                new WithoutDataResource(
                    Response::HTTP_CREATED,
                    'SUCCESS_CREATE_DATA',
                    'Berhasil Menyimpan Data',
                    "Data inventaris '{$inventory->name}' berhasil ditambahkan.",
                ),
                Response::HTTP_CREATED
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('| Inventory Store | - Error : ' . $e->getMessage());
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

            $inventory = Inventory::withTrashed()->find($id);
            if (!$inventory) {
                return response()->json(
                    new WithoutDataResource(
                        Response::HTTP_NOT_FOUND,
                        'DATA_NOT_FOUND',
                        'Data Tidak Ditemukan',
                        'Data inventaris tidak ditemukan.'
                    ),
                    Response::HTTP_NOT_FOUND
                );
            }

            return response()->json(
                new WithDataResource(
                    Response::HTTP_OK,
                    'SUCCESS_GET_DATA',
                    'Berhasil Mengambil Data',
                    "Data inventaris '{$inventory->name}' berhasil didapatkan.",
                    new InventoriesResource($inventory)
                ),
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::error('| Inventory Show | - Error: ' . $e->getMessage());
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

    public function update(UpdateInventoryRequest $request, $id)
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

            $inventory = Inventory::withTrashed()->find($id);
            if (!$inventory) {
                return response()->json(
                    new WithoutDataResource(
                        Response::HTTP_NOT_FOUND,
                        'DATA_NOT_FOUND',
                        'Data Tidak Ditemukan',
                        'Data inventaris tidak ditemukan.'
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

            // ✅ Update data inventory
            $inventory->update([
                'name'           => $data['name'] ?? $inventory->name,
                'description'    => $data['description'] ?? $inventory->description,
                'amount'         => $data['amount'] ?? $inventory->amount,
                'amount_usage'   => $data['amount_usage'] ?? $inventory->amount_usage,
                'document_id'    => $finalDocumentIds ?: null,
            ]);

            DB::commit();

            return response()->json(
                new WithoutDataResource(
                    Response::HTTP_OK,
                    'SUCCESS_UPDATE_DATA',
                    'Berhasil Memperbarui Data',
                    "Data inventaris '{$inventory->name}' berhasil diperbarui."
                ),
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('| Inventory Update | - Error : ' . $e->getMessage());
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

            $inventory = Inventory::find($id);
            if (!$inventory) {
                return response()->json(
                    new WithoutDataResource(
                        Response::HTTP_NOT_FOUND,
                        'DATA_NOT_FOUND',
                        'Data Tidak Ditemukan',
                        'Data inventaris tidak ditemukan.'
                    ),
                    Response::HTTP_NOT_FOUND
                );
            }

            $inventory->delete();

            DB::commit();

            return response()->json(
                new WithoutDataResource(
                    Response::HTTP_OK,
                    'SUCCESS_DELETE_DATA',
                    'Berhasil Menghapus Data',
                    "Data inventaris '{$inventory->name}' berhasil dihapus."
                ),
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('| Inventory Destroy | - Error: ' . $e->getMessage());
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

            $inventory = Inventory::onlyTrashed()->find($id);
            if (!$inventory) {
                return response()->json(
                    new WithoutDataResource(
                        Response::HTTP_NOT_FOUND,
                        'DATA_NOT_FOUND',
                        'Data Tidak Ditemukan',
                        'Data inventaris tidak ditemukan atau belum dihapus.'
                    ),
                    Response::HTTP_NOT_FOUND
                );
            }

            $inventory->restore();

            DB::commit();

            return response()->json(
                new WithDataResource(
                    Response::HTTP_OK,
                    'SUCCESS_RESTORE_DATA',
                    'Berhasil Mengembalikan Data',
                    "Data inventaris '{$inventory->name}' berhasil dikembalikan.",
                    new InventoriesResource($inventory)
                ),
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('| Inventory Restore | - Error: ' . $e->getMessage());
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
