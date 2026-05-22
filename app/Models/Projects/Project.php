<?php

namespace App\Models\Projects;

use App\Models\Concerns\BelongsToTenant;
use App\Models\Crm\Client;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, BelongsToTenant, SoftDeletes;

    protected $fillable = [
        'tenant_id', 'name', 'client_id', 'description', 'status',
        'starts_on', 'due_on', 'color',
    ];

    protected function casts(): array
    {
        return [
            'starts_on' => 'date',
            'due_on'    => 'date',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function columns(): HasMany
    {
        return $this->hasMany(TaskColumn::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(ProjectMember::class);
    }
}
