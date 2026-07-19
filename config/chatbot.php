<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Chatbot FAQ Threshold
    |--------------------------------------------------------------------------
    |
    | Defines the minimum score required for a FAQ to be considered a match.
    |
    */
    'faq_threshold' => (int) env('CHATBOT_FAQ_THRESHOLD', 4),

    /* Jumlah pasangan percakapan terakhir yang dikirim sebagai konteks. */
    'history_limit' => (int) env('CHATBOT_HISTORY_LIMIT', 4),

    /* Pertanyaan dan jawaban chatbot dihapus otomatis setelah periode ini. */
    'log_retention_days' => (int) env('CHATBOT_LOG_RETENTION_DAYS', 90),
];
