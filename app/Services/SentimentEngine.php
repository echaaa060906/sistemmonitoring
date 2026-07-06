<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class SentimentEngine
{
    /**
     * Menganalisis sentimen suatu teks menggunakan Lexicon Based Sentiment Analysis.
     * Mengembalikan array skor dan kesimpulan sentimen.
     */
    public function analyze(string $text): array
    {
        // 1. Ambil semua kamus kata dari database
        $positiveWords = DB::table('positive_words')->pluck('word')->toArray();
        $negativeWords = DB::table('negative_words')->pluck('word')->toArray();

        // 2. Bersihkan teks, ubah ke lowercase, dan pecah menjadi array kata
        $cleanText = strtolower(preg_replace('/[^a-zA-Z\s]/', '', $text));
        $words = preg_split('/\s+/', $cleanText, -1, PREG_SPLIT_NO_EMPTY);

        $positiveScore = 0;
        $negativeScore = 0;
        $matchedPositive = [];
        $matchedNegative = [];

        // 3. Bandingkan kata-kata
        foreach ($words as $word) {
            if (in_array($word, $positiveWords)) {
                $positiveScore++;
                $matchedPositive[] = $word;
            }
            if (in_array($word, $negativeWords)) {
                $negativeScore++;
                $matchedNegative[] = $word;
            }
        }

        // 4. Hitung persentase sentimen
        $totalMatches = $positiveScore + $negativeScore;
        $positivePct = 0;
        $negativePct = 0;
        $neutralPct = 100;

        if ($totalMatches > 0) {
            $positivePct = round(($positiveScore / $totalMatches) * 100);
            $negativePct = round(($negativeScore / $totalMatches) * 100);
            $neutralPct = 100 - ($positivePct + $negativePct);
        }

        // 5. Tentukan sentimen akhir
        if ($positiveScore > $negativeScore) {
            $sentiment = 'Positive';
        } elseif ($negativeScore > $positiveScore) {
            $sentiment = 'Negative';
        } else {
            $sentiment = 'Neutral';
        }

        return [
            'sentiment' => $sentiment,
            'positive_score' => $positiveScore,
            'negative_score' => $negativeScore,
            'positive_pct' => $positivePct,
            'negative_pct' => $negativePct,
            'neutral_pct' => $neutralPct,
            'matched_positive' => array_unique($matchedPositive),
            'matched_negative' => array_unique($matchedNegative),
        ];
    }
}
