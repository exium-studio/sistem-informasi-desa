<?php

namespace App\Http\Controllers\Web\MasterData;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\MasterData\CreateCitizenshipRequest;
use App\Http\Requests\Web\MasterData\UpdateCitizenshipRequest;
use App\Http\Resources\Templates\Response\WithDataResource;
use App\Http\Resources\Templates\Response\WithoutDataResource;
use App\Models\Citizenship;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;;

class CitizenshipController extends Controller
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

            $citizenships = Citizenship::withTrashed()->get();
            if ($citizenships->isEmpty()) {
                return response()->json(new WithoutDataResource(
                    Response::HTTP_OK,
                    'SUCCESS_GET_DATA',
                    'Berhasil Mengambil Data',
                    'Belum ada data kewarganegaraan yang tersedia.'
                ), Response::HTTP_OK);
            }

            return response()->json(new WithDataResource(
                Response::HTTP_OK,
                'SUCCESS_GET_DATA',
                'Berhasil Mengambil Data',
                'Data kewarganegaraan berhasil didapatkan.',
                $citizenships
            ), Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('| Citizenship Index | - Error : ' . $e->getMessage());
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

    public function store(CreateCitizenshipRequest $request)
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

            Citizenship::create([
                'label' => $request->label,
            ]);

            DB::commit();
            return response()->json(
                new WithoutDataResource(
                    Response::HTTP_CREATED,
                    'SUCCESS_CREATE_DATA',
                    'Berhasil Menyimpan Data',
                    "Data kewarganegaraan '{$request->label}' berhasil ditambahkan."
                ),
                Response::HTTP_CREATED
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('| Citizenship Store | - Error : ' . $e->getMessage());
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

            $citizenship = Citizenship::withTrashed()->find($id);
            if (!$citizenship) {
                return response()->json(new WithoutDataResource(
                    Response::HTTP_NOT_FOUND,
                    'DATA_NOT_FOUND',
                    'Data Tidak Ditemukan',
                    'Kewarganegaraan dengan ID tersebut tidak ditemukan.'
                ), Response::HTTP_NOT_FOUND);
            }

            return response()->json(new WithDataResource(
                Response::HTTP_OK,
                'SUCCESS_GET_DATA',
                'Berhasil Mengambil Data',
                "Data kewarganegaraan '{$citizenship->label}' berhasil didapatkan.",
                $citizenship
            ), Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('| Citizenship Show | - Error : ' . $e->getMessage());
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

    public function update(UpdateCitizenshipRequest $request, $id)
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

            $citizenship = Citizenship::withTrashed()->find($id);
            if (!$citizenship) {
                return response()->json(new WithoutDataResource(
                    Response::HTTP_NOT_FOUND,
                    'DATA_NOT_FOUND',
                    'Data Tidak Ditemukan',
                    'Kewarganegaraan dengan ID tersebut tidak ditemukan.'
                ), Response::HTTP_NOT_FOUND);
            }

            $citizenship->update([
                'label' => $request->label,
            ]);

            DB::commit();
            return response()->json(
                new WithoutDataResource(
                    Response::HTTP_OK,
                    'SUCCESS_UPDATE_DATA',
                    'Berhasil Memperbarui Data',
                    "Data kewarganegaraan '{$citizenship->label}' berhasil diperbarui."
                ),
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('| Citizenship Update | - Error : ' . $e->getMessage());
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

            $citizenship = Citizenship::find($id);
            if (!$citizenship) {
                return response()->json(new WithoutDataResource(
                    Response::HTTP_NOT_FOUND,
                    'DATA_NOT_FOUND',
                    'Data Tidak Ditemukan',
                    'Kewarganegaraan dengan ID tersebut tidak ditemukan.'
                ), Response::HTTP_NOT_FOUND);
            }

            $citizenship->delete();

            DB::commit();
            return response()->json(new WithoutDataResource(
                Response::HTTP_OK,
                'SUCCESS_DELETE_DATA',
                'Berhasil Menghapus Data',
                "Data kewarganegaraan '{$citizenship->label}' berhasil dihapus."
            ), Response::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('| Citizenship Destroy | - Error : ' . $e->getMessage());
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

            $citizenship = Citizenship::onlyTrashed()->find($id);
            if (!$citizenship) {
                return response()->json(new WithoutDataResource(
                    Response::HTTP_NOT_FOUND,
                    'DATA_NOT_FOUND',
                    'Data Tidak Ditemukan',
                    'Kewarganegaraan dengan ID tersebut tidak ditemukan atau belum dihapus.'
                ), Response::HTTP_NOT_FOUND);
            }

            $citizenship->restore();

            DB::commit();
            return response()->json(new WithDataResource(
                Response::HTTP_OK,
                'SUCCESS_RESTORE_DATA',
                'Berhasil Mengembalikan Data',
                "Data kewarganegaraan '{$citizenship->label}' berhasil dikembalikan.",
                $citizenship
            ), Response::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('| Citizenship Restore | - Error : ' . $e->getMessage());
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
