<?php
return [
 'name'=>env('APP_NAME','تالارتو'), 'env'=>env('APP_ENV','production'), 'debug'=>(bool)env('APP_DEBUG',false),
 'url'=>env('APP_URL','http://localhost'), 'timezone'=>env('APP_TIMEZONE','Asia/Tehran'), 'locale'=>env('APP_LOCALE','fa'),
 'fallback_locale'=>env('APP_FALLBACK_LOCALE','fa'), 'faker_locale'=>env('APP_FAKER_LOCALE','fa_IR'),
 'cipher'=>'AES-256-CBC', 'key'=>env('APP_KEY'), 'previous_keys'=>array_filter(explode(',', env('APP_PREVIOUS_KEYS',''))),
 'lead_email'=>env('LEAD_EMAIL','')
];
