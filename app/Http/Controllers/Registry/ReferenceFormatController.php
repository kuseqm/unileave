<?php

namespace App\Http\Controllers\Registry;

use App\Http\Controllers\Controller;
use App\Models\ReferenceFormat;
use App\Models\University;
use App\Models\Department;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReferenceFormatController extends Controller
{
    /**
     * Display a listing of reference formats
     */
    public function index()
    {
        $formats = ReferenceFormat::with('university')
            ->orderBy('name')
            ->paginate(15);
        
        return view('registry.formats.index', compact('formats'));
    }
    
    /**
     * Show the form for creating a new reference format
     */
    public function create()
    {
        $universities = University::where('status', 'active')->get();
        $departments = Department::where('status', 'active')->get();
        $schools = School::where('status', 'active')->get();
        
        return view('registry.formats.create', compact('universities', 'departments', 'schools'));
    }
    
    /**
     * Store a newly created reference format
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:reference_formats,name',
            'university_id' => 'required|exists:universities,id',
            'entity_type' => 'nullable|string|in:department,school',
            'entity_id' => 'required_if:entity_type,department|required_if:entity_type,school|nullable|integer',
            'document_type' => 'required|string|in:leave,permission,travel,contract,general',
            'action_type' => 'nullable|string|max:50',
            'format_pattern' => 'required|string|max:255',
            'separator' => 'nullable|string|max:5',
            'serial_length' => 'required|integer|min:3|max:6',
            'serial_start' => 'required|integer|min:1',
            'current_serial' => 'required|integer|min:0',
            'file_capacity' => 'required|integer|min:0',
            'file_naming_pattern' => 'nullable|string|max:255',
            'auto_create_new_file' => 'boolean',
            'track_physical_location' => 'boolean',
            'is_active' => 'boolean',
        ]);
        
        // Set defaults
        $validated['separator'] = $validated['separator'] ?? '/';
        $validated['file_naming_pattern'] = $validated['file_naming_pattern'] ?? '{prefix}-VOL-{number:03d}';
        $validated['auto_create_new_file'] = $request->has('auto_create_new_file');
        $validated['track_physical_location'] = $request->has('track_physical_location');
        $validated['is_active'] = $request->has('is_active');
        
        ReferenceFormat::create($validated);
        
        return redirect()->route('registry.formats.index')
            ->with('success', 'Reference format created successfully!');
    }
    
    /**
     * Display the specified reference format
     */
    public function show($id)
    {
        $format = ReferenceFormat::with(['university', 'letterFiles', 'activeFile'])
            ->findOrFail($id);
        
        // Generate example numbers
        $exampleNumber = $this->generateExampleNumber($format);
        $nextNumber = $this->generateNextNumber($format);
        
        return view('registry.formats.show', compact('format', 'exampleNumber', 'nextNumber'));
    }
    
    /**
     * Show the form for editing the specified reference format
     */
    public function edit($id)
    {
        $format = ReferenceFormat::findOrFail($id);
        $universities = University::where('status', 'active')->get();
        $departments = Department::where('status', 'active')->get();
        $schools = School::where('status', 'active')->get();
        
        return view('registry.formats.edit', compact('format', 'universities', 'departments', 'schools'));
    }
    
    /**
     * Update the specified reference format
     */
    public function update(Request $request, $id)
    {
        $format = ReferenceFormat::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:reference_formats,name,' . $id,
            'university_id' => 'required|exists:universities,id',
            'entity_type' => 'nullable|string|in:department,school',
            'entity_id' => 'required_if:entity_type,department|required_if:entity_type,school|nullable|integer',
            'document_type' => 'required|string|in:leave,permission,travel,contract,general',
            'action_type' => 'nullable|string|max:50',
            'format_pattern' => 'required|string|max:255',
            'separator' => 'nullable|string|max:5',
            'serial_length' => 'required|integer|min:3|max:6',
            'serial_start' => 'required|integer|min:1',
            'current_serial' => 'required|integer|min:0',
            'file_capacity' => 'required|integer|min:0',
            'file_naming_pattern' => 'nullable|string|max:255',
            'auto_create_new_file' => 'boolean',
            'track_physical_location' => 'boolean',
            'is_active' => 'boolean',
        ]);
        
        // Set defaults
        $validated['separator'] = $validated['separator'] ?? '/';
        $validated['file_naming_pattern'] = $validated['file_naming_pattern'] ?? '{prefix}-VOL-{number:03d}';
        $validated['auto_create_new_file'] = $request->has('auto_create_new_file');
        $validated['track_physical_location'] = $request->has('track_physical_location');
        $validated['is_active'] = $request->has('is_active');
        
        $format->update($validated);
        
        return redirect()->route('registry.formats.index')
            ->with('success', 'Reference format updated successfully!');
    }
    
    /**
     * Remove the specified reference format
     */
    public function destroy($id)
    {
        $format = ReferenceFormat::findOrFail($id);
        
        // Check if format has been used
        if ($format->current_serial > 0) {
            return back()->with('error', 'Cannot delete format that has been used. Deactivate it instead.');
        }
        
        $format->delete();
        
        return redirect()->route('registry.formats.index')
            ->with('success', 'Reference format deleted successfully!');
    }
    
    /**
     * Get entities based on type (AJAX)
     */
    public function getEntities(Request $request)
    {
        $type = $request->input('type');
        
        if ($type === 'department') {
            $entities = Department::where('status', 'active')
                ->select('id', 'name', 'code')
                ->get();
        } elseif ($type === 'school') {
            $entities = School::where('status', 'active')
                ->select('id', 'name', 'code')
                ->get();
        } else {
            $entities = collect();
        }
        
        return response()->json($entities);
    }
    
    /**
     * Generate preview of reference number (AJAX)
     */
    public function preview(Request $request)
    {
        $pattern = $request->input('pattern');
        $serialLength = $request->input('serial_length', 4);
        $documentType = $request->input('document_type', 'leave');
        $universityCode = $request->input('university_code', 'SJUT');
        $entityCode = $request->input('entity_code', 'SVAC');
        
        $year = date('Y');
        $serial = str_pad(1, $serialLength, '0', STR_PAD_LEFT);
        $volume = 'VOL I';
        $actionType = $request->input('action_type', 'APR');
        
        // Document type codes
        $typeCodes = [
            'leave' => '013',
            'permission' => '014',
            'travel' => '015',
            'contract' => '016',
            'general' => '017',
        ];
        $typeCode = $typeCodes[$documentType] ?? '000';
        
        $typeAbbr = [
            'leave' => 'LVE',
            'permission' => 'PER',
            'travel' => 'TRV',
            'contract' => 'CTR',
            'general' => 'GEN',
        ];
        $typeAbbreviation = $typeAbbr[$documentType] ?? 'DOC';
        
        // Replace all placeholders
        $preview = str_replace(
            [
                '{year}', 
                '{serial}', 
                '{prefix}', 
                '{type}', 
                '{entity_code}', 
                '{university_code}', 
                '{volume}', 
                '{type_code}',
                '{action_type}'
            ],
            [
                $year, 
                $serial, 
                $universityCode, 
                $typeAbbreviation, 
                $entityCode, 
                $universityCode, 
                $volume, 
                $typeCode,
                $actionType
            ],
            $pattern
        );
        
        return response()->json(['preview' => $preview]);
    }
    
    /**
     * Generate an example reference number for display
     */
    private function generateExampleNumber($format)
    {
        $year = date('Y');
        $serial = str_pad($format->serial_start, $format->serial_length, '0', STR_PAD_LEFT);
        
        // Get university code
        $universityCode = $format->university->code ?? 'UNIV';
        
        // Get entity code
        $entityCode = $this->getEntityCode($format);
        
        // Document type codes
        $typeCodes = [
            'leave' => '013',
            'permission' => '014',
            'travel' => '015',
            'contract' => '016',
            'general' => '017',
        ];
        $typeCode = $typeCodes[$format->document_type] ?? '000';
        
        $typeAbbr = [
            'leave' => 'LVE',
            'permission' => 'PER',
            'travel' => 'TRV',
            'contract' => 'CTR',
            'general' => 'GEN',
        ];
        $typeAbbreviation = $typeAbbr[$format->document_type] ?? 'DOC';
        
        $volume = 'VOL I';
        $actionType = $format->action_type ?? 'APR';
        
        return str_replace(
            [
                '{year}', '{serial}', '{prefix}', '{type}', '{entity_code}',
                '{university_code}', '{volume}', '{type_code}', '{action_type}'
            ],
            [
                $year, $serial, $universityCode, $typeAbbreviation, $entityCode,
                $universityCode, $volume, $typeCode, $actionType
            ],
            $format->format_pattern
        );
    }
    
    /**
     * Generate the next reference number
     */
    private function generateNextNumber($format)
    {
        $year = date('Y');
        $nextSerial = $format->current_serial + 1;
        $serial = str_pad($nextSerial, $format->serial_length, '0', STR_PAD_LEFT);
        
        $universityCode = $format->university->code ?? 'UNIV';
        $entityCode = $this->getEntityCode($format);
        
        $typeCodes = [
            'leave' => '013',
            'permission' => '014',
            'travel' => '015',
            'contract' => '016',
            'general' => '017',
        ];
        $typeCode = $typeCodes[$format->document_type] ?? '000';
        
        $typeAbbr = [
            'leave' => 'LVE',
            'permission' => 'PER',
            'travel' => 'TRV',
            'contract' => 'CTR',
            'general' => 'GEN',
        ];
        $typeAbbreviation = $typeAbbr[$format->document_type] ?? 'DOC';
        
        // Calculate volume based on capacity
        $volumeNumber = floor($nextSerial / max($format->file_capacity, 1)) + 1;
        $volume = "VOL " . $this->numberToRoman($volumeNumber);
        $actionType = $format->action_type ?? 'APR';
        
        return str_replace(
            [
                '{year}', '{serial}', '{prefix}', '{type}', '{entity_code}',
                '{university_code}', '{volume}', '{type_code}', '{action_type}'
            ],
            [
                $year, $serial, $universityCode, $typeAbbreviation, $entityCode,
                $universityCode, $volume, $typeCode, $actionType
            ],
            $format->format_pattern
        );
    }
    
    /**
     * Get entity code from format
     */
    private function getEntityCode($format)
    {
        if ($format->entity_type === 'department' && $format->entity_id) {
            $department = Department::find($format->entity_id);
            return $department->code ?? 'DEPT';
        }
        
        if ($format->entity_type === 'school' && $format->entity_id) {
            $school = School::find($format->entity_id);
            return $school->code ?? 'SCH';
        }
        
        return 'GEN';
    }
    
    /**
     * Convert number to Roman numerals (for volumes)
     */
    private function numberToRoman($number)
    {
        $map = [
            10 => 'X', 9 => 'IX', 5 => 'V', 4 => 'IV',
            1 => 'I'
        ];
        $result = '';
        while ($number > 0) {
            foreach ($map as $value => $numeral) {
                if ($number >= $value) {
                    $result .= $numeral;
                    $number -= $value;
                    break;
                }
            }
        }
        return $result;
    }
}