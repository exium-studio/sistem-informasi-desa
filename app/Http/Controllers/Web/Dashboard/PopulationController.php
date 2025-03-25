<?php

namespace App\Http\Controllers\Web\Dashboard;

use App\Helpers\CalculationHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\Templates\Response\WithDataResource;
use App\Http\Resources\Templates\Response\WithoutDataResource;
use App\Models\FamilyCard;
use App\Models\PopulationGrowth;
use App\Models\Religion;
use App\Models\Resident;
use App\Models\Village;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class PopulationController extends Controller
{
    public function index()
    {
        try {
            if (!Gate::allows('population.view')) {
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

            $currentYear = now()->year;
            $lastYear = $currentYear - 1;

            $populasi_sekarang = PopulationGrowth::getUsersWithActiveStatus();
            $total_population_now = $populasi_sekarang->count();

            $population_last_year = PopulationGrowth::where('year', $lastYear)->first()?->citizen_total ?? 0;
            $population_growth = CalculationHelper::calculateGrowthPercentage($total_population_now, $population_last_year);

            $family_now = FamilyCard::whereYear('created_at', $currentYear)->count();
            $family_last_year = FamilyCard::whereYear('created_at', $lastYear)->count();
            $family_growth = CalculationHelper::calculateGrowthPercentage($family_now, $family_last_year);

            $village_funds = Village::getDanaDesa();

            return response()->json(
                new WithDataResource(
                    Response::HTTP_OK,
                    'SUCCESS_GET_DATA',
                    'Berhasil Mengambil Data',
                    'Data populasi berhasil didapatkan.',
                    [
                        'total_population' => $total_population_now,
                        'population_growth' => $population_growth,
                        'total_family' => $family_now,
                        'family_growth' => $family_growth,
                        'total_village_funds' => $village_funds
                    ],
                )
            );
        } catch (\Exception $e) {
            Log::error('| Population Index | - Error : ' . $e->getMessage());
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

    public function growthByReligion(Request $request)
    {
        try {
            if (!Gate::allows('population.view')) {
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

            // Ambil daftar agama
            $religions = Religion::select('id', 'label')->get();

            // Ambil data per agama & bulan
            $raw = Resident::selectRaw('residents.religion_id, EXTRACT(MONTH FROM users.register_at) AS month, COUNT(*) as total')
                ->join('users', 'users.id', '=', 'residents.user_id')
                ->whereYear('users.register_at', $year)
                ->groupByRaw('residents.religion_id, EXTRACT(MONTH FROM users.register_at)')
                ->get();

            // Map data ke bentuk: [month][agama_label] = total
            $monthlyData = [];

            foreach ($raw as $row) {
                $month = (int) $row->month;
                $label = $religions->firstWhere('id', $row->religion_id)?->label ?? 'Lainnya';
                $monthlyData[$month][$label] = (int) $row->total;
            }

            // Final formatting
            $result = [];
            foreach (range(1, 12) as $month) {
                $data = $monthlyData[$month] ?? [];

                $row = [
                    'total_population' => array_sum($data),
                ];

                foreach ($religions as $religion) {
                    $label = $religion->label;
                    $row[$label] = $data[$label] ?? 0;
                }

                $result[] = $row;
            }

            return response()->json(
                new WithDataResource(
                    Response::HTTP_OK,
                    'SUCCESS_GET_DATA',
                    'Berhasil Mengambil Data',
                    'Data pertumbuhan penduduk berdasarkan agama berhasil didapatkan.',
                    $result
                ),
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::error('| Population Growth By Religion | - Error : ' . $e->getMessage());
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
