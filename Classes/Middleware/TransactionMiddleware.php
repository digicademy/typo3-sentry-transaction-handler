<?php

declare(strict_types=1);

namespace Digicademy\Typo3SentryTransactionHandler\Middleware;

use Networkteam\SentryClient\Service\SentryService;
use Psr\Http\Message\{
    ResponseInterface,
    ServerRequestInterface
};
use Psr\Http\Server\{
    MiddlewareInterface,
    RequestHandlerInterface
};
use Sentry\SentrySdk;
use Sentry\Tracing\{
    TransactionContext,
    TransactionSource
};
use function Sentry\startTransaction;

final class TransactionMiddleware implements MiddlewareInterface
{
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        // networkteam/sentry-client initializes the SDK lazily from its
        // exception handlers and log writer, so middleware cannot assume
        // it is already set up. Force initialization here.
        SentryService::inititalize();

        $hub = SentrySdk::getCurrentHub();
        if ($hub->getClient() === null) {
            return $handler->handle($request);
        }

        $context = TransactionContext::fromHeaders(
            $request->getHeaderLine('sentry-trace'),
            $request->getHeaderLine('baggage')
        );
        $context
            ->setName($request->getUri()->getPath())
            ->setOp('http.server')
            ->setSource(TransactionSource::url());

        $transaction = startTransaction($context);
        $hub->setSpan($transaction);

        try {
            $response = $handler->handle($request);
            $transaction->setHttpStatus($response->getStatusCode());

            return $response;
        } finally {
            $transaction->finish();
        }
    }
}
