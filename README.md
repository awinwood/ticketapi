# 🚀 Support Ticket API Test
A production ready Laravel API that simulates a high-volume support ticket system.

The project includes the following features:
- Laravel 12 backend using SQLite (are required by the test)
- Secured API using Laravel Sanctum
- Scheduled commands for generating & processing support tickets
- Audit logging middleware
- Fully paginated API endpoints returning JSON Resources
- Minimal example frontend to demonstrate consuming the API
- Postman collection for all endpoints
- Full test suite with at least 80% coverage

## 📦 Tech Stack
Backend:
- Laravel 12
- PHP 8.3
- SQLite
- Sanctum (API token auth)

Dev Environment:
- DDev + Docker
- WSL2 (Ubuntu)

## 🛠️ Installation and Setup

1. Clone the repo

```bash
git clone https://github.com/awinwood/ticketapi ticketapi
cd ticketapi
```

2. Create the SQLlite file

```bash
touch database/database.sqlite
chmod 775 database/database.sqlite
```

3. Environment Config

Copy the `.env` example
```bash
cp .env.example .env
```
Update the Session settings (to avoid SQLite db locks while testing)
```ini
SESSION_DRIVER=file
```
Setup the App Key
```bash
php artisan migrate
```

4. Install dependencies

```bash
composer install
```

5. Migrate (creates the required tables in SQLite)

```bash
php artisan migrate
```

6. Run dev server
```bash
php artisan serve 
```

## 🔐 Authentication (Sanctum)
API endpoints are secured using **Bearer tokens** generated via Sanctum

Generate a token:
```bash
php artisan tinker

$user = \App\Models\User::first() ?? \App\Models\User::factory()->create();
$token = $user->createToken('test-token', ['read:tickets'])->plainTextToken;

echo $token;
```
Use it in Postman
```http
Authorization: Bearer <token>
```

## 📅 Scheduled Commands
The system includes two scheduled commands:

Generate dummy tickets every minute
```bash
php artisan tickets:generate
```

Process (close) open tickets every 5 minutes
```bash
php artisan tickets:process
```

## 📡 API Endpoints
All responses are structured JSON using Laravel API Resources.

### GET /api/tickets/open
- Returns paginated list of open tickets
- Sorted by oldest first

### GET /api/tickets/closed
- Returns paginated list of closed tickets
- Sorted by most recently closed

### GET /api/users/{userId}/tickets
- Tickets belonging to a specific user

### GET /api/stats
Returns:
```json
{
    "total_tickets": 1234567,
    "total_unprocessed_tickets": 34000,
    "top_user": {
       "name": "Jane Doe",
       "email": "jane@example.com",
       "tickets_count": 1400
    },
    "most_recent_processed_at": "2025-01-01 12:30:00"
}
```

## 🕵️ Audit Logging
Every API request goes through a custom middleware that logs:
- Authenticated user
- IP address
- Method
- URI
- Query/body payloads

Logged in the api_audits table.


## 🧪 Tests
Test suite includes:
- Model tests
- Enum tests
- API endpoint tests
- Pagination tests
- Command tests (generator + processor)
- Middleware/audit logging tests
- At least 80% coverage (as required)

Run tests with coverage:
```bash
php artisan test --coverage
```

## 📁 Postman Collection
A complete Postman collection is included at `postman/SupportTickets.postman_collection.json`

It includes:
- All endpoints
- Example requests & example responses
- Environment variable for baseUrl
- Pre-set Authorization: Bearer {{apiToken}} header

## 📝 Notes

- Designed to scale to 1M+ ticket rows via pagination, indexing, and lightweight queries
- SQLite is used only because the technical test requires it
- In production this would use MySQL/Postgres + Horizon + queues
