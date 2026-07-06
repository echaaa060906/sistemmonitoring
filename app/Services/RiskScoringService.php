<?php

namespace App\Services;

class RiskScoringService
{
    /**
     * Calculate Risk Score based on inputs
     * @param float $weatherRisk (0-100)
     * @param float $inflationRisk (0-100)
     * @param float $currencyRisk (0-100)
     * @param float $newsSentimentRisk (0-100)
     * @return array
     */
    public function calculateScore($weatherRisk, $inflationRisk, $currencyRisk, $newsSentimentRisk)
    {
        // Simple Weighted Risk Model (example weights)
        // Weather: 30%, Inflation: 20%, Currency: 10%, News: 40%
        
        $totalRisk = ($weatherRisk * 0.3) + ($inflationRisk * 0.2) + ($currencyRisk * 0.1) + ($newsSentimentRisk * 0.4);
        
        $totalRisk = round($totalRisk);
        
        if ($totalRisk < 35) {
            $class = 'Low';
        } elseif ($totalRisk <= 60) {
            $class = 'Medium';
        } else {
            $class = 'High';
        }

        return [
            'weather_risk' => round($weatherRisk),
            'inflation_risk' => round($inflationRisk),
            'currency_risk' => round($currencyRisk),
            'news_sentiment_risk' => round($newsSentimentRisk),
            'total_risk' => $totalRisk,
            'class' => $class
        ];
    }
}
