<?php

namespace App\Http\Controllers\Traits;

use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

trait DashboardTrait
{
    /**
     * Generate role color - static for predefined roles, dynamic for new ones
     */
    protected function generateRoleColor(string $roleName): string
    {
        $staticColors = [
            'Polsuska' => '#F75026',
            'Kondektur' => '#00078A',
            'Teknisi Kereta Api' => '#1CC8CE',
        ];

        if (isset($staticColors[$roleName])) {
            return $staticColors[$roleName];
        }

        $hash = md5($roleName);
        $h = hexdec(substr($hash, 0, 2)) % 360;
        $s = 65 + (hexdec(substr($hash, 2, 2)) % 20);
        $l = 40 + (hexdec(substr($hash, 4, 2)) % 15);
        return "hsl($h, {$s}%, {$l}%)";
    }

    /**
     * Get all non-Admin roles with their colors
     */
    protected function getDynamicRoles()
    {
        $roles = Role::where('name', '!=', 'Admin')->orderBy('name')->get();
        $rolesData = [];
        
        foreach ($roles as $role) {
            $rolesData[] = [
                'name' => $role->name,
                'color' => $this->generateRoleColor($role->name)
            ];
        }
        
        return $rolesData;
    }

    /**
     * Build dynamic chart data by role (used by multiple methods)
     */
    protected function buildDynamicRoleChartData($data, $dates, $roles)
    {
        $result = [
            'roles' => [],
            'datasets' => []
        ];

        foreach ($roles as $roleData) {
            $roleName = $roleData['name'];
            $roleKey = Str::slug($roleName, '_');
            
            $result['roles'][] = [
                'name' => $roleName,
                'key' => $roleKey,
                'color' => $roleData['color']
            ];
            
            $result['datasets'][$roleKey] = [
                'data' => array_fill(0, count($dates), 0),
                'details' => array_fill(0, count($dates), [])
            ];
        }

        foreach ($data as $row) {
            $dateIndex = array_search($row->date, $dates);
            if ($dateIndex === false) continue;

            foreach ($result['roles'] as $roleInfo) {
                if ($row->role_name === $roleInfo['name']) {
                    $roleKey = $roleInfo['key'];
                    $detailStr = $row->train_name . '(' . $row->no_ka . '): ' . $row->total;
                    $result['datasets'][$roleKey]['data'][$dateIndex] += $row->total;
                    $result['datasets'][$roleKey]['details'][$dateIndex][] = $detailStr;
                    break;
                }
            }
        }

        return $result;
    }
}
