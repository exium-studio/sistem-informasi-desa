<?php

namespace Database\Seeders\Gens;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CivilDoctypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $documents = [
            [
                'label' => 'Kartu Keluarga (KK)',
                'category' => 'dokumen_kependudukan',
                'description' => 'Dokumen resmi yang mencatat susunan, hubungan, dan jumlah anggota dalam satu keluarga.',
                'max_upload' => 2048000
            ],
            [
                'label' => 'Kartu Tanda Penduduk (KTP)',
                'category' => 'dokumen_kependudukan',
                'description' => 'Identitas resmi yang wajib dimiliki oleh setiap warga negara Indonesia yang telah memenuhi syarat usia.',
                'max_upload' => 1024000
            ],
            [
                'label' => 'Kartu Identitas Anak (KIA)',
                'category' => 'dokumen_kependudukan',
                'description' => 'Kartu identitas bagi anak-anak yang berusia di bawah 17 tahun.',
                'max_upload' => 1024000
            ],
            [
                'label' => 'Surat Keterangan Domisili',
                'category' => 'dokumen_kependudukan',
                'description' => 'Surat resmi yang menyatakan tempat tinggal seseorang di suatu wilayah.',
                'max_upload' => 1024000
            ],
            [
                'label' => 'Surat Keterangan Tempat Tinggal',
                'category' => 'dokumen_kependudukan',
                'description' => 'Dokumen bagi penduduk sementara yang menyatakan tempat tinggalnya.',
                'max_upload' => 1024000
            ],
            [
                'label' => 'Surat Pindah (SKPWNI)',
                'category' => 'dokumen_kependudukan',
                'description' => 'Dokumen untuk mengurus perpindahan tempat tinggal antar desa, kecamatan, atau provinsi.',
                'max_upload' => 2048000
            ],
            [
                'label' => 'Akta Kelahiran',
                'category' => 'dokumen_sipil',
                'description' => 'Dokumen yang mencatat kelahiran seseorang secara resmi.',
                'max_upload' => 2048000
            ],
            [
                'label' => 'Akta Kematian',
                'category' => 'dokumen_sipil',
                'description' => 'Dokumen resmi yang mencatat kematian seseorang.',
                'max_upload' => 2048000
            ],
            [
                'label' => 'Akta Perkawinan',
                'category' => 'dokumen_sipil',
                'description' => 'Dokumen yang mencatat secara resmi pernikahan pasangan.',
                'max_upload' => 2048000
            ],
            [
                'label' => 'Akta Perceraian',
                'category' => 'dokumen_sipil',
                'description' => 'Dokumen yang mencatat perceraian pasangan yang telah menikah.',
                'max_upload' => 2048000
            ],
            [
                'label' => 'Akta Pengakuan Anak',
                'category' => 'dokumen_sipil',
                'description' => 'Dokumen yang menyatakan pengakuan anak oleh orang tua.',
                'max_upload' => 2048000
            ],
            [
                'label' => 'Akta Pengesahan Anak',
                'category' => 'dokumen_sipil',
                'description' => 'Dokumen yang mengesahkan status hukum seorang anak.',
                'max_upload' => 2048000
            ],
            [
                'label' => 'Akta Perubahan Nama',
                'category' => 'dokumen_sipil',
                'description' => 'Dokumen resmi yang mencatat perubahan nama seseorang.',
                'max_upload' => 2048000
            ],
        ];

        foreach ($documents as $document) {
            DB::table('civil_doc_types')->insert([
                'label' => $document['label'],
                'category' => $document['category'],
                'description' => $document['description'],
                'max_upload' => $document['max_upload'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
