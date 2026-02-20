<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PresentationController extends Controller
{
    public function index()
    {
        return view('presentasi');
    }

    public function generate(Request $request)
    {
        // Increase time limit for robust seeding
        set_time_limit(300);

        try {
            // Optional: Truncate tables for a clean slate
            // We disable foreign key checks to allow truncation
            Schema::disableForeignKeyConstraints();
            DB::table('users')->truncate();
            DB::table('trains')->truncate();
            DB::table('rangkaians')->truncate();
            DB::table('schedules')->truncate();
            DB::table('scan_reports')->truncate();
            DB::table('verifications')->truncate();
            DB::table('model_has_roles')->truncate();
            // We don't truncate 'roles' and 'permissions' usually, but seeder handles creation.
            Schema::enableForeignKeyConstraints();

            // Run the Seeder
            Artisan::call('db:seed', ['--class' => 'PresentationSeeder']);

            return redirect()->back()->with('success', 'Magic! Database has been populated with presentation data.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Magic Failed: ' . $e->getMessage());
        }
    }
    public function destroy()
    {
        try {
            Schema::disableForeignKeyConstraints();
            DB::table('users')->truncate();
            DB::table('trains')->truncate();
            DB::table('rangkaians')->truncate();
            DB::table('schedules')->truncate();
            DB::table('scan_reports')->truncate();
            DB::table('verifications')->truncate();
            DB::table('model_has_roles')->truncate();
            Schema::enableForeignKeyConstraints();

            // Re-seed Base Data (Permissions, Roles, Base Users)
            Artisan::call('db:seed', ['--class' => 'DatabaseSeeder']); 
            
            return redirect()->back()->with('success', 'Database RESET to initial state (Base data restored).');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Reset Failed: ' . $e->getMessage());
        }
    }
}
