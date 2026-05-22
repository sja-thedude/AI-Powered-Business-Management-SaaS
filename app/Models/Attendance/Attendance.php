<?php

namespace App\Models\Attendance;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'employee_id', 'shift_id', 'work_date', 'check_in',
        'check_out', 'status', 'source', 'worked_minutes',
    ];

    protected function casts(): array
    {
        return [
            'work_date' => 'date',
            'check_in'  => 'datetime',
            'check_out' => 'datetime',
        ];
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }
}
