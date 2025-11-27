<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/dashboard');
});

Route::get('/dashboard', function () {
    return view('dashboard');
});

Route::get('/accounts', function () {
    return view('accounts');
});

Route::get('/categories', function () {
    return view('categories');
});

Route::get('/transactions', function () {
    return view('transactions');
});

Route::get('/investments', function () {
    return view('investments');
});

Route::get('/quotes', function () {
    return view('quotes');
});

Route::get('/projection', function () {
    return view('projection');
});

Route::get('/credit-cards', function () {
    return view('credit-cards');
});

Route::get('/receivables', function () {
    return view('receivables');
});

Route::get('/fixed-expenses', function () {
    return view('fixed-expenses');
});

Route::get('/goals', function () {
    return view('goals');
});
