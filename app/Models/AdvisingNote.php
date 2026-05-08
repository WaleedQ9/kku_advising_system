<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdvisingNote extends Model
{
    protected $fillable = [
        'student_id',
        'user_id',
        'title',
        'note_type',
        'type',
        'content',
        'follow_up_required',
    ];

    protected $casts = [
        'follow_up_required' => 'boolean',
    ];

    // ========== Relations ==========

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    /**
     * إنشاء ملاحظة وربطها تلقائياً بالطالب
     */
    public static function createNote(array $data): self
    {
        if (isset($data['note_type']) && !isset($data['type'])) {
            $data['type'] = $data['note_type'];
        }

        return self::create($data);
    }


    public function markAsFollowUp(): void
    {
        $this->update(['follow_up_required' => true]);
    }


    public function sendNotificationToStudent(): void
    {
        // TODO: implement when notification system is added
    }
}
