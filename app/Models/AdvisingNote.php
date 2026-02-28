<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdvisingNote extends Model
{
    //
    protected $fillable = ['student_id', 'user_id', 'type', 'content'];


    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * علاقة الملاحظة بالمرشد (المستخدم)
     */
    public function user()
    {
        // نربط الحقل user_id الموجود في جدول الملاحظات بجدول المستخدمين
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * علاقة الملاحظة بالطالب
     */
}
