<?php

namespace App\Models;

use App\Traits\DateTimeFormatTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestBreakTime extends Model
{
    use HasFactory;
    use DateTimeFormatTrait;

    protected $guarded = ['id'];

    public function stampCorrectionRequest()
    {
        return $this->belongsTo(StampCorrectionRequest::class);
    }
}
