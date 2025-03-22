<?php

namespace App\Http\Controllers\Web\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\Templates\Response\WithDataResource;
use App\Http\Resources\Templates\Response\WithoutDataResource;
use App\Models\FamilyCard;
use App\Models\PopulationGrowth;
use App\Models\Village;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class PopulasiController extends Controller
{
    public function populasi()
    {
        try {
            if (!Gate::allows('dashboard.view')) {
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

            $populasi = PopulationGrowth::getUsersWithActiveStatus();
            $populasi_tahun_lalu = PopulationGrowth::getUsersRegisteredBeforeThisYear();
            $family_card = FamilyCard::getFamiliCard();
            $village_funds = Village::getDanaDesa();

            return response()->json(
                new WithDataResource(
                    Response::HTTP_OK,
                    'SUCCESS_GET_DATA',
                    'Berhasil Mengambil Data',
                    'Data populasi berhasil didapatkan.',
                    [
                        'populasi_sekarang' => $populasi->count(),
                        'populasi_tahun_lalu' => $populasi_tahun_lalu->count(),
                        'kk_total' => $family_card->count(),
                        'dana_desa' => $village_funds
                    ],
                )
            );
        } catch (\Exception $e) {
            Log::error('| Dashboard Populasi | - Error : ' . $e->getMessage());
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
