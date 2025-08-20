# CRM Application

This is a simple CRM application to manage customer records with features like Create, Read, Update, Delete, and Search.

## Getting Started

### Prerequisites
- Docker
- Node.js (for Angular)
- Composer (for Laravel)

### Backend (Laravel)
1. Clone the repository.
2. Run `docker-compose up` to start the services (API, MySQL, Elasticsearch, Nginx).
3. The backend API will be available at `http://localhost:8000`.

### Frontend (Angular)
1. Navigate to the `frontend/` directory.
2. Run `npm install` to install dependencies.
3. Run `ng serve` to start the Angular development server.
4. The frontend will be available at `http://localhost:4200`.

### Running Tests
- For Backend: `php artisan test`
- For Frontend: `ng test`

## API Endpoints
- `GET /api/customers`
- `POST /api/customers`
- `PUT /api/customers/{id}`
- `DELETE /api/customers/{id}`
