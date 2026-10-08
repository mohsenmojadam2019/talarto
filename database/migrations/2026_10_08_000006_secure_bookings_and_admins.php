<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('calendar_occupancies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->unique()->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->string('time_slot', 20);
            $table->timestamps();
            $table->unique(['date', 'time_slot']);
        });
        Schema::create('admin_users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('email')->unique();
            $table->string('password');
            $table->boolean('active')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });
        // Fail loudly on conflicting historical reservations: never silently discard a paid booking.
        foreach (DB::table('reservations')->whereIn('status', ['confirmed', 'done'])->orderBy('id')->get() as $reservation) {
            DB::table('calendar_occupancies')->insert([
                'reservation_id' => $reservation->id,
                'date' => $reservation->event_date,
                'time_slot' => $reservation->time_slot,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        Schema::table('payments', function (Blueprint $table) {
            $table->unique(['method', 'reference'], 'payments_method_reference_unique');
        });
    }
    public function down(): void {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropUnique('payments_method_reference_unique');
        });
        Schema::dropIfExists('admin_users');
        Schema::dropIfExists('calendar_occupancies');
    }
};
