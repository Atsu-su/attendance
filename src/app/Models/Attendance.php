<?php

namespace App\Models;

use App\Traits\DateTimeFormatTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;
    use DateTimeFormatTrait;

    /*
    public const BF_WORK = ['bf_work' => '勤務外'];

    public static function getBfWorkKey()
    {
        return array_key_first(self::BF_WORK);
    }

    public static function getBfWorkValue()
    {
        return self::BF_WORK[self::getBfWorkKey()];
    }
    */

    public const BF_WORK = 'bf_work';
    public const ON_DUTY = 'on_duty';
    public const BREAK = 'break';
    public const OFF_DUTY = 'off_duty';

    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function stampCorrectionRequests()
    {
        return $this->hasMany(StampCorrectionRequest::class);
    }

    public function breakTimes()
    {
        return $this->hasMany(BreakTime::class);
    }

    public function getWorkStatusAttribute()
    {
        switch ($this->status) {
            case self::BF_WORK:
                return '勤務外';
            case self::ON_DUTY:
                return '勤務中';
            case self::BREAK:
                return '休憩中';
            case self::OFF_DUTY:
                return '退勤済';
            default:
                return '不明';
        }
    }
}
