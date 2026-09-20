<?php namespace Davox\CoreExtensions\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * AddFlagIconToSitesTable Migration
 *
 * @link https://docs.octobercms.com/4.x/extend/database/structure.html
 */
return new class extends Migration
{
    /**
     * up builds the migration
     */
    public function up()
    {
        Schema::table('system_site_definitions', function(Blueprint $table) {
            $table->string('custom_flag_icon')->nullable();
        });
    }

    /**
     * down reverses the migration
     */
    public function down()
    {
        Schema::table('system_site_definitions', function(Blueprint $table) {
            $table->dropColumn('custom_flag_icon');
        });
    }
};
