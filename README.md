# 🍳 ag203-recipe-for-an-A

**Social Network of Recipes** — AG203 Web Application Programming  
University of Essex | Achilleas Karatzas | 2025-2026

> Where cooking meets coding!

## Tech Stack
- **Frontend:** HTML5, CSS3, JavaScript, jQuery, AJAX
- **Backend:** PHP 8, MySQL
- **Security:** PDO prepared statements, password_hash, htmlspecialchars, MIME validation

## Features
1. User registration & profile
2. Login / Logout (PHP Sessions)
3. Upload recipes (text + image)
4. Like & Comment (AJAX, no page reload)
5. Visitor mode (view only, no feedback)

## Project Structure
```
├── index.php              # Recipe feed (home)
├── login.php              # Login page
├── register.php           # Registration page
├── upload.php             # Upload recipe
├── recipe.php             # Recipe detail (?id=N)
├── profile.php            # User profile
├── logout.php             # Logout handler
├── config/
│   └── database.php       # PDO connection
├── api/
│   ├── like.php           # AJAX like toggle
│   └── comment.php        # AJAX comment post
├── css/
│   └── styles.css         # Responsive stylesheet
├── js/
│   ├── main.js            # jQuery + DOM
│   ├── validation.js      # Form validation
│   └── ajax.js            # AJAX calls (like, comment)
├── uploads/               # Recipe images
├── sql/
│   └── schema.sql         # Database schema
├── screenshots/           # Report screenshots
├── diagrams/              # ERD, architecture
└── report/                # Final report
```

## Setup
1. Import `sql/schema.sql` into MySQL
2. Edit `config/database.php` with your DB credentials
3. Run with MAMP/XAMPP (PHP 8 + MySQL)
4. Open `http://localhost/ag203-recipe-for-an-A-essex/`
