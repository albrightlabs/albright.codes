<?php namespace Albrightlabs\Publishing\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * CreatePreviewsTable Migration
 */
class CreatePreviewsTable extends Migration
{
    public function up()
    {
        Schema::create('albrightlabs_publishing_previews', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('nonce', 255)->nullable();
            $table->string('plugincode', 255)->nullable();
            $table->string('modelname', 255)->nullable();
            $table->integer('modelid')->nullable();
            $table->integer('adminid')->nullable();
            $table->text('admin')->nullable();
            $table->text('content')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('albrightlabs_publishing_previews');
    }
}
