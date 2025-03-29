<?php

namespace App\Http\Controllers\Web\MasterData;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\MasterData\CreateEducationRequest;
use App\Http\Requests\Web\MasterData\UpdateEducationRequest;
use App\Http\Resources\Templates\Response\WithDataResource;
use App\Http\Resources\Templates\Response\WithoutDataResource;
use App\Models\Education;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class EducationController extends Controller
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

            $educations = Education::withTrashed()->get();
            if ($educations->isEmpty()) {
                return response()->json(new WithoutDataResource(
                    Response::HTTP_OK,
                    'SUCCESS_GET_DATA',
                    'Berhasil Mengambil Data',
                    'Belum ada data pendidikan yang tersedia.'
                ), Response::HTTP_OK);
            }

            return response()->json(
                new WithDataResource(
                    Response::HTTP_OK,
                    'SUCCESS_GET_DATA',
                    'Berhasil Mengambil Data',
                    'Data pendidikan berhasil didapatkan.',
                    $educations
                ),
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::error('| Education Index | - Error : ' . $e->getMessage());
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

    public function store(CreateEducationRequest $request)
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

            Education::create([
                'label' => $request->label,
            ]);

            DB::commit();
            return response()->json(
                new WithoutDataResource(
                    Response::HTTP_CREATED,
                    'SUCCESS_CREATE_DATA',
                    'Berhasil Menyimpan Data',
                    "Data pendidikan {$request->label} berhasil ditambahkan."
                ),
                Response::HTTP_CREATED
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('| Education Store | - Error : ' . $e->getMessage());
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

            $education = Education::withTrashed()->find($id);
            if (!$education) {
                return response()->json(new WithoutDataResource(
                    Response::HTTP_NOT_FOUND,
                    'DATA_NOT_FOUND',
                    'Data Tidak Ditemukan',
                    'Pendidikan dengan ID tersebut tidak ditemukan.'
                ), Response::HTTP_NOT_FOUND);
            }

            return response()->json(new WithDataResource(
                Response::HTTP_OK,
                'SUCCESS_GET_DATA',
                'Berhasil Mengambil Data',
                "Data pendidikan '{$education->label}' berhasil didapatkan.",
                $education
            ), Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('| Education Show | - Error : ' . $e->getMessage());
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

    public function update(UpdateEducationRequest $request, $id)
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

            $education = Education::withTrashed()->find($id);
            if (!$education) {
                return response()->json(new WithoutDataResource(
                    Response::HTTP_NOT_FOUND,
                    'DATA_NOT_FOUND',
                    'Data Tidak Ditemukan',
                    'Pendidikan dengan ID tersebut tidak ditemukan.'
                ), Response::HTTP_NOT_FOUND);
            }

            $education->update([
                'label' => $request->label,
            ]);

            DB::commit();
            return response()->json(
                new WithoutDataResource(
                    Response::HTTP_OK,
                    'SUCCESS_UPDATE_DATA',
                    'Berhasil Memperbarui Data',
                    "Data pendidikan '{$education->label}' berhasil diperbarui."
                ),
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('| Education Update | - Error : ' . $e->getMessage());
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

            $education = Education::find($id);
            if (!$education) {
                return response()->json(new WithoutDataResource(
                    Response::HTTP_NOT_FOUND,
                    'DATA_NOT_FOUND',
                    'Data Tidak Ditemukan',
                    'Pendidikan dengan ID tersebut tidak ditemukan.'
                ), Response::HTTP_NOT_FOUND);
            }

            $education->delete();

            DB::commit();
            return response()->json(new WithoutDataResource(
                Response::HTTP_OK,
                'SUCCESS_DELETE_DATA',
                'Berhasil Menghapus Data',
                "Data pendidikan '{$education->label}' berhasil dihapus."
            ), Response::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('| Education Destroy | - Error : ' . $e->getMessage());
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

            $education = Education::onlyTrashed()->find($id);
            if (!$education) {
                return response()->json(new WithoutDataResource(
                    Response::HTTP_NOT_FOUND,
                    'DATA_NOT_FOUND',
                    'Data Tidak Ditemukan',
                    'Pendidikan dengan ID tersebut tidak ditemukan atau belum dihapus.'
                ), Response::HTTP_NOT_FOUND);
            }

            $education->restore();

            DB::commit();
            return response()->json(new WithDataResource(
                Response::HTTP_OK,
                'SUCCESS_RESTORE_DATA',
                'Berhasil Mengembalikan Data',
                "Data pendidikan '{$education->label}' berhasil dikembalikan.",
                $education
            ), Response::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('| Education Restore | - Error : ' . $e->getMessage());
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
