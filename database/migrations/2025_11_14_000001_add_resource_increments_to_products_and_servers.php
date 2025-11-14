<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedInteger('player_slots')->default(0);
            $table->unsignedInteger('slot_increment_step')->default(0);
            $table->decimal('slot_increment_price', 15, 4)->default(0);
            $table->unsignedInteger('slot_increment_max_steps')->default(0);
            $table->unsignedInteger('memory_increment_mb')->default(0);
            $table->decimal('memory_increment_price', 15, 4)->default(0);
            $table->unsignedInteger('memory_increment_max_steps')->default(0);
        });

        Schema::table('servers', function (Blueprint $table) {
            $table->unsignedInteger('memory_override')->nullable()->after('product_id');
            $table->unsignedInteger('slot_override')->nullable()->after('memory_override');
            $table->unsignedInteger('memory_increment_steps')->default(0)->after('slot_override');
            $table->unsignedInteger('slot_increment_steps')->default(0)->after('memory_increment_steps');
            $table->decimal('price_override', 15, 4)->nullable()->after('slot_increment_steps');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('servers', function (Blueprint $table) {
            $table->dropColumn([
                'price_override',
                'slot_increment_steps',
                'memory_increment_steps',
                'slot_override',
                'memory_override',
            ]);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'memory_increment_max_steps',
                'memory_increment_price',
                'memory_increment_mb',
                'slot_increment_max_steps',
                'slot_increment_price',
                'slot_increment_step',
                'player_slots',
            ]);
        });
    }
};

