<?php

return [

    'ttl_minutes' => (int) env('OTP_TTL_MINUTES', 5),

    'max_attempts' => 5,

    // log | (add real SMS drivers, e.g. africastalking)
    'sms_driver' => env('SMS_DRIVER', 'log'),

    // Dev convenience only: echo the code in the response of /otp/request.
    'debug_return' => (bool) env('OTP_DEBUG_RETURN', false),

];
