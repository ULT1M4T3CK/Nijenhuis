<?php
/**
 * Canonical Mollie webhook endpoint.
 *
 * This path is used by `mollie_api.php` when creating payments:
 *   /webhook/mollie
 *
 * Depending on the deployment, this may be proxied to a Python webhook server.
 * For PHP-based hosting (or as a fallback), we route the request to the existing
 * PHP handler in `backend/webhooks/mollie/webhook_handler_plesk.php`.
 */

require_once __DIR__ . '/../../backend/webhooks/mollie/webhook_handler_plesk.php';

