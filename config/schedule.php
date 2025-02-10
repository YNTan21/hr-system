<?php

return [
    // Run predictions daily at midnight
    \App\Console\Commands\PredictLeaves::class => [
        'schedule' => 'dailyAt("00:00")',
        // or for weekly: 'schedule' => 'weekly()->sundays()->at("00:00")',
    ],
]; 