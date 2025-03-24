<?php

namespace App\Helpers;

class CalculationHelper
{
	// Hitung persentase pertumbuhan
	public static function calculateGrowthPercentage(int $current, int $previous): float
	{
		if ($previous === 0) {
			return 0;
		}

		return round((($current - $previous) / $previous) * 100, 1);
	}

	// Hitung densitas penduduk
	public static function calculatePopulationDensity(int $population, ?int $areaInMeters): string
	{
		if (!$areaInMeters || $areaInMeters === 0) {
			return 'Tidak Valid';
		}

		$areaInKm2 = $areaInMeters / 1_000_000;
		$density = $population / $areaInKm2;

		return match (true) {
			$density < 1000 => 'Jarang',
			$density <= 4000 => 'Sedang',
			$density > 4000 => 'Padat',
			default => 'Tidak Valid',
		};
	}
}
