<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
return new class extends Migration {
    public function up(): void {
        Schema::create('users', function (Blueprint $t) {
            $t->id();
            $t->string('name', 120);
            $t->string('mobile', 20)->unique();
            $t->string('email')->nullable();
            $t->string('password');
            $t->rememberToken();
            $t->timestamps();
        });
        Schema::create('addons', function (Blueprint $t) {
            $t->id();
            $t->string('category', 80)->default('خدمات جانبی');
            $t->string('title');
            $t->text('description')->nullable();
            $t->string('pricing_type', 30)->default('fixed');
            $t->unsignedBigInteger('unit_price')->default(0);
            $t->decimal('min_quantity', 10, 2)->default(1);
            $t->decimal('max_quantity', 10, 2)->nullable();
            $t->boolean('active')->default(true);
            $t->unsignedInteger('sort_order')->default(0);
            $t->json('metadata')->nullable();
            $t->timestamps();
        });
        Schema::create('pricing_rules', function (Blueprint $t) {
            $t->id();
            $t->string('title');
            $t->string('direction', 20)->default('increase');
            $t->string('rule_type', 20)->default('percentage');
            $t->unsignedBigInteger('amount')->default(0);
            $t->date('start_date')->nullable()->index();
            $t->date('end_date')->nullable()->index();
            $t->json('weekdays')->nullable();
            $t->foreignId('package_id')->nullable()->constrained()->nullOnDelete();
            $t->string('event_type', 80)->nullable();
            $t->string('time_slot', 20)->nullable();
            $t->boolean('active')->default(true);
            $t->unsignedInteger('priority')->default(100);
            $t->timestamps();
        });
        Schema::table('reservations', function (Blueprint $t) {
            $t->unsignedBigInteger('user_id')->nullable()->index()->after('id');
            $t->string('tracking_code', 24)->nullable()->unique()->after('user_id');
            $t->string('time_slot', 20)->default('night')->after('date_jalali');
            $t->unsignedInteger('child_count')->default(0)->after('guest_count');
            $t->unsignedBigInteger('base_price')->default(0);
            $t->unsignedBigInteger('guest_total')->default(0);
            $t->unsignedBigInteger('menu_total')->default(0);
            $t->unsignedBigInteger('addon_total')->default(0);
            $t->bigInteger('date_adjustment_total')->default(0);
            $t->unsignedBigInteger('discount_total')->default(0);
            $t->unsignedBigInteger('final_price')->default(0);
            $t->unsignedBigInteger('paid_amount')->default(0);
            $t->string('payment_status', 30)->default('unpaid')->index();
            $t->json('price_snapshot')->nullable();
            $t->timestamp('hold_expires_at')->nullable()->index();
            $t->timestamp('confirmed_at')->nullable();
        });
        Schema::create('reservation_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('reservation_id')->constrained()->cascadeOnDelete();
            $t->string('item_type', 30)->index();
            $t->unsignedBigInteger('item_id')->nullable();
            $t->string('title_snapshot');
            $t->string('pricing_type', 30)->default('fixed');
            $t->decimal('quantity', 12, 2)->default(1);
            $t->bigInteger('unit_price')->default(0);
            $t->bigInteger('total_price')->default(0);
            $t->json('metadata')->nullable();
            $t->timestamps();
        });
        Schema::create('reservation_quotes', function (Blueprint $t) {
            $t->id();
            $t->foreignId('reservation_id')->constrained()->cascadeOnDelete();
            $t->unsignedInteger('version')->default(1);
            $t->unsignedBigInteger('subtotal')->default(0);
            $t->bigInteger('adjustment_total')->default(0);
            $t->unsignedBigInteger('discount_total')->default(0);
            $t->unsignedBigInteger('total')->default(0);
            $t->string('status', 20)->default('issued')->index();
            $t->json('snapshot')->nullable();
            $t->timestamp('issued_at')->nullable();
            $t->timestamp('accepted_at')->nullable();
            $t->timestamps();
            $t->unique(['reservation_id','version']);
        });
        Schema::create('payments', function (Blueprint $t) {
            $t->id();
            $t->foreignId('reservation_id')->constrained()->cascadeOnDelete();
            $t->unsignedBigInteger('user_id')->nullable()->index();
            $t->unsignedBigInteger('amount');
            $t->string('method', 40)->default('manual');
            $t->string('status', 20)->default('pending')->index();
            $t->string('reference')->nullable();
            $t->timestamp('paid_at')->nullable();
            $t->text('note')->nullable();
            $t->timestamps();
        });
        Schema::create('calendar_holds', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('user_id')->index();
            $t->foreignId('reservation_id')->nullable()->constrained()->cascadeOnDelete();
            $t->date('date')->index();
            $t->string('time_slot', 20)->default('night');
            $t->timestamp('expires_at')->index();
            $t->timestamps();
            $t->unique(['date','time_slot']);
        });
        Schema::create('activity_logs', function (Blueprint $t) {
            $t->id();
            $t->foreignId('reservation_id')->nullable()->constrained()->cascadeOnDelete();
            $t->unsignedBigInteger('user_id')->nullable()->index();
            $t->string('actor_type', 30)->default('system');
            $t->string('action', 80);
            $t->json('payload')->nullable();
            $t->timestamps();
        });
        $now=now();
        DB::table('addons')->insert([
            ['category'=>'دکور و دیزاین','title'=>'گل‌آرایی VIP','description'=>'ارتقای گل‌آرایی جایگاه و میزهای اصلی.','pricing_type'=>'fixed','unit_price'=>18000000,'min_quantity'=>1,'max_quantity'=>null,'active'=>1,'sort_order'=>10,'created_at'=>$now,'updated_at'=>$now],
            ['category'=>'دکور و دیزاین','title'=>'استیج ویژه','description'=>'استیج اختصاصی متناسب با تم مراسم.','pricing_type'=>'fixed','unit_price'=>12000000,'min_quantity'=>1,'max_quantity'=>null,'active'=>1,'sort_order'=>20,'created_at'=>$now,'updated_at'=>$now],
            ['category'=>'نور و صدا','title'=>'نورپردازی تکمیلی','description'=>'نورپردازی دکوراتیو و افکت‌های تکمیلی سالن.','pricing_type'=>'fixed','unit_price'=>15000000,'min_quantity'=>1,'max_quantity'=>null,'active'=>1,'sort_order'=>30,'created_at'=>$now,'updated_at'=>$now],
            ['category'=>'پذیرایی','title'=>'پکیج میوه و شیرینی ویژه','description'=>'ارتقای پذیرایی برای هر مهمان.','pricing_type'=>'per_guest','unit_price'=>180000,'min_quantity'=>1,'max_quantity'=>null,'active'=>1,'sort_order'=>40,'created_at'=>$now,'updated_at'=>$now],
            ['category'=>'مراسم','title'=>'سفره عقد تشریفاتی','description'=>'سفره عقد کامل با چیدمان ویژه.','pricing_type'=>'fixed','unit_price'=>20000000,'min_quantity'=>1,'max_quantity'=>null,'active'=>1,'sort_order'=>50,'created_at'=>$now,'updated_at'=>$now],
            ['category'=>'مراسم','title'=>'ساعت اضافه مراسم','description'=>'تمدید زمان اجرای مراسم.','pricing_type'=>'per_hour','unit_price'=>8000000,'min_quantity'=>1,'max_quantity'=>4,'active'=>1,'sort_order'=>60,'created_at'=>$now,'updated_at'=>$now],
        ]);
        DB::table('pricing_rules')->insert([
            ['title'=>'پنجشنبه','direction'=>'increase','rule_type'=>'percentage','amount'=>15,'weekdays'=>json_encode([4]),'active'=>1,'priority'=>100,'created_at'=>$now,'updated_at'=>$now],
            ['title'=>'جمعه','direction'=>'increase','rule_type'=>'percentage','amount'=>20,'weekdays'=>json_encode([5]),'active'=>1,'priority'=>110,'created_at'=>$now,'updated_at'=>$now],
        ]);
    }
    public function down(): void {
        foreach (['activity_logs','calendar_holds','payments','reservation_quotes','reservation_items','pricing_rules','addons'] as $table) Schema::dropIfExists($table);
        Schema::table('reservations', function (Blueprint $t) {
            $t->dropColumn(['user_id','tracking_code','time_slot','child_count','base_price','guest_total','menu_total','addon_total','date_adjustment_total','discount_total','final_price','paid_amount','payment_status','price_snapshot','hold_expires_at','confirmed_at']);
        });
        Schema::dropIfExists('users');
    }
};
