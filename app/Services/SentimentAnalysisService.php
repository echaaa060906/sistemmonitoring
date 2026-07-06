<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class SentimentAnalysisService
{
    protected $positiveWords = [];
    protected $negativeWords = [];

    public function __construct()
    {
        $this->loadDictionaries();
    }

    protected function loadDictionaries()
    {
        // For simplicity, we can load them once and cache or just query
        $this->positiveWords = DB::table('positive_words')->pluck('word')->map(fn($w) => strtolower($w))->toArray();
        $this->negativeWords = DB::table('negative_words')->pluck('word')->map(fn($w) => strtolower($w))->toArray();
        
        // If empty, let's seed some default words just in case
        if (empty($this->positiveWords)) {
            $this->positiveWords = ['growth', 'increase', 'profit', 'stable', 'improve', 'boom', 'surge', 'gains', 'recovery', 'uptrend', 'positive', 'good', 'success', 'expand'];
        }
        if (empty($this->negativeWords)) {
            $this->negativeWords = ['war', 'crisis', 'inflation', 'delay', 'disaster', 'crash', 'slump', 'decline', 'drop', 'loss', 'recession', 'negative', 'bad', 'fail', 'conflict', 'strike'];
        }
    }

    public function analyze($text)
    {
        $positiveScore = 0;
        $negativeScore = 0;
        
        // Clean and tokenize text
        $text = strtolower($text);
        $text = preg_replace('/[^a-z0-9\s]/', '', $text);
        $words = explode(' ', $text);
        
        $matchedPos = [];
        $matchedNeg = [];

        foreach ($words as $word) {
            $word = trim($word);
            if (empty($word)) continue;
            
            if (in_array($word, $this->positiveWords)) {
                $positiveScore++;
                $matchedPos[] = $word;
            }
            if (in_array($word, $this->negativeWords)) {
                $negativeScore++;
                $matchedNeg[] = $word;
            }
        }
        
        $totalMatches = $positiveScore + $negativeScore;
        
        if ($totalMatches == 0) {
            $sentiment = 'Neutral';
        } else {
            if ($positiveScore > $negativeScore) {
                $sentiment = 'Positive';
            } elseif ($negativeScore > $positiveScore) {
                $sentiment = 'Negative';
            } else {
                $sentiment = 'Neutral';
            }
        }
        
        $positivePct = $totalMatches > 0 ? round(($positiveScore / $totalMatches) * 100) : 0;
        
        return [
            'sentiment' => $sentiment,
            'positive_score' => $positiveScore,
            'negative_score' => $negativeScore,
            'positive_pct' => $positivePct,
            'matched_pos' => array_unique($matchedPos),
            'matched_neg' => array_unique($matchedNeg)
        ];
    }
}
