<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiskFlag extends Model
{
    protected $fillable = [
        'student_id',
        'type',       // Low_GPA | High_Absence
        'severity',   // High | Medium
        'is_resolved',
    ];

    protected $casts = [
        'is_resolved' => 'boolean',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * احسب الـ severity بناءً على GPA أو عدد الغيابات
     */
    public static function calculateSeverity(float $gpa, int $absences): string
    {
        if ($gpa < 1.5 || $absences >= 6) {
            return 'High';
        }
        return 'Medium';
    }

    /**
     * حدّث أو أنشئ flag للطالب
     */
    public static function triggerAlert(Student $student): void
    {
        // Low GPA flag
        if ($student->gpa < 2.0 && $student->total_credits > 0) {
            self::updateOrCreate(
                ['student_id' => $student->id, 'type' => 'Low_GPA', 'is_resolved' => false],
                ['severity' => self::calculateSeverity($student->gpa, 0)]
            );
        }

        // High Absence flag — نجمع مجموع الغيابات من pivot
        $totalAbsences = $student->courses()->sum('absences_count');
        if ($totalAbsences >= 4) {
            self::updateOrCreate(
                ['student_id' => $student->id, 'type' => 'High_Absence', 'is_resolved' => false],
                ['severity' => self::calculateSeverity($student->gpa, $totalAbsences)]
            );
        }
    }

    /**
     * حل الـ flag وربطه بملاحظة إرشادية
     */
    public function resolve(string $advisorNote = ''): bool
    {
        return $this->update(['is_resolved' => true]);
    }
}
