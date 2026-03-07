<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Train;
use App\Models\Schedule;
use App\Models\Rangkaian;
use App\Models\ScanReport;
use App\Models\Verification;
use Spatie\Permission\Models\Role;
use Faker\Factory as Faker;
use Carbon\Carbon;

class PresentationSeeder extends Seeder
{
    public function run(): void
    {
        // 1. SETUP PERFORMA
        ini_set('memory_limit', '-1');
        set_time_limit(0);
        DB::disableQueryLog();

        $this->command->warn('MEMULAI GENERATE DATA (MODE FIXED SCHEDULE)...');
        
        // Cleanup Data Lama
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Verification::truncate();
        ScanReport::truncate();
        Schedule::truncate();
        Rangkaian::truncate();
        Train::truncate();
        User::where('email', 'like', '%@railattend.simulasi')->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $faker = Faker::create('id_ID');

        // ==========================================
        // TAHAP 1: DATA MASTER (Roles, Users, Trains, Rangkaian)
        // ==========================================

        // A. ROLES
        $roles = ['Kondektur', 'Teknisi Kereta Api', 'Polsuska'];
        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        }

        // B. USERS (200 Orang)
        $this->command->info('1. Membuat 200 User (User.NIPP@railattend.simulasi, Password: password123)...');
        $usersData = [];
        $password = Hash::make('password123');

        for ($i = 1; $i <= 200; $i++) {
            $role = $faker->randomElement($roles);
            $nipp = 10000 + $i;
            $user = User::create([
                'name' => $faker->name,
                'email' => "user.{$nipp}@railattend.simulasi",
                'nipp' => (string)$nipp,
                'password' => $password,
                'is_online' => rand(0, 1),
                'avatar' => null 
            ]);
            $user->assignRole($role);
            $usersData[] = $user;
        }

        // C. TRAINS & RANGKAIAN (4 Kereta)
        $this->command->info('2. Membuat 4 Kereta (Ranggajati, Cirebon Fakultatif, Cakrabuana, Gunungjati) & Rangkaian...');
        $trainNames = ['Ranggajati', 'Cirebon Fakultatif', 'Cakrabuana', 'Gunungjati'];
        $trainRecords = []; // Simpan objek kereta

        foreach ($trainNames as $name) {
            $train = Train::create(['name' => $name]);
            $trainRecords[] = $train;

            // Buat 10 Gerbong per Kereta
            // Wajib ada Pembangkit & Makan
            $configs = [
                ['t' => 'Kereta Pembangkit', 'c' => 'P'],
                ['t' => 'Eksekutif', 'c' => 'K1'],
                ['t' => 'Eksekutif', 'c' => 'K1'],
                ['t' => 'Eksekutif', 'c' => 'K1'],
                ['t' => 'Kereta Makan', 'c' => 'M1'],
                ['t' => 'Bisnis', 'c' => 'K2'],
                ['t' => 'Bisnis', 'c' => 'K2'],
                ['t' => 'Ekonomi', 'c' => 'K3'],
                ['t' => 'Ekonomi', 'c' => 'K3'],
                ['t' => 'Ekonomi', 'c' => 'K3'],
            ];

            foreach ($configs as $k => $conf) {
                Rangkaian::create([
                    'train_id' => $train->id,
                    'name' => $conf['c'] . '-0' . ($k + 1) . rand(10, 99),
                    'type' => $conf['t'],
                    'urutan' => $k + 1,
                    'qr_code' => Str::random(10), // Tersedia QR Code
                    'is_verified' => 0
                ]);
            }
        }

        // D. JADWAL FIXED (16 JADWAL TOTAL)
        $this->command->info('3. Membuat 16 Jadwal Master (4 per Kereta, Bolak-Balik, Jeda 5 Jam)...');
        $fixedSchedules = [];

        foreach ($trainRecords as $train) {
            // 4 jadwal per kereta
            for ($j = 1; $j <= 4; $j++) {
                $isPergi = $j % 2 != 0; 
                // Jam dipisahkan 5 jam antar jadwal (6, 11, 16, 21) -> logic adjusted slightly
                $jamBase = 6 + (($j - 1) * 5); // 06:00, 11:00, 16:00, 21:00
                
                $schedule = Schedule::create([
                    'train_id' => $train->id,
                    'user_id' => $usersData[0]->id,
                    'no_ka' => ($train->id * 10 + $j) . ($isPergi ? 'F' : 'A'), // Nomor KA Berbeda
                    'origin' => $isPergi ? 'Cirebon' : 'Jakarta',
                    'destination' => $isPergi ? 'Jakarta' : 'Cirebon', // Tujuan bolak balik
                    'departure_time' => sprintf("%02d:00:00", $jamBase),
                    'arrival_time' => sprintf("%02d:00:00", $jamBase + 3),
                    // Set Date to Today so Filters catch it easily
                    'date' => Carbon::today()->format('Y-m-d'), 
                ]);
                
                $fixedSchedules[] = $schedule;
            }
        }

        // ==========================================
        // TAHAP 2: SIMULASI DINAS USER
        // ==========================================
        $this->command->info('4. MEMULAI SIMULASI DINAS (USER MENAUNGI BANYAK JADWAL)...');
        
        // Ambil 100 User Acak untuk simulasi
        shuffle($usersData);
        $activeUsers = array_slice($usersData, 0, 100);

        $progressBar = $this->command->getOutput()->createProgressBar(count($activeUsers));
        $progressBar->start();

        foreach ($activeUsers as $user) {
            
            // Wajib scan minimal 8-10 kali putaran dalam 1 sesi
            // Dan user memiliki 9-16 sesi (jadwal yang berbeda)
            $jumlahSesi = rand(9, 16);
            
            // Sessions mostly within LAST 14 DAYS
            $baseDate = Carbon::today()->subDays(14); 

            for ($s = 1; $s <= $jumlahSesi; $s++) {
                
                // 1. Pilih 1 Jadwal dari 16 Jadwal Fixed
                $selectedSchedule = $fixedSchedules[array_rand($fixedSchedules)];
                
                // 2. Tentukan Tanggal Dinas
                $daysToAdd = ($s / $jumlahSesi) * 14; 
                $tglDinas = $baseDate->copy()->addDays($daysToAdd);
                
                // Set Jam Dinas sesuai Jadwal Master
                $jamBerangkat = Carbon::parse($selectedSchedule->departure_time);
                $waktuSimulasi = $tglDinas->copy()->setTime($jamBerangkat->hour, $jamBerangkat->minute);
                
                // Ambil Rangkaian
                $gerbongs = Rangkaian::where('train_id', $selectedSchedule->train_id)
                            ->orderBy('urutan')
                            ->get();

                // 3. Loop Putaran (8 - 10 kali putaran)
                $jumlahPutaran = rand(8, 10);

                for ($r = 1; $r <= $jumlahPutaran; $r++) {
                    
                    $startTime = $waktuSimulasi->copy();
                    
                    // Buat Scan Report
                    $scanReport = ScanReport::create([
                        'user_id' => $user->id,
                        'schedule_id' => $selectedSchedule->id, 
                        'train_id' => $selectedSchedule->train_id,
                        'status' => 'process',
                        'created_at' => $startTime,
                        'updated_at' => $startTime,
                        'submitted_at' => null
                    ]);

                    // 4. Scan Gerbong (jarak berkisar 2-5 menit)
                    $currentScanTime = $startTime->copy();

                    foreach ($gerbongs as $gerbong) {
                        $interval = rand(120, 300); // 2-5 Menit per gerbong
                        $currentScanTime->addSeconds($interval);

                        Verification::create([
                            'schedule_id' => $selectedSchedule->id,
                            'scan_report_id' => $scanReport->id,
                            'rangkaian_id' => $gerbong->id,
                            'user_id' => $user->id,
                            'verified_at' => $currentScanTime->toDateTimeString(),
                            'created_at' => $currentScanTime->toDateTimeString(),
                            'updated_at' => $currentScanTime->toDateTimeString(),
                        ]);
                    }

                    // Selesai 1 Putaran
                    $scanReport->update([
                        'status' => 'completed',
                        'submitted_at' => $currentScanTime, 
                        'updated_at' => $currentScanTime
                    ]);

                    // Jarak per putaran antara scan report dan scan pertama yaitu 20menit-2jam
                    // (Scanner berikutnya mulai 20m-2h setelah scan terakhir ini)
                    $waktuSimulasi = $currentScanTime->copy()->addMinutes(rand(20, 120));
                }
            }
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->command->newLine();
        $this->command->info('SUKSES! Data generated sesuai permintaan detail user.');
    }
}
