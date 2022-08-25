<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEmailSetColumnIntoShortUrl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('short_urls', function (Blueprint $table) {
            $table->ENUM('expiry_email_sent',['Yes','No'])->default('No');  
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('short_urls', function (Blueprint $table) {
            $table->dropColumn('expiry_email_sent');
        });
    }
}
