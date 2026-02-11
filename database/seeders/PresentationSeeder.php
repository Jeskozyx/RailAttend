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
        ini_set('memory_limit', '-1');
        set_time_limit(0);
        DB::disableQueryLog();

        $this->command->warn('MEMULAI GENERATE DATA (RECURRING FIXED)...');

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Verification::truncate();
        ScanReport::truncate();
        Schedule::truncate();
        Rangkaian::truncate();
        Train::truncate();
        User::where('email', 'like', '%@railattend.simulasi')->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $faker = Faker::create('id_ID');

        /*
        =================================================
        TAHAP 1: MASTER DATA (SAMA SEPERTI ASLI)
        =================================================
        */

        $roles = ['Kondektur', 'Teknisi Kereta Api', 'Polsuska'];
        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        }

        $this->command->info('1. Membuat 20 User...');
        $usersData = [];
        $password = Hash::make('password123');

        for ($i = 1; $i <= 5; $i++) {
            $role = $faker->randomElement($roles);
            $nipp = 10000 + $i;

            $user = User::create([
                'name' => $faker->name,
                'email' => "user.{$nipp}@railattend.simulasi",
                'nipp' => (string)$nipp,
                'password' => $password,
                'is_online' => rand(0, 1),
            ]);

            $user->assignRole($role);
            $usersData[] = $user;
        }

        /*
        =================================================
        TRAINS & RANGKAIAN (SAMA)
        =================================================
        */

        $trainNames = ['Cakrabuana', 'Ranggajati', 'Gunungjati', 'Cirebon Fakultatif'];
        $trainRecords = [];

        foreach ($trainNames as $name) {
            $train = Train::create(['name' => $name]);
            $trainRecords[] = $train;

            for ($k = 1; $k <= 6; $k++) {
                Rangkaian::create([
                    'train_id' => $train->id,
                    'name' => "K{$k}-" . rand(10, 99),
                    'type' => 'Ekonomi',
                    'urutan' => $k,
                    'qr_code' => Str::random(10),
                    'is_verified' => 0
                ]);
            }
        }

        /*
        =================================================
        FIXED SCHEDULE MASTER
        =================================================
        */

        $fixedSchedules = [];

        foreach ($trainRecords as $train) {
            for ($j = 1; $j <= 4; $j++) {

                $schedule = Schedule::create([
                    'train_id' => $train->id,
                    'user_id' => $usersData[0]->id,
                    'no_ka' => ($train->id * 10 + $j) . 'F',
                    'origin' => 'Cirebon',
                    'destination' => 'Jakarta',
                    'departure_time' => sprintf("%02d:00:00", 6 + ($j * 3)),
                    'arrival_time' => sprintf("%02d:00:00", 9 + ($j * 3)),
                    'date' => Carbon::today()->format('Y-m-d'),
                ]);

                $fixedSchedules[] = $schedule;
            }
        }

        /*
        =================================================
        TAHAP 2: RECURRING LOGIC (INI YANG DIPERBAIKI)
        =================================================
        */

        $this->command->info('4. SIMULASI DINAS RECURRING 7 HARI...');

        shuffle($usersData);
        $activeUsers = $usersData;

        foreach ($activeUsers as $user) {

            // ✅ Setiap user punya 2 favorite schedule tetap
            $favoriteSchedules = collect($fixedSchedules)->random(7);

            $baseDate = Carbon::today()->subDays(6);

            for ($day = 0; $day < 10; $day++) {

                $tglDinas = $baseDate->copy()->addDays($day);

                // Pilih salah satu dari 2 favorit
                $selectedSchedule = $favoriteSchedules->random();

                $jamBerangkat = Carbon::parse($selectedSchedule->departure_time);
                $waktuSimulasi = $tglDinas->copy()->setTime(
                    $jamBerangkat->hour,
                    $jamBerangkat->minute
                );

                $gerbongs = Rangkaian::where('train_id', $selectedSchedule->train_id)
                    ->orderBy('urutan')
                    ->get();

                $jumlahPutaran = rand(8, 10);

                for ($r = 1; $r <= $jumlahPutaran; $r++) {

                    $scanReport = ScanReport::create([
                        'user_id' => $user->id,
                        'schedule_id' => $selectedSchedule->id,
                        'train_id' => $selectedSchedule->train_id,
                        'status' => 'process',
                        'notes' => "Putaran ke-{$r}",
                        'created_at' => $waktuSimulasi,
                        'updated_at' => $waktuSimulasi,
                        'submitted_at' => null
                    ]);

                    $currentScanTime = $waktuSimulasi->copy();

                    foreach ($gerbongs as $gerbong) {
                        $currentScanTime->addSeconds(rand(15, 40));

                        Verification::create([
                            'schedule_id' => $selectedSchedule->id,
                            'scan_report_id' => $scanReport->id,
                            'rangkaian_id' => $gerbong->id,
                            'user_id' => $user->id,
                            'verified_at' => $currentScanTime,
                            'created_at' => $currentScanTime,
                            'updated_at' => $currentScanTime,
                        ]);
                    }

                    $scanReport->update([
                        'status' => 'completed',
                        'submitted_at' => $currentScanTime,
                        'updated_at' => $currentScanTime
                    ]);

                    $waktuSimulasi = $currentScanTime->copy()->addMinutes(rand(20, 60));
                }
            }
        }

        $this->command->info('SUKSES! RECURRING ACTIVE.');
    }
}
