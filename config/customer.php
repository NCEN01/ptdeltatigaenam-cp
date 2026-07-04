<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Auto-verify customer email
    |--------------------------------------------------------------------------
    |
    | When true, newly registered customers are marked as verified immediately
    | and skip the email verification step. Handy for local development where
    | mail is only written to the log (MAIL_MAILER=log). Keep this false in
    | production so real email verification is enforced.
    |
    */
    'auto_verify' => (bool) env('CUSTOMER_AUTO_VERIFY', false),
];
