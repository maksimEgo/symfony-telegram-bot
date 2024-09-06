<?php

declare(strict_types=1);

use Nyholm\Psr7\Factory\Psr17Factory;
use Spiral\RoadRunner;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\Request;

require __DIR__ . '/vendor/autoload.php';

$dotEnv = new Dotenv();
$dotEnv->load(__DIR__ . '/.env');

$worker = RoadRunner\Worker::create();

$psrFactory = new Psr17Factory();
$kernel     = new App\Kernel($_SERVER['APP_ENV'], true);

$httpFoundationFactory = new HttpFoundationFactory();
$psrHttpFactory        = new PsrHttpFactory($psrFactory, $psrFactory, $psrFactory, $psrFactory);

$psr7Worker = new RoadRunner\Http\PSR7Worker($worker, $psrFactory, $psrFactory, $psrFactory);

try {
    while ($req = $psr7Worker->waitRequest()) {
        try {

            /** @var Request $symfonyRequest */
            $symfonyRequest = $httpFoundationFactory->createRequest($req);

            $symfonyResponse = $kernel->handle($symfonyRequest);
            $psr7Response    = $psrHttpFactory->createResponse($symfonyResponse);

            $psr7Worker->respond($psr7Response);
            $kernel->terminate($symfonyRequest, $symfonyResponse);
        } catch (Throwable $throwable) {
            $psr7Worker->getWorker()->error((string)$throwable);
        }
    }
} catch (JsonException $jsonException) {
    throw new JsonException($jsonException->getMessage());
}
