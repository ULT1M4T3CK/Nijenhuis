<?php
// components/pricing_engine.php

/**
 * Calculate the price for a boat rental.
 * 
 * @param string|array $boatInput Either a boatType ID (string) or a boat array.
 * @param int $numberOfDays Number of days for the rental.
 * @param array $boatsList Optional list of boats (needed if $boatInput is an ID).
 * @param bool $useMotor Whether the engine/motor option is selected (affects sailboat pricing).
 * @return float The calculated price.
 */
function calculateBoatPrice($boatInput, $numberOfDays, $boatsList = [], $useMotor = false) {
    $boat = null;

    // Resolve the boat object
    if (is_array($boatInput)) {
        $boat = $boatInput;
    } else {
        // Find boat by ID in the provided list
        foreach ($boatsList as $b) {
            if (($b['id'] ?? '') === $boatInput) {
                $boat = $b;
                break;
            }
        }
    }

    // If boat not found or invalid
    if (!$boat) {
        return 0.0;
    }

    // Basic Validation
    if ($numberOfDays < 1) $numberOfDays = 1;

    // Select pricing array: use pricingWithEngine if motor option selected and available
    $pricing = $boat['pricing'] ?? [];
    $pricePerDay = (float)($boat['pricePerDay'] ?? 0);
    
    if ($useMotor && !empty($boat['pricingWithEngine'])) {
        $pricing = $boat['pricingWithEngine'];
        // Use first element as pricePerDay for motor variant
        if (isset($pricing[0]) && $pricing[0] > 0) {
            $pricePerDay = (float)$pricing[0];
        }
    }

    // Pricing Logic
    if ($numberOfDays === 1) {
        return $pricePerDay;
    } elseif ($numberOfDays >= 2 && $numberOfDays <= 7) {
        // Use tiered pricing if available
        if (!empty($pricing) && isset($pricing[$numberOfDays - 1])) {
            return (float)$pricing[$numberOfDays - 1];
        }
        // Fallback to daily rate
        return $pricePerDay * $numberOfDays;
    } elseif ($numberOfDays > 7) {
        // Weekly price + extra days
        $weeklyPrice = 0;
        if (!empty($pricing) && isset($pricing[6])) {
            $weeklyPrice = (float)$pricing[6];
        } else {
            $weeklyPrice = $pricePerDay * 7;
        }
        
        if ($weeklyPrice > 0) {
            $extraDays = $numberOfDays - 7;
            $costPerExtraDay = $weeklyPrice / 7;
            return $weeklyPrice + ($extraDays * $costPerExtraDay);
        }
    }

    return 0.0;
}
?>
