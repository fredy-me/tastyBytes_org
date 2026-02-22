<INSTRUCTIONS>
You are working in the `TastyBytes` repository.

## Project Intent
TastyBytes is a PHP + SQL recipe management system with:
- User registration/login and role-based access (`user`, `admin`)
- Recipe submission with admin moderation (`pending` → `approved`/`rejected`)
- Favorites per user
- Admin user management (disable/delete users)

## Hard Requirements (do not deviate)
- Data storage is **server-side PHP + SQL database only** (no `localStorage` backend).
- Passwords must be stored as **secure hashes** (use `password_hash` / `password_verify`; never plaintext).
- “Real-time updates” means the UI must reflect whether a submitted recipe has been `approved` or `rejected` by an admin (implementation can be polling/refresh unless specified otherwise).
- Recipe images are **URL-based only** (`image_url`); do not implement file uploads.
- Recipe titles must be **globally unique** (enforce with a DB unique constraint and handle conflicts in UX).

## Data Model Expectations
- `USERS` should include an account status concept (at minimum `active`/`disabled`) so admins can disable users.
- `RECIPES.status` is one of `pending`, `approved`, `rejected`.
- Favorites are unique per (`user_id`, `recipe_id`).

## Security / Quality Bar
- Use prepared statements (PDO) for all SQL.
- Enforce authorization on every write action (users can only modify their own recipes; admins only can moderate/manage users).
- If a user is `disabled`, block login and privileged actions.

## Repo Workflow
- Keep changes minimal and aligned to the SRS + clarified requirements in the conversation.
- Prefer simple PHP architecture (controllers/models/views) and clear directory structure over frameworks unless the user asks otherwise.
</INSTRUCTIONS>

