# Operational data (not web-accessible)

JSON files in this directory are read and written **only by PHP/Python** on the server.  
Nginx/Apache must **deny** direct HTTP access to `*.json` (see `deploy/aws/nginx-aws.conf` and root `.htaccess`).

Optional override: set `DATA_DIR` in `.env` to an absolute path outside the document root.

On first deploy after this layout, legacy files under `admin/*.json` and `content/articles.json` are copied here automatically when missing (see `nijenhuis_migrate_legacy_data_files()` in `components/data_paths.php`).
