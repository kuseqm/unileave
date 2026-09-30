<?php

namespace App\Http\Controllers\Registry;

use App\Http\Controllers\Controller;
use App\Models\LetterFile;
use App\Models\ReferenceFormat;
use App\Models\University;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LetterFileController extends Controller
{
    /**
     * Display a listing of letter files
     */
    public function index(Request $request)
    {
        $query = LetterFile::with(['referenceFormat', 'creator']);

        // Filter by reference format
        if ($request->filled('format_id')) {
            $query->where('reference_format_id', $request->format_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'closed') {
                $query->closed();
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('file_number', 'like', "%{$search}%")
                    ->orWhere('file_name', 'like', "%{$search}%");
            });
        }

        $grandTotal = LetterFile::count();

        $files = $query->orderBy('created_at', 'desc')->paginate($request->get('per_page', 15))->appends($request->query());
        $formats = ReferenceFormat::active()->get();

        if ($request->wantsJson()) {
            return response()->json([
                'html' => view('registry.files._results', compact('files'))->render(),
                'total' => $files->total(),
                'grand_total' => $grandTotal,
            ]);
        }

        return view('registry.files.index', compact('files', 'formats'));
    }

    /**
     * Show the form for creating a new letter file
     */
    public function create(Request $request)
    {
        $formats = ReferenceFormat::active()->get();
        $selectedFormat = $request->get('format_id');

        // If format is selected, get next available file number
        $nextFileNumber = null;
        if ($selectedFormat) {
            $format = ReferenceFormat::find($selectedFormat);
            if ($format) {
                $nextFileNumber = $this->calculateNextFileNumber($format);
            }
        }

        return view('registry.files.create', compact('formats', 'selectedFormat', 'nextFileNumber'));
    }

    /**
     * Store a newly created letter file
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'reference_format_id' => 'required|exists:reference_formats,id',
            'file_number' => 'required|string|max:100|unique:letter_files,file_number',
            'file_name' => 'required|string|max:255',
            'start_serial' => 'required|integer|min:1',
            'end_serial' => 'nullable|integer|min:0',
            'physical_location' => 'nullable|string|max:500',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['current_serial'] = $validated['start_serial'] - 1;
        $validated['is_active'] = $request->has('is_active');
        $validated['opened_at'] = now();
        $validated['created_by'] = Auth::id();

        LetterFile::create($validated);

        return redirect()->route('registry.files.index')
            ->with('success', 'Letter file created successfully!');
    }

    /**
     * Display the specified letter file
     */
    public function show($id)
    {
        $file = LetterFile::with(['referenceFormat', 'creator', 'referenceFormat.university'])
            ->findOrFail($id);

        // Get recent letters in this file
        $recentLetters = $file->letters()->latest()->limit(10)->get();

        return view('registry.files.show', compact('file', 'recentLetters'));
    }

    /**
     * Show the form for editing the specified letter file
     */
    public function edit($id)
    {
        $file = LetterFile::findOrFail($id);
        $formats = ReferenceFormat::active()->get();

        return view('registry.files.edit', compact('file', 'formats'));
    }

    /**
     * Update the specified letter file
     */
    public function update(Request $request, $id)
    {
        $file = LetterFile::findOrFail($id);

        $validated = $request->validate([
            'reference_format_id' => 'required|exists:reference_formats,id',
            'file_number' => 'required|string|max:100|unique:letter_files,file_number,'.$id,
            'file_name' => 'required|string|max:255',
            'start_serial' => 'required|integer|min:1',
            'end_serial' => 'nullable|integer|min:0',
            'physical_location' => 'nullable|string|max:500',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $file->update($validated);

        return redirect()->route('registry.files.show', $file->id)
            ->with('success', 'Letter file updated successfully!');
    }

    /**
     * Remove the specified letter file
     */
    public function destroy($id)
    {
        $file = LetterFile::findOrFail($id);

        // Check if file has letters
        if ($file->letters()->count() > 0) {
            return back()->with('error', 'Cannot delete file that contains letters. Close it instead.');
        }

        $file->delete();

        return redirect()->route('registry.files.index')
            ->with('success', 'Letter file deleted successfully!');
    }

    /**
     * Close a letter file
     */
    public function close($id)
    {
        $file = LetterFile::findOrFail($id);

        if ($file->isFull() || ! $file->is_active) {
            $file->close();

            return redirect()->route('registry.files.show', $file->id)
                ->with('success', 'Letter file closed successfully!');
        }

        return redirect()->route('registry.files.show', $file->id)
            ->with('warning', 'File is not full. You can still use it.');
    }

    /**
     * Reopen a closed letter file
     */
    public function reopen($id)
    {
        $file = LetterFile::findOrFail($id);

        if (! $file->isFull()) {
            $file->reopen();

            return redirect()->route('registry.files.show', $file->id)
                ->with('success', 'Letter file reopened successfully!');
        }

        return redirect()->route('registry.files.show', $file->id)
            ->with('warning', 'File is full and cannot be reopened.');
    }

    /**
     * Generate next file number for a format (AJAX endpoint)
     */
    public function getNextFileNumber(Request $request)
    {
        $formatId = $request->input('format_id');

        if (! $formatId) {
            return response()->json(['file_number' => '']);
        }

        $format = ReferenceFormat::find($formatId);
        if (! $format) {
            return response()->json(['file_number' => '']);
        }

        $nextFileNumber = $this->calculateNextFileNumber($format);

        return response()->json(['file_number' => $nextFileNumber]);
    }

    /**
     * Get file statistics (AJAX)
     */
    public function getStats($id)
    {
        $file = LetterFile::findOrFail($id);

        return response()->json([
            'usage_percentage' => $file->usage_percentage,
            'remaining_capacity' => $file->remaining_capacity,
            'current_serial_formatted' => $file->current_serial_formatted,
            'total_letters' => $file->letters()->count(),
            'is_full' => $file->isFull(),
            'has_capacity' => $file->hasCapacity(),
        ]);
    }

    /**
     * Calculate next file number for a format (internal helper)
     */
    private function calculateNextFileNumber($format)
    {
        $pattern = $format->file_naming_pattern ?? '{prefix}-VOL-{number:03d}';

        // Get university code
        $universityCode = $format->university->code ?? 'FILE';

        // Get the last file number
        $lastFile = LetterFile::where('reference_format_id', $format->id)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastFile) {
            // Extract volume number and increment
            preg_match('/(\d+)/', $lastFile->file_number, $matches);
            $volumeNumber = isset($matches[1]) ? (int) $matches[1] + 1 : 1;
        } else {
            $volumeNumber = 1;
        }

        // Replace placeholders
        $fileNumber = str_replace(
            ['{prefix}', '{number:03d}'],
            [$universityCode, str_pad($volumeNumber, 3, '0', STR_PAD_LEFT)],
            $pattern
        );

        return $fileNumber;
    }
}
