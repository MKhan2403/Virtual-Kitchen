# Virtual Kitchen — PHP + MySQL

Portfolio project: Virtual Kitchen — a simple recipe sharing web app.
Designed for demonstration in a cybersecurity internship application: secure coding, auth, input validation, prepared statements, CSRF tokens, file upload validation.

## Setup (local)
1. Install PHP (>=7.4), MySQL, and a web server (Apache/Nginx). Or use XAMPP / MAMP.
2. Place the project in your web root (e.g., `htdocs/virtual-kitchen/`).
3. Create `uploads/` folder and ensure it's writable by the webserver.
4. Edit `inc/config.php` and set DB credentials.
5. Run SQL:
   ```bash
   mysql -u root -p < vkitchen.sql
