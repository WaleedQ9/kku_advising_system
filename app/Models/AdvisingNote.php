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
        // Normalise: always store in note_type; keep type in sync for legacy reads
        if (isset($data['type']) && !isset($data['note_type'])) {
            $data['note_type'] = $data['type'];
        }
        unset($data['type']); // type is not in fillable; write via raw update below

        $note = self::create($data);

        // Keep the legacy `type` column in sync so old queries still work
        $note->getConnection()
             ->table('advising_notes')
             ->where('id', $note->id)
             ->update(['type' => $note->note_type]);

        return $note;
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
