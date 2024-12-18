<?php

namespace App\Models;

use App\Traits\DateTimeFormatTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BreakTime extends Model
{
    protected $guarded = ['id'];

    use HasFactory;
    use DateTimeFormatTrait;

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }
}
