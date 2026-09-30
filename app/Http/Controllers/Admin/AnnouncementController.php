<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Traits\PerPageTrait;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    use PerPageTrait;

    private const CATEGORIES = ['general', 'leave', 'event', 'academic'];

    private const STATUSES = ['live', 'scheduled', 'expired', 'inactive'];

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $category = $request->query('category');
        $status = $request->query('status');
        $today = now()->toDateString();

        $query = Announcement::query();

        if ($category && in_array($category, self::CATEGORIES, true)) {
            $query->where('category', $category);
        }

        if ($status && in_array($status, self::STATUSES, true)) {
            match ($status) {
                'inactive' => $query->where('is_active', false),
                'scheduled' => $query->where('is_active', true)->whereDate('starts_at', '>', $today),
                'expired' => $query->where('is_active', true)->whereDate('expires_at', '<', $today),
                'live' => $query->where('is_active', true)
                    ->where(fn ($q) => $q->whereNull('starts_at')->orWhereDate('starts_at', '<=', $today))
                    ->where(fn ($q) => $q->whereNull('expires_at')->orWhereDate('expires_at', '>=', $today)),
            };
        }

        $announcements = $query->orderByDesc('is_pinned')
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->paginate($this->getPerPage($request, 20))
            ->withQueryString();

        $categories = self::CATEGORIES;
        $statuses = self::STATUSES;

        return view('admin.announcements.index', compact('announcements', 'categories', 'statuses', 'category', 'status'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = self::CATEGORIES;

        return view('admin.announcements.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string|max:2000',
            'category' => 'required|in:'.implode(',', self::CATEGORIES),
            'is_pinned' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at',
        ]);

        Announcement::create([
            ...$validated,
            'is_pinned' => $request->boolean('is_pinned'),
            'is_active' => $request->boolean('is_active', true),
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Announcement created and now visible on the landing page.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $announcement = Announcement::findOrFail($id);
        $categories = self::CATEGORIES;

        return view('admin.announcements.edit', compact('announcement', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $announcement = Announcement::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string|max:2000',
            'category' => 'required|in:'.implode(',', self::CATEGORIES),
            'is_pinned' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at',
        ]);

        $announcement->update([
            ...$validated,
            'is_pinned' => $request->boolean('is_pinned'),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Announcement updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Announcement::findOrFail($id)->delete();

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Announcement deleted.');
    }

    public function moveUp(string $id)
    {
        return $this->swap($id, -1);
    }

    public function moveDown(string $id)
    {
        return $this->swap($id, 1);
    }

    /**
     * Swap display order with the neighbor above/below, within the same
     * pinned/unpinned group. Normalizes the whole group to sequential
     * sort_order values first, since most rows still share the default 0
     * until someone actually reorders them.
     */
    private function swap(string $id, int $direction)
    {
        $announcement = Announcement::findOrFail($id);

        $group = Announcement::where('is_pinned', $announcement->is_pinned)
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get()
            ->values();

        foreach ($group as $position => $item) {
            if ($item->sort_order !== $position) {
                $item->update(['sort_order' => $position]);
            }
        }

        $index = $group->search(fn ($item) => $item->id === $announcement->id);
        $swapWithIndex = $index + $direction;

        if (! isset($group[$swapWithIndex])) {
            return redirect()->route('admin.announcements.index');
        }

        $current = $group[$index];
        $swapWith = $group[$swapWithIndex];

        $currentOrder = $current->sort_order;
        $current->update(['sort_order' => $swapWith->sort_order]);
        $swapWith->update(['sort_order' => $currentOrder]);

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Order updated.');
    }
}
