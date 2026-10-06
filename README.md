# TYPO3 Sentry Transaction Handler

A small TYPO3 extension that opens one Sentry performance transaction per HTTP
request, so that `traces_sample_rate` in your Sentry (or GlitchTip) configuration actually produces data in the "Performance" view.

It is intended as a companion to [networkteam/sentry-client][sentry-client], which handles error and log forwarding but does not instrument requests for tracing.

[sentry-client]: https://github.com/networkteam/sentry_client

## Why this exists

The underlying `sentry/sentry` PHP SDK does not auto-instrument incoming HTTP
requests the way the Laravel or Symfony SDKs do. Setting `traces_sample_rate` on its own has no effect.

This extension registers a PSR-15 middleware that:

- opens an `http.server` transaction at the start of every frontend and backend request,
- names it `METHOD /path` with `TransactionSource::url()`,
- binds it to the current Sentry hub so spans created inside the request attach to it,
- continues an incoming trace when `sentry-trace` and `baggage` headers are present (distributed tracing),
- records `setHttpStatus(...)` from the response and calls `finish()` in a `finally` block, so transactions close even on exceptions.

If no Sentry client is configured, the middleware is a transparent no-op.

## Requirements

- PHP 8.3 – 8.5
- TYPO3 12.4, 13, or 14
- [`networkteam/sentry-client`][sentry-client] 6.x (brings in `sentry/sentry` 4.6+)

## Installation

```bash
composer require digicademy/typo3-sentry-transaction-handler
```

The extension is activated automatically in a Composer-managed TYPO3 install.
For non-Composer installs, download it and activate it via the Extension Manager.

## Configuration

All configuration is inherited from `networkteam/sentry-client`. In particular, set the sample rate through the usual Sentry options:

```php
// typo3conf/system/additional.php (or settings.php)
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['sentry_client']['options']['traces_sample_rate'] = 0.1;
```

Everything the Sentry PHP SDK accepts as an [option][sentry-options] works here.

[sentry-options]: https://docs.sentry.io/platforms/php/configuration/options/

### GlitchTip

This extension was written against a self-hosted [GlitchTip][glitchtip] instance, which uses the Sentry envelope protocol. No GlitchTip-specific configuration is needed.

[glitchtip]: https://glitchtip.com/

## How it works

The middleware is registered in both the `frontend` and `backend` stacks, in each case ordered `before` an early public anchor (`typo3/cms-frontend/site` and `typo3/cms-backend/backend-routing` respectively). It brackets the full request processing on the way in and the full response generation on the way out.

## Limitations

- **CLI entry points are not instrumented.** Scheduler tasks, Symfony Commands, and anything else that bypasses the HTTP middleware stack will not produce transactions. If you need coverage there, wrap the command with your own `startTransaction()` / `finish()` pair.
- **Transaction names use raw URL paths.** This is good enough for most dashboards but can produce high-cardinality names on sites with many unique URLs. If you have a mapping of parameterized routes available, you can rename the transaction inside a subsequent middleware via `SentrySdk::getCurrentHub()->getTransaction()?->setName(...)`.
- **Every request opens a transaction, including static redirects and `/favicon.ico`.** Use `traces_sample_rate` (or `traces_sampler` for per-request decisions) for sane sample rates.

## License

GPL-2.0-or-later.
