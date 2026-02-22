# TastyBytes — Recipe Management System

TastyBytes is a web-based recipe management platform where users can create and browse recipes, and administrators moderate submissions to ensure quality and originality.

## Scope

- Users can register/login, create/edit/delete their own recipes, browse approved recipes, and manage favorites.
- Admins can approve/reject recipes, remove duplicate/inappropriate content, and manage users (disable/delete).

## Tech Stack

- PHP (server-side)
- SQL database (MySQL/MariaDB recommended)
- HTML5, CSS, Bootstrap (UI)

## Core Objects (Data Model)

### `USERS`

- `user_id` (PK)
- `username`
- `email` (unique)
- `password` (hashed)
- `role` (`user` | `admin`)
- `status` (`active` | `disabled`)
- `created_at`

### `RECIPES`

- `recipe_id` (PK)
- `title` (globally unique)
- `category`
- `ingredients`
- `steps`
- `image_url` (URL only; no upload)
- `status` (`pending` | `approved` | `rejected`)
- `user_id` (FK → `USERS.user_id`)
- `created_at`
- `updated_at`

### `FAVORITES`

- `favorite_id` (PK)
- `user_id` (FK → `USERS.user_id`)
- `recipe_id` (FK → `RECIPES.recipe_id`)
- `created_at`
- Unique constraint: (`user_id`, `recipe_id`)

## Key Rules / Behavior

- New recipes are created as `pending` and are not visible publicly until `approved`.
- Admin decision is reflected in the UI so the author can see whether a recipe is `approved` or `rejected` (“real-time” in the product sense).
- Recipe titles must be globally unique; admins use this to detect duplicates during approval.
- Recipe images are stored as `image_url` (link), not as uploaded files.
- Passwords must be stored hashed (never plain text).

## Suggested Project Structure (when implemented)

- `public/` — `index.php`, static assets, entry points
- `app/`
  - `Controllers/` — request handling
  - `Models/` — `User`, `Recipe`, `Favorite` data access
  - `Views/` — UI templates
- `config/` — database connection/config
- `database/` — schema/migrations/seed data

## Starter Database Schema (draft)

```sql
CREATE TABLE users (
  user_id VARCHAR(50) PRIMARY KEY,
  username VARCHAR(50) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role VARCHAR(20) NOT NULL,
  status VARCHAR(20) NOT NULL DEFAULT 'active',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE recipes (
  recipe_id VARCHAR(50) PRIMARY KEY,
  title VARCHAR(100) NOT NULL UNIQUE,
  category VARCHAR(50) NOT NULL,
  ingredients TEXT NOT NULL,
  steps TEXT NOT NULL,
  image_url TEXT NULL,
  status VARCHAR(20) NOT NULL DEFAULT 'pending',
  user_id VARCHAR(50) NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NULL,
  FOREIGN KEY (user_id) REFERENCES users(user_id)
);

CREATE TABLE favorites (
  favorite_id VARCHAR(50) PRIMARY KEY,
  user_id VARCHAR(50) NOT NULL,
  recipe_id VARCHAR(50) NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(user_id),
  FOREIGN KEY (recipe_id) REFERENCES recipes(recipe_id),
  UNIQUE (user_id, recipe_id)
);
```

## Setup (expected)

1. Create a MySQL/MariaDB database (e.g. `tastybytes`).
2. Apply the project schema (to be added under `database/`).
3. Configure DB credentials in `config/` (to be added).
4. Run with a local PHP server or Apache/Nginx + PHP-FPM.
