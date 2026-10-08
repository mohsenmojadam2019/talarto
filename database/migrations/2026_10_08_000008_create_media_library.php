<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('media_assets', function(Blueprint $table) {
            $table->id();
            $table->foreignId('gallery_item_id')->nullable()->unique()->constrained('gallery_items')->nullOnDelete();
            $table->string('title', 150);
            $table->string('alt_text', 220)->nullable();
            $table->string('category', 80)->default('سالن');
            $table->string('url', 1024)->unique();
            $table->string('source', 40)->default('uploaded');
            $table->unsignedBigInteger('bytes')->default(0);
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
        // Import the existing catalog without modifying genuine customer-uploaded files.
        $items = DB::table('gallery_items')->orderBy('sort_order')->get();
        foreach($items as $entry) {
            if (!str_starts_with((string)$entry->image,'/storage/') &&
                !str_starts_with((string)$entry->image,'/assets/venue/')) continue;
            $relative = ltrim((string)$entry->image,'/');
            $path = public_path($relative);
            $known = is_file($path);
            $dimensions = $known ? @getimagesize($path) : false;
            DB::table('media_assets')->insertOrIgnore([
                'gallery_item_id'=>$entry->id,
                'title'=>$entry->title ?: 'تصویر مراسم',
                'alt_text'=>$entry->title,
                'category'=>$entry->category ?: 'سالن',
                'url'=>$entry->image,
                'source'=>str_starts_with($entry->image,'/assets/venue/')?'concept_visual':'uploaded',
                'bytes'=>$known?filesize($path):0,
                'width'=>$dimensions?$dimensions[0]:null,
                'height'=>$dimensions?$dimensions[1]:null,
                'sort_order'=>$entry->sort_order,
                'active'=>$entry->active,
                'created_at'=>now(),'updated_at'=>now(),
            ]);
        }
    }
    public function down(): void { Schema::dropIfExists('media_assets'); }
};
