<?php

namespace App\Traits;

trait WeightConversion
{
    /**
     * Konwertuj kg na lbs
     */
    public function kgToLbs($kg)
    {
        return round($kg * 2.20462, 2);
    }

    /**
     * Konwertuj lbs na kg
     */
    public function lbsToKg($lbs)
    {
        return round($lbs / 2.20462, 2);
    }

    /**
     * Konwertuj wagę zgodnie z preferencjami użytkownika
     */
    public function convertWeight($weight, $fromUnit, $toUnit)
    {
        if ($fromUnit === $toUnit) {
            return $weight;
        }

        if ($fromUnit === 'kg' && $toUnit === 'lbs') {
            return $this->kgToLbs($weight);
        }

        if ($fromUnit === 'lbs' && $toUnit === 'kg') {
            return $this->lbsToKg($weight);
        }

        return $weight;
    }

    /**
     * Automatyczna konwersja wag w sesji treningowej
     */
    public function convertSessionWeights($userPreferredUnit)
    {
        // Konwertuj total_weight
        if ($this->weight_type !== $userPreferredUnit) {
            $this->setAttribute('converted_total_weight', 
                $this->convertWeight($this->total_weight, $this->weight_type, $userPreferredUnit)
            );
            $this->setAttribute('display_unit', $userPreferredUnit);
        } else {
            $this->setAttribute('converted_total_weight', $this->total_weight);
            $this->setAttribute('display_unit', $this->weight_type);
        }

        return $this;
    }
}