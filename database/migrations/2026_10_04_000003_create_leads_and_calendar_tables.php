<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema;
return new class extends Migration{public function up():void{
 Schema::create('reservations',function(Blueprint $t){$t->id();$t->string('name');$t->string('mobile',20);$t->string('event_type',80);$t->unsignedInteger('guest_count');$t->date('event_date')->index();$t->string('date_jalali',10);$t->foreignId('package_id')->nullable()->constrained()->nullOnDelete();$t->unsignedBigInteger('budget')->nullable();$t->text('message')->nullable();$t->string('status')->default('new')->index();$t->text('admin_note')->nullable();$t->timestamps();});
 Schema::create('visit_requests',function(Blueprint $t){$t->id();$t->string('name');$t->string('mobile',20);$t->date('preferred_date');$t->string('date_jalali',10);$t->unsignedInteger('guest_count')->nullable();$t->text('message')->nullable();$t->string('status')->default('new');$t->text('admin_note')->nullable();$t->timestamps();});
 Schema::create('contact_messages',function(Blueprint $t){$t->id();$t->string('name');$t->string('mobile',20);$t->string('subject')->nullable();$t->text('message');$t->string('status')->default('new');$t->timestamps();});
 Schema::create('calendar_dates',function(Blueprint $t){$t->id();$t->date('date')->unique();$t->string('status')->default('unavailable');$t->string('note')->nullable();$t->timestamps();});
 }public function down():void{foreach(['calendar_dates','contact_messages','visit_requests','reservations'] as $table)Schema::dropIfExists($table);}};
