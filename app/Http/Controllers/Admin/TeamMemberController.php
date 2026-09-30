<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use App\Traits\PerPageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TeamMemberController extends Controller
{
    use PerPageTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $members = TeamMember::orderBy('sort_order')->orderBy('id')->paginate($this->getPerPage($request, 20))->appends($request->query());

        return view('admin.team-members.index', compact('members'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.team-members.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'bio' => 'nullable|string|max:1000',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'is_active' => 'nullable|boolean',
        ]);

        $photoPath = $request->hasFile('photo')
            ? $request->file('photo')->store('team', 'public')
            : null;

        TeamMember::create([
            'name' => $validated['name'],
            'role' => $validated['role'],
            'bio' => $validated['bio'] ?? null,
            'photo_path' => $photoPath,
            'sort_order' => (int) TeamMember::max('sort_order') + 1,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.team-members.index')
            ->with('success', 'Team member added and now visible on the landing page.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $member = TeamMember::findOrFail($id);

        return view('admin.team-members.edit', compact('member'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $member = TeamMember::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'bio' => 'nullable|string|max:1000',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'is_active' => 'nullable|boolean',
        ]);

        $photoPath = $member->photo_path;

        if ($request->hasFile('photo')) {
            if ($photoPath && Storage::disk('public')->exists($photoPath)) {
                Storage::disk('public')->delete($photoPath);
            }
            $photoPath = $request->file('photo')->store('team', 'public');
        }

        $member->update([
            'name' => $validated['name'],
            'role' => $validated['role'],
            'bio' => $validated['bio'] ?? null,
            'photo_path' => $photoPath,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.team-members.index')
            ->with('success', 'Team member updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $member = TeamMember::findOrFail($id);

        if ($member->photo_path && Storage::disk('public')->exists($member->photo_path)) {
            Storage::disk('public')->delete($member->photo_path);
        }

        $member->delete();

        return redirect()->route('admin.team-members.index')
            ->with('success', 'Team member removed.');
    }

    public function moveUp(string $id)
    {
        return $this->swap($id, -1);
    }

    public function moveDown(string $id)
    {
        return $this->swap($id, 1);
    }

    private function swap(string $id, int $direction)
    {
        $member = TeamMember::findOrFail($id);

        $all = TeamMember::orderBy('sort_order')->orderBy('id')->get()->values();

        foreach ($all as $position => $item) {
            if ($item->sort_order !== $position) {
                $item->update(['sort_order' => $position]);
            }
        }

        $index = $all->search(fn ($item) => $item->id === $member->id);
        $swapWithIndex = $index + $direction;

        if (! isset($all[$swapWithIndex])) {
            return redirect()->route('admin.team-members.index');
        }

        $current = $all[$index];
        $swapWith = $all[$swapWithIndex];

        $currentOrder = $current->sort_order;
        $current->update(['sort_order' => $swapWith->sort_order]);
        $swapWith->update(['sort_order' => $currentOrder]);

        return redirect()->route('admin.team-members.index')
            ->with('success', 'Order updated.');
    }
}
