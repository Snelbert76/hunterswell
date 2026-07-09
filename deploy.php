<?php
/**
 * Hunters Well GitHub webhook deployment endpoint.
 *
 * This file is intentionally safe to keep in a public repository because it
 * does not contain the webhook secret. The secret must live on the hosting
 * server outside this repository, ideally at:
 *
 *   /home/snelbert/web/hunterswell.co.uk/private/github_webhook_secret
 *
 * GitHub should POST push events to:
 *
 *   https://hunterswell.co.uk/deploy.php
 */

declare(strict_types=1);

const DEPLOY_BRANCH = 'refs/heads/main';
const SECRET_FILE = '/home/snelbert/web/hunterswell.co.uk/private/github_webhook_secret';
const REPO_DIR = __DIR__;
const LOG_FILE = '/home/snelbert/web/hunterswell.co.uk/private/deploy.log';

function respond(int $status, string $message): never
{
    http_response_code($status);
    header('Content-Type: text/plain; charset=utf-8');
    echo $message . "\n";
    exit;
}

function log_line(string $message): void
{
    $line = '[' . gmdate('Y-m-d H:i:s') . ' UTC] ' . $message . PHP_EOL;
    @file_put_contents(LOG_FILE, $line, FILE_APPEND | LOCK_EX);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(405, 'Method not allowed');
}

$secret = getenv('HUNTERSWELL_DEPLOY_SECRET') ?: '';

if ($secret === '' && is_readable(SECRET_FILE)) {
    $secret = trim((string) file_get_contents(SECRET_FILE));
}

if ($secret === '') {
    log_line('Deployment rejected: missing webhook secret on server.');
    respond(500, 'Webhook secret is not configured on the server');
}

$payload = (string) file_get_contents('php://input');
$signatureHeader = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';
$expectedSignature = 'sha256=' . hash_hmac('sha256', $payload, $secret);

if ($signatureHeader === '' || !hash_equals($expectedSignature, $signatureHeader)) {
    log_line('Deployment rejected: invalid GitHub signature.');
    respond(403, 'Invalid signature');
}

$event = $_SERVER['HTTP_X_GITHUB_EVENT'] ?? '';
if ($event !== 'push') {
    log_line('Ignored GitHub event: ' . $event);
    respond(202, 'Ignored event: ' . $event);
}

$data = json_decode($payload, true);
if (!is_array($data)) {
    log_line('Deployment rejected: invalid JSON payload.');
    respond(400, 'Invalid JSON payload');
}

if (($data['ref'] ?? '') !== DEPLOY_BRANCH) {
    log_line('Ignored push to non-main ref: ' . ($data['ref'] ?? 'unknown'));
    respond(202, 'Ignored non-main branch');
}

$repository = $data['repository']['full_name'] ?? '';
if ($repository !== 'Snelbert76/hunterswell') {
    log_line('Deployment rejected: unexpected repository ' . $repository);
    respond(403, 'Unexpected repository');
}

$command = 'cd ' . escapeshellarg(REPO_DIR)
    . ' && git fetch origin main 2>&1'
    . ' && git reset --hard origin/main 2>&1'
    . ' && git clean -fd 2>&1';

$output = [];
$returnCode = 0;
exec($command, $output, $returnCode);

$logOutput = implode("\n", $output);
log_line('Deployment command finished with code ' . $returnCode . "\n" . $logOutput);

if ($returnCode !== 0) {
    respond(500, "Deployment failed:\n" . $logOutput);
}

respond(200, "Deployment complete:\n" . $logOutput);
