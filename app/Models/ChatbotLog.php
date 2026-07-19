<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Prunable;

class ChatbotLog extends Model
{
    use HasFactory, Prunable;

    protected $fillable = [
        'session_id',
        'ip_hash',
        'question',
        'answer_source',
        'matched_faq_id',
        'response_text',
        'status',
        'feedback',
        'feedback_at',
        'error_message',
    ];

    protected function casts(): array
    {
        return [
            'matched_faq_id' => 'integer',
            'feedback_at' => 'datetime',
        ];
    }

    /**
     * Relation to ChatbotFaq (BelongsTo)
     */
    public function faq()
    {
        return $this->belongsTo(ChatbotFaq::class, 'matched_faq_id');
    }

    public function prunable(): Builder
    {
        $retentionDays = max(1, (int) config('chatbot.log_retention_days', 90));

        return $this->where('created_at', '<=', now()->subDays($retentionDays));
    }
}
