<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('barbecue_item_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barbecue_category_id')
                ->constrained('barbecue_categories')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->string('name');
            $table->string('unit'); // kg, l, un
            $table->decimal('default_per_adult', 6, 2)->default(0);
            $table->decimal('default_per_child', 6, 2)->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barbecue_item_types');
    }
};

