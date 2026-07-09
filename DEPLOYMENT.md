# Hunters Well deployment

This site is a static HTML/CSS/JS website hosted from:

```text
/home/snelbert/web/hunterswell.co.uk/public_html
```

The ideal setup is for `public_html` itself to be the Git checkout.

## 1. Pull the latest deployment files

SSH into the hosting server and run:

```bash
cd /home/snelbert/web/hunterswell.co.uk/public_html
git pull origin main
```

This adds:

- `deploy.php`
- `scripts/create-webhook-secret.sh`
- this deployment guide

## 2. Create the private webhook secret

Run:

```bash
cd /home/snelbert/web/hunterswell.co.uk/public_html
bash scripts/create-webhook-secret.sh
```

The script prints a long secret. Copy it. You will need it when creating the GitHub webhook.

The secret is stored outside the public Git repository at:

```text
/home/snelbert/web/hunterswell.co.uk/private/github_webhook_secret
```

Do not commit this value to GitHub.

## 3. Create the GitHub webhook

In GitHub, open:

```text
Snelbert76/hunterswell > Settings > Webhooks > Add webhook
```

Use these values:

| Field | Value |
|---|---|
| Payload URL | `https://hunterswell.co.uk/deploy.php` |
| Content type | `application/json` |
| Secret | Paste the value from `github_webhook_secret` |
| SSL verification | Enable SSL verification |
| Events | Just the push event |
| Active | Yes |

Save the webhook.

## 4. Test the deployment

Make any small change in GitHub, or ask ChatGPT to update the site.

GitHub should send a push event to:

```text
https://hunterswell.co.uk/deploy.php
```

The server will verify GitHub's signature and then run:

```bash
git fetch origin main
git reset --hard origin/main
git clean -fd
```

## 5. View deployment logs

On the server:

```bash
cat /home/snelbert/web/hunterswell.co.uk/private/deploy.log
```

## Security notes

- The deployment secret is not stored in this public repository.
- `deploy.php` rejects non-POST requests.
- `deploy.php` verifies the GitHub `X-Hub-Signature-256` HMAC header.
- Only pushes to `main` are deployed.
- Only the `Snelbert76/hunterswell` repository is accepted.

## Manual fallback

If the webhook fails, deploy manually:

```bash
cd /home/snelbert/web/hunterswell.co.uk/public_html
git pull origin main
```
