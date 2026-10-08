<?php

use App\Models\{AdminUser,SiteSetting};
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;

Artisan::command('talarto:admin-create {email}', function (string $email) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $this->error('Invalid email address.');
        return 1;
    }
    $name = $this->ask('Admin name', 'Talarto Admin');
    $password = $this->secret('Choose a strong password (at least 12 characters)');
    if (!is_string($password) || strlen($password) < 12) {
        $this->error('Password must contain at least 12 characters.');
        return 1;
    }
    AdminUser::updateOrCreate(['email' => strtolower($email)], [
        'name' => $name, 'password' => Hash::make($password), 'active' => true,
    ]);
    $this->info('Administrator account ready.');
    return 0;
})->purpose('Create or rotate a hashed administrator account');

Artisan::command('talarto:seed-demo-if-empty', function () {
    if (SiteSetting::query()->exists()) {
        $this->info('Existing venue settings retained, skipping demo seed.');
        return 0;
    }
    Artisan::call('db:seed', ['--force' => true]);
    $this->info('Demo data seeded once. Replace before production.');
    return 0;
})->purpose('Seed demo venue only if the site has no settings');
