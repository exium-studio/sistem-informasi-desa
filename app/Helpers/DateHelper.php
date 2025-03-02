<?php

namespace App\Helpers;

use Carbon\Carbon;

class DateHelper
{
    /**
     * Konversi format tanggal ke format Indonesia.
     *
     * @param string $dateTime
     * @param int $formatType
     * @return string
     */
    public static function formatTanggalIndonesia($dateTime, $formatType = 1)
    {
        if (!$dateTime) {
            return '-';
        }

        $carbonDate = Carbon::parse($dateTime)->locale('id');

        // Mapping nama hari dan bulan dalam bahasa Indonesia
        $hari = $carbonDate->translatedFormat('l');
        $tanggal = $carbonDate->translatedFormat('j');
        $bulan = $carbonDate->translatedFormat('F');
        $tahun = $carbonDate->translatedFormat('Y');
        $jamMenit = $carbonDate->translatedFormat('H:i');

        switch ($formatType) {
            case 1: // Senin, 1 Januari 2025
                return "$hari, $tanggal $bulan $tahun";
            
            case 2: // Senin, 1 Januari 2025 pukul 15:30 WIB
                return "$hari, $tanggal $bulan $tahun pukul $jamMenit WIB";

            case 3: // 1 Januari 2025
                return "$tanggal $bulan $tahun";

            case 4: // 01-01-2025
                return $carbonDate->translatedFormat('d-m-Y');

			case 5: // 01/01/2025
				return $carbonDate->translatedFormat('d/m/Y');

            default:
                return "$tanggal $bulan $tahun";
        }
    }
}
