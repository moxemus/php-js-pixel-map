<?php

namespace src;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use src\components\Handler;

$app = AppFactory::create();

/**
 * Options request for all routes
 *
 */
$app->options('/{routes:.+}', function (Request $request, Response $response) {
    return $response
        ->withHeader('Access-Control-Allow-Origin', 'https://btc-bot.herokuapp.com')
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
});

/**
 * Index file
 *
 */
$app->get('/', function (Request $request, $response) {
    $response->getBody()->write(file_get_contents(__DIR__ . '/../index.html'));
    return $response;
});

/**
 * Update map cell
 *
 */
$app->post('/map', function (Request $request, Response $response) {
    $ip = $request->getServerParam('REMOTE_ADDR');
    $json = $request->getBody();
    $data = json_decode($json, true);

    $color = $data['color'] ?? null;
    $colIndex = $data['colIndex'] ?? null;
    $rowIndex = $data['rowIndex'] ?? null;
    if (!is_null($color) || !is_null($colIndex) || !is_null(!$rowIndex)) {
        return $response->withStatus(400);
    }

    # TODO : Add validation to color because it string

    $handler = new Handler();
    $handler->updateMapCell($ip, $color, (int)$rowIndex, (int)$colIndex);


    return $response;
});

/**
 * Get map
 *
 */
$app->get('/map', function (Request $request, Response $response) {
    $handler = new Handler();
    $data = $handler->getMap();
    $response->write(json_encode($data));

    return $response;
});