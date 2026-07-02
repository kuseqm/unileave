<?php

namespace App\Services;

use App\Models\ImportHistory;
use App\Jobs\ProcessImportJob;

class ImportService
{
    protected $sequenceService;
    
    public function __construct(SequenceNumberService $sequenceService)
    {
        $this->sequenceService = $sequenceService;
    }
    
    public function validateFile($file, $user)
    {
        if (!$file || !$file->isValid()) {
            throw new \Exception('Uploaded file is invalid or corrupted');
        }
        
        if ($file->getSize() > 10 * 1024 * 1024) {
            throw new \Exception('File size exceeds 10MB limit');
        }
        
        $extension = $file->getClientOriginalExtension();
        if (!in_array(strtolower($extension), ['xlsx', 'csv'])) {
            throw new \Exception('Only .xlsx or .csv files are allowed');
        }
        
        return true;
    }
    
    public function processImport($file, $user, $universityId, $campusId, $schoolId, $departmentId, $isTestMode = false)
    {
        // Validate all location IDs
        if (empty($universityId) || empty($campusId) || empty($schoolId) || empty($departmentId)) {
            throw new \Exception("Missing location information. University: {$universityId}, Campus: {$campusId}, School: {$schoolId}, Department: {$departmentId}");
        }

        $originalName = $file->getClientOriginalName();
        
        preg_match('/([A-Z]{3}\d{11})/', $originalName, $matches);
        
        if (isset($matches[1])) {
            $sequenceNumber = $matches[1];
            $roleCode = substr($sequenceNumber, 0, 3);
            
            $existingHistory = ImportHistory::where('sequence_number', $sequenceNumber)->first();
            if ($existingHistory) {
                $roleCode = $this->sequenceService->getRoleCode($user);
                $sequenceNumber = $this->sequenceService->generate($roleCode);
            }
        } else {
            $roleCode = $this->sequenceService->getRoleCode($user);
            $sequenceNumber = $this->sequenceService->generate($roleCode);
        }
        
        $uploadDir = storage_path('app/imports/uploads/' . $user->id);
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $extension = $file->getClientOriginalExtension();
        $storedFilename = $sequenceNumber . '.' . $extension;
        $relativePath = 'imports/uploads/' . $user->id . '/' . $storedFilename;
        $fullPath = storage_path('app/' . $relativePath);
        
        $file->move($uploadDir, $storedFilename);
        
        if (!file_exists($fullPath)) {
            throw new \Exception('Failed to save uploaded file');
        }
        
        $fileSize = filesize($fullPath);
        
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($fullPath);
        $worksheet = $spreadsheet->getActiveSheet();
        $totalRows = $worksheet->getHighestRow() - 1;
        
        $history = ImportHistory::create([
            'user_id' => $user->id,
            'role_code' => $roleCode,
            'sequence_number' => $sequenceNumber,
            'operation_type' => 'upload',
            'original_filename' => $originalName,
            'stored_filename' => $relativePath,
            'file_size' => $fileSize,
            'total_rows' => $totalRows,
            'status' => 'pending',
            'is_test_mode' => $isTestMode,
            'university_id' => $universityId,
            'campus_id' => $campusId,
            'school_id' => $schoolId,
            'department_id' => $departmentId,
        ]);
        
        $job = new ProcessImportJob($history->id);
        $job->handle();
        
        $history->refresh();
        
        return $history;
    }
}