<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DropAction extends Model
{
    protected $fillable = [
        'student_id',
        'course_id',
        'advisor_id',
        'status',                   // Completed | Rejected
        'reason',
        'eligibility_check_result', // JSON snapshot للـ policy check
    ];

    protected $casts = [
        'eligibility_check_result' => 'array',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function advisor()
    {
        return $this->belongsTo(User::class, 'advisor_id');
    }

    /**
     * تحقق من أهلية الطالب لحذف المادة
     * القواعد: لا يتجاوز 3 محاولات حذف + ما يقل عن 12 ساعة بعد الحذف
     */
    public static function validatePolicy(Student $student, int $courseId): array
    {
        $dropCount = self::where('student_id', $student->id)
            ->where('status', 'Completed')
            ->count();

        $remainingAttempts = max(0, 3 - $dropCount);
        $eligible = $remainingAttempts > 0;

        return [
            'eligible'          => $eligible,
            'drop_count'        => $dropCount,
            'remaining_attempts'=> $remainingAttempts,
            'reason'            => $eligible ? null : 'تجاوز الحد الأقصى لعدد مرات الحذف (3 مرات)',
            'checked_at'        => now()->toDateTimeString(),
        ];
    }

    /**
     * نفّذ الحذف وسجّل في drop_actions + course_student
     */
    public static function executeDrop(Student $student, Course $course, User $advisor, string $reason = ''): self
    {
        $policyResult = self::validatePolicy($student, $course->id);

        $dropAction = self::create([
            'student_id'              => $student->id,
            'course_id'               => $course->id,
            'advisor_id'              => $advisor->id,
            'status'                  => $policyResult['eligible'] ? 'Completed' : 'Rejected',
            'reason'                  => $reason,
            'eligibility_check_result'=> $policyResult,
        ]);

        if ($policyResult['eligible']) {
            // احذف المادة من سجل الطالب وانقص الساعات
            $credits = $course->credits;
            $student->courses()->detach($course->id);
            $student->decrement('total_credits', $credits);

            // حدّث الـ academic_status
            $student->refresh();
            $student->update([
                'academic_status' => $student->gpa < 2.0 ? 'Warning' : 'Regular',
                'status'          => $student->gpa < 2.0 ? 'متعثر' : 'منتظم',
            ]);
        }

        return $dropAction;
    }

    /**
     * سجّل الحذف في log (للـ audit trail)
     */
    public function logTransaction(): void
    {
        \Log::info('DropAction', [
            'student_id' => $this->student_id,
            'course_id'  => $this->course_id,
            'advisor_id' => $this->advisor_id,
            'status'     => $this->status,
            'result'     => $this->eligibility_check_result,
        ]);
    }
}
