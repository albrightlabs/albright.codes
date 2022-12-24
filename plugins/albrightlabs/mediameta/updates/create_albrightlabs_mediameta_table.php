<?php
namespace Albrightlabs\MediaMeta\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateAlbrightlabsMediametaTable extends Migration
{

    public function up()
    {
        Schema::create('albrightlabs_mediameta', function($table)
        {

            $table->bigIncrements('id');
            $table->string('filepath',250)->nullable();
            $table->longText('description')->nullable();
            $table->bigInteger('user_id')->nullable()->unsigned();
            $table->timestamps();
            $table->softdeletes();

        });
    }

    public function down()
	{
	    Schema::dropIfExists('albrightlabs_mediameta');
	}

}
