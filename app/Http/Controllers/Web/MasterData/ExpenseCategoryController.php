<?php

namespace App\Http\Controllers\Web\MasterData;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\MasterData\CreateExpenseCategoryRequest;
use App\Http\Requests\Web\MasterData\UpdateExpenseCategoryRequest;
use App\Http\Resources\Templates\Response\WithDataResource;
use App\Http\Resources\Templates\Response\WithoutDataResource;
use App\Models\ExpenseCategory;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class ExpenseCategoryController extends Controller
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

            $categories = ExpenseCategory::withTrashed()->get();
            if ($categories->isEmpty()) {
                return response()->json(new WithoutDataResource(
                    Response::HTTP_OK,
                    'SUCCESS_GET_DATA',
                    'Berhasil Mengambil Data',
                    'Belum ada data kategori pengeluaran yang tersedia.'
                ), Response::HTTP_OK);
            }

            return response()->json(new WithDataResource(
                Response::HTTP_OK,
                'SUCCESS_GET_DATA',
                'Berhasil Mengambil Data',
                'Data kategori pengeluaran berhasil didapatkan.',
                $categories
            ), Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('| ExpenseCategory Index | - Error : ' . $e->getMessage());
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


    public function store(CreateExpenseCategoryRequest $request)
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

            ExpenseCategory::create([
                'label' => $request->label,
            ]);

            DB::commit();
            return response()->json(
                new WithoutDataResource(
                    Response::HTTP_CREATED,
                    'SUCCESS_CREATE_DATA',
                    'Berhasil Menyimpan Data',
                    "Data kategori pengeluaran '{$request->label}' berhasil ditambahkan."
                ),
                Response::HTTP_CREATED
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('| ExpenseCategory Store | - Error : ' . $e->getMessage());
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

            $category = ExpenseCategory::withTrashed()->find($id);
            if (!$category) {
                return response()->json(new WithoutDataResource(
                    Response::HTTP_NOT_FOUND,
                    'DATA_NOT_FOUND',
                    'Data Tidak Ditemukan',
                    'Kategori pengeluaran dengan ID tersebut tidak ditemukan.'
                ), Response::HTTP_NOT_FOUND);
            }

            return response()->json(new WithDataResource(
                Response::HTTP_OK,
                'SUCCESS_GET_DATA',
                'Berhasil Mengambil Data',
                "Data kategori pengeluaran '{$category->label}' berhasil didapatkan.",
                $category
            ), Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('| ExpenseCategory Show | - Error : ' . $e->getMessage());
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

    public function update(UpdateExpenseCategoryRequest $request, $id)
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

            $category = ExpenseCategory::withTrashed()->find($id);
            if (!$category) {
                return response()->json(new WithoutDataResource(
                    Response::HTTP_NOT_FOUND,
                    'DATA_NOT_FOUND',
                    'Data Tidak Ditemukan',
                    'Kategori pengeluaran dengan ID tersebut tidak ditemukan.'
                ), Response::HTTP_NOT_FOUND);
            }

            $category->update([
                'label' => $request->label,
            ]);

            DB::commit();
            return response()->json(
                new WithoutDataResource(
                    Response::HTTP_OK,
                    'SUCCESS_UPDATE_DATA',
                    'Berhasil Memperbarui Data',
                    "Data kategori pengeluaran '{$category->label}' berhasil diperbarui."
                ),
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('| ExpenseCategory Update | - Error : ' . $e->getMessage());
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

            $category = ExpenseCategory::find($id);
            if (!$category) {
                return response()->json(new WithoutDataResource(
                    Response::HTTP_NOT_FOUND,
                    'DATA_NOT_FOUND',
                    'Data Tidak Ditemukan',
                    'Kategori pengeluaran dengan ID tersebut tidak ditemukan.'
                ), Response::HTTP_NOT_FOUND);
            }

            $category->delete();

            DB::commit();
            return response()->json(new WithoutDataResource(
                Response::HTTP_OK,
                'SUCCESS_DELETE_DATA',
                'Berhasil Menghapus Data',
                "Data kategori pengeluaran '{$category->label}' berhasil dihapus."
            ), Response::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('| ExpenseCategory Destroy | - Error : ' . $e->getMessage());
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

            $category = ExpenseCategory::onlyTrashed()->find($id);
            if (!$category) {
                return response()->json(
                    new WithoutDataResource(
                        Response::HTTP_NOT_FOUND,
                        'DATA_NOT_FOUND',
                        'Data Tidak Ditemukan',
                        'Kategori pengeluaran dengan ID tersebut tidak ditemukan atau belum dihapus.'
                    ),
                    Response::HTTP_NOT_FOUND
                );
            }

            $category->restore();

            DB::commit();
            return response()->json(
                new WithDataResource(
                    Response::HTTP_OK,
                    'SUCCESS_RESTORE_DATA',
                    'Berhasil Mengembalikan Data',
                    "Data kategori pengeluaran '{$category->label}' berhasil dikembalikan.",
                    $category
                ),
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('| ExpenseCategory Restore | - Error : ' . $e->getMessage());
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
