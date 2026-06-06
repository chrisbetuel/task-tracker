# TODO_FIX_DB

- [ ] Ensure `projects.url` exists in the deployed/local database.
- [x] Verify migrations for projects include `url` (create + add migration).
- [x] Run `php artisan migrate --force` to apply pending migrations.
- [ ] If `projects.url` is still missing, run targeted migration or add a new migration guarded by column existence.
- [ ] Add/confirm a permanent DB-safe fix (migration that checks for column before adding).

