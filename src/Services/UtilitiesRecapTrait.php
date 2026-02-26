<?php

namespace App\Services;

trait UtilitiesRecapTrait
{
    /**
     * @param array $report
     * @return array
     */
    protected function computeCommission(array $report)
    {
        $commission = [];
        $commission['total'] = 0;
        $commission['actif'] = 0;
        $commission['passif'] = 0;

        foreach ($report as $key => $value) {
            if (in_array($key, ['parrainage', 'achat_personal'])) {
                $commission['actif'] += $value;
            }

            if (in_array($key, ['binaire', 'generationnel', 'indirect_bonus'])) {
                $commission['passif'] += $value;
            }
        }

        $commission['total'] = $commission['actif'] + $commission['passif'];

        return $commission;
    }
}
