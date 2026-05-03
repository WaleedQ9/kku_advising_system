<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',   // student_no في التوثيق — نُبقي student_id للتوافق
        'name_ar',
        'name_en',
        'major',        // مضاف من التوثيق
        'department_id',
        'gpa',
        'total_credits',
        'status',           // منتظم | متعثر | خريج  (عرض عربي)
        'academic_status',  // Regular | Warning      (منطق النظام)
        'advisor_id',
    ];

    protected $casts = [
        'gpa' => 'float',
    ];

    // ========== Relations ==========

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_student')
            ->withPivot('current_grade', 'absences_count')
            ->withTimestamps();
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function advisor()
    {
        return $this->belongsTo(User::class, 'advisor_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function advisingNotes()
    {
        return $this->hasMany(AdvisingNote::class);
    }

    public function riskFlags()
    {
        return $this->hasMany(RiskFlag::class);
    }

    public function dropActions()
    {
        return $this->hasMany(DropAction::class);
    }

    // ========== Business Methods (من التوثيق) ==========

    /**
     * جلب الملف الأكاديمي الكامل
     */
    public function getAcademicProfile(): array
    {
        return [
            'student_id'      => $this->student_id,
            'name'            => $this->name_ar,
            'major'           => $this->major,
            'gpa'             => $this->gpa,
            'total_credits'   => $this->total_credits,
            'academic_status' => $this->academic_status,
            'courses_count'   => $this->courses()->count(),
            'active_flags'    => $this->riskFlags()->where('is_resolved', false)->count(),
        ];
    }

    /**
     * تحقق من أهلية الطالب لحذف مادة معينة
     */
    public function checkDropEligibility(int $courseId): array
    {
        return DropAction::validatePolicy($this, $courseId);
    }

    /**
     * هل للطالب تنبيهات نشطة غير محلولة؟
     */
    public function hasRiskFlags(): bool
    {
        return $this->riskFlags()->where('is_resolved', false)->exists();
    }
}
