# Employee Task Management

## Requirements
- PHP 8.0 or higher
- MySQL 5.7 or 8.0
- Apache or Nginx web server
- XAMPP or WAMP recommended for local setup

## Local Setup (XAMPP/WAMP)
1. Install XAMPP or WAMP with PHP 8+ and MySQL 5.7/8.
2. Clone this repository into the server's web root (e.g. `htdocs` for XAMPP or `www` for WAMP).
3. Start Apache and MySQL from the control panel.
4. Create a database named `employee_db`.
5. Import the schema and optional sample data using phpMyAdmin or the MySQL CLI:
   ```bash
   mysql -u root -p employee_db < sql/schema.sql
   mysql -u root -p employee_db < sql/sample_data.sql
   ```
6. Sign in with the default administrator account:
   - **Email:** `admin@ttsetglobal.com`
   - **Password:** `Admin@123`

## Configuration
Update `config/config.php` with your database credentials and the base URL of your environment. The `BASE_URL` constant controls URL generation in the app. Ensure the `public/uploads` directory is writable by the web server:
```bash
chmod -R 775 public/uploads
```

## Security
- CSRF tokens are generated and verified for form submissions.
- All database access uses PDO prepared statements to prevent SQL injection.
- Passwords are hashed using PHP's `password_hash` before storage.

