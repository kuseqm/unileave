<?php

namespace App\Console\Commands;

use App\Models\AuthorizedSignature;
use App\Models\IdCard;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportStaffIdHrCards extends Command
{
    protected $signature = 'import:staff-id-hr-cards
        {--connection=staff_id_hr_staging : DB connection name the old dump was imported into}
        {--map=staff_id_hr_import_map.json : Filename (in storage/app) of the staff mapping from import:staff-id-hr}';

    protected $description = 'Import real ID cards and authorized signatures from staff_id_hr, after import:staff-id-hr has run';

    public function handle()
    {
        $connection = $this->option('connection');
        $mapPath = storage_path('app/'.$this->option('map'));

        if (! file_exists($mapPath)) {
            $this->error("Staff mapping file not found: {$mapPath}. Run import:staff-id-hr first.");

            return self::FAILURE;
        }

        $staffMap = json_decode(file_get_contents($mapPath), true);

        // 1. Authorized signatures — straight copy, no remapping needed.
        $signatureMap = [];
        $oldSignatures = DB::connection($connection)->table('authorized_signatures')->get();

        foreach ($oldSignatures as $old) {
            $new = AuthorizedSignature::create([
                'name' => $old->name,
                'signature_path' => $old->signature_path,
            ]);

            $signatureMap[$old->id] = $new->id;
        }

        $this->info('Imported '.count($signatureMap).' authorized signatures.');

        // 2. Staff photos — the map's `photo` is a staff_id_hr-relative path like
        // "photos/xxx.jpg"; the files were copied into storage/app/public/avatars,
        // so the staff avatar column needs the folder name rewritten to match.
        $avatarsUpdated = 0;

        foreach ($staffMap as $entry) {
            if (! $entry['photo']) {
                continue;
            }

            $filename = basename($entry['photo']);
            User::where('id', $entry['staff_id'])->update(['avatar' => 'avatars/'.$filename]);
            $avatarsUpdated++;
        }

        $this->info("Updated {$avatarsUpdated} staff avatars.");

        // 3. ID cards — remap old employee.id -> new staff.id via the map, and old
        // authorized_signature_id -> new id via $signatureMap.
        $oldCards = DB::connection($connection)->table('id_cards')->get();
        $cardsImported = 0;
        $cardsSkipped = 0;

        foreach ($oldCards as $old) {
            $mapped = $staffMap[$old->employee_id] ?? null;

            if (! $mapped) {
                $this->warn("Skipping id_card #{$old->id}: no staff mapping for employee #{$old->employee_id}.");
                $cardsSkipped++;

                continue;
            }

            IdCard::create([
                'staff_id' => $mapped['staff_id'],
                'issued_at' => $old->issued_at,
                'valid_until' => $old->valid_until,
                'authorized_by' => $old->authorized_by,
                'signature_path' => $old->signature_path,
                'authorized_signature_id' => $old->authorized_signature_id
                    ? ($signatureMap[$old->authorized_signature_id] ?? null)
                    : null,
            ]);

            $cardsImported++;
        }

        $this->info("Imported {$cardsImported} ID cards".($cardsSkipped ? " ({$cardsSkipped} skipped)." : '.'));

        return self::SUCCESS;
    }
}
