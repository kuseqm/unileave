<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        if (! auth()->user()->hasRole('admin')) {
            return redirect()->route('dashboard')->with('error', 'Access denied');
        }

        return view('admin.settings.index');
    }

    public function updateGeneral(Request $request)
    {
        if (! auth()->user()->hasRole('admin')) {
            return redirect()->route('dashboard')->with('error', 'Access denied');
        }

        return redirect()->back()->with('success', 'General settings updated');
    }

    public function updateNotifications(Request $request)
    {
        if (! auth()->user()->hasRole('admin')) {
            return redirect()->route('dashboard')->with('error', 'Access denied');
        }

        return redirect()->back()->with('success', 'Notification settings updated');
    }

    public function updateLeavePolicy(Request $request)
    {
        if (! auth()->user()->hasRole('admin')) {
            return redirect()->route('dashboard')->with('error', 'Access denied');
        }

        return redirect()->back()->with('success', 'Leave policy updated');
    }
}
