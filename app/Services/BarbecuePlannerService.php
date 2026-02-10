<?php

namespace App\Services;

use App\Models\BarbecueItemType;
use Illuminate\Support\Collection;

class BarbecuePlannerService
{
    public function plan(int $men, int $women, int $children, ?array $selectedTypeIds = null): array
    {
        $query = BarbecueItemType::query()->where('active', true)->with('category');
        if ($selectedTypeIds && count($selectedTypeIds) > 0) {
            $query->whereIn('id', $selectedTypeIds);
        }

        /** @var Collection<int, BarbecueItemType> $types */
        $types = $query->get();

        $items = [];
        $adults = $men + $women;
        $sideSelected = false;
        if ($selectedTypeIds && count($selectedTypeIds) > 0) {
            foreach ($types as $t) {
                if ($t->category->slug === 'side') {
                    $sideSelected = true;
                    break;
                }
            }
        }
        foreach ($types as $type) {
            $cat = $type->category->slug;
            $qty = 0.0;
            if ($cat === 'meat') {
                $qty = ($men * 0.35) + ($women * 0.25) + ($children * 0.15);
                if (!$sideSelected) {
                    $qty += ($men + $women + $children) * 0.10;
                }
            } elseif ($cat === 'beer') {
                $qty = ($men * 1.5) + ($women * 1.0) + ($children * 0.0);
            } elseif ($cat === 'soda') {
                $qty = ($men * 0.5) + ($women * 1.0) + ($children * 1.0);
            } elseif ($cat === 'side') {
                if (mb_strtolower($type->name) === 'pão de alho') {
                    $qty = ($men * 2) + ($women * 1) + ($children * 1);
                } else {
                    $qty = ($type->default_per_adult * $adults) + ($type->default_per_child * $children);
                }
            }
            $items[] = [
                'id' => $type->id,
                'name' => $type->name,
                'unit' => $type->unit,
                'category_slug' => $cat,
                'category_name' => $type->category->name,
                'qty_raw' => $qty,
            ];
        }

        // Ajuste de proporção para carnes bovinas
        $meats = array_values(array_filter($items, fn ($i) => $i['category_slug'] === 'meat'));
        $totalMeat = array_sum(array_map(fn ($i) => $i['qty_raw'], $meats));
        $bovinaIndex = null;
        foreach ($meats as $idx => $m) {
            if (mb_strtolower($m['name']) === 'bovina') {
                $bovinaIndex = $idx;
                break;
            }
        }
        if ($bovinaIndex !== null && $totalMeat > 0) {
            if (count($meats) === 2) {
                // 70% bovina, 30% outro
                $meats[$bovinaIndex]['qty_raw'] = 0.70 * $totalMeat;
                $otherIndex = $bovinaIndex === 0 ? 1 : 0;
                $meats[$otherIndex]['qty_raw'] = 0.30 * $totalMeat;
            } else {
                // Garantir pelo menos 50% para bovina
                $currentBovina = $meats[$bovinaIndex]['qty_raw'];
                $minBovina = 0.50 * $totalMeat;
                if ($currentBovina < $minBovina) {
                    $otherCurrentTotal = $totalMeat - $currentBovina;
                    $meats[$bovinaIndex]['qty_raw'] = $minBovina;
                    $otherTargetTotal = $totalMeat - $minBovina;
                    if ($otherCurrentTotal > 0) {
                        $scale = $otherTargetTotal / $otherCurrentTotal;
                        foreach ($meats as $idx => $m) {
                            if ($idx !== $bovinaIndex) {
                                $meats[$idx]['qty_raw'] = $m['qty_raw'] * $scale;
                            }
                        }
                    } else {
                        // apenas bovina
                        foreach ($meats as $idx => $m) {
                            if ($idx !== $bovinaIndex) {
                                $meats[$idx]['qty_raw'] = 0.0;
                            }
                        }
                    }
                }
            }
            // Atualiza itens com valores ajustados
            // Mapear pelo id para aplicar nos itens originais
            $byId = [];
            foreach ($meats as $m) {
                $byId[$m['id']] = $m['qty_raw'];
            }
            foreach ($items as &$i) {
                if ($i['category_slug'] === 'meat' && isset($byId[$i['id']])) {
                    $i['qty_raw'] = $byId[$i['id']];
                }
            }
            unset($i);
        }

        // Agrupar e normalizar
        $grouped = [];
        foreach ($items as $i) {
            $catSlug = $i['category_slug'];
            if (!isset($grouped[$catSlug])) {
                $grouped[$catSlug] = [
                    'category' => $i['category_name'],
                    'items' => [],
                ];
            }
            $grouped[$catSlug]['items'][] = [
                'id' => $i['id'],
                'name' => $i['name'],
                'unit' => $i['unit'],
                'quantity' => $this->normalizeQuantity($i['qty_raw'], $i['unit']),
            ];
        }

        return [
            'men' => $men,
            'women' => $women,
            'children' => $children,
            'groups' => $grouped,
        ];
    }

    private function normalizeQuantity(float $qty, string $unit): float|int
    {
        // Arredondamentos simples por tipo de unidade
        switch ($unit) {
            case 'kg':
                return round($qty, 2);
            case 'l':
                return round($qty, 2);
            case 'un':
                return (int) ceil($qty);
            default:
                return round($qty, 2);
        }
    }
}
