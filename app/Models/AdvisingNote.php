<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdvisingNote extends Model
{
    protected $fillable = [
        'student_id',
        'user_id',
        'title',              // مضاف من التوثيق
        'note_type',          // Academic | Behavioral (enum من التوثيق)
        'type',               // الحقل الأصلي — مُبقى للتوافق مع الكود القديم
        'content',
        'follow_up_required', // مضاف من التوثيق
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

    // ========== Methods (من التوثيق) ==========

    /**
     * إنشاء ملاحظة وربطها تلقائياً بالطالب
     */
    public static function createNote(array $data): self
    {
        // نزامن note_type مع type للتوافق العكسي
        if (isset($data['note_type']) && !isset($data['type'])) {
            $data['type'] = $data['note_type'];
        }

        return self::create($data);
    }

    /**
     * وسّم الملاحظة كتحتاج متابعة
     */
    public function markAsFollowUp(): void
    {
        $this->update(['follow_up_required' => true]);
    }

    /**
     * أرسل إشعار للطالب (placeholder — يُكمَل مع notification system)
     */
    public function sendNotificationToStudent(): void
    {
        // TODO: implement when notification system is added
    }
}
