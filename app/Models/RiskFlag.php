<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiskFlag extends Model
{
    protected $fillable = [
        'student_id',
        'type',
        'severity',
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
     */
    public static function calculateSeverity(float $gpa, int $absences): string
    {
        if ($gpa < 1.5 || $absences >= 6) {
            return 'High';
        }
        return 'Medium';
    }

    /**
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

        $totalAbsences = $student->courses()->sum('absences_count');
        if ($totalAbsences >= 4) {
            self::updateOrCreate(
                ['student_id' => $student->id, 'type' => 'High_Absence', 'is_resolved' => false],
                ['severity' => self::calculateSeverity($student->gpa, $totalAbsences)]
            );
        }
    }

    /**
     */
    public function resolve(string $advisorNote = ''): bool
    {
        return $this->update(['is_resolved' => true]);
    }
}
