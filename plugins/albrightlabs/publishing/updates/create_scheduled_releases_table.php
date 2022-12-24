<?php namespace Albrightlabs\Publishing\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * CreateScheduledReleasesTable Migration
 */
class CreateScheduledReleasesTable extends Migration
{
    public function up()
    {
        Schema::create('albrightlabs_publishing_scheduled_releases', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('plugincode')->nullable();
            $table->string('modelname')->nullable();
            $table->integer('modelid')->nullable();
            $table->integer('adminid')->nullable();
            $table->text('admin')->nullable();
            $table->text('content')->nullable();
            $table->timestamp('publish_at')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('albrightlabs_publishing_scheduled_releases');
    }
}
