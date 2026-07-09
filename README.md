# Hunters Well website

A simple static website for Hunters Well Ltd.

No build step is required. The live site only needs these files:

- `index.html`
- `assets/styles.css`
- `assets/script.js`

## Deploy using SFTP

Upload the contents of this repository to the public web root on your hosting package, usually one of:

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

## Deploy using Git on the hosting server

If the hosting server supports SSH and Git, you can clone the repository into the web root:

```bash
cd ~/public_html
git clone https://github.com/Snelbert76/hunterswell.git .
```

For future updates:

```bash
cd ~/public_html
git pull origin main
```

If the folder is not empty, clone somewhere temporary first and then copy the files into the web root.

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
