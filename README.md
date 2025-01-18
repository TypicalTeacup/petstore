# Petstore
A Laravel implementation of [the example Swagger server](https://petstore.swagger.io/) with a very simple frontend.
## Getting Started
1. Clone the repo
   ```sh
   git clone https://github.com/TypicalTeacup/petstore.git
   ```
2. Copy `.env.example` to `.env` 
   ```sh
   cp .env.example .env
   ```
3. Install dependencies
   ```sh
   composer install
   npm install
   ```
4. Generate application key 
   ```sh
   php artisan key:generate
   ```
5. Start the development server
   ```sh
   ./vendor/bin/sail up -d
   ./vendor/bin/sail npm run dev
   ```
6. Symlink public storage
   ```sh
   ./vendor/bin/sail artisan storage:link
   ```
7. Run migrations
   ```sh
   ./vendor/bin/sail artisan migrate
   ```
8. Add categories and tags inside the database
   ```sql
   INSERT INTO categories (name) VALUES ('category1'), ('category2');
   INSERT INTO tags (name) VALUES ('tag1'), ('tag2');
   ```
