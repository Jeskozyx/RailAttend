<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class ProfileController extends Controller
{

    public function edit()
    {
        $user = auth()->user();

        // Calculate Weekly Rounds (Current Week)
        $weeklyRounds = \App\Models\ScanReport::where('user_id', $user->id)
            ->where('status', 'completed')
            ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->count();

        // Calculate Average Rounds (Last 4 Weeks)
        // Group by week and count, then average
        $averageStats = \App\Models\ScanReport::where('user_id', $user->id)
            ->where('status', 'completed')
            ->where('created_at', '>=', now()->subWeeks(4))
            ->selectRaw('YEARWEEK(created_at) as week, count(*) as total')
            ->groupBy('week')
            ->get();

        $averageRounds = $averageStats->count() > 0 
            ? round($averageStats->avg('total')) 
            : 0;

        return view('pages.user.profile', compact('user', 'weeklyRounds', 'averageRounds'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        if ($request->hasFile('avatar')) {
            if ($user->avatar && Storage::exists('public/' . $user->avatar)) {
                Storage::delete('public/' . $user->avatar);
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        $user->save();

        return redirect()->route('profile.edit')->with('success', 'Profil berhasil diperbarui.');
    }
}
