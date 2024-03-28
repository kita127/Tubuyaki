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
    public function up()
    {
        Schema::table('tweet_details', function (Blueprint $table) {
            $table->foreignId('tweet_type_id')->after('tweet_id')->references('id')->on('tweet_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tweet_details', function (Blueprint $table) {
            //
        });
    }
};
