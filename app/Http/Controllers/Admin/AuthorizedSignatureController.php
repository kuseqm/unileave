<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuthorizedSignature;
use App\Traits\PerPageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AuthorizedSignatureController extends Controller
{
    use PerPageTrait;

    /**
     * Display list of authorized signatures
     */
    public function index(Request $request)
    {
        $perPage = $this->getPerPage($request, 10);

        $query = AuthorizedSignature::orderBy('name');

        if ($search = $request->get('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        $signatures = $query->paginate($perPage)->appends($request->query());

        $totalSignatures = AuthorizedSignature::count();
        $newThisMonth = AuthorizedSignature::whereMonth('created_at', now()->month)->count();
        $inUse = AuthorizedSignature::has('idCards')->count();
        $unused = $totalSignatures - $inUse;

        if ($request->wantsJson()) {
            return response()->json([
                'html' => view('admin.authorized-signatures._results', compact('signatures'))->render(),
                'total' => $signatures->total(),
                'grand_total' => $totalSignatures,
            ]);
        }

        return view('admin.authorized-signatures.index', compact(
            'signatures', 'totalSignatures', 'newThisMonth', 'inUse', 'unused'
        ));
    }

    /**
     * Show form to create new authorized signature
     */
    public function create()
    {
        return view('admin.authorized-signatures.create');
    }

    /**
     * Store new authorized signature
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'signature' => 'required|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        $path = $request->file('signature')->store('signatures', 'public');

        AuthorizedSignature::create([
            'name' => $request->name,
            'signature_path' => $path,
        ]);

        return redirect()->route('admin.authorized-signatures.index')
            ->with('success', 'Authorized signature added successfully.');
    }

    /**
     * Show form to edit authorized signature
     */
    public function edit($id)
    {
        $signature = AuthorizedSignature::findOrFail($id);

        return view('admin.authorized-signatures.edit', compact('signature'));
    }

    /**
     * Update authorized signature
     */
    public function update(Request $request, $id)
    {
        $signature = AuthorizedSignature::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'signature' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        $data = ['name' => $request->name];

        if ($request->hasFile('signature')) {
            if ($signature->signature_path && Storage::disk('public')->exists($signature->signature_path)) {
                Storage::disk('public')->delete($signature->signature_path);
            }

            $data['signature_path'] = $request->file('signature')->store('signatures', 'public');
        }

        $signature->update($data);

        return redirect()->route('admin.authorized-signatures.index')
            ->with('success', 'Authorized signature updated successfully.');
    }

    /**
     * Delete authorized signature
     */
    public function destroy($id)
    {
        $signature = AuthorizedSignature::findOrFail($id);

        if ($signature->idCards()->count() > 0) {
            return redirect()->route('admin.authorized-signatures.index')
                ->with('error', 'Cannot delete a signature that is used by existing ID cards.');
        }

        if ($signature->signature_path && Storage::disk('public')->exists($signature->signature_path)) {
            Storage::disk('public')->delete($signature->signature_path);
        }

        $signature->delete();

        return redirect()->route('admin.authorized-signatures.index')
            ->with('success', 'Authorized signature deleted successfully.');
    }
}
