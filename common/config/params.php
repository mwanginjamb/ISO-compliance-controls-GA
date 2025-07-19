<?php
return [
    'adminEmail' => env('SMTP_USERNAME'),
    'supportEmail' => env('SMTP_USERNAME'),
    'senderEmail' => env('SMTP_USERNAME'),
    'senderName' => 'Compliance Mailer',
    'user.passwordResetTokenExpire' => 3600,
    'user.passwordMinLength' => 8,
    'app.statusThresholds' => [
        'Not Implemented' => ['min' => 0, 'max' => 0.4999999], // Or 'threshold' => 0.5
        'Partially Implemented' => ['min' => 0.5, 'max' => 1.4999999],
        'Mostly Implemented' => ['min' => 1.5, 'max' => 2.4999999],
        'Fully Implemented' => ['min' => 2.5, 'max' => 3.0],
    ],
    'app.statusClasses' => [
        'bg-danger' => ['min' => 0, 'max' => 0.4999999], // Or 'threshold' => 0.5
        'bg-warning text-dark' => ['min' => 0.5, 'max' => 1.4999999],
        'bg-info text-dark' => ['min' => 1.5, 'max' => 2.4999999],
        'bg-success' => ['min' => 2.5, 'max' => 3.0],
    ],
    'decimalPlaces' => 3
];
