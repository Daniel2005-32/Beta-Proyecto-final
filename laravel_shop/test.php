<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$user = \App\Models\User::find(1);
$request = Illuminate\Http\Request::create('/api/admin/products', 'GET');
$request->setUserResolver(function() use ($user) { return $user; });

$controller = app()->make(\App\Http\Controllers\ProductController::class);
$response = $controller->adminIndex($request);

echo $response->getContent();

