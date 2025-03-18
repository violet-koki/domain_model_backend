<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SendMailController;

Route::post('mail/bulk_send', [SendMailController::class, 'sendBulkEmail']);
