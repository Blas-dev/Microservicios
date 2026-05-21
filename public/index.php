<?php

require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Illuminate\Database\Capsule\Manager as Capsule;
use App\Models\SprintModel;
use App\Models\HistoriasModel;


// Crear aplicación Slim
$app = AppFactory::create();

$app->addBodyParsingMiddleware();


// =========================
// CONEXIÓN A BASE DE DATOS
// =========================

$capsule = new Capsule;

$capsule->addConnection([
    'driver' => 'mysql',
    'host' => '127.0.0.1',

    // CAMBIA ESTO POR EL NOMBRE DE TU BD
    'database' => 'gestor_historias_db',

    'username' => 'root',
    'password' => '',

    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix' => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();


// =========================
// CORS
// =========================

$app->options('/{routes:.+}', fn($req, $res) => $res);

$app->add(function (Request $request, $handler) {

    $origin = $request->getHeaderLine('Origin') ?: '*';

    $response = $handler->handle($request);

    return $response
        ->withHeader('Access-Control-Allow-Origin', $origin)
        ->withHeader(
            'Access-Control-Allow-Headers',
            'X-Requested-With, Content-Type, Accept, Origin, Authorization'
        )
        ->withHeader(
            'Access-Control-Allow-Methods',
            'GET, POST, PUT, DELETE, OPTIONS'
        )
        ->withHeader(
            'Access-Control-Allow-Credentials',
            'true'
        );
});


// =========================
// RUTA DE PRUEBA
// =========================

$app->get('/', function (
    Request $request,
    Response $response
) {

    $response->getBody()->write(
        json_encode([
            'mensaje' => 'API funcionando correctamente'
        ])
    );

    return $response->withHeader(
        'Content-Type',
        'application/json'
    );
});

$app->get('/sprints', function (
    Request $request,
    Response $response
) {

    $sprints = SprintModel::all();

    $response->getBody()->write(
        $sprints->toJson()
    );

    return $response->withHeader(
        'Content-Type',
        'application/json'
    );
});

$app->get('/sprints/{id}', function (
    Request $request,
    Response $response,
    $args
) {

    $sprint = SprintModel::find($args['id']);

    if (!$sprint) {

        $response->getBody()->write(
            json_encode([
                'mensaje' => 'Sprint no encontrado'
            ])
        );

        return $response->withStatus(404);
    }

    $response->getBody()->write(
        $sprint->toJson()
    );

    return $response->withHeader(
        'Content-Type',
        'application/json'
    );
});

$app->post('/sprints', function (
    Request $request,
    Response $response
) {

    $data = $request->getParsedBody();

    $sprint = SprintModel::create([
        'nombre' => $data['nombre'],
        'fecha_inicio' => $data['fecha_inicio'],
        'fecha_fin' => $data['fecha_fin']
    ]);

    $response->getBody()->write(
        $sprint->toJson()
    );

    return $response->withHeader(
        'Content-Type',
        'application/json'
    );
});

$app->put('/sprints/{id}', function (
    Request $request,
    Response $response,
    $args
) {

    $sprint = SprintModel::find($args['id']);

    if (!$sprint) {

        $response->getBody()->write(
            json_encode([
                'mensaje' => 'Sprint no encontrado'
            ])
        );

        return $response->withStatus(404);
    }

    $data = $request->getParsedBody();

    $sprint->update($data);

    $response->getBody()->write(
        $sprint->toJson()
    );

    return $response->withHeader(
        'Content-Type',
        'application/json'
    );
});

$app->delete('/sprints/{id}', function (
    Request $request,
    Response $response,
    $args
) {

    $sprint = SprintModel::find($args['id']);

    if (!$sprint) {

        $response->getBody()->write(
            json_encode([
                'mensaje' => 'Sprint no encontrado'
            ])
        );

        return $response->withStatus(404);
    }

    $sprint->delete();

    $response->getBody()->write(
        json_encode([
            'mensaje' => 'Sprint eliminado'
        ])
    );

    return $response->withHeader(
        'Content-Type',
        'application/json'
    );
});

$app->get('/historias', function (
    Request $request,
    Response $response
) {

    $historias = HistoriasModel::all();

    $response->getBody()->write(
        $historias->toJson()
    );

    return $response->withHeader(
        'Content-Type',
        'application/json'
    );
});

$app->post('/historias', function (
    Request $request,
    Response $response
) {

    $data = $request->getParsedBody();

    $historia = HistoriasModel::create([
        'titulo' => $data['titulo'],
        'descripcion' => $data['descripcion'],
        'responsable' => $data['responsable'],
        'estado' => $data['estado'],
        'puntos' => $data['puntos'],
        'fecha_creacion' => $data['fecha_creacion'],
        'fecha_finalizacion' => $data['fecha_finalizacion'],
        'sprint_id' => $data['sprint_id']
    ]);

    $response->getBody()->write(
        $historia->toJson()
    );

    return $response->withHeader(
        'Content-Type',
        'application/json'
    );
});

$app->get('/historias/{id}', function (
    Request $request,
    Response $response,
    $args
) {

    $historia = HistoriasModel::find($args['id']);

    if (!$historia) {

        $response->getBody()->write(
            json_encode([
                'mensaje' => 'Historia no encontrada'
            ])
        );

        return $response->withStatus(404);
    }

    $response->getBody()->write(
        $historia->toJson()
    );

    return $response->withHeader(
        'Content-Type',
        'application/json'
    );
});

$app->put('/historias/{id}', function (
    Request $request,
    Response $response,
    $args
) {

    $historia = HistoriasModel::find($args['id']);

    if (!$historia) {

        $response->getBody()->write(json_encode([
            'mensaje' => 'Historia no encontrada'
        ]));

        return $response
            ->withStatus(404)
            ->withHeader('Content-Type', 'application/json');
    }

    $data = $request->getParsedBody();

    $historia->titulo = $data['titulo'] ?? $historia->titulo;
    $historia->descripcion = $data['descripcion'] ?? $historia->descripcion;
    $historia->responsable = $data['responsable'] ?? $historia->responsable;
    $historia->estado = $data['estado'] ?? $historia->estado;
    $historia->puntos = $data['puntos'] ?? $historia->puntos;
    $historia->fecha_creacion = $data['fecha_creacion'] ?? $historia->fecha_creacion;
    $historia->fecha_finalizacion = $data['fecha_finalizacion'] ?? $historia->fecha_finalizacion;
    $historia->sprint_id = $data['sprint_id'] ?? $historia->sprint_id;

    $historia->save();

    $response->getBody()->write(
        json_encode([
            'mensaje' => 'Historia actualizada',
            'data' => $historia
        ])
    );

    return $response->withHeader(
        'Content-Type',
        'application/json'
    );
});

$app->delete('/historias/{id}', function (
    Request $request,
    Response $response,
    $args
) {

    $historia = HistoriasModel::find($args['id']);

    if (!$historia) {

        $response->getBody()->write(
            json_encode([
                'mensaje' => 'Historia no encontrada'
            ])
        );

        return $response->withStatus(404);
    }

    $historia->delete();

    $response->getBody()->write(
        json_encode([
            'mensaje' => 'Historia eliminada'
        ])
    );

    return $response->withHeader(
        'Content-Type',
        'application/json'
    );
});

$app->run();