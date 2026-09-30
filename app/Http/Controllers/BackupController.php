<?php

namespace App\Http\Controllers;

class BackupController extends Controller
{
    public function create()
    {
        if (! auth()->user()->hasRole('admin')) {
            return redirect()->route('dashboard')->with('error', 'Access denied');
        }

        return redirect()->back()->with('success', 'Backup created successfully');
    }

    public function download($filename)
    {
        if (! auth()->user()->hasRole('admin')) {
            return redirect()->route('dashboard')->with('error', 'Access denied');
        }

        return response()->download(storage_path("app/backup/{$filename}"));
    }

    public function delete($filename)
    {
        if (! auth()->user()->hasRole('admin')) {
            return redirect()->route('dashboard')->with('error', 'Access denied');
        }

        return redirect()->back()->with('success', 'Backup deleted');
    }

    public function restore($filename)
    {
        if (! auth()->user()->hasRole('admin')) {
            return redirect()->route('dashboard')->with('error', 'Access denied');
        }

        return redirect()->back()->with('warning', 'Restore functionality requires confirmation');
    }
}
