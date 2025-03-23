<?php

namespace App\Helpers;

class CalculationHelper 
{
	/**
	 * Hitung pertumbuhan warga dan kk dalam persen dari dua angka
	 *
	 * @param int $current  Jumlah tahun sekarang
	 * @param int $previous Jumlah tahun sebelumnya
	 * @return float
	 */
	public static function calculateGrowthPercentage(int $current, int $previous): float
	{
		if ($previous === 0) {
			return 0;
		}

		return round((($current - $previous) / $previous) * 100, 1);
	}
}
