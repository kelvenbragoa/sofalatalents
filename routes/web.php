<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\GlobalController;
use Illuminate\Support\Facades\Route;

// VotaAqui Routes - Homepage principal na raiz
Route::get('/', function () {
    return view('votaaqui');
});

// Rota de votação
Route::get('/votar/{id}', function () {
    return view('votaaqui');
})->where('id', '[0-9]+');

// Rotas alternativas para VotaAqui
Route::get('/votaaqui', function () {
    return view('votaaqui');
});

Route::get('/votaaqui/{any}', function () {
    return view('votaaqui');
})->where('any', '.*');

Route::get('/home', function () {
    return view('votaaqui');
});

Route::get('/homepage', function () {
    return view('votaaqui');
});

// Legacy homepage (Blade version) - mantida para referência
Route::get('/homepage-blade', function () {
    return view('homepage');
});

Route::get('/relatorio',[GlobalController::class,'relatorio']);

//Last route to overlap every route hitting laravel route
Route::get('{view}', ApplicationController::class)->where('view','(.*)');