<?php

use Illuminate\Support\Facades\Route;
use Zhineng\Checkpoint\Tencent\Http\Controllers\WebhookController;

Route::get('webhook', WebhookController::class)->name('webhook');
