<?php

namespace Database\Seeders\Gens;

use App\Models\OfficialContact;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OfficialContactSeeder extends Seeder
{
    public function run(): void
    {
        $roles = ['Lurah', 'Kepala RW', 'Kepala RT'];
        $contactTypes = ['whatsapp', 'telephone', 'instagram', 'facebook', 'x', 'email', 'website'];

        foreach ($roles as $roleName) {
            $user = User::role($roleName)->first();
            if ($user) {
                // Menentukan jumlah kontak untuk user ini, misalnya 1 sampai 3 kontak
                $contactCount = rand(2, 7);

                // Shuffle untuk mengacak jenis kontak yang akan dipilih
                $shuffledContactTypes = $contactTypes;
                shuffle($shuffledContactTypes);

                // Pilih sejumlah kontak yang unik untuk user ini berdasarkan shuffled array
                $selectedContactTypes = array_slice($shuffledContactTypes, 0, $contactCount);

                // Loop untuk membuat kontak
                foreach ($selectedContactTypes as $type) {
                    // Buat nilai kontak yang sesuai dengan type (contoh bisa random untuk contoh)
                    $value = $this->generateContactValue($type);

                    // Create Official Contact
                    OfficialContact::create([
                        'contact_person' => $user->id,  // Menggunakan user_id
                        'type' => $type,                // Menggunakan enum yang sudah didefinisikan
                        'value' => $value,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    private function generateContactValue($type)
    {
        switch ($type) {
            case 'whatsapp':
                return '0812' . rand(1000000, 9999999);  // Contoh format whatsapp
            case 'telephone':
                return '021' . rand(10000000, 99999999);  // Contoh format telepon
            case 'instagram':
                return 'https://instagram.com/user_' . rand(1000, 9999);
            case 'facebook':
                return 'https://facebook.com/user_' . rand(1000, 9999);
            case 'x': // Misalnya Twitter atau platform lain
                return 'https://x.com/user_' . rand(1000, 9999);
            case 'email':
                return 'user_' . rand(1, 999) . '@example.com';
            case 'website':
                return 'https://userwebsite_' . rand(1000, 9999) . '.com';
            default:
                return '';
        }
    }
}
