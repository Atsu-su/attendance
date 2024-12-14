<?php

namespace App\Models;

use App\Traits\DateTimeFormatTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StampCorrectionRequest extends Model
{
    use HasFactory;
    use DateTimeFormatTrait;

    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    public function requestBreakTimes()
    {
        return $this->hasMany(RequestBreakTime::class);
    }

    public function getApprovalStatusAttribute()
    {
        return $this->is_approved ? '承認済' : '未承認';
    }
}
