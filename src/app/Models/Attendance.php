<?php

namespace App\Models;

use App\Traits\DateTimeFormatTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;
    use DateTimeFormatTrait;

    public const BF_WORK = ['bf_work', '勤務外'];
    public const ON_DUTY = ['on_duty', '勤務中'];
    public const BREAK = ['break', '休憩中'];
    public const OFF_DUTY = ['off_duty', '退勤済'];

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
            case self::BF_WORK[0]:
                return self::BF_WORK[1];
            case self::ON_DUTY[0]:
                return self::ON_DUTY[1];
            case self::BREAK[0]:
                return self::BREAK[1];
            case self::OFF_DUTY[0]:
                return self::OFF_DUTY[1];
            default:
                return '不明';
        }
    }
}
