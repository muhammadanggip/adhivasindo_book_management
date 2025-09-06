# API Documentation - Book Management System

## Overview
Sistem manajemen buku ini menggunakan **Laravel API Resources** untuk memberikan response JSON yang konsisten dan terstruktur. API Resources membantu mengontrol format data yang dikembalikan ke client.

## API Resources yang Digunakan

### 1. BookResource
Mengubah model `Book` menjadi response JSON yang konsisten.

**Format Response:**
```json
{
  "id": 1,
  "title": "Laravel: Up & Running",
  "author": "Matt Stauffer",
  "published_year": 2020,
  "isbn": "9781492041203",
  "stock": 5,
  "available_stock": 3,
  "is_available": true,
  "created_at": "2024-01-15T10:30:00.000000Z",
  "updated_at": "2024-01-15T10:30:00.000000Z"
}
```

### 2. BookLoanResource
Mengubah data peminjaman buku menjadi response JSON yang terstruktur.

**Format Response:**
```json
{
  "id": 1,
  "user_id": 1,
  "book_id": 1,
  "user_name": "John Doe",
  "user_email": "john@example.com",
  "book_title": "Laravel: Up & Running",
  "book_author": "Matt Stauffer",
  "loaned_at": "2024-01-15T10:30:00.000000Z",
  "expected_return_at": "2024-01-22T10:30:00.000000Z",
  "returned_at": null,
  "is_returned": false,
  "is_overdue": false,
  "created_at": "2024-01-15T10:30:00.000000Z",
  "updated_at": "2024-01-15T10:30:00.000000Z"
}
```

### 3. UserResource
Mengubah model `User` menjadi response JSON yang aman (tanpa password).

**Format Response:**
```json
{
  "id": 1,
  "name": "John Doe",
  "email": "john@example.com",
  "email_verified_at": "2024-01-15T10:30:00.000000Z",
  "is_verified": true,
  "created_at": "2024-01-15T10:30:00.000000Z",
  "updated_at": "2024-01-15T10:30:00.000000Z"
}
```

## API Endpoints

### Authentication
Semua endpoint memerlukan authentication menggunakan Laravel Sanctum.

**Headers yang diperlukan:**
```
Authorization: Bearer {token}
Content-Type: application/json
Accept: application/json
```

### Books API

#### GET /api/books
Mendapatkan daftar buku dengan pagination dan filter.

**Query Parameters:**
- `search` (optional): Pencarian berdasarkan judul buku
- `author` (optional): Filter berdasarkan penulis
- `year` (optional): Filter berdasarkan tahun terbit
- `page` (optional): Halaman untuk pagination

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "title": "Laravel: Up & Running",
      "author": "Matt Stauffer",
      "published_year": 2020,
      "isbn": "9781492041203",
      "stock": 5,
      "available_stock": 3,
      "is_available": true,
      "created_at": "2024-01-15T10:30:00.000000Z",
      "updated_at": "2024-01-15T10:30:00.000000Z"
    }
  ],
  "links": {
    "first": "http://localhost/api/books?page=1",
    "last": "http://localhost/api/books?page=2",
    "prev": null,
    "next": "http://localhost/api/books?page=2"
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 2,
    "per_page": 15,
    "to": 15,
    "total": 30
  }
}
```

#### POST /api/books
Membuat buku baru.

**Request Body:**
```json
{
  "title": "Laravel: Up & Running",
  "author": "Matt Stauffer",
  "published_year": 2020,
  "isbn": "9781492041203",
  "stock": 5
}
```

**Response:** BookResource

#### GET /api/books/{id}
Mendapatkan detail buku berdasarkan ID.

**Response:** BookResource

#### PUT /api/books/{id}
Mengupdate buku berdasarkan ID.

**Request Body:** (sama seperti POST, semua field optional)

**Response:** BookResource

#### DELETE /api/books/{id}
Menghapus buku berdasarkan ID.

**Response:**
```json
{
  "message": "Book deleted successfully"
}
```

### Book Loans API

#### GET /api/loans
Mendapatkan daftar semua peminjaman buku.

**Query Parameters:**
- `user_id` (optional): Filter berdasarkan user ID
- `page` (optional): Halaman untuk pagination

**Response:** Collection of BookLoanResource

#### POST /api/loans
Membuat peminjaman buku baru.

**Request Body:**
```json
{
  "user_id": 1,
  "book_id": 1,
  "expected_return_at": "2024-01-22T10:30:00.000000Z"
}
```

**Response:** BookLoanResource dengan status 201

#### GET /api/loans/{id}
Mendapatkan detail peminjaman berdasarkan ID.

**Response:** BookLoanResource

#### PUT /api/loans/{id}/return
Menandai buku sebagai dikembalikan.

**Response:** BookLoanResource

#### GET /api/loans/user/{userId}
Mendapatkan daftar peminjaman berdasarkan user ID.

**Response:** Collection of BookLoanResource

## Keuntungan Menggunakan API Resources

### 1. **Konsistensi**
- Format response yang sama di semua endpoint
- Field yang konsisten untuk setiap resource

### 2. **Keamanan**
- Menyembunyikan field sensitif (seperti password)
- Kontrol penuh atas data yang diekspos

### 3. **Transformasi Data**
- Mengubah format data sesuai kebutuhan
- Menambahkan field computed (seperti `is_available`, `is_overdue`)

### 4. **Maintainability**
- Mudah untuk mengubah format response
- Centralized logic untuk formatting

### 5. **Documentation**
- Swagger/OpenAPI integration
- Self-documenting API

## Contoh Penggunaan

### JavaScript/Fetch
```javascript
// Mendapatkan daftar buku
const response = await fetch('/api/books', {
  headers: {
    'Authorization': 'Bearer ' + token,
    'Accept': 'application/json'
  }
});
const data = await response.json();

// Membuat peminjaman baru
const loanResponse = await fetch('/api/loans', {
  method: 'POST',
  headers: {
    'Authorization': 'Bearer ' + token,
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  },
  body: JSON.stringify({
    user_id: 1,
    book_id: 1,
    expected_return_at: '2024-01-22T10:30:00.000000Z'
  })
});
const loan = await loanResponse.json();
```

### PHP/cURL
```php
// Mendapatkan daftar buku
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost/api/books');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $token,
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$books = json_decode($response, true);
curl_close($ch);
```

## Error Handling

API menggunakan standard HTTP status codes:

- `200` - Success
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized
- `404` - Not Found
- `422` - Validation Error
- `500` - Server Error

**Error Response Format:**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "title": ["The title field is required."],
    "isbn": ["The isbn has already been taken."]
  }
}
```

## Testing

Untuk testing API, gunakan tools seperti:
- Postman
- Insomnia
- Laravel HTTP Client
- PHPUnit Feature Tests

**Contoh Test:**
```php
public function test_can_get_books_list()
{
    $response = $this->getJson('/api/books');
    
    $response->assertStatus(200)
             ->assertJsonStructure([
                 'data' => [
                     '*' => [
                         'id',
                         'title',
                         'author',
                         'published_year',
                         'isbn',
                         'stock',
                         'available_stock',
                         'is_available'
                     ]
                 ],
                 'links',
                 'meta'
             ]);
}
```
