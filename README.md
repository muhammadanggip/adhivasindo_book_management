# Book Management System

Sistem manajemen buku yang komprehensif dibangun dengan Laravel 12.x, dilengkapi dengan API Resources, Swagger Documentation, dan antarmuka web yang modern.

## Fitur Utama

### Manajemen Buku
- CRUD lengkap untuk buku (Create, Read, Update, Delete)
- Pagination dan pencarian
- Validasi data (ISBN unik, tahun publikasi, stok minimal)
- Filter berdasarkan author dan tahun

### Sistem Peminjaman
- Relasi many-to-many antara User dan Book
- Validasi stok otomatis (tidak bisa meminjam jika stok habis)
- Tanggal pengembalian yang diharapkan
- Status peminjaman (aktif, dikembalikan, terlambat)

### Autentikasi
- Laravel Breeze untuk autentikasi web
- Laravel Sanctum untuk API authentication
- Profile management
- Email verification

### Antarmuka Modern
- Bootstrap 5 dengan tema custom
- Responsive design
- Select2 untuk dropdown yang dapat dicari
- Modal konfirmasi untuk aksi berbahaya
- Icons dan styling modern

### API Resources
- RESTful API endpoints
- Laravel API Resources untuk response yang konsisten
- Swagger/OpenAPI documentation
- Authentication dengan Bearer token

### Notifikasi
- Queue system untuk email notifications
- Job untuk mengirim notifikasi peminjaman

### Testing
- Unit tests dan Feature tests
- API testing dengan authentication

## Teknologi yang Digunakan

- **Laravel 12.x** - PHP Framework
- **MySQL** - Database
- **Bootstrap 5** - CSS Framework
- **Laravel Breeze** - Authentication scaffolding
- **Laravel Sanctum** - API authentication
- **L5-Swagger** - API documentation
- **Select2** - Enhanced dropdowns
- **Bootstrap Icons** - Icon library

## Persyaratan Sistem

- PHP >= 8.2
- Composer
- MySQL >= 5.7
- Node.js & NPM (untuk asset compilation)

## Instalasi

1. **Clone repository**
   ```bash
   git clone https://github.com/yourusername/book-management.git
   cd book-management
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Setup environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Konfigurasi database**
   Edit file `.env` dan sesuaikan konfigurasi database:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=book_management
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

5. **Jalankan migrasi dan seeder**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

6. **Jalankan server**
   ```bash
   php artisan serve
   ```

7. **Akses aplikasi**
   - Web Interface: http://localhost:8000
   - API Documentation: http://localhost:8000/swagger-ui/
## Default Users

Setelah menjalankan seeder, Anda dapat login dengan:

- **User/Email**: admin@mail.com
- **Password**: admin

## API Endpoints

### Books
- `GET /api/books` - List semua buku
- `POST /api/books` - Tambah buku baru
- `GET /api/books/{id}` - Detail buku
- `PUT /api/books/{id}` - Update buku
- `DELETE /api/books/{id}` - Hapus buku

### Book Loans
- `GET /api/loans` - List semua peminjaman
- `POST /api/loans` - Buat peminjaman baru
- `GET /api/loans/{id}` - Detail peminjaman
- `PUT /api/loans/{id}/return` - Mark sebagai dikembalikan
- `GET /api/loans/user/{userId}` - Peminjaman per user

### Authentication
- `POST /api/login` - Login untuk mendapatkan token
- `POST /api/logout` - Logout
- `GET /api/user` - Get user info

## Testing

Jalankan semua tests:
```bash
php artisan test
```

## Dokumentasi API

Dokumentasi API lengkap tersedia di:
- **Swagger UI**: http://localhost:8000/swagger-ui/
