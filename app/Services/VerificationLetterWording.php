<?php

namespace App\Services;

use App\Models\VerificationLetter;
use Carbon\CarbonInterface;

/**
 * The exact wording of the certificate / transcript verification letters,
 * taken from SJUT's real samples (Desktop/KUSEKWA/VERIFICATION): the standard
 * letter in English and Swahili, and the longer DataFlow letter. Kept out of
 * the Blade view so each sentence can be tested, and so single vs several
 * graduates ("He completed" vs "They completed") is decided in one place.
 *
 * Every string returned is safe HTML: values that came from a person are
 * escaped here, the <sup>/<strong> tags are ours.
 */
class VerificationLetterWording
{
    // DomPDF's core fonts cannot print a curly apostrophe, so this one is straight.
    private const UNIVERSITY = "St John's University of Tanzania";

    private const ENGLISH_MONTHS = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

    private const SWAHILI_MONTHS = ['Januari', 'Februari', 'Machi', 'Aprili', 'Mei', 'Juni', 'Julai', 'Agosti', 'Septemba', 'Oktoba', 'Novemba', 'Desemba'];

    private const ENGLISH_NUMBERS = [1 => 'one', 2 => 'two', 3 => 'three', 4 => 'four', 5 => 'five', 6 => 'six', 7 => 'seven', 8 => 'eight', 9 => 'nine', 10 => 'ten'];

    private const SWAHILI_CERTIFICATES = [1 => 'cheti kimoja', 2 => 'vyeti viwili', 3 => 'vyeti vitatu', 4 => 'vyeti vinne', 5 => 'vyeti vitano', 6 => 'vyeti sita', 7 => 'vyeti saba', 8 => 'vyeti nane', 9 => 'vyeti tisa', 10 => 'vyeti kumi'];

    /**
     * @return array{
     *     subject: string,
     *     intro: string,
     *     before_table: array<int, array{number: int, html: string}>,
     *     table: array{headers: array<int, string>, rows: array<int, array<int, string>>, aligned: array<int, string>, widths: array<int, int>, small: bool}|null,
     *     after_table: array<int, array{number: int, html: string, sub?: array<int, array{label: string, html: string, children?: array<int, array{label: string, html: string}>}>}>,
     *     sign_off: string|null
     * }
     */
    public function build(VerificationLetter $letter): array
    {
        $content = match (true) {
            $letter->template === 'custom' => $this->custom($letter),
            $letter->template === 'dataflow' => $this->dataflow($letter),
            $letter->language === 'sw' => $this->swahili($letter),
            default => $this->english($letter),
        };

        return $letter->template === 'custom' ? $content : $this->withExtraParagraph($content, $letter);
    }

    /**
     * A letter written entirely by the person: paragraphs are separated by a
     * line break, the first is the unnumbered opening one and the rest are
     * numbered from 2 - the same shape as every other SJUT letter.
     */
    private function custom(VerificationLetter $letter): array
    {
        $paragraphs = collect(preg_split('/\R+/', trim((string) $letter->body_text)))
            ->map(fn ($line) => trim($line))
            ->filter()
            ->values();

        return [
            'subject' => $letter->subject ?: '',
            'intro' => e((string) $paragraphs->shift()),
            'before_table' => $paragraphs->map(fn ($text, $index) => ['number' => $index + 2, 'html' => e($text)])->all(),
            'table' => null,
            'after_table' => [],
            'sign_off' => $letter->language === 'sw' ? null : 'Kind regards',
        ];
    }

    /**
     * The optional extra paragraph goes right after the table, before the
     * closing lines ("I trust that, this Information will be helpful..."), and
     * the closing paragraphs are numbered on from it.
     */
    private function withExtraParagraph(array $content, VerificationLetter $letter): array
    {
        $extra = trim((string) $letter->extra_text);

        if ($extra !== '') {
            array_unshift($content['after_table'], ['number' => 0, 'html' => nl2br(e($extra))]);
        }

        $number = (collect($content['before_table'])->last()['number'] ?? 1) + 1;
        foreach ($content['after_table'] as $index => $paragraph) {
            $content['after_table'][$index]['number'] = $number++;
        }

        return $content;
    }

    /** A date inside a sentence: "6th July 2026" (no superscript, which DomPDF can break across lines). */
    private function inlineDate(?CarbonInterface $date, string $language): string
    {
        if (! $date) {
            return '';
        }

        return $language === 'sw'
            ? $this->date($date, 'sw')
            : $date->day.$this->ordinalSuffix($date->day).' '.self::ENGLISH_MONTHS[$date->month - 1].' '.$date->year;
    }

    /** The letter's own date at the top: "24th July 2026" (superscript) or "24 Julai 2026". */
    public function date(?CarbonInterface $date, string $language): string
    {
        if (! $date) {
            return '';
        }

        if ($language === 'sw') {
            return $date->day.' '.self::SWAHILI_MONTHS[$date->month - 1].' '.$date->year;
        }

        return '<span style="white-space: nowrap;">'.$date->day.'<sup>'.$this->ordinalSuffix($date->day).'</sup> '.self::ENGLISH_MONTHS[$date->month - 1].' '.$date->year.'</span>';
    }

    // ------------------------------------------------------------ English (standard)

    private function english(VerificationLetter $letter): array
    {
        $many = $letter->items->count() > 1;
        $documents = $this->englishDocuments($letter, $many);

        return [
            'subject' => $letter->subject ?: 'RE: DOCUMENTS VERIFICATION',
            'intro' => $this->englishIntro($letter, $many),
            'before_table' => [[
                'number' => 2,
                'html' => 'I hereby verify that, the documents presented to you by '.($many ? 'our graduates' : 'our graduate').' are genuine. '
                    .$this->pronoun($letter, 'subject').' completed studies at this University and graduated as described in the table below.',
            ]],
            'table' => $this->table($letter, ['SN', 'NAMES', $this->numberHeader($letter, 'en'), 'PROGRAMME(S)', 'GRAD YEAR'], [], [0 => 30, 2 => 78, 4 => 78]),
            'after_table' => [
                ['number' => 3, 'html' => 'Therefore, I confirm authenticity of the presented '.$this->englishPresented($letter, $many).'.'],
                ['number' => 4, 'html' => 'I trust that, this Information will be helpful to you.'],
            ],
            'sign_off' => 'Kind regards',
        ];
    }

    private function englishIntro(VerificationLetter $letter, bool $many): string
    {
        $reference = $letter->received_reference ? ' bearing reference number <strong><em>'.e($letter->received_reference).'</em></strong>' : '';
        $dated = $letter->received_date ? ' dated '.$this->inlineDate($letter->received_date, 'en') : '';
        $asked = $many ? 'graduates' : 'a graduate';

        return match ($letter->received_via) {
            'email' => 'This refers to your email correspondence, including the attached letter'.$reference.$dated
                .'. The aforementioned letter requests confirmation of the '.$this->englishDocuments($letter, $many)
                .' for '.$asked.' of '.self::UNIVERSITY.'.',
            'graduate' => 'We are writing with regard to the correspondence received from '.($many ? 'our graduates' : 'our graduate')
                .($letter->received_date ? ', dated '.$this->inlineDate($letter->received_date, 'en') : '')
                .'. '.$this->pronoun($letter, 'possessive').' letter requests verification of the academic '
                .$this->englishDocuments($letter, $many).' officially issued to '.$this->pronoun($letter, 'object')
                .' by '.self::UNIVERSITY.'.',
            default => 'This refers to your correspondence letter'.$reference.$dated
                .'. The aforementioned letter requests confirmation of the '.$this->englishDocuments($letter, $many)
                .' for '.$asked.' of '.self::UNIVERSITY.'.',
        };
    }

    // ------------------------------------------------------------ Swahili (standard)

    private function swahili(VerificationLetter $letter): array
    {
        $items = $letter->items;
        $many = $items->count() > 1;
        $count = $items->count();
        $reference = $letter->received_reference ? ', yenye namba ya kumbukumbu <strong>'.e($letter->received_reference).'</strong>' : '';
        $dated = $letter->received_date ? ' ya tarehe '.$this->inlineDate($letter->received_date, 'sw') : '';
        $received = $letter->received_via === 'email' ? 'barua pepe yako' : 'barua yako';

        $asked = $many
            ? 'uhakiki wa '.(self::SWAHILI_CERTIFICATES[$count] ?? 'vyeti '.$count).' ('.$count.') vya wahitimu wa Chuo hiki'
            : 'taarifa za uhakiki wa cheti cha mhitimu wa Chuo hiki, ndugu '.e($this->plainName($items->first()->name));

        $verified = $many
            ? 'Napenda <strong>kukuthibitishia</strong> kuwa nakala za vyeti vya wahitimu wetu vilivyoambatanishwa na barua yako ni halali na vyeti hivyo vinamilikiwa na wahitimu wetu kwa uhalali kama ilivyoainishwa kwenye jedwali lifuatalo.'
            : 'Napenda <strong>kukuthibitishia</strong> kuwa nakala ya cheti cha mhitimu wetu ulivyoambatanisha katika barua yako ni halali na kinamilikiwa na mhitimu wetu kwa uhalali baada ya kuwa amekithi vigezo vyote vya kutunukiwa '.e($items->first()->programme).' kama ilivyoainishwa kwenye jedwali lifuatalo.';

        return [
            'subject' => $letter->subject ?: ($many ? 'YAH: UHAKIKI WA VYETI' : 'YAH: UHAKIKI WA CHETI CHA TAALUMA'),
            'intro' => 'Chuo Kikuu cha St John cha Tanzania, kilipokea '.$received.$reference.$dated.' ikiomba kupatiwa '.$asked.'.',
            'before_table' => [['number' => 2, 'html' => $verified]],
            'table' => $this->table($letter, ['SN', 'JINA', $this->numberHeader($letter, 'sw'), 'Kiwango cha Elimu', 'MWAKA'], [4 => 'center'], [0 => 30, 2 => 105, 4 => 62]),
            'after_table' => [
                ['number' => 3, 'html' => 'Ni matumaini yangu kwamba, taarifa hii itakuwa msaada kwako katika kutekeleza hatua zingine kwa ajili ya '.($many ? 'wahitimu' : 'mhitimu').' wetu.'],
                ['number' => 4, 'html' => 'Nashukuru kwa ushirikiano.'],
            ],
            'sign_off' => null,
        ];
    }

    // ------------------------------------------------------------ DataFlow

    private function dataflow(VerificationLetter $letter): array
    {
        $items = $letter->items;
        $many = $items->count() > 1;
        $reference = $letter->received_reference ? ' bearing reference number <strong><em>'.e($letter->received_reference).'</em></strong>' : '';
        $dated = $letter->received_date ? ' of '.$this->inlineDate($letter->received_date, 'en') : '';
        $medium = $letter->received_via === 'letter' ? 'correspondence letter' : 'email correspondence';
        $noun = $letter->received_via === 'letter' ? 'letter' : 'email';

        $durations = $items->pluck('duration_years')->filter()->unique();
        $durationLine = $durations->count() === 1
            ? 'Duration of the programme is '.$this->englishNumber((int) $durations->first()).' ('.$durations->first().') years.'
            : 'Duration of the programme: '.$items->map(fn ($item) => e($item->barcode).' – '.($item->duration_years ? $this->englishNumber((int) $item->duration_years).' ('.$item->duration_years.') years' : 'not recorded'))->implode('; ').'.';

        $periods = $items->map(function ($item, $index) {
            $years = $item->duration_years ? $this->englishNumber((int) $item->duration_years).' academic years' : null;

            return [
                'label' => chr(97 + $index).'.',
                'html' => e($item->barcode).', from '.e($item->study_from ?: '—').' to '.e($item->study_to ?: '—').($years ? ', '.$years : ''),
            ];
        })->all();

        $confirmed = $many ? 'certificates' : 'certificate';

        return [
            'subject' => $letter->subject ?: 'RE: VERIFICATION REQUEST',
            'intro' => 'This refers to your '.$medium.$reference.$dated.'. The aforementioned '.$noun.' requests confirmation of '
                .$confirmed.' for '.($many ? 'graduates' : 'a graduate').' of '.self::UNIVERSITY.'.',
            'before_table' => [[
                'number' => 2,
                'html' => 'I confirm that the documents submitted to you by '.($many ? 'our graduates' : 'our graduate').' are authentic. '
                    .($many ? 'They graduated' : $this->pronoun($letter, 'subject').' graduated').' from '.self::UNIVERSITY.' as outlined in the table below.',
            ]],
            'table' => $this->dataflowTable($letter),
            'after_table' => [
                [
                    'number' => 3,
                    'html' => '',
                    'sub' => [
                        ['label' => '(i)', 'html' => 'I confirm that the attached '.$confirmed.' to be <strong>authentic</strong>'],
                        ['label' => '(ii)', 'html' => 'Mode of study was <strong>'.e($letter->mode_of_study ?: 'Full Time').'</strong>'],
                        ['label' => '(iii)', 'html' => $durationLine],
                        ['label' => '(iv)', 'html' => 'Period of Study is as follows:', 'children' => $periods],
                        ['label' => '(v)', 'html' => 'I also can confirm the graduation dates as shown in the Grad Year column in the table above.'],
                    ],
                ],
                ['number' => 4, 'html' => 'Consequently, I affirm the authenticity of the submitted '.$confirmed.'.'],
                ['number' => 5, 'html' => 'I believe this information will be beneficial to you.'],
            ],
            'sign_off' => 'Kind regards',
        ];
    }

    private function dataflowTable(VerificationLetter $letter): array
    {
        $rows = $letter->items->values()->map(fn ($item, $index) => [
            (string) ($index + 1),
            e($item->barcode),
            e($item->name),
            e($item->number),
            e($item->programme),
            e($item->graduation_year),
        ])->all();

        return [
            'headers' => ['SN', 'BARCODE', 'NAME', $this->numberHeader($letter, 'en'), 'PROGRAMME', 'GRAD YEAR'],
            'rows' => $rows,
            'aligned' => [5 => 'right'],
            'widths' => [0 => 28, 1 => 178, 3 => 64, 5 => 66],
            'small' => true,
        ];
    }

    // ------------------------------------------------------------ shared

    private function table(VerificationLetter $letter, array $headers, array $aligned, array $widths): array
    {
        $rows = $letter->items->values()->map(fn ($item, $index) => [
            (string) ($index + 1),
            e($item->name),
            e($item->number),
            e($item->programme),
            e($item->graduation_year),
        ])->all();

        return ['headers' => $headers, 'rows' => $rows, 'aligned' => $aligned, 'widths' => $widths, 'small' => false];
    }

    private function numberHeader(VerificationLetter $letter, string $language): string
    {
        $registration = $letter->number_type === 'registration';

        return match (true) {
            $language === 'sw' => $registration ? 'NA YA USAJILI' : 'NA YA CHETI',
            default => $registration ? 'Reg NO' : 'Cert NO',
        };
    }

    private function englishDocuments(VerificationLetter $letter, bool $many): string
    {
        $documents = $letter->documents ?? ['certificate'];
        $certificate = $many ? 'certificates' : 'certificate';

        return in_array('transcript', $documents, true)
            ? ($many ? 'certificates and transcripts' : 'certificate and transcript')
            : $certificate;
    }

    private function englishPresented(VerificationLetter $letter, bool $many): string
    {
        $documents = $letter->documents ?? ['certificate'];

        return in_array('transcript', $documents, true)
            ? ($many ? 'certificates and Transcripts' : 'certificate and Transcript')
            : ($many ? 'certificates' : 'certificate');
    }

    /** He / She / They for the graduate(s) the letter is about. */
    private function pronoun(VerificationLetter $letter, string $case): string
    {
        $items = $letter->items;
        $gender = $items->count() === 1 ? $items->first()->gender : null;

        $forms = match ($gender) {
            'M' => ['subject' => 'He', 'object' => 'him', 'possessive' => 'His'],
            'F' => ['subject' => 'She', 'object' => 'her', 'possessive' => 'Her'],
            default => $items->count() === 1
                ? ['subject' => 'He/She', 'object' => 'him/her', 'possessive' => 'His/Her']
                : ['subject' => 'They', 'object' => 'them', 'possessive' => 'Their'],
        };

        return $forms[$case];
    }

    private function englishNumber(int $number): string
    {
        return self::ENGLISH_NUMBERS[$number] ?? (string) $number;
    }

    private function ordinalSuffix(int $day): string
    {
        if ($day >= 11 && $day <= 13) {
            return 'th';
        }

        return match ($day % 10) {
            1 => 'st',
            2 => 'nd',
            3 => 'rd',
            default => 'th',
        };
    }

    /** "MANG'ANA, Agness Ernest" -> "Agness Ernest MANG'ANA", the way a Swahili sentence names a person. */
    private function plainName(string $formatted): string
    {
        if (! str_contains($formatted, ',')) {
            return $formatted;
        }

        [$surname, $given] = array_map('trim', explode(',', $formatted, 2));

        return trim($given.' '.$surname);
    }
}
