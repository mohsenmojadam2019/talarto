<?php
return [
'name'=>env('APP_NAME','تالارتو'),'env'=>env('APP_ENV','production'),'debug'=>(bool)env('APP_DEBUG',false),'url'=>env('APP_URL','http://localhost'),
'timezone'=>'Asia/Tehran','locale'=>'fa','fallback_locale'=>'fa','faker_locale'=>'fa_IR','key'=>env('APP_KEY'),'cipher'=>'AES-256-CBC',
'admin_email'=>env('ADMIN_EMAIL','admin@talarto.ir'),'admin_password'=>env('ADMIN_PASSWORD','ChangeMe123!')
];
