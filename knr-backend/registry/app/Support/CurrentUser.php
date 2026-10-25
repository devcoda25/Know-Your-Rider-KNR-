<?php

namespace App\Support;

use Illuminate\Http\Request;

/**
 * The caller, as verified by the gateway. Identity arrives in trusted headers
 * (X-User-Id, X-User-Roles) that only the gateway can set.
 */
class CurrentUser
{
    /** @param  array<int, array{role: string, scope_type: ?string, scope_id: ?string}>  $roles */
    public function __construct(
        public readonly ?string $id,
        public readonly array $roles = [],
    ) {}

    public static function fromRequest(Request $request): self
    {
        $roles = json_decode((string) $request->header('X-User-Roles', '[]'), true);

        return new self(
            $request->header('X-User-Id') ?: null,
            is_array($roles) ? $roles : [],
        );
    }

    public function isGuest(): bool
    {
        return $this->id === null;
    }

    /** @param  string|array<int, string>  $keys */
    public function hasRole(string|array $keys): bool
    {
        $keys = (array) $keys;

        return collect($this->roles)->contains(fn ($r) => in_array($r['role'] ?? null, $keys, true));
    }

    /** IDs this user holds a role for within one scope type, e.g. all their stage ids. */
    public function scopeIds(string $scopeType): array
    {
        return collect($this->roles)
            ->where('scope_type', $scopeType)
            ->pluck('scope_id')
            ->filter()
            ->values()
            ->all();
    }

    /**
     * May this caller see the full verified profile of a rider?
     * Police and super admins: any rider. Stage roles: only riders of their own stage.
     * TODO: LC officials / municipal officers scoped by division or parish.
     */
    public function canViewFull(\App\Models\Rider $rider): bool
    {
        if ($this->hasRole(['super_admin', 'traffic_officer'])) {
            return true;
        }

        return $this->hasRole(['stage_head', 'stage_official'])
            && in_array($rider->stage_id, $this->scopeIds('stage'), true);
    }

    /** May this caller register or change riders of the given stage? */
    public function canManageStage(string $stageId): bool
    {
        return $this->hasRole('super_admin')
            || ($this->hasRole(['stage_head', 'stage_official']) && in_array($stageId, $this->scopeIds('stage'), true));
    }
}
