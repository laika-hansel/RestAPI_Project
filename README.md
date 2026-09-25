# TaskFlow — REST API for Task Management

A production-ready **RESTful API** for task management built with **Laravel 13** (PHP 8.4), backed by **MySQL**, with an interactive **HTML5/CSS3/JavaScript** frontend dashboard and an automated **PHPUnit** test suite.

---

## 🚀 Features

- **Full Task CRUD**:
  - **Create** tasks with title, description, status, and due date
  - **View** all tasks or fetch individual tasks by ID
  - **Update** tasks with full (`PUT`) or partial (`PATCH`) updates
  - **Delete** tasks permanently from the database
- **Task Schema**:
  - `id`: BigInt Primary Key (Auto-increment)
  - `title`: String (Required, max 255 chars)
  - `description`: Text (Optional, up to 5,000 chars)
  - `status`: Enum (`todo`, `in-progress`, `done`) — defaults to `todo`
  - `due_date`: Date (`YYYY-MM-DD`, Optional)
  - `created_at` & `updated_at`: ISO 8601 Datetime
- **Status Filtering & Search**:
  - Filter tasks via query parameters: `GET /api/tasks?status=todo` (`in-progress` or `done`)
  - Keyword search via `GET /api/tasks?search=keyword`
- **Robust Input Validation**:
  - Standardized JSON validation error responses (HTTP 422 Unprocessable Content)
  - Specific, friendly error messages (e.g., *"The task title is required."*, *"The status must be one of: todo, in-progress, done."*)
- **Database Persistence**:
  - Persistent storage in **MySQL** (`task_manager_db`) via Eloquent ORM & migrations
- **Automated Test Suite**:
  - 10 comprehensive PHPUnit feature tests (56 assertions) covering all CRUD operations, status filtering, validation rules, 404 handling, and analytics
- **Interactive Web Interface**:
  - Single-page application built with HTML5, CSS3, and JavaScript at `http://localhost:8000`
  - Real-time statistics cards (Total, To Do, In Progress, Completed, Overdue)
  - Interactive status filter tabs and search bar
  - Task creation and edit modals with validation alerts
  - Built-in API Endpoints Reference table

---

## 🛠️ Technology Stack

| Layer | Technology |
|---|---|
| **Backend Framework** | Laravel 13 (PHP 8.4) |
| **Database** | MySQL (XAMPP / standalone MySQL on port 3306) |
| **Frontend UI** | HTML5, Modern CSS3 (Dark Theme), Vanilla JavaScript (ES6+ `fetch`) |
| **Testing** | PHPUnit 12 with SQLite In-Memory Database for fast, isolated test runs |
| **Data Validation** | Laravel FormRequest (`StoreTaskRequest`, `UpdateTaskRequest`) |
| **Serialization** | Laravel API Resource (`TaskResource`) |

---

## 📋 REST API Endpoints Reference

Base URL: `http://localhost:8000/api`

| Method | Endpoint | Description | Status Code |
|---|---|---|---|
| `GET` | `/api/tasks` | List tasks (supports `?status=todo\|in-progress\|done` & `?search=...`) | `200 OK` |
| `POST` | `/api/tasks` | Create a new task | `201 Created` |
| `GET` | `/api/tasks/{id}` | Retrieve a single task by ID | `200 OK` / `404 Not Found` |
| `PUT` | `/api/tasks/{id}` | Full update of a task | `200 OK` / `422 Unprocessable` |
| `PATCH` | `/api/tasks/{id}` | Partial update of a task (e.g. status) | `200 OK` / `422 Unprocessable` |
| `DELETE` | `/api/tasks/{id}` | Delete a task | `200 OK` / `404 Not Found` |
| `GET` | `/api/tasks/stats` | Aggregated metrics (total, todo, in_progress, done, overdue) | `200 OK` |
| `GET` | `/api/health` | Health check endpoint | `200 OK` |

---

## 💻 cURL & PowerShell Examples

### 1. List All Tasks
```bash
curl -X GET http://localhost:8000/api/tasks \
  -H "Accept: application/json"
```

### 2. Filter Tasks by Status (`in-progress`)
```bash
curl -X GET "http://localhost:8000/api/tasks?status=in-progress" \
  -H "Accept: application/json"
```

### 3. Create a New Task
```bash
curl -X POST http://localhost:8000/api/tasks \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "title": "Build unit tests",
    "description": "Ensure at least 5 automated tests pass",
    "status": "todo",
    "due_date": "2026-10-15"
  }'
```

**Success Response (HTTP 201 Created):**
```json
{
  "success": true,
  "message": "Task created successfully",
  "data": {
    "id": 6,
    "title": "Build unit tests",
    "description": "Ensure at least 5 automated tests pass",
    "status": "todo",
    "due_date": "2026-10-15",
    "created_at": "2026-09-24T16:18:50.000000Z",
    "updated_at": "2026-09-24T16:18:50.000000Z"
  }
}
```

### 4. Input Validation Error (Missing Title)
```bash
curl -X POST http://localhost:8000/api/tasks \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "description": "Task without title"
  }'
```

**Validation Error Response (HTTP 422 Unprocessable Content):**
```json
{
  "success": false,
  "message": "Validation error",
  "errors": {
    "title": [
      "The task title is required."
    ]
  }
}
```

### 5. Update Task Status via PATCH
```bash
curl -X PATCH http://localhost:8000/api/tasks/1 \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "status": "done"
  }'
```

### 6. Delete a Task
```bash
curl -X DELETE http://localhost:8000/api/tasks/1 \
  -H "Accept: application/json"
```

---

## ⚙️ Installation & Setup

### 1. Prerequisites
- **PHP >= 8.2** (PHP 8.4 recommended) with extensions: `pdo_mysql`, `curl`, `mbstring`, `openssl`, `pdo_sqlite`
- **Composer** (v2.x)
- **MySQL** running on `localhost:3306` (e.g. via XAMPP)

### 2. Clone / Open Directory
```bash
cd "REST API"
```

### 3. Install Dependencies
```bash
composer install
```

### 4. Environment Configuration
Create a `.env` file (copied from `.env.example`):
```env
APP_NAME="TaskFlow API"
APP_ENV=local
APP_KEY=base64:hjJ6XUAkywf5NHycD8swAR2/+UjqvSAD0768CEaNnFg=
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=task_manager_db
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Create MySQL Database & Run Migrations
In MySQL, create the database:
```sql
CREATE DATABASE IF NOT EXISTS task_manager_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Run migrations and seed realistic sample tasks:
```bash
php artisan migrate:fresh --seed
```

### 6. Start the Server
```bash
php artisan serve --port=8000
```
- Web Application: **[http://localhost:8000](http://localhost:8000)**
- REST API Base: **[http://localhost:8000/api/tasks](http://localhost:8000/api/tasks)**

---

## 🧪 Running Automated Tests

Run the complete automated test suite using Artisan:

```bash
php artisan test
```

Or run PHPUnit directly:
```bash
php ./vendor/bin/phpunit
```

### Test Suite Overview:
The test suite in `tests/Feature/TaskApiTest.php` includes **10 automated tests** with **56 assertions**:
1. `test_can_list_all_tasks`: Asserts `GET /api/tasks` returns status 200 with structured JSON.
2. `test_can_filter_tasks_by_status`: Asserts `GET /api/tasks?status=in-progress` returns only filtered records.
3. `test_can_create_a_task_successfully`: Asserts `POST /api/tasks` stores data and returns HTTP 201.
4. `test_validation_error_when_title_is_missing`: Asserts HTTP 422 with clear error message when title is omitted.
5. `test_validation_error_when_status_is_invalid`: Asserts HTTP 422 when status is not in `[todo, in-progress, done]`.
6. `test_can_view_a_single_task`: Asserts `GET /api/tasks/{id}` returns the specific task.
7. `test_returns_404_for_non_existent_task`: Asserts `GET /api/tasks/99999` returns HTTP 404.
8. `test_can_update_a_task`: Asserts `PATCH /api/tasks/{id}` updates task details in the database.
9. `test_can_delete_a_task`: Asserts `DELETE /api/tasks/{id}` deletes the record and returns HTTP 200.
10. `test_can_retrieve_task_statistics`: Asserts `GET /api/tasks/stats` computes correct totals and status counts.

---

## 📁 Project Structure

```text
REST API/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── TaskController.php       # Full CRUD & stats API controller
│   │   ├── Requests/
│   │   │   ├── StoreTaskRequest.php     # Validation rules for creation
│   │   │   └── UpdateTaskRequest.php    # Validation rules for updates
│   │   └── Resources/
│   │       └── TaskResource.php         # JSON API resource transformer
│   └── Models/
│       └── Task.php                     # Eloquent model with fillable & scopes
├── database/
│   ├── factories/
│   │   └── TaskFactory.php              # Factory for test data generation
│   ├── migrations/
│   │   └── 2026_09_24_161350_create_tasks_table.php # MySQL table schema
│   └── seeders/
│       ├── DatabaseSeeder.php
│       └── TaskSeeder.php               # Initial seed data
├── resources/
│   └── views/
│       └── welcome.blade.php            # Interactive HTML5/CSS3/JS UI dashboard
├── routes/
│   ├── api.php                          # RESTful API route definitions
│   └── web.php                          # Web route serving the frontend
├── tests/
│   └── Feature/
│       └── TaskApiTest.php              # 10 automated PHPUnit feature tests
├── .env.example                         # Environment configuration template
├── phpunit.xml                          # Test environment configuration
└── README.md                            # Complete setup & API documentation
```
