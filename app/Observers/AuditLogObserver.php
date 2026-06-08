<?php

namespace App\Observers;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class AuditLogObserver
{
    public function created(Model $model): void
    {
        $this->record($model, 'created', [], $model->getAttributes());
    }

    public function updated(Model $model): void
    {
        $this->record($model, 'updated', $model->getOriginal(), $model->getChanges());
    }

    public function deleted(Model $model): void
    {
        $this->record($model, 'deleted', $model->getOriginal(), []);
    }

    private function record(Model $model, string $event, array $oldValues, array $newValues): void
    {
        if (!Schema::hasTable('audit_logs')) {
            return;
        }

        $user = Auth::user();

        AuditLog::create([
            'auditable_type' => get_class($model),
            'auditable_id' => $model->getKey(),
            'event' => $event,
            'title' => $this->titleFor($model, $event),
            'old_values' => $this->cleanValues($oldValues),
            'new_values' => $this->cleanValues($newValues),
            'url' => request()?->fullUrl(),
            'ip_address' => request()?->ip(),
            'user_agent' => substr((string) request()?->userAgent(), 0, 500),
            'user_id' => $user?->id,
            'user_name' => $user?->email ?? session('user_email') ?? 'System',
        ]);
    }

    private function titleFor(Model $model, string $event): string
    {
        $shortName = class_basename($model);
        $name = $model->name ?? $model->itemsname ?? $model->title ?? null;

        return trim(ucfirst($event) . ' ' . $shortName . ($name ? ': ' . $name : ' #' . $model->getKey()));
    }

    private function cleanValues(array $values): array
    {
        unset($values['password'], $values['remember_token']);

        return array_slice($values, 0, 40, true);
    }
}
