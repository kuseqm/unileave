<?php

namespace App\Http\Controllers;

use App\Models\Campus;
use App\Models\GraduationCeremony;
use App\Models\StudentGraduation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Read-only counts of graduates: by year, faculty, programme, campus and
 * study level, plus a programme-by-year grid. Only graduates with a
 * graduation year are counted, and only the signed-in user's own
 * university - the same scope as the Graduates list.
 */
class StudentStatisticsController extends Controller
{
    public const MATRIX_METRICS = ['total' => 'Total', 'females' => 'Females', 'males' => 'Males'];

    private const COUNTS = "COUNT(*) AS total, SUM(student_graduations.gender = 'M') AS males, SUM(student_graduations.gender = 'F') AS females";

    public function index(Request $request)
    {
        $university = auth()->user()->university;

        $campuses = Campus::where('university_id', $university->id)->orderBy('name')->get(['id', 'name']);
        $years = GraduationCeremony::orderByDesc('year')->pluck('year');

        $campusId = $campuses->contains('id', $request->integer('campus_id')) ? $request->integer('campus_id') : null;
        $year = $years->contains($request->integer('year')) ? $request->integer('year') : null;
        $metric = array_key_exists($request->query('metric'), self::MATRIX_METRICS) ? $request->query('metric') : 'total';

        $base = fn (): Builder => StudentGraduation::query()
            ->where('student_graduations.university_id', $university->id)
            ->where('student_graduations.is_graduated', true)
            ->whereNotNull('student_graduations.graduation_year')
            ->when($campusId, fn ($query) => $query->where('student_graduations.campus_id', $campusId))
            ->when($year, fn ($query) => $query->where('student_graduations.graduation_year', $year));

        $totals = $base()->selectRaw(self::COUNTS)->first();

        $byYear = $base()
            ->selectRaw('student_graduations.graduation_year AS label, '.self::COUNTS)
            ->groupBy('student_graduations.graduation_year')
            ->orderByDesc('student_graduations.graduation_year')
            ->get();

        $byFaculty = $base()
            ->join('programs', 'programs.id', '=', 'student_graduations.program_id')
            ->join('schools', 'schools.id', '=', 'programs.school_id')
            ->selectRaw('schools.code AS label, schools.name AS sub, '.self::COUNTS)
            ->groupBy('schools.id', 'schools.code', 'schools.name')
            ->orderByDesc('total')
            ->orderBy('label')
            ->get();

        $byProgramme = $base()
            ->join('programs', 'programs.id', '=', 'student_graduations.program_id')
            ->join('schools', 'schools.id', '=', 'programs.school_id')
            ->selectRaw('programs.name AS label, schools.code AS sub, '.self::COUNTS)
            ->groupBy('programs.id', 'programs.name', 'schools.code')
            ->orderByDesc('total')
            ->orderBy('label')
            ->get();

        $byCampus = $base()
            ->join('campuses', 'campuses.id', '=', 'student_graduations.campus_id')
            ->selectRaw('campuses.name AS label, NULL AS sub, '.self::COUNTS)
            ->groupBy('campuses.id', 'campuses.name')
            ->orderByDesc('total')
            ->orderBy('label')
            ->get();

        $byLevel = $base()
            ->join('programs', 'programs.id', '=', 'student_graduations.program_id')
            ->selectRaw("COALESCE(programs.study_level, 'Not set') AS label, NULL AS sub, ".self::COUNTS)
            ->groupBy('programs.study_level')
            ->orderByDesc('total')
            ->orderBy('label')
            ->get();

        [$matrixYears, $matrix] = $this->programmeByYear($university->id, $campusId, $year, $metric);

        return view('students.statistics', [
            'totals' => $totals,
            'byYear' => $byYear,
            'byFaculty' => $byFaculty,
            'byProgramme' => $byProgramme,
            'byCampus' => $byCampus,
            'byLevel' => $byLevel,
            'matrixYears' => $matrixYears,
            'matrix' => $matrix,
            'metric' => $metric,
            'metrics' => self::MATRIX_METRICS,
            'campuses' => $campuses,
            'years' => $years,
            'campusId' => $campusId,
            'year' => $year,
        ]);
    }

    public function programGender(Request $request)
    {
        $university = auth()->user()->university;

        $campuses = Campus::where('university_id', $university->id)->orderBy('name')->get(['id', 'name']);
        $years = GraduationCeremony::orderByDesc('year')->pluck('year');

        $campusId = $campuses->contains('id', $request->integer('campus_id')) ? $request->integer('campus_id') : null;
        $year = $years->contains($request->integer('year')) ? $request->integer('year') : null;

        $base = fn (): Builder => StudentGraduation::query()
            ->where('student_graduations.university_id', $university->id)
            ->where('student_graduations.is_graduated', true)
            ->whereNotNull('student_graduations.graduation_year')
            ->when($campusId, fn ($query) => $query->where('student_graduations.campus_id', $campusId))
            ->when($year, fn ($query) => $query->where('student_graduations.graduation_year', $year));

        $byProgramme = $base()
            ->join('programs', 'programs.id', '=', 'student_graduations.program_id')
            ->join('schools', 'schools.id', '=', 'programs.school_id')
            ->selectRaw('COALESCE(programs.code, programs.name) AS label, programs.name AS full_name, schools.code AS sub, '.self::COUNTS)
            ->groupBy('programs.id', 'programs.code', 'programs.name', 'schools.code')
            ->orderByDesc('total')
            ->orderBy('label')
            ->get();

        $totals = $base()->selectRaw(self::COUNTS)->first();

        return view('students.statistics.programs', [
            'byProgramme' => $byProgramme,
            'totals' => $totals,
            'campuses' => $campuses,
            'years' => $years,
            'campusId' => $campusId,
            'year' => $year,
        ]);
    }

    public function programCharts(Request $request)
    {
        $university = auth()->user()->university;

        $campuses = Campus::where('university_id', $university->id)->orderBy('name')->get(['id', 'name']);
        $years = GraduationCeremony::orderByDesc('year')->pluck('year');

        $campusId = $campuses->contains('id', $request->integer('campus_id')) ? $request->integer('campus_id') : null;
        $year = $years->contains($request->integer('year')) ? $request->integer('year') : null;

        $base = fn (): Builder => StudentGraduation::query()
            ->where('student_graduations.university_id', $university->id)
            ->where('student_graduations.is_graduated', true)
            ->whereNotNull('student_graduations.graduation_year')
            ->when($campusId, fn ($query) => $query->where('student_graduations.campus_id', $campusId))
            ->when($year, fn ($query) => $query->where('student_graduations.graduation_year', $year));

        $rows = $base()
            ->join('programs', 'programs.id', '=', 'student_graduations.program_id')
            ->join('schools', 'schools.id', '=', 'programs.school_id')
            ->selectRaw('programs.id AS program_id, COALESCE(programs.code, programs.name) AS label, programs.name AS full_name, schools.code AS sub, student_graduations.graduation_year AS year, COUNT(*) AS total, SUM(student_graduations.gender = \'M\') AS males, SUM(student_graduations.gender = \'F\') AS females')
            ->groupBy('programs.id', 'programs.code', 'programs.name', 'schools.code', 'student_graduations.graduation_year')
            ->orderBy('label')
            ->orderBy('year')
            ->get();

        $programs = $rows->groupBy('program_id')->map(function (Collection $programmeRows) {
            $years = $programmeRows->pluck('year')->sort()->values()->map(fn ($y) => (int) $y);

            return [
                'label' => $programmeRows->first()->label,
                'full_name' => $programmeRows->first()->full_name,
                'sub' => $programmeRows->first()->sub,
                'total' => $programmeRows->sum('total'),
                'years' => $years,
                'males' => $programmeRows->mapWithKeys(fn ($r) => [(int) $r->year => (int) $r->males])->all(),
                'females' => $programmeRows->mapWithKeys(fn ($r) => [(int) $r->year => (int) $r->females])->all(),
            ];
        })->sortByDesc('total')->values();

        $totals = $base()->selectRaw(self::COUNTS)->first();

        return view('students.statistics.program-charts', [
            'programs' => $programs,
            'totals' => $totals,
            'campuses' => $campuses,
            'years' => $years,
            'campusId' => $campusId,
            'year' => $year,
        ]);
    }

    /**
     * @return array{0: Collection<int, int>, 1: Collection<int, array{label: string, cells: array<int, int>, total: int}>}
     */
    private function programmeByYear(int $universityId, ?int $campusId, ?int $year, string $metric): array
    {
        $rows = StudentGraduation::query()
            ->where('student_graduations.university_id', $universityId)
            ->where('student_graduations.is_graduated', true)
            ->whereNotNull('student_graduations.graduation_year')
            ->when($campusId, fn ($query) => $query->where('student_graduations.campus_id', $campusId))
            ->when($year, fn ($query) => $query->where('student_graduations.graduation_year', $year))
            ->join('programs', 'programs.id', '=', 'student_graduations.program_id')
            ->selectRaw('programs.id AS program_id, programs.name AS label, student_graduations.graduation_year AS year, COUNT(*) AS total, SUM(student_graduations.gender = \'M\') AS males, SUM(student_graduations.gender = \'F\') AS females')
            ->groupBy('programs.id', 'programs.name', 'student_graduations.graduation_year')
            ->get();

        $years = $rows->pluck('year')->unique()->sort()->values();

        $matrix = $rows->groupBy('program_id')->map(function (Collection $programmeRows) use ($metric) {
            $cells = $programmeRows->mapWithKeys(fn ($row) => [(int) $row->year => (int) $row->{$metric}])->all();

            return ['label' => $programmeRows->first()->label, 'cells' => $cells, 'total' => array_sum($cells)];
        })->sortByDesc('total')->values();

        return [$years, $matrix];
    }
}
