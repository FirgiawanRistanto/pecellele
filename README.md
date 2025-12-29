# Pecel-Lele-Laravel Project

This is a Laravel project for a "Pecel Lele" (a type of Indonesian fried catfish dish) ordering system.

## Prerequisites

Before you begin, ensure you have the following installed on your system:

*   **PHP**: Version 8.2 or higher.
*   **Composer**: PHP dependency manager.
*   **Node.js & npm**: JavaScript runtime and package manager.
*   **Database**: MySQL, PostgreSQL, or SQLite (configure in `.env`).

## Installation Steps

Follow these steps to get the project up and running on your local machine:

1.  **Clone the repository:**
    ```bash
    git clone <repository-url>
    cd Pecel-Lele-Laravel
    ```
    (Replace `<repository-url>` with the actual URL of your repository.)

2.  **Install PHP Dependencies:**
    ```bash
    composer install
    ```

3.  **Copy Environment File:**
    Create your environment file by copying the example file:
    ```bash
    cp .env.example .env
    # For Windows users, use:
    # copy .env.example .env
    ```

4.  **Generate Application Key:**
    ```bash
    php artisan key:generate
    ```

5.  **Configure `.env` File:**
    Open the newly created `.env` file and configure your database connection and any other necessary environment variables.
    Example for MySQL:
    ```dotenv
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=your_database_name
    DB_USERNAME=your_database_user
    DB_PASSWORD=your_database_password
    ```

6.  **Run Database Migrations and Seeders:**
    This will create the necessary tables in your database and populate them with initial data (e.g., menu items, admin user).
    ```bash
    php artisan migrate --seed
    ```

7.  **Install Node.js Dependencies:**
    ```bash
    npm install
    ```

8.  **Compile Frontend Assets:**
    This project uses Vite for asset compilation.
    For development (with hot-reloading):
    ```bash
    npm run dev
    ```
    For production build:
    ```bash
    npm run build
    ```

9.  **Start the Laravel Development Server:**
    ```bash
    php artisan serve
    ```
    This will typically start the server at `http://127.0.0.1:8000`.

## Usage

*   Access the application in your web browser at the address provided by `php artisan serve`.
*   If seeders were run, you might have default user credentials (e.g., an admin user) to log in. Check your `database/seeders` for details.

---
Enjoy your Pecel-Lele ordering experience!