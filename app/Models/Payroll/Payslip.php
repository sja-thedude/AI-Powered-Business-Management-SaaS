<?php

namespace App\Models\Payroll;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payslip extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'payroll_run_id', 'employee_id', 'gross', 'tax',
        'deductions', 'net', 'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'gross'      => 'decimal:2',
            'tax'        => 'decimal:2',
            'deductions' => 'decimal:2',
            'net'        => 'decimal:2',
            'paid_at'    => 'datetime',
        ];
    }

    public function payrollRun(): BelongsTo
    {
        return $this->belongsTo(PayrollRun::class);
    }
}
