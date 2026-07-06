<?php

namespace App\Services;

class RiskScoringEngine
{
    /**
     * Hitung total skor risiko menggunakan Weighted Risk Model.
     * Mengembalikan array rincian risiko per indikator dan totalnya.
     */
    public function calculate(float $weatherRisk, float $inflationRisk, float $currencyRisk, float $sentimentRisk): array
    {
        // Bobot tiap metrik (total 100%)
        // Weather: 30%, Inflation: 20%, Currency: 10%, News Sentiment: 40% (sesuai bobot persentase di PDF)
        $wWeather = 0.30;
        $wInflation = 0.20;
        $wCurrency = 0.10;
        $wSentiment = 0.40;

        // Hitung total skor risiko terbobot
        $totalRisk = ($weatherRisk * $wWeather) +
                     ($inflationRisk * $wInflation) +
                     ($currencyRisk * $wCurrency) +
                     ($sentimentRisk * $wSentiment);

        $totalRisk = round($totalRisk);

        // Tentukan kategori level risiko
        if ($totalRisk < 30) {
            $level = 'Low Risk';
            $class = 'low';
        } elseif ($totalRisk < 60) {
            $level = 'Medium Risk';
            $class = 'medium';
        } else {
            $level = 'High Risk';
            $class = 'high';
        }

        return [
            'weather_risk' => $weatherRisk,
            'inflation_risk' => $inflationRisk,
            'currency_risk' => $currencyRisk,
            'sentiment_risk' => $sentimentRisk,
            'total_risk' => $totalRisk,
            'level' => $level,
            'class' => $class
        ];
    }
}
