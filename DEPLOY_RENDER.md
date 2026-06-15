# Deploy to Render + Supabase

## 1. Supabase

Create a Supabase project, then copy the Postgres connection values:

- `DB_HOST`
- `DB_PORT`
- `DB_DATABASE`
- `DB_USERNAME`
- `DB_PASSWORD`
- `DB_SSLMODE=require`

Use the transaction pooler host if Supabase recommends it for your project.

## 2. Render

Create a new Blueprint or Web Service from this repository. The included `render.yaml` configures a Docker web service.

Set these Render environment variables manually:

- `APP_KEY`: generate locally with `php artisan key:generate --show`
- `APP_URL`: your Render URL, for example `https://your-app.onrender.com`
- `DB_HOST`
- `DB_DATABASE`
- `DB_USERNAME`
- `DB_PASSWORD`

## 3. First deploy

After the first successful deploy, run migrations from Render Shell:

```bash
php artisan migrate --force
```

If you use scheduled publishing, run this command periodically with a Render Cron Job:

```bash
php artisan schedule:run
```

## Notes

Render free web services can sleep when inactive. For public users, upgrade the web service plan when you need faster wake-up and more stable traffic.

Uploaded files should not stay on the Render local filesystem for production. Use S3-compatible storage, Supabase Storage, or another persistent file service before real users upload media.
