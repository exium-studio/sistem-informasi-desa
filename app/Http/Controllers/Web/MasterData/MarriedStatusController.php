<?php

namespace App\Http\Controllers\Web\MasterData;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\MasterData\CreateMariedStatusRequest;
use App\Http\Requests\Web\MasterData\UpdateMariedStatusRequest;
use App\Http\Resources\Templates\Response\WithDataResource;
use App\Http\Resources\Templates\Response\WithoutDataResource;
use App\Models\MariedStatus;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class MarriedStatusController extends Controller
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

            $statuses = MariedStatus::withTrashed()->get();
            if ($statuses->isEmpty()) {
                return response()->json(new WithoutDataResource(
                    Response::HTTP_OK,
                    'SUCCESS_GET_DATA',
                    'Berhasil Mengambil Data',
                    'Belum ada data status pernikahan yang tersedia.'
                ), Response::HTTP_OK);
            }

            return response()->json(new WithDataResource(
                Response::HTTP_OK,
                'SUCCESS_GET_DATA',
                'Berhasil Mengambil Data',
                'Data status pernikahan berhasil didapatkan.',
                $statuses
            ), Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('| MarriedStatus Index | - Error : ' . $e->getMessage());
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

    public function store(CreateMariedStatusRequest $request)
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

            MariedStatus::create([
                'label' => $request->label,
            ]);

            DB::commit();
            return response()->json(
                new WithoutDataResource(
                    Response::HTTP_CREATED,
                    'SUCCESS_CREATE_DATA',
                    'Berhasil Menyimpan Data',
                    "Data status pernikahan '{$request->label}' berhasil ditambahkan."
                ),
                Response::HTTP_CREATED
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('| MarriedStatus Store | - Error : ' . $e->getMessage());
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

            $status = MariedStatus::withTrashed()->find($id);
            if (!$status) {
                return response()->json(new WithoutDataResource(
                    Response::HTTP_NOT_FOUND,
                    'DATA_NOT_FOUND',
                    'Data Tidak Ditemukan',
                    'Status pernikahan dengan ID tersebut tidak ditemukan.'
                ), Response::HTTP_NOT_FOUND);
            }

            return response()->json(new WithDataResource(
                Response::HTTP_OK,
                'SUCCESS_GET_DATA',
                'Berhasil Mengambil Data',
                "Data status pernikahan '{$status->label}' berhasil didapatkan.",
                $status
            ), Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('| MarriedStatus Show | - Error : ' . $e->getMessage());
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

    public function update(UpdateMariedStatusRequest $request, $id)
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

            DB::beginTransaction();

            $status = MariedStatus::withTrashed()->find($id);
            if (!$status) {
                return response()->json(new WithoutDataResource(
                    Response::HTTP_NOT_FOUND,
                    'DATA_NOT_FOUND',
                    'Data Tidak Ditemukan',
                    'Status pernikahan dengan ID tersebut tidak ditemukan.'
                ), Response::HTTP_NOT_FOUND);
            }

            $status->update([
                'label' => $request->label,
            ]);

            DB::commit();
            return response()->json(
                new WithoutDataResource(
                    Response::HTTP_OK,
                    'SUCCESS_UPDATE_DATA',
                    'Berhasil Memperbarui Data',
                    "Data status pernikahan '{$status->label}' berhasil diperbarui."
                ),
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('| MarriedStatus Update | - Error : ' . $e->getMessage());
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

            $status = MariedStatus::find($id);
            if (!$status) {
                return response()->json(new WithoutDataResource(
                    Response::HTTP_NOT_FOUND,
                    'DATA_NOT_FOUND',
                    'Data Tidak Ditemukan',
                    'Status pernikahan dengan ID tersebut tidak ditemukan.'
                ), Response::HTTP_NOT_FOUND);
            }

            $status->delete();

            DB::commit();
            return response()->json(new WithoutDataResource(
                Response::HTTP_OK,
                'SUCCESS_DELETE_DATA',
                'Berhasil Menghapus Data',
                "Data status pernikahan '{$status->label}' berhasil dihapus."
            ), Response::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('| MarriedStatus Destroy | - Error : ' . $e->getMessage());
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

            $status = MariedStatus::onlyTrashed()->find($id);
            if (!$status) {
                return response()->json(new WithoutDataResource(
                    Response::HTTP_NOT_FOUND,
                    'DATA_NOT_FOUND',
                    'Data Tidak Ditemukan',
                    'Status pernikahan dengan ID tersebut tidak ditemukan atau belum dihapus.'
                ), Response::HTTP_NOT_FOUND);
            }

            $status->restore();

            DB::commit();
            return response()->json(new WithDataResource(
                Response::HTTP_OK,
                'SUCCESS_RESTORE_DATA',
                'Berhasil Mengembalikan Data',
                "Data status pernikahan '{$status->label}' berhasil dikembalikan.",
                $status
            ), Response::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('| MarriedStatus Restore | - Error : ' . $e->getMessage());
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
