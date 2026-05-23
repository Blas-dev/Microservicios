<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Illuminate\Database\Capsule\Manager as Capsule;
use App\Models\SprintModel;
use App\Models\HistoriasModel;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

$capsule = new Capsule;
$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => '127.0.0.1',
    'database'  => 'gestor_historias_db',
    'username'  => 'root',
    'password'  => '',
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});

$app->add(function ($request, $handler) {
    $response = $handler->handle($request);
    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        ->withHeader('Access-Control-Allow-Credentials', 'true');
});

$app->get('/sprints', function (Request $request, Response $response) {
    $sprints = SprintModel::all();
    $response->getBody()->write(json_encode($sprints));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/sprints', function (Request $request, Response $response) {
    $data = json_decode($request->getBody()->getContents(), true);
    $sprint = SprintModel::create($data);
    $response->getBody()->write(json_encode($sprint));
    return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
});

$app->get('/historias', function (Request $request, Response $response) {
    $historias = HistoriasModel::with('sprint')->get();
    $response->getBody()->write(json_encode($historias));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/historias', function (Request $request, Response $response) {
    $data = json_decode($request->getBody()->getContents(), true);
    $historia = HistoriasModel::create($data);
    $response->getBody()->write(json_encode($historia));
    return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
});

$app->put('/historias/{id}', function (Request $request, Response $response, $args) {
    $id = $args['id'];
    $data = json_decode($request->getBody()->getContents(), true);
    $historia = HistoriasModel::find($id);
    
    if ($historia) {
        $historia->update($data);
        $response->getBody()->write(json_encode($historia));
        return $response->withHeader('Content-Type', 'application/json');
    }
    
    return $response->withStatus(404);
});

$app->delete('/historias/{id}', function (Request $request, Response $response, $args) {
    $id = $args['id'];
    $historia = HistoriasModel::find($id);
    
    if ($historia) {
        $historia->delete();
        return $response->withStatus(204);
    }
    
    return $response->withStatus(404);
});

$app->get('/informe', function (Request $request, Response $response) {
    $historias = HistoriasModel::all();
    
    $informe = [
        'general' => [
            'finalizadas' => $historias->where('estado', 'finalizada')->count(),
            'pendientes' => $historias->whereIn('estado', ['nueva', 'activa'])->count(),
            'impedimentos' => $historias->where('estado', 'impedimento')->count(),
        ],
        'por_responsable' => []
    ];

    foreach ($historias->groupBy('responsable') as $responsable => $historias_resp) {
        $informe['por_responsable'][$responsable] = [
            'finalizadas' => $historias_resp->where('estado', 'finalizada')->count(),
            'pendientes' => $historias_resp->whereIn('estado', ['nueva', 'activa'])->count(),
            'impedimentos' => $historias_resp->where('estado', 'impedimento')->count(),
        ];
    }

    $response->getBody()->write(json_encode($informe));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->run();