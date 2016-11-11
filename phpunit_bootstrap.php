<?php

$app = include 'bootstrap/app.php';

Illuminate\Support\Facades\Artisan::call("migrate:refresh");
