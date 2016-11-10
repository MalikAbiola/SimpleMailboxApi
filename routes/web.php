<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$app->get('/', function () use ($app) {
    return [
        'description' => "Oberlo Working Case Assignment",
        'author' => "Malik Abiola"
    ];
});

$app->get('/mails', 'MailController@index');

$app->get('/archives', 'MailController@archives');

$app->get('/mails/{id}', 'MailController@show');

$app->patch('/mails/{id}', 'MailController@update');
