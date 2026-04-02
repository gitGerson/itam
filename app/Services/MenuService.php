<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Contracts\Routing\UrlGenerator;

class MenuService
{
    public function __construct(
        protected UrlGenerator $urlGenerator
    ) {
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function itemsForContext(string $context, ?User $user = null): array
    {
        return collect(config('menu.items', []))
            ->filter(fn (array $item): bool => $this->isVisible($item, $context, $user))
            ->map(fn (array $item): array => $this->resolveItem($item))
            ->values()
            ->all();
    }

    protected function isVisible(array $item, string $context, ?User $user): bool
    {
        if (($item['enabled'] ?? true) !== true) {
            return false;
        }

        $contexts = $item['contexts'] ?? [];
        if (!in_array($context, $contexts, true)) {
            return false;
        }

        $permission = $item['permission'] ?? null;

        if ($permission === null) {
            return true;
        }

        return $user?->hasPermission($permission) === true;
    }

    /**
     * @return array<string, mixed>
     */
    protected function resolveItem(array $item): array
    {
        return [
            'key' => $item['key'],
            'title' => $item['label'],
            'description' => $item['search']['description'] ?? $item['label'],
            'url' => $this->urlGenerator->route($item['route'], $item['route_params'] ?? []),
            'icon' => $item['icon'],
            'keywords' => array_values($item['search']['keywords'] ?? []),
            'permission' => $item['permission'] ?? null,
            'section' => $item['section'] ?? null,
            'active_patterns' => $item['active_patterns'] ?? [],
        ];
    }
}
