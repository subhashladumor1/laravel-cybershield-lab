<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('lab.dashboard');
});

Route::prefix('lab')->name('lab.')->group(function () {
    Route::get('/dashboard', \App\Livewire\Lab\Dashboard::class)->name('dashboard');
    Route::get('/waf', \App\Livewire\Lab\WafLab::class)->name('waf');
    Route::get('/rate-limiter', \App\Livewire\Lab\RateLimiterLab::class)->name('rate-limiter');
    Route::get('/bot', \App\Livewire\Lab\BotLab::class)->name('bot');
    Route::get('/network', \App\Livewire\Lab\NetworkLab::class)->name('network');
    Route::get('/scanner', \App\Livewire\Lab\ScannerLab::class)->name('scanner');
    Route::get('/monitoring', \App\Livewire\Lab\MonitoringHub::class)->name('monitoring');
    Route::get('/reference', \App\Livewire\Lab\SecurityReference::class)->name('reference');
    Route::get('/terminal', \App\Livewire\Lab\ArtisanTerminal::class)->name('terminal');
    
    // Probe endpoint for WAF testing
    Route::get('/probe', function () {
        return response()->json(['status' => 'success', 'message' => 'Request reached the application.']);
    })->name('probe');

    // Probe endpoint for Bot testing
    Route::post('/probe/bot', function () {
        return response()->json(['status' => 'success', 'message' => 'Human request verified.']);
    })->middleware('cybershield.detect_bot')->name('probe.bot');

    // Probe endpoint for Rate Limit testing
    Route::get('/probe/rate-limit', function () {
        return response()->json(['status' => 'success', 'message' => 'Request allowed within limits.']);
    })->middleware('cybershield.ip_rate_limiter')->name('probe.rate_limit');
});
