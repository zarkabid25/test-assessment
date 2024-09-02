# Role-Based API with Laravel

## Project Setup
1. Clone the repository:
   ```bash
   git clone https://github.com/yourusername/role-based-api.git
   cd role-based-api
2. Install dependencies:
    ```bash
   composer install
3. Copy .env.example to .env and configure the database settings:
    ```bash
   cp .env.example .env
4. Generate application key:
    ```bash
   php artisan key:generate
5. Run migrations and seeders:
    ```bash
   php artisan migrate --seed
6. Serve the application:
    ```bash
   php artisan serve
# Environment Variables:
    1. DB_CONNECTION: Database connection type
    2. DB_HOST: Database host
    3. DB_PORT: Database port
    4. DB_DATABASE: Database name
    5. DB_USERNAME: Database username
    6. DB_PASSWORD: Database password

# API Endpoints:
    POST /register: Register a new user
    POST /login: Login
    POST /logout: Logout (Authenticated)
    POST /invite: Send an invitation (Admin only, Authenticated)
    POST /resend-invite: Resend an invitation (Admin only, Authenticated)
    GET /tasks: Get tasks list (Authenticated)
    POST /tasks: Create a new task (Authenticated)
    GET /tasks/{id}: View a task (Authenticated)
    PUT /tasks/{id}: Update a task (Authenticated)
    DELETE /tasks/{id}: Delete a task (Authenticated)

# Structuring:

1. Role-Based Access Control (RBAC): 
    Implemented using Spatie’s Laravel Roles & Permission package for defining
    and managing user roles.
    
2. API Authentication: 
    Implemented using Laravel Sanctum for API token authentication.
