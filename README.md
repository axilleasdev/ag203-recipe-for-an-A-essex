# 👨‍🍳 Achilleas' Kitchen — Social Network of Recipes

A full-stack web application for sharing, discovering, and interacting with recipes. Built with PHP, MySQL, JavaScript/jQuery, and AJAX.

![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?logo=mysql&logoColor=white)
![jQuery](https://img.shields.io/badge/jQuery-3.7-0769AD?logo=jquery&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-Compose-2496ED?logo=docker&logoColor=white)

## ✨ Features

- **User Authentication** — Register, login, logout with bcrypt password hashing
- **Recipe Management** — Upload recipes with title, description, ingredients, instructions & image
- **Like System** — Toggle likes via AJAX (no page reload)
- **Comment System** — Post comments dynamically via AJAX
- **Visitor Mode** — Browse recipes without an account (no interaction)
- **Personalized Content** — Session-based navigation, edit/delete own recipes only
- **Responsive Design** — Mobile-first CSS with Flexbox/Grid

## 🛡️ Security

- Prepared statements (PDO) — SQL injection prevention
- `password_hash()` / `password_verify()` — Bcrypt hashing
- `htmlspecialchars()` — XSS prevention
- MIME type validation — Secure file uploads
- Session-based access control

## 🚀 Quick Start

### Prerequisites

- [Docker](https://www.docker.com/products/docker-desktop/) & Docker Compose

### Run

```bash
git clone https://github.com/achilleaskar/ag203-recipe-for-an-A-essex.git
cd ag203-recipe-for-an-A-essex
docker-compose up
```

That's it! The app will be available at:

| Service | URL |
|---------|-----|
| **App** | http://localhost:8080 |
| **phpMyAdmin** | http://localhost:8081 |

### Stop

```bash
docker-compose down
```

To also remove the database volume:

```bash
docker-compose down -v
```

## 📁 Project Structure

```
├── config/
│   └── database.php        # PDO connection
├── api/
│   ├── like.php            # Like toggle endpoint (JSON)
│   └── comment.php         # Comment endpoint (JSON)
├── js/
│   ├── validation.js       # Client-side form validation
│   └── ajax.js             # AJAX calls (jQuery)
├── css/
│   └── styles.css          # Responsive stylesheet
├── sql/
│   ├── schema.sql          # Database schema (4 tables)
│   └── init.sql            # Auto-initialization
├── uploads/                # User-uploaded images
├── diagrams/               # ERD & Architecture diagrams
├── index.php               # Recipe feed (home)
├── recipe.php              # Recipe detail + likes/comments
├── register.php            # User registration
├── login.php               # User login
├── logout.php              # Session destroy
├── upload.php              # Upload new recipe
├── edit_recipe.php         # Edit own recipe
├── delete_recipe.php       # Delete own recipe
├── profile.php             # User profile
└── docker-compose.yml      # Docker services
```

## 🗄️ Database Schema

4 tables in 3NF with foreign keys and cascading deletes:

- **users** — id, username (unique), email (unique), password (bcrypt), created_at
- **recipes** — id, user_id (FK), title, description, ingredients, instructions, image_path, created_at
- **likes** — id, user_id (FK), recipe_id (FK), created_at, UNIQUE(user_id, recipe_id)
- **comments** — id, user_id (FK), recipe_id (FK), comment_text, created_at

## 🛠️ Tech Stack

| Layer | Technology |
|-------|-----------|
| Front-End | HTML5, CSS3, JavaScript, jQuery 3.7 |
| Back-End | PHP 8.2 |
| Database | MySQL 8.0 |
| Server | Apache (Docker) |
| Async | AJAX (jQuery $.ajax) |

## 📝 License

University of Essex — AG203 Web Application Programming, Assignment 1.

**Author:** Achilleas Karatzas
