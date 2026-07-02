<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\IncomingLetter;
use App\Models\LeaveRequest;

class PortalController extends Controller
{
    /**
     * Show the portal dashboard
     */
    public function index()
    {
        $user = auth()->user();
        
        // Get available systems based on user role
        $systems = $this->getAvailableSystems($user);
        
        return view('portal.dashboard', compact('user', 'systems'));
    }
    
    /**
     * Determine which systems this user can access based on role
     * 
     * TO ADD A NEW SYSTEM:
     * 1. Add a new entry to the $systems array below
     * 2. Specify the conditions under which it should appear (role check)
     * 3. Define: name, description, url, icon, color, coming_soon
     */
    private function getAvailableSystems($user)
    {
        $systems = [];
        
        // ============================================
        // 1. LEAVE MANAGEMENT SYSTEM
        // ============================================
        // Everyone has access to Leave System
        $systems[] = [
            'name' => 'UniLeave',
            'description' => 'Request leave, check balances, view history',
            'url' => route('dashboard'),
            'icon' => 'calendar',
            'color' => 'primary',
            'coming_soon' => false,
        ];
        
        // ============================================
        // 2. CENTRAL REGISTRY SYSTEM
        // ============================================
        // Only users with specific roles can access
        if ($user && ($user->hasRole('admin') || 
                      $user->hasRole('registry-officer') || 
                      $user->hasRole('registrar') || 
                      $user->hasRole('hod'))) {
            $systems[] = [
                'name' => 'Central Registry',
                'description' => 'Manage incoming/outgoing letters and correspondence',
                'url' => route('registry.incoming.index'),
                'icon' => 'mail',
                'color' => 'info',
                'coming_soon' => false,
            ];
        }
        
        // ============================================
        // 3. EXAMINATIONS SYSTEM (Future)
        // ============================================
        if ($user && ($user->hasRole('admin') || 
                      $user->hasRole('examinations') || 
                      $user->hasRole('academic-staff'))) {
            $systems[] = [
                'name' => 'Examinations',
                'description' => 'Manage exams, results, and regulations',
                'url' => '#',
                'icon' => 'clipboard',
                'color' => 'warning',
                'coming_soon' => true,
            ];
        }
        
        // ============================================
        // 4. SENATE MEETINGS SYSTEM (Future)
        // ============================================
        if ($user && ($user->hasRole('admin') || 
                      $user->hasRole('senate-member') || 
                      $user->hasRole('registrar'))) {
            $systems[] = [
                'name' => 'Senate Meetings',
                'description' => 'Agendas, minutes, and resolutions',
                'url' => '#',
                'icon' => 'users',
                'color' => 'success',
                'coming_soon' => true,
            ];
        }
        
        // ============================================
        // 5. ADMINISTRATION PANEL
        // ============================================
        if ($user && $user->hasRole('admin')) {
            $systems[] = [
                'name' => 'Administration',
                'description' => 'User management, system settings, audit logs',
                'url' => '/admin',
                'icon' => 'settings',
                'color' => 'danger',
                'coming_soon' => false,
            ];
        }
        
        // ============================================
        // 6. ADD YOUR NEW SYSTEM HERE
        // ============================================
        // Copy this template and customize:
        /*
        if ($user && ($user->hasRole('role-name'))) {
            $systems[] = [
                'name' => 'System Name',
                'description' => 'System description here',
                'url' => route('system.route.name'),
                'icon' => 'icon-name',  // calendar, mail, clipboard, users, settings, apps
                'color' => 'color-name', // primary, info, warning, success, danger, secondary
                'coming_soon' => false,  // true if not yet built
            ];
        }
        */
        
        return $systems;
    }
}