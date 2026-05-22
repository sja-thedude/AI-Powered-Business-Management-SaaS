<?php

namespace App\Models\Payroll;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PayrollRun extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'period', 'run_date', 'status', 'total_gross', 'total_net',
    ];

    protected function casts(): array
    {
        return [
            'run_date'    => 'date',
            'total_gross' => 'decimal:2',
            'total_net'   => 'decimal:2',
        ];
    }

    public function payslips(): HasMany
    {
        return $this->hasMany(Payslip::class);
    }
}
