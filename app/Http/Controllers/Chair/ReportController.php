<?php

namespace App\Http\Controllers\Chair;

use App\Http\Controllers\Controller;
use App\Models\AdvisingNote;
use App\Models\RiskFlag;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function print(Request $request)
    {
        $deptId  = auth()->user()->department_id;
        $data    = $this->getReportData($request, $deptId);

        return view('chair.report-print', array_merge($data, [
            'filters'  => $request->only('advisor_id', 'major', 'status'),
            'generatedAt' => now()->format('Y-m-d H:i'),
        ]));
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $deptId = auth()->user()->department_id;
        $data   = $this->getReportData($request, $deptId);

        $filename = 'department_report_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($data) {
            $handle = fopen('php://output', 'w');
            // BOM for Excel Arabic support
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, ['رقم الطالب', 'الاسم', 'التخصص', 'المعدل', 'الحالة', 'المرشد', 'الإنذارات النشطة', 'ملاحظات الإرشاد']);

            foreach ($data['students'] as $student) {
                fputcsv($handle, [
                    $student->student_id,
                    $student->name_ar,
                    $student->major ?? '—',
                    $student->gpa,
                    $student->status,
                    $student->advisor?->name ?? '—',
                    $student->active_flags_count,
                    $student->advising_notes_count,
                ]);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function getReportData(Request $request, int $deptId): array
    {
        $query = Student::where('department_id', $deptId)
            ->with(['advisor', 'riskFlags' => fn($q) => $q->where('is_resolved', false)])
            ->withCount([
                'riskFlags as active_flags_count' => fn($q) => $q->where('is_resolved', false),
                'advisingNotes as advising_notes_count',
            ]);

        if ($request->filled('advisor_id')) {
            $query->where('advisor_id', $request->advisor_id);
        }

        if ($request->filled('major')) {
            $query->where('major', $request->major);
        }

        if ($request->filled('status')) {
            $query->where('academic_status', $request->status);
        }

        $students = $query->orderBy('gpa')->get();

        $advisors = User::role('advisor')
            ->where('department_id', $deptId)
            ->get();

        $majors = Student::where('department_id', $deptId)
            ->whereNotNull('major')
            ->distinct()
            ->pluck('major');

        $summary = [
            'total'    => $students->count(),
            'warning'  => $students->where('academic_status', 'Warning')->count(),
            'regular'  => $students->where('academic_status', 'Regular')->count(),
            'avg_gpa'  => round($students->avg('gpa'), 2),
            'flags'    => $students->sum('active_flags_count'),
        ];

        return compact('students', 'advisors', 'majors', 'summary');
    }
}
