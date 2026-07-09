# Hunters Well website

Deployment test.

A simple static website for Hunters Well Ltd.

No build step is required. The live site only needs these files:

- `index.html`
- `assets/styles.css`
- `assets/script.js`
- supporting static assets and pages

## Deploy using Git on the hosting server

The preferred live setup is for this repository to be checked out directly into:

```text
/home/snelbert/web/hunterswell.co.uk/public_html
```

Manual deployment:

```bash
cd /home/snelbert/web/hunterswell.co.uk/public_html
git pull origin main
```

## Automatic deployment

This repo includes a secure GitHub webhook deployment endpoint:

```text
deploy.php
```

It verifies GitHub's `X-Hub-Signature-256` header, accepts only push events to `main`, and then updates the live checkout.

Full setup instructions are in:

```text
DEPLOYMENT.md
```

## Deploy using SFTP

You can also upload the repository contents to the public web root manually using SFTP, usually one of:

- `public_html`
- `htdocs`
- `www`
- `web`

Make sure `index.html` is at the root of the public folder, for example:

```text
public_html/index.html
public_html/assets/styles.css
public_html/assets/script.js
```

## Local preview

Open `index.html` directly in a browser, or run a simple local server:

```bash
python3 -m http.server 8080
```

Then visit:

```text
http://localhost:8080
```

## Notes

This is intentionally plain HTML, CSS and JavaScript so it can run on low-cost shared hosting without Node.js, Vercel, Netlify or any build tooling.
