<?php

namespace App\Http\Controllers;

use App\Models\ReportDefinition;
use App\Models\ReportLog;
use App\Models\Department;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportExport;
use chillerlan\QRCode\QRCode as QRCodeLib;
use chillerlan\QRCode\QROptions;

class ReportController extends Controller
{
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * List all reports with dynamic statistics.
     */
    public function index()
    {
        $reports = ReportDefinition::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $last30DaysCount = ReportLog::where('created_at', '>=', now()->subDays(30))->count();

        $mostViewed = ReportLog::select('report_definition_id', \DB::raw('count(*) as total'))
            ->groupBy('report_definition_id')
            ->orderBy('total', 'desc')
            ->first();

        $mostViewedName = $mostViewed 
            ? optional(ReportDefinition::find($mostViewed->report_definition_id))->display_name 
            : 'N/A';

        return view('reports.index', compact('reports', 'last30DaysCount', 'mostViewedName'));
    }

    /**
     * Show a specific report with filters, table, and chart.
     */
    public function show($slug)
    {
        $definition = ReportDefinition::where('name', $slug)->firstOrFail();
        $filters = $this->extractFilters($definition);
        
        // Handle special report types - using generic show view
        if ($slug === 'department_roster') {
            $reportData = $this->reportService->getDepartmentRoster($filters);
            // Use the generic show view (no separate blade file needed)
            return view('reports.show', compact('definition', 'reportData', 'filters'));
        }
        
        $reportData = $this->reportService->getData($definition, $filters);
        return view('reports.show', compact('definition', 'reportData', 'filters'));
    }

    /**
     * Export report to PDF.
     */
    public function exportPdf($slug)
    {
        $definition = ReportDefinition::where('name', $slug)->firstOrFail();
        $filters = $this->extractFilters($definition);
        
        // Handle special report types
        if ($slug === 'department_roster') {
            $reportData = $this->reportService->getDepartmentRoster($filters);
        } else {
            $reportData = $this->reportService->getData($definition, $filters);
        }

        // Generate a unique 8‑character verification code
        do {
            $verificationCode = $this->generateVerificationCode();
        } while (ReportLog::where('verification_code', $verificationCode)->exists());

        // Create the log entry
        ReportLog::create([
            'user_id' => auth()->id(),
            'report_definition_id' => $definition->id,
            'format' => 'pdf',
            'filters_applied' => $filters,
            'verification_code' => $verificationCode,
        ]);

        $qrCode = $this->generateQrCode($verificationCode, $definition);

        $pdf = Pdf::loadView('reports.pdf', compact('definition', 'reportData', 'filters', 'verificationCode', 'qrCode'));
        return $pdf->download($definition->name . '_' . date('Y-m-d') . '.pdf');
    }

    /**
     * Export report to Excel.
     */
    public function exportExcel($slug)
    {
        $definition = ReportDefinition::where('name', $slug)->firstOrFail();
        $filters = $this->extractFilters($definition);
        
        // Handle special report types
        if ($slug === 'department_roster') {
            $reportData = $this->reportService->getDepartmentRoster($filters);
            $rows = $reportData['rows'] ?? collect();
        } else {
            $reportData = $this->reportService->getData($definition, $filters);
            $rows = $reportData['rows'] ?? collect();
        }

        return Excel::download(
            new ReportExport($rows, $definition),
            $definition->name . '_' . date('Y-m-d') . '.xlsx'
        );
    }

    /**
     * Extract filter values from request based on the report's filter definition.
     */
    private function extractFilters($definition)
    {
        $filters = [];
        
        $rawFilters = $definition->filters;
        
        if (is_string($rawFilters)) {
            $filtersArray = json_decode($rawFilters, true) ?: [];
        } elseif (is_array($rawFilters)) {
            $filtersArray = $rawFilters;
        } else {
            $filtersArray = [];
        }
        
        foreach (array_keys($filtersArray) as $filter) {
            if (request()->has($filter)) {
                $filters[$filter] = request($filter);
            }
        }
        
        return $filters;
    }

    /**
     * Generate a random verification code (8 chars: uppercase + numbers).
     */
    private function generateVerificationCode()
    {
        $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ123456789';
        $code = '';
        for ($i = 0; $i < 8; $i++) {
            $code .= $characters[random_int(0, strlen($characters) - 1)];
        }
        return $code;
    }

    /**
     * Generate QR code using chillerlan/php-qrcode (pure PHP, no Imagick required).
     */
    private function generateQrCode($verificationCode, $definition)
    {
        $url = url('/verify/' . $verificationCode);
        
        $options = new QROptions([
            'version' => 5,
            'outputType' => 'png',
            'eccLevel' => 'H',
            'scale' => 5,
            'outputBase64' => true,
        ]);
        
        $qrCode = (new QRCodeLib($options))->render($url);
        return $qrCode;
    }

    /**
     * Optional permission check – kept for future use.
     */
    private function authorizeReportAccess($definition)
    {
        $user = auth()->user();
        if ($definition->permission && !$user->hasPermissionTo($definition->permission)) {
            abort(403, 'You do not have permission to view this report.');
        }
    }
}