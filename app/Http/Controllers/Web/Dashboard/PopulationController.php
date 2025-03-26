<?php

namespace App\Http\Controllers\Web\Dashboard;

use App\Helpers\CalculationHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\Templates\Response\WithDataResource;
use App\Http\Resources\Templates\Response\WithoutDataResource;
use App\Models\Citizenship;
use App\Models\Education;
use App\Models\FamilyCard;
use App\Models\MariedStatus;
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

    public function growthPerYear(Request $request)
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

            $result = [
                'religion' => $this->getPopulationByReligion($year),
                'education' => $this->getPopulationByEducation($year),
                'married_status' => $this->getPopulationByMariedStatus($year),
                'citizenship' => $this->getPopulationByCitizenship($year),
                'gender' => $this->getPopulationByGender($year),
            ];

            return response()->json(
                new WithDataResource(
                    Response::HTTP_OK,
                    'SUCCESS_GET_DATA',
                    'Berhasil Mengambil Data',
                    'Data pertumbuhan penduduk berhasil didapatkan.',
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

    private function getPopulationByReligion(int $year): array
    {
        $labels = Religion::select('id', 'label')->get();

        $raw = Resident::selectRaw('residents.religion_id, EXTRACT(MONTH FROM users.register_at) AS month, COUNT(*) as total')
            ->join('users', 'users.id', '=', 'residents.user_id')
            ->where('users.id', '!=', 1)
            ->whereYear('users.register_at', $year)
            ->groupByRaw('residents.religion_id, EXTRACT(MONTH FROM users.register_at)')
            ->get();

        return $this->formatPopulationByCategory($raw, $labels, 'religion_id');
    }

    private function getPopulationByEducation(int $year): array
    {
        $labels = Education::select('id', 'label')->get();

        $raw = Resident::selectRaw('residents.education_id, EXTRACT(MONTH FROM users.register_at) AS month, COUNT(*) as total')
            ->join('users', 'users.id', '=', 'residents.user_id')
            ->where('users.id', '!=', 1)
            ->whereYear('users.register_at', $year)
            ->groupByRaw('residents.education_id, EXTRACT(MONTH FROM users.register_at)')
            ->get();

        return $this->formatPopulationByCategory($raw, $labels, 'education_id');
    }

    private function getPopulationByMariedStatus(int $year): array
    {
        $labels = MariedStatus::select('id', 'label')->get();

        $raw = Resident::selectRaw('residents.maried_status_id, EXTRACT(MONTH FROM users.register_at) AS month, COUNT(*) as total')
            ->join('users', 'users.id', '=', 'residents.user_id')
            ->where('users.id', '!=', 1)
            ->whereYear('users.register_at', $year)
            ->groupByRaw('residents.maried_status_id, EXTRACT(MONTH FROM users.register_at)')
            ->get();

        return $this->formatPopulationByCategory($raw, $labels, 'maried_status_id');
    }

    private function getPopulationByCitizenship(int $year): array
    {
        $citizenships = Citizenship::select('id', 'label')->get();

        // Mapping id ke grup (WNI atau WNA)
        $groupMap = $citizenships->mapWithKeys(function ($item) {
            $group = $item->label === 'Indonesia' ? 'WNI' : 'WNA';
            return [$item->id => $group];
        });

        // Query data agregat per bulan
        $raw = Resident::selectRaw('residents.citizenship_id, EXTRACT(MONTH FROM users.register_at) AS month, COUNT(*) as total')
            ->join('users', 'users.id', '=', 'residents.user_id')
            ->where('users.id', '!=', 1)
            ->whereYear('users.register_at', $year)
            ->groupByRaw('residents.citizenship_id, EXTRACT(MONTH FROM users.register_at)')
            ->get();

        // Format per bulan
        $monthlyData = [];

        foreach ($raw as $row) {
            $month = (int) $row->month;
            $group = $groupMap[$row->citizenship_id] ?? 'Lainnya';
            $monthlyData[$month][$group] = ($monthlyData[$month][$group] ?? 0) + $row->total;
        }

        // Format final
        $result = [];
        foreach (range(1, 12) as $month) {
            $data = $monthlyData[$month] ?? [];

            $row = [
                'total_population' => array_sum($data),
                'WNI' => $data['WNI'] ?? 0,
                'WNA' => $data['WNA'] ?? 0,
            ];

            $result[] = $row;
        }

        return $result;
    }

    private function getPopulationByGender(int $year): array
    {
        // Query gender boolean + bulan + total
        $raw = Resident::selectRaw('residents.gender, EXTRACT(MONTH FROM users.register_at) AS month, COUNT(*) as total')
            ->join('users', 'users.id', '=', 'residents.user_id')
            ->where('users.id', '!=', 1)
            ->whereYear('users.register_at', $year)
            ->groupByRaw('residents.gender, EXTRACT(MONTH FROM users.register_at)')
            ->get();

        // Mapping gender label
        $monthlyData = [];

        foreach ($raw as $row) {
            $month = (int) $row->month;
            $label = $row->gender ? 'Laki-laki' : 'Perempuan';
            $monthlyData[$month][$label] = (int) $row->total;
        }

        // Final output
        $result = [];
        foreach (range(1, 12) as $month) {
            $data = $monthlyData[$month] ?? [];

            $result[] = [
                'total_population' => array_sum($data),
                'Laki-laki' => $data['Laki-laki'] ?? 0,
                'Perempuan' => $data['Perempuan'] ?? 0,
            ];
        }

        return $result;
    }

    private function formatPopulationByCategory($raw, $labels, string $foreignKey): array
    {
        $monthlyData = [];

        foreach ($raw as $row) {
            $month = (int) $row->month;
            $label = $labels->firstWhere('id', $row->{$foreignKey})?->label ?? 'Lainnya';
            $monthlyData[$month][$label] = (int) $row->total;
        }

        $result = [];
        foreach (range(1, 12) as $month) {
            $data = $monthlyData[$month] ?? [];

            $row = [
                'total_population' => array_sum($data),
            ];

            foreach ($labels as $labelObj) {
                $label = $labelObj->label;
                $row[$label] = $data[$label] ?? 0;
            }

            $result[] = $row;
        }

        return $result;
    }
}
