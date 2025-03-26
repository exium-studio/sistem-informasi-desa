<?php

namespace App\Http\Controllers\Web\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\Templates\Response\WithDataResource;
use App\Http\Resources\Templates\Response\WithoutDataResource;
use App\Models\Expenses;
use App\Models\Income;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class FundMutationController extends Controller
{
    public function index(Request $request)
    {
        try {
            if (!Gate::allows('fundmutation.view')) {
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

            $year = $request->input('year', now()->year);

            // Ambil data income
            $incomeData = Income::selectRaw('EXTRACT(MONTH FROM created_at) as month, SUM(value) as total')
                ->whereYear('created_at', $year)
                ->groupByRaw('EXTRACT(MONTH FROM created_at)')
                ->pluck('total', 'month');

            // Ambil data expense
            $expenseData = Expenses::selectRaw('EXTRACT(MONTH FROM created_at) as month, SUM(value) as total')
                ->whereYear('created_at', $year)
                ->groupByRaw('EXTRACT(MONTH FROM created_at)')
                ->pluck('total', 'month');

            $result = [];

            foreach (range(1, 12) as $i) {
                $result[] = [
                    'income' => (int) ($incomeData[$i] ?? 0),
                    'expense' => (int) ($expenseData[$i] ?? 0),
                ];
            }

            return response()->json(
                new WithDataResource(
                    Response::HTTP_OK,
                    'SUCCESS_GET_DATA',
                    'Berhasil Mengambil Data',
                    'Data pendanaan berhasil didapatkan.',
                    $result
                ),
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::error('| Fund Mutation Index | - Error : ' . $e->getMessage());
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
}
