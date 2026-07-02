<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\University;
use App\Models\Campus;
use App\Models\School;
use App\Models\Department;
use App\Models\ImportHistory;
use App\Models\ImportSequence;
use App\Services\ImportService;
use App\Traits\PerPageTrait;
use App\Traits\LogsActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ImportController extends Controller
{
    use PerPageTrait;
    use LogsActivity;
    
    protected $importService;
    
    public function __construct(ImportService $importService)
    {
        $this->importService = $importService;
    }
    
    public function index(Request $request)
    {
        $perPage = $this->getPerPage($request, 15);
        $universities = University::where('status', 'active')->get();
        $user = auth()->user();
        
        $query = ImportHistory::with('user');
        
        if (!$user->hasRole('admin')) {
            $query->where('user_id', $user->id);
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $history = $query->orderBy('created_at', 'desc')->paginate($perPage);
        
        $stats = [
            'total_imports' => ImportHistory::where('user_id', $user->id)->count(),
            'total_rows' => ImportHistory::where('user_id', $user->id)->sum('successful_rows'),
            'failed_imports' => ImportHistory::where('user_id', $user->id)->where('status', 'failed')->count(),
            'completed_imports' => ImportHistory::where('user_id', $user->id)->where('status', 'completed')->count(),
            'processing_imports' => ImportHistory::where('user_id', $user->id)->where('status', 'processing')->count(),
        ];
        
        $lastImport = ImportHistory::where('user_id', $user->id)->latest()->first();
        $lastImportDate = $lastImport ? $lastImport->created_at->format('d M Y H:i') : 'N/A';
        
        return view('admin.imports.index', compact('universities', 'history', 'stats', 'lastImportDate'));
    }
    
    public function downloadTemplate(Request $request)
    {
        $request->validate([
            'university_id' => 'required|exists:universities,id',
            'campus_id' => 'required|exists:campuses,id',
            'school_id' => 'required|exists:schools,id',
            'department_id' => 'required|exists:departments,id',
        ]);
        
        $department = Department::find($request->department_id);
        $school = School::find($request->school_id);
        $campus = Campus::find($request->campus_id);
        $university = University::find($request->university_id);
        
        $roleCode = $this->getRoleCode(auth()->user());
        $sequenceNumber = $this->generateSequenceNumber($roleCode);
        
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Staff Data');
        
        $headers = [
            'A1' => 'Staff ID',
            'B1' => 'Title',
            'C1' => 'First Name',
            'D1' => 'Middle Name',
            'E1' => 'Last Name',
            'F1' => 'Email',
            'G1' => 'Staff Category',
            'H1' => 'Joining Date',
            'I1' => 'Phone',
        ];
        
        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }
        
        $headerStyle = [
            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E3A5F']],
        ];
        $sheet->getStyle('A1:I1')->applyFromArray($headerStyle);
        
        $sheet->setCellValue('A3', 'Pre-selected: ' . $university->name . ' | ' . $campus->name . ' | ' . $school->name . ' | ' . $department->name);
        $sheet->getStyle('A3')->getFont()->setItalic(true)->setSize(9);
        
        $sheet->setCellValue('A2', 'STF2024001 (Optional)');
        $sheet->setCellValue('B2', 'Dr');
        $sheet->setCellValue('C2', 'John');
        $sheet->setCellValue('D2', 'Robert');
        $sheet->setCellValue('E2', 'Doe');
        $sheet->setCellValue('F2', 'john.doe@example.com');
        $sheet->setCellValue('G2', 'academic');
        $sheet->setCellValue('H2', '2024-01-15');
        $sheet->setCellValue('I2', '+255712345678');
        
        $sheet->getStyle('A2:I2')->applyFromArray([
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F5F5F5']],
            'font' => ['color' => ['rgb' => '999999']],
        ]);
        
        $sheet->setCellValue('A4', '⚠ DELETE THIS EXAMPLE ROW (ROW 2) BEFORE UPLOADING');
        $sheet->getStyle('A4')->getFont()->setBold(true)->setSize(9);
        $sheet->getStyle('A4')->getFont()->getColor()->setARGB('FFFF0000');
        
        $titleValidation = $sheet->getDataValidation('B2:B1000');
        $titleValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
        $titleValidation->setAllowBlank(true);
        $titleValidation->setShowDropDown(true);
        $titleValidation->setFormula1('"Mr,Mrs,Ms,Dr,Prof,Eng,Rev"');
        
        $categoryValidation = $sheet->getDataValidation('G2:G1000');
        $categoryValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
        $categoryValidation->setAllowBlank(false);
        $categoryValidation->setShowDropDown(true);
        $categoryValidation->setFormula1('"academic,administrative,supporting"');
        
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        $sheet->freezePane('A2');
        
        $filename = $sequenceNumber . '.xlsx';
        
        ImportHistory::create([
            'user_id' => auth()->id(),
            'role_code' => $roleCode,
            'sequence_number' => $sequenceNumber,
            'operation_type' => 'download',
            'original_filename' => $filename,
            'stored_filename' => $filename,
            'status' => 'completed',
        ]);
        
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        return response()->stream(function() use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
    
    // AJAX: Get schools by campus
    public function getSchools($campusId)
    {
        $schools = School::where('campus_id', $campusId)
            ->where('status', 'active')
            ->get(['id', 'name']);
        
        return response()->json($schools);
    }
    
    // AJAX: Get departments by school
    public function getDepartmentsBySchool($schoolId)
    {
        $departments = Department::where('school_id', $schoolId)
            ->where('status', 'active')
            ->get(['id', 'name']);
        
        return response()->json($departments);
    }
    
    public function upload(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file|mimes:xlsx,csv|max:10240',
                'university_id' => 'required|exists:universities,id',
                'campus_id' => 'required|exists:campuses,id',
                'school_id' => 'required|exists:schools,id',
                'department_id' => 'required|exists:departments,id',
                'test_mode' => 'sometimes|boolean',
            ]);

            if (!$request->hasFile('file') || !$request->file('file')->isValid()) {
                return redirect()->back()->with('error', 'No valid file was uploaded.');
            }

            $file = $request->file('file');

            $this->importService->validateFile($file, auth()->user());

            // Store selected hierarchy in session for persistence
            session([
                'import_university_id' => $request->university_id,
                'import_campus_id' => $request->campus_id,
                'import_school_id' => $request->school_id,
                'import_department_id' => $request->department_id,
            ]);

            $history = $this->importService->processImport(
                $file,
                auth()->user(),
                $request->university_id,
                $request->campus_id,
                $request->school_id,
                $request->department_id,
                $request->has('test_mode')
            );

            // Refresh history to get updated counts
            $history->refresh();

            // Log the bulk import
            $this->logBulkImport($history, $history->successful_rows, $history->failed_rows);

            return redirect()->route('admin.import.staff')
                ->with('success', "File uploaded successfully. Import ID: {$history->sequence_number}");

        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
    
    public function getCampuses($universityId)
    {
        $campuses = Campus::where('university_id', $universityId)
            ->where('status', 'active')
            ->get(['id', 'name']);
        
        return response()->json($campuses);
    }
    
    public function getDepartments($campusId)
    {
        $departments = Department::where('campus_id', $campusId)
            ->where('status', 'active')
            ->get(['id', 'name']);
        
        return response()->json($departments);
    }
    
    public function progress($id)
    {
        $import = ImportHistory::findOrFail($id);
        $user = auth()->user();
        
        if (!$user->hasRole('admin') && $import->user_id != $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        return response()->json([
            'status' => $import->status,
            'total_rows' => $import->total_rows,
            'successful_rows' => $import->successful_rows,
            'failed_rows' => $import->failed_rows,
            'progress' => $import->total_rows > 0 ? round(($import->successful_rows / $import->total_rows) * 100) : 0,
        ]);
    }
    
    public function downloadErrorReport($id)
    {
        $import = ImportHistory::findOrFail($id);
        $user = auth()->user();
        
        if (!$user->hasRole('admin') && $import->user_id != $user->id) {
            abort(403);
        }
        
        if (!$import->error_report_path || !Storage::exists($import->error_report_path)) {
            abort(404, 'Error report not found');
        }
        
        return response()->download(
            storage_path('app/' . $import->error_report_path),
            'error_' . $import->sequence_number . '.xlsx'
        );
    }
    
    public function getDetails($id)
    {
        $import = ImportHistory::with('user')->findOrFail($id);
        $user = auth()->user();
        
        if (!$user->hasRole('admin') && $import->user_id != $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $errors = [];
        if ($import->errors) {
            $errors = is_array($import->errors) ? $import->errors : json_decode($import->errors, true) ?? [];
        }
        
        return response()->json([
            'id' => $import->id,
            'sequence_number' => $import->sequence_number,
            'status' => $import->status,
            'operation_type' => $import->operation_type,
            'original_filename' => $import->original_filename,
            'file_size_formatted' => $this->formatBytes($import->file_size ?? 0),
            'total_rows' => $import->total_rows,
            'successful_rows' => $import->successful_rows,
            'failed_rows' => $import->failed_rows,
            'user_name' => $import->user->first_name . ' ' . $import->user->last_name,
            'created_at' => $import->created_at->format('d M Y H:i:s'),
            'errors' => $errors,
        ]);
    }
    
    public function deleteImport($id)
    {
        $user = auth()->user();
        if (!$user->hasRole('admin')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        
        $import = ImportHistory::findOrFail($id);
        
        if ($import->error_report_path && Storage::exists($import->error_report_path)) {
            Storage::delete($import->error_report_path);
        }
        
        $import->delete();
        
        return response()->json(['success' => true]);
    }
    
    public function quarterlyReport(Request $request)
    {
        $request->validate([
            'year' => 'required|integer|min:2020|max:2030',
            'quarter' => 'required|integer|min:1|max:4',
        ]);
        
        $user = auth()->user();
        $startDate = \Carbon\Carbon::create($request->year, ($request->quarter - 1) * 3 + 1, 1)->startOfMonth();
        $endDate = $startDate->copy()->addMonths(3)->subDay();
        
        $query = ImportHistory::whereBetween('created_at', [$startDate, $endDate]);
        
        if (!$user->hasRole('admin')) {
            $query->where('user_id', $user->id);
        }
        
        $reports = $query->orderBy('created_at', 'desc')->get();
        
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->setCellValue('A1', 'Quarterly Import Report');
        $sheet->setCellValue('A2', "Period: {$startDate->format('d M Y')} - {$endDate->format('d M Y')}");
        $sheet->setCellValue('A4', 'Seq No');
        $sheet->setCellValue('B4', 'Type');
        $sheet->setCellValue('C4', 'User');
        $sheet->setCellValue('D4', 'Date');
        $sheet->setCellValue('E4', 'Total');
        $sheet->setCellValue('F4', 'Success');
        $sheet->setCellValue('G4', 'Failed');
        $sheet->setCellValue('H4', 'Status');
        
        $row = 5;
        foreach ($reports as $report) {
            $sheet->setCellValue('A' . $row, $report->sequence_number);
            $sheet->setCellValue('B' . $row, ucfirst($report->operation_type));
            $sheet->setCellValue('C' . $row, $report->user->first_name . ' ' . $report->user->last_name);
            $sheet->setCellValue('D' . $row, $report->created_at->format('d M Y H:i'));
            $sheet->setCellValue('E' . $row, $report->total_rows);
            $sheet->setCellValue('F' . $row, $report->successful_rows);
            $sheet->setCellValue('G' . $row, $report->failed_rows);
            $sheet->setCellValue('H' . $row, ucfirst($report->status));
            $row++;
        }
        
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        $filename = "import_report_Q{$request->quarter}_{$request->year}.xlsx";
        $path = storage_path("app/imports/reports/{$filename}");
        
        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }
        
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($path);
        
        return response()->download($path, $filename)->deleteFileAfterSend(true);
    }
    
    private function getRoleCode($user)
    {
        if ($user->hasRole('admin')) return 'ADM';
        if ($user->hasRole('hr-manager')) return 'HRM';
        if ($user->hasRole('hr-officer')) return 'HRO';
        return 'STA';
    }
    
    private function generateSequenceNumber($roleCode)
    {
        $sequence = ImportSequence::firstOrCreate(
            ['role_code' => $roleCode],
            ['last_sequence' => 0]
        );
        
        $sequence->increment('last_sequence');
        $sequence->refresh();
        
        return $roleCode . now()->format('ymdH') . str_pad($sequence->last_sequence, 4, '0', STR_PAD_LEFT);
    }
    
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}