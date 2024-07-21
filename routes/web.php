<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
    Route::get('/inicio', function () {
        return Inertia::render('App/Home');
    })->name('home');
    Route::get('/compras', function () {
        return Inertia::render('App/Shops/Index');
    })->name('shops');
    Route::get('/compra/crear', function () {
        return Inertia::render('App/Shops/Create');
    })->name('shops.create');
    Route::get('/contribuyentes', function () {
        return Inertia::render('App/Companies/Index');
    })->name('companies');
    Route::get('/contribuyente/crear', function () {
        return Inertia::render('App/Companies/Create');
    })->name('companies.create');
});
