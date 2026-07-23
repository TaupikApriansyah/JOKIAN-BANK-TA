<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
      Schema::table('tracking_status', function (Blueprint $table) {
    $table->unsignedBigInteger('id_berkas');
});

    }

    public function down()
    {
        Schema::table('tracking_status', function (Blueprint $table) {
            $table->dropColumn('id_berkas');
        });
    }
};
