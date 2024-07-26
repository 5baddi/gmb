<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLocationIdFieldToAutoPosting extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
//        Schema::table('scheduled_posts', function (Blueprint $table) {
//            $table->string('account_id')->after('user_id');
//            $table->string('location_id')->after('account_id');
//        });

        Schema::table('scheduled_post_media', function (Blueprint $table) {
            $table->string('account_id')->nullable()->after('scheduled_post_id');
            $table->string('location_id')->nullable()->after('account_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('scheduled_posts', function (Blueprint $table) {
            $table->dropColumn('account_id');
            $table->dropColumn('location_id');
        });

        Schema::table('scheduled_post_media', function (Blueprint $table) {
            $table->dropColumn('account_id');
            $table->dropColumn('location_id');
        });
    }
}
