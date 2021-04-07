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
            $table->string('token');
            $table->string('channel');
            $table->json('ocr')->nullable();
            $table->json('evaluations')->nullable();
            $table->json('id_card_images')->nullable();
            $table->json('frames')->nullable();
            $table->string('video_path')->nullable();
            $table->timestamps();

            $table->index(['identifiable_id', 'identifiable_type']);
        });
    }
}