<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\AuditLog;
use App\Models\ImportHistory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $historyId;

    public function __construct($historyId)
    {
        $this->historyId = $historyId;
    }

    public function handle()
    {
        $history = ImportHistory::find($this->historyId);
        
        if (!$history) {
            Log::error('Import job failed: History not found for ID ' . $this->historyId);
            return;
        }
        
        $history->update(['status' => 'processing']);
        
        try {
            $universityId = $history->university_id;
            $campusId = $history->campus_id;
            $schoolId = $history->school_id;
            $departmentId = $history->department_id;
            
            Log::info("Importing with: University ID: {$universityId}, Campus ID: {$campusId}, School ID: {$schoolId}, Department ID: {$departmentId}");
            
            if (!$universityId || !$campusId || !$schoolId || !$departmentId) {
                throw new \Exception("Missing location information. University: {$universityId}, Campus: {$campusId}, School: {$schoolId}, Department: {$departmentId}");
            }
            
            $relativePath = $history->stored_filename;
            $windowsPath = str_replace('/', DIRECTORY_SEPARATOR, $relativePath);
            $fullPath = storage_path('app' . DIRECTORY_SEPARATOR . $windowsPath);
            $fullPath = str_replace(DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $fullPath);
            
            Log::info('Looking for file at: ' . $fullPath);
            
            if (!file_exists($fullPath)) {
                throw new \Exception("File not found at: " . $fullPath);
            }
            
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($fullPath);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();
            
            array_shift($rows);
            
            $successfulRows = 0;
            $failedRows = 0;
            $errors = [];
            
            DB::beginTransaction();
            
            foreach ($rows as $rowIndex => $row) {
                if (empty(trim($row[0] ?? '')) && empty(trim($row[2] ?? '')) && empty(trim($row[4] ?? ''))) {
                    continue;
                }
                
                try {
                    $staffId = trim($row[0] ?? '');
                    $title = trim($row[1] ?? '');
                    $firstName = trim($row[2] ?? '');
                    $middleName = trim($row[3] ?? '');
                    $lastName = trim($row[4] ?? '');
                    $email = trim($row[5] ?? '');
                    $staffCategory = trim($row[6] ?? '');
                    $joiningDateRaw = trim($row[7] ?? '');
                    $phone = trim($row[8] ?? '');
                    
                    $joiningDate = preg_replace('/\s.*$/', '', $joiningDateRaw);
                    
                    if (empty($firstName)) {
                        throw new \Exception("Row " . ($rowIndex + 2) . ": First Name is required");
                    }
                    if (empty($lastName)) {
                        throw new \Exception("Row " . ($rowIndex + 2) . ": Last Name is required");
                    }
                    if (empty($email)) {
                        throw new \Exception("Row " . ($rowIndex + 2) . ": Email is required");
                    }
                    if (User::where('email', $email)->exists()) {
                        throw new \Exception("Row " . ($rowIndex + 2) . ": Email '{$email}' already exists");
                    }
                    
                    $validCategories = ['academic', 'administrative', 'supporting'];
                    if (!in_array($staffCategory, $validCategories)) {
                        throw new \Exception("Row " . ($rowIndex + 2) . ": Staff Category must be: " . implode(', ', $validCategories));
                    }
                    
                    if (empty($joiningDate)) {
                        throw new \Exception("Row " . ($rowIndex + 2) . ": Joining Date is required");
                    }
                    
                    $date = \DateTime::createFromFormat('Y-m-d', $joiningDate);
                    if (!$date || $date->format('Y-m-d') !== $joiningDate) {
                        throw new \Exception("Row " . ($rowIndex + 2) . ": Joining Date must be YYYY-MM-DD. Got: {$joiningDate}");
                    }
                    
                    if (empty($staffId)) {
                        $lastStaff = User::orderBy('id', 'desc')->first();
                        $nextId = ($lastStaff ? $lastStaff->id + 1 : 1);
                        $staffId = 'STF' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
                    }
                    
                    $user = User::create([
                        'staff_id' => $staffId,
                        'title' => $title ?: null,
                        'first_name' => $firstName,
                        'middle_name' => $middleName ?: null,
                        'last_name' => $lastName,
                        'email' => $email,
                        'password' => Hash::make('Temp@123'),
                        'staff_category' => $staffCategory,
                        'joining_date' => $joiningDate,
                        'phone' => $phone ?: null,
                        'status' => 'active',
                        'password_change_required' => true,
                        'university_id' => $universityId,
                        'campus_id' => $campusId,
                        'school_id' => $schoolId,
                        'department_id' => $departmentId,
                    ]);
                    
                    $role = $staffCategory === 'administrative' ? 'hr-officer' : 'staff';
                    $user->assignRole($role);
                    
                    // Log each staff created during import
                    AuditLog::log(
                        'create',
                        'Staff',
                        $user->id,
                        null,
                        [
                            'staff_id' => $user->staff_id,
                            'name' => $user->full_name,
                            'email' => $user->email,
                            'method' => 'bulk_import',
                            'import_batch' => $history->sequence_number,
                        ]
                    );
                    
                    $successfulRows++;
                    Log::info("Created staff: {$firstName} {$lastName} ({$email})");
                    
                } catch (\Exception $e) {
                    $failedRows++;
                    $errors[] = $e->getMessage();
                    Log::warning($e->getMessage());
                }
            }
            
            if (!$history->is_test_mode && $successfulRows > 0) {
                DB::commit();
                Log::info("Import completed: {$successfulRows} successful, {$failedRows} failed");
            } else {
                DB::rollBack();
                Log::info("Test mode or no successful rows: Rolling back");
            }
            
            $history->update([
                'status' => 'completed',
                'total_rows' => $successfulRows + $failedRows,
                'successful_rows' => $successfulRows,
                'failed_rows' => $failedRows,
                'errors' => $failedRows > 0 ? json_encode($errors) : null,
            ]);
            
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
            
        } catch (\Exception $e) {
            DB::rollBack();
            $history->update([
                'status' => 'failed',
                'errors' => json_encode(['System error: ' . $e->getMessage()]),
            ]);
            Log::error('Import job failed: ' . $e->getMessage());
        }
    }
}