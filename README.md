# Virtual Kitchen — Secure Recipe Sharing Platform  

Virtual Kitchen is a **database-driven web application** built with **PHP, MySQL, and modern security practices**.  
It allows food enthusiasts to explore, share, and manage recipes — while demonstrating **secure web development** skills suitable for cybersecurity placements and internships.  

## Features  
- Public users: 
  - Browse all recipes
  - Search by recipe name, type, or ingredient
  - View recipe details (ingredients, instructions, owner info)
- Registered users:  
  - Create new recipes (with images).  
  - Edit or delete their own recipes.  
  - Manage their account securely.  
- Security-first design: protection against **XSS, SQL Injection, CSRF, Session Hijacking, and Insecure File Uploads**.

## Database
**users**
- `uid` (Primary Key)
- `username` (unique)
- `password` (hashed)
- `email` (unique)
- `created_at`

**recipes**
- `rid` (Primary Key)
- `name`
- `description`
- `type` (ENUM)
- `cookingtime`
- `ingredients`
- `instructions`
- `image`
- `uid` (Foreign Key → users.uid)
- `created_at`

## 🔐 Security Practices Demonstrated  

### 1. Password Hashing  
All user passwords are securely stored using PHP’s password_hash() and verified with password_verify().  

**$hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $pdo->prepare('INSERT INTO users (username, password, email) VALUES (:u, :p, :e)');
$stmt->execute(['u' => $username, 'p' => $hash, 'e' => $email]);**

Prevents storage of plaintext passwords

### 2. SQL Injection Prevention
Every database query uses prepared statements with bound parameters.

**$stmt = $pdo->prepare('SELECT uid, password FROM users WHERE username = :u OR email = :u');
$stmt->execute(['u' => $username]);**

### 3. XSS Protection
All user-supplied output is escaped with htmlspecialchars()

**function e(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}**

Prevents malicious scripts from being injected

### 4. CSRF Protection
Forms include hidden tokens, validated on submit

**<input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">**

**if (!csrf_check($_POST['csrf'])) {
    $errors[] = 'Invalid CSRF token.';
}**

Prevents unauthorised form submissions

### 5. Session Security
Sessions hardened with secure cookie flags and regeneration.

**session_set_cookie_params([
  'httponly' => true,
  'secure' => isset($_SERVER['HTTPS']),
  'samesite' => 'Strict'
]);
session_start();
session_regenerate_id(true);**

Mitigates session fixation and hijacking

### 6. Output Escaping
`htmlspecialchars()` prevents XSS attacks

### 7. Authentication & Authorisation
 Only recipe owners can edit their recipes
## Tech Stack

**Backend:** PHP + MySQL

**Frontend:** HTML5, CSS3 (responsive design)

**Database:** MySQL (schema included in vkitchen.sql)

**Security:** CSRF tokens, password hashing, prepared statements, session hardening

## About

This project was built as a portfolio showcase for demonstrating secure web development practices, aiming at cybersecurity placements and internships.

## Website Visuals
- **Homepage** → shows recipe grid, with search + filter bar.  
- **Register / Login** → simple forms, styled with your CSS, includes CSRF tokens.  
- **Add Recipe** → form with recipe details + image.  
- **View Recipe** → card layout showing image, description, ingredients, and instructions.  
- **Edit Recipe** → available only to recipe owners.  

It’s a **clean, card-based UI**, with a pastel theme (from your CSS). Recruiters will instantly see it’s **functional, secure, and user-friendly**.  
