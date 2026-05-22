<?php

namespace App\Models\Concerns;

use App\Models\AuditLog;
use App\Support\TenantContext;

/**
 * Records an append-only audit trail for a model's lifecycle. Add the trait
 * and (optionally) declare `protected array $auditExclude = [...]` to keep
 * volatile/secret columns out of the diff.
 */
trait Auditable
{
    public static function bootAuditable(): void
    {
        static::created(fn ($model) => $model->writeAudit('created', null, $model->auditableNew()));

        static::updated(function ($model) {
            $changes = $model->getChanges();
            unset($changes['updated_at']);
            if (empty($changes)) {
                return;
            }
            $old = array_intersect_key($model->getOriginal(), $changes);
            $model->writeAudit('updated', $model->scrub($old), $model->scrub($changes));
        });

        static::deleted(fn ($model) => $model->writeAudit('deleted', $model->auditableNew(), null));
    }

    protected function writeAudit(string $event, ?array $old, ?array $new): void
    {
        AuditLog::create([
            'tenant_id'      => $this->resolveAuditTenantId(),
            'user_id'        => auth()->id(),
            'event'          => $event,
            'auditable_type' => static::class,
            'auditable_id'   => $this->getKey(),
            'old_values'     => $old,
            'new_values'     => $new,
            'url'            => app()->runningInConsole() ? 'console' : request()->fullUrl(),
            'ip_address'     => app()->runningInConsole() ? null : request()->ip(),
            'user_agent'     => app()->runningInConsole() ? null : request()->userAgent(),
            'created_at'     => now(),
        ]);
    }

    protected function resolveAuditTenantId(): ?int
    {
        return $this->getAttribute('tenant_id') ?? app(TenantContext::class)->id();
    }

    protected function auditableNew(): array
    {
        return $this->scrub($this->getAttributes());
    }

    protected function scrub(array $values): array
    {
        $exclude = array_merge(
            ['password', 'remember_token', 'updated_at', 'created_at'],
            $this->auditExclude ?? []
        );

        return array_diff_key($values, array_flip($exclude));
    }
}
