# TYPO3 Sentry Transaction Handler

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2026-10-06

### Added

- PSR-15 middleware that opens an `http.server` Sentry transaction per request, sets the HTTP status on the response, and finishes it in a `finally` block
- Continuation of incoming distributed traces via `sentry-trace` and `baggage` request headers
- Registration of the middleware in both the `frontend` and `backend` stacks, anchored before public identifiers (`typo3/cms-frontend/site`, `typo3/cms-backend/backend-routing`)
- Compatibility with TYPO3 12.4, 13, and 14
