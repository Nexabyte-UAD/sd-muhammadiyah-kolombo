<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class ChatbotFaq extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'question',
        'answer',
        'keywords',
        'category',
        'is_active',
        'usage_count',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'usage_count' => 'integer',
        ];
    }

    /**
     * Scope a query to only include active FAQs.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Parse keywords string into an array of clean, lowercase words.
     */
    public function getCleanKeywordsArray(): array
    {
        if (empty($this->keywords)) {
            return [];
        }

        $keywords = explode(',', $this->keywords);
        
        $cleanKeywords = array_map(function($keyword) {
            return trim(mb_strtolower($keyword, 'UTF-8'));
        }, $keywords);

        // Remove empty keywords
        return array_values(array_filter($cleanKeywords, function($keyword) {
            return !empty($keyword);
        }));
    }

    /**
     * Relation to ChatbotLog (HasMany)
     */
    public function logs()
    {
        return $this->hasMany(ChatbotLog::class, 'matched_faq_id');
    }
}
