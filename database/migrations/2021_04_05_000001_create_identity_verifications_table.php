<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIdentityVerificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('identity_verifications', function (Blueprint $table) {
            $table->id();
            $table->morphs('identifiable');
            $table->string('name');
            $table->string('id_number');
            $table->string('status');
            $table->timestamps();

            $table->index(['identifiable_id', 'identifiable_type']);
        });
    }
}