# NSBM NCircle

Student community for notes and study sharing.
Stack: HTML, CSS, JavaScript, PHP, MySQL (plain PHP, MVC-lite, no framework), Bootstrap 5.3, Bootstrap Icons.

## Setup (local)
1. Clone the repo. PHP 8.1+ needs the `pdo_mysql`, `fileinfo`, `mbstring`, `zip` and `curl` extensions.
2. Copy `.env.example` to `.env` and fill in your DB details.
3. Create a MySQL database, then import `database/schema.sql` followed by `database/seed.sql`.
   **Importing `schema.sql` drops its tables first, so it wipes existing data.**
4. Start the site, allowing 10 MB uploads:
   ```
   php -d upload_max_filesize=10M -d post_max_size=22M -S localhost:8000 -t public public/index.php
   ```
   Or point Apache's document root at the `public/` folder (`public/.htaccess` handles routing).
5. Open the site. Demo logins are in `docs/DEMO_ACCOUNTS.md`.

For GIFs, add a free key from developers.giphy.com to `.env` as `GIPHY_API_KEY`. Without one, the GIF button is hidden.

Emails go to `storage/logs/mail.log` while `MAIL_DRIVER=log`. With `APP_DEBUG=true`, the check-inbox and forgot-password pages also show the link directly. To send real emails, set `MAIL_DRIVER=resend`, `RESEND_API_KEY`, `MAIL_FROM_ADDRESS` and `MAIL_FROM_NAME`.

## Updating an existing database
Run new files from `database/migrations/` once, in date order. They keep your existing data. Back up first:
```
mysqldump -u <user> -p <database> > backup.sql
mysql -u <user> -p <database> < database/migrations/2026-11-06-faculty-members-meetings.sql
```

## Going live
- Set `APP_DEBUG=false` and the real `APP_URL`. This hides error details and turns off `/dev/styleguide`.
- Set `MAIL_DRIVER=resend` with a key, and verify the from address's domain at resend.com, or new students cannot verify.
- Serve over HTTPS, and only expose `public/` as the document root.
- Remove the demo accounts.

## Project layout
- `routes/web.php`: every URL, and who can open it
- `app/Controllers`, `app/Models`, `app/Services`, `app/Core`: the backend
- `views/components`: shared UI pieces, all shown at `/dev/styleguide`. Look there before writing new markup
- `public/assets/css/theme.css`: all colours, fonts and radii. Never hardcode these elsewhere

## Tests
The end-to-end test creates throwaway accounts and content, so always run it against a separate test database, never your real one. Create one, import `schema.sql` and `seed.sql` into it, then:
```
MAIL_DRIVER=log DB_NAME=ncircle_test php -d upload_max_filesize=10M -d post_max_size=22M -S 127.0.0.1:8770 -t public public/index.php
php tests/smoke.php http://127.0.0.1:8770
```
Values set on the command line override `.env`. The test expects fresh seed data, so re-import the test database before each run.

To test GIFs without a real key, also run `php -S 127.0.0.1:8799 tests/giphy-mock.php` and add `GIPHY_API_KEY=test-secret-key GIPHY_API_BASE=http://127.0.0.1:8799/v1` to the server command.

## Team
(list members and the sections they own)

## Links
- Live site: TODO
- Repository: TODO
