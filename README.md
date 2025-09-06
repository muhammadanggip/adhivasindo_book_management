# Book Management System

A comprehensive Laravel 12.x API for managing books and book loans with authentication, search functionality, and automated notifications.

## Features

### 📚 Book Management
- **CRUD Operations**: Create, read, update, and delete books
- **Search & Filter**: Search by title, filter by author and publication year
- **Validation**: ISBN uniqueness, year validation, stock management
- **Pagination**: Efficient data loading with pagination

### 📖 Book Loan System
- **Many-to-Many Relationship**: Users can borrow multiple books, books can be borrowed by multiple users
- **Stock Management**: Automatic stock tracking and availability checking
- **Loan History**: Track loan and return dates
- **Validation**: Prevent duplicate loans and unavailable book loans

### 🔐 Authentication
- **Laravel Sanctum**: API token-based authentication
- **Laravel Breeze**: Complete authentication scaffolding
- **User Management**: User registration, login, and profile management

### 🔔 Notifications
- **Queue System**: Asynchronous email notifications for book loans
- **Job Processing**: Background job processing for better performance
- **Logging**: Comprehensive logging for debugging and monitoring

### 🧪 Testing
- **Feature Tests**: Complete API endpoint testing
- **Unit Tests**: Individual component testing
- **Database Testing**: Test database isolation and cleanup

### 📖 API Documentation
- **Swagger/OpenAPI**: Interactive API documentation
- **Resource Transformers**: Consistent API response formatting
- **Validation Documentation**: Complete request/response schemas

## Installation

### Prerequisites
- PHP 8.2 or higher
- Composer
- MySQL 5.7 or higher
- Node.js and NPM (for frontend assets)

### Step 1: Clone the Repository
```bash
git clone <repository-url>
cd book_management
```

### Step 2: Install Dependencies
```bash
composer install
npm install
```

### Step 3: Environment Configuration
```bash
cp .env.example .env
php artisan key:generate
```

Update the `.env` file with your database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=book_management
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### Step 4: Database Setup
```bash
php artisan migrate
php artisan db:seed
```

### Step 5: Generate API Documentation
```bash
php artisan l5-swagger:generate
```

### Step 6: Start the Development Server
```bash
php artisan serve
```

The application will be available at `http://localhost:8000`

## API Endpoints

### Authentication
- `POST /api/register` - Register a new user
- `POST /api/login` - Login user
- `POST /api/logout` - Logout user
- `GET /api/user` - Get authenticated user

### Books
- `GET /api/books` - List all books (with search/filter)
- `POST /api/books` - Create a new book
- `GET /api/books/{id}` - Get a specific book
- `PUT /api/books/{id}` - Update a book
- `DELETE /api/books/{id}` - Delete a book

### Book Loans
- `GET /api/loans` - List all loans
- `POST /api/loans` - Create a new loan
- `GET /api/loans/{id}` - Get a specific loan
- `PUT /api/loans/{id}` - Update a loan (return book)
- `DELETE /api/loans/{id}` - Delete a loan
- `GET /api/loans/user/{user_id}` - Get loans for a specific user

## API Usage Examples

### Get Books with Search
```bash
curl -X GET "http://localhost:8000/api/books?search=Laravel&author=Taylor&year=2023" \
  -H "Accept: application/json"
```

### Create a Book
```bash
curl -X POST "http://localhost:8000/api/books" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "title": "Laravel: Up & Running",
    "author": "Matt Stauffer",
    "published_year": 2023,
    "isbn": "978-1-492-04068-8",
    "stock": 5
  }'
```

### Loan a Book
```bash
curl -X POST "http://localhost:8000/api/loans" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "user_id": 1,
    "book_id": 1
  }'
```

## Database Schema

### Books Table
```sql
- id (primary key)
- title (string)
- author (string)
- published_year (year)
- isbn (string, unique)
- stock (integer)
- created_at (timestamp)
- updated_at (timestamp)
```

### Book Loans Table
```sql
- id (primary key)
- user_id (foreign key)
- book_id (foreign key)
- loaned_at (timestamp)
- returned_at (timestamp, nullable)
- created_at (timestamp)
- updated_at (timestamp)
```

## Testing

### Run All Tests
```bash
php artisan test
```

### Run Specific Test Suites
```bash
php artisan test --filter=BookTest
php artisan test --filter=BookLoanTest
```

### Test Coverage
```bash
php artisan test --coverage
```

## Queue Processing

### Start Queue Worker
```bash
php artisan queue:work
```

### Process Failed Jobs
```bash
php artisan queue:retry all
```

## API Documentation

Access the interactive API documentation at:
- **Swagger UI**: `http://localhost:8000/api/documentation`

## Code Quality

### Code Style
```bash
./vendor/bin/pint
```

### Static Analysis
```bash
./vendor/bin/phpstan analyse
```

## Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   └── Api/
│   │       ├── BookController.php
│   │       └── BookLoanController.php
│   └── Resources/
│       ├── BookResource.php
│       └── BookLoanResource.php
├── Jobs/
│   └── SendBookLoanNotification.php
├── Models/
│   ├── Book.php
│   └── User.php
database/
├── factories/
│   └── BookFactory.php
├── migrations/
│   ├── create_books_table.php
│   └── create_book_loans_table.php
└── seeders/
    ├── BookSeeder.php
    └── UserSeeder.php
tests/
├── Feature/
│   ├── BookTest.php
│   └── BookLoanTest.php
```

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Support

For support, email support@example.com or create an issue in the repository.

## Changelog

### Version 1.0.0
- Initial release
- Book management system
- Book loan system
- Authentication with Laravel Sanctum
- API documentation with Swagger
- Comprehensive test suite
- Queue system for notifications
