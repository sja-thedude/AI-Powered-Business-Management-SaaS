<?php

namespace App\Models\Payroll;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxRule extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'name', 'rate', 'applies_to', 'active',
    ];

    protected function casts(): array
    {
        return [
            'rate'   => 'decimal:2',
            'active' => 'boolean',
        ];
    }
}
