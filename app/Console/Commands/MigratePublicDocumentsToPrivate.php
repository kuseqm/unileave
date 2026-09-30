<?php

namespace App\Console\Commands;

use App\Models\Document;
use App\Models\IncomingLetter;
use App\Models\MessageAttachment;
use App\Models\TravelPermission;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

#[Signature('app:migrate-public-documents-to-private')]
#[Description('Move existing leave/travel/message/incoming-letter documents from the public disk to the private local disk.')]
class MigratePublicDocumentsToPrivate extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Migrating documents: leave documents, travel documents, message attachments, incoming letters.');
        $this->newLine();

        $totals = [
            'moved' => 0,
            'already_local' => 0,
            'missing' => 0,
        ];

        $this->migrateLeaveDocuments($totals);
        $this->migrateTravelDocuments($totals);
        $this->migrateMessageAttachments($totals);
        $this->migrateIncomingLetters($totals);

        $this->newLine();
        $this->info('Migration summary (all types combined):');
        $this->table(
            ['Moved', 'Already local', 'Missing'],
            [[$totals['moved'], $totals['already_local'], $totals['missing']]]
        );

        return self::SUCCESS;
    }

    /**
     * Move a single file from the public disk to the local disk at the same relative path.
     *
     * @param  array<string, int>  $totals
     * @return string One of 'moved', 'already_local', 'missing'.
     */
    private function movePathToLocal(?string $path, array &$totals): string
    {
        if (! $path) {
            $totals['missing']++;

            return 'missing';
        }

        if (Storage::disk('local')->exists($path)) {
            $totals['already_local']++;

            return 'already_local';
        }

        if (! Storage::disk('public')->exists($path)) {
            $totals['missing']++;

            return 'missing';
        }

        $contents = Storage::disk('public')->get($path);
        Storage::disk('local')->put($path, $contents);
        Storage::disk('public')->delete($path);

        $totals['moved']++;

        return 'moved';
    }

    /**
     * @param  array<string, int>  $totals
     */
    private function migrateLeaveDocuments(array &$totals): void
    {
        $moved = 0;
        $alreadyLocal = 0;
        $missing = 0;

        Document::query()->whereNotNull('file_path')->each(function (Document $document) use (&$moved, &$alreadyLocal, &$missing, &$totals) {
            $result = $this->movePathToLocal($document->file_path, $totals);

            match ($result) {
                'moved' => $moved++,
                'already_local' => $alreadyLocal++,
                default => $missing++,
            };
        });

        $this->line("Leave documents: moved={$moved}, already_local={$alreadyLocal}, missing={$missing}");
    }

    /**
     * @param  array<string, int>  $totals
     */
    private function migrateTravelDocuments(array &$totals): void
    {
        $moved = 0;
        $alreadyLocal = 0;
        $missing = 0;

        TravelPermission::query()
            ->where(function ($q) {
                $q->whereNotNull('invitation_letter_path')->orWhereNotNull('endorsement_path');
            })
            ->each(function (TravelPermission $travelRequest) use (&$moved, &$alreadyLocal, &$missing, &$totals) {
                foreach (['invitation_letter_path', 'endorsement_path'] as $column) {
                    if (! $travelRequest->{$column}) {
                        continue;
                    }

                    $result = $this->movePathToLocal($travelRequest->{$column}, $totals);

                    match ($result) {
                        'moved' => $moved++,
                        'already_local' => $alreadyLocal++,
                        default => $missing++,
                    };
                }
            });

        $this->line("Travel documents: moved={$moved}, already_local={$alreadyLocal}, missing={$missing}");
    }

    /**
     * @param  array<string, int>  $totals
     */
    private function migrateMessageAttachments(array &$totals): void
    {
        $moved = 0;
        $alreadyLocal = 0;
        $missing = 0;

        MessageAttachment::query()->whereNotNull('file_path')->each(function (MessageAttachment $attachment) use (&$moved, &$alreadyLocal, &$missing, &$totals) {
            $result = $this->movePathToLocal($attachment->file_path, $totals);

            match ($result) {
                'moved' => $moved++,
                'already_local' => $alreadyLocal++,
                default => $missing++,
            };

            if ($result !== 'missing' && $attachment->disk !== 'local') {
                $attachment->update(['disk' => 'local']);
            }
        });

        $this->line("Message attachments: moved={$moved}, already_local={$alreadyLocal}, missing={$missing}");
    }

    /**
     * @param  array<string, int>  $totals
     */
    private function migrateIncomingLetters(array &$totals): void
    {
        $moved = 0;
        $alreadyLocal = 0;
        $missing = 0;

        IncomingLetter::query()->whereNotNull('pdf_path')->each(function (IncomingLetter $letter) use (&$moved, &$alreadyLocal, &$missing, &$totals) {
            $result = $this->movePathToLocal($letter->pdf_path, $totals);

            match ($result) {
                'moved' => $moved++,
                'already_local' => $alreadyLocal++,
                default => $missing++,
            };
        });

        $this->line("Incoming letters: moved={$moved}, already_local={$alreadyLocal}, missing={$missing}");
    }
}
