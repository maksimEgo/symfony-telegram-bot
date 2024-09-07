<?php

declare(strict_types=1);

use Nyholm\Psr7\Factory\Psr17Factory;
use Spiral\RoadRunner;
use Spiral\RoadRunner\Jobs\Consumer;
use Spiral\RoadRunner\Environment;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\Request;

require __DIR__ . '/vendor/autoload.php';

(static function (): void {
    $dotEnv = new Dotenv();
    $dotEnv->load(__DIR__ . '/.env');
})();

$worker = RoadRunner\Worker::create();
$env    = Environment::fromGlobals();

$psrFactory            = new Psr17Factory();
$httpFoundationFactory = new HttpFoundationFactory();
$psrHttpFactory        = new PsrHttpFactory($psrFactory, $psrFactory, $psrFactory, $psrFactory);

$psr7Worker    = new RoadRunner\Http\PSR7Worker($worker, $psrFactory, $psrFactory, $psrFactory);
$queueConsumer = new Consumer($worker);

$kernel = new App\Kernel($_SERVER['APP_ENV'], true);

try {
    while (true) {
        if ($env->getMode() === RoadRunner\Environment\Mode::MODE_HTTP) {
            $req = $psr7Worker->waitRequest();

            if ($req === null) {
                break;
            }

            try {
                $symfonyRequest  = $httpFoundationFactory->createRequest($req);
                $symfonyResponse = $kernel->handle($symfonyRequest);
                $psr7Response    = $psrHttpFactory->createResponse($symfonyResponse);

                $psr7Worker->respond($psr7Response);
                $kernel->terminate($symfonyRequest, $symfonyResponse);
            } catch (Throwable $throwable) {
                $psr7Worker->getWorker()->error((string) $throwable);
            }
        } elseif ($env->getMode() === RoadRunner\Environment\Mode::MODE_JOBS) {
            $task = $queueConsumer->waitTask();

            if ($task === null) {
                break;
            }

            try {
                $task->ack();
            } catch (Throwable $e) {
                $task->nack($e);
            }
        }
    }
} catch (Throwable $exception) {
    $worker->error('Unhandled exception: ' . $exception->getMessage());
}
