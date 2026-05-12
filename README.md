# Laravel Blog API - Many-to-Many Relationships & File Uploads

A RESTful blogging API built with Laravel 11, featuring advanced relationships, file uploads, authentication, and dynamic querying.

## 🚀 Features

- **User Authentication** – Register, login, logout, and fetch profile using Laravel Sanctum.
- **Posts CRUD** – Create, read, update, and delete blog posts.
- **Many-to-Many Relationship** – Posts ↔ Tags via a pivot table with timestamps.
- **File Uploads** – Featured image upload with validation, unique naming, and storage linking.
- **Auto-generated Fields** – Slugs from titles, excerpts from content, and reading time.
- **Public & Protected Routes** – Anyone can read published posts; authors manage their own.
- **Advanced Filtering & Sorting** – By category, tag, status, search across title/content/excerpt, sort by views (popularity).
- **Atomic View Count** – Increment views safely with `increment()`.
- **Role‑based Authorization** – Only post owners can edit/delete their posts.

## 🛠 Prerequisites

- PHP 8.1+
- Composer
- PostgreSQL
- Laravel 11 (the project is built on it)

## 📌 API Endpoints

All routes are prefixed with `/api/v1`.

### Public Routes (no authentication required)

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST   | `/register` | Register a new user |
| POST   | `/login`    | Login and get bearer token |
| GET    | `/posts`    | List published posts (with filters) |
| GET    | `/posts/{slug}` | View a single post (increments view count) |
| GET    | `/categories` | List all categories |
| GET    | `/categories/{category}` | View a single category |
| GET    | `/tags`    | List all tags |
| GET    | `/tags/{tag}` | View a single tag |

**Filtering example:**  
`/posts?category=technology&tag=laravel&search=API&sort_by=popular&per_page=10`

### Protected Routes (require `Authorization: Bearer <token>` header)

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST   | `/logout` | Logout (revoke current token) |
| GET    | `/me`     | Get authenticated user’s profile |
| GET    | `/my-posts` | List current user’s posts (including drafts) |
| POST   | `/posts`  | Create a new post (supports image upload) |
| PUT    | `/posts/{post}` | Update a post (sync tags, replace image) |
| DELETE | `/posts/{post}` | Delete a post |
| POST   | `/posts/{post}/publish` | Publish a draft |
| POST   | `/posts/{post}/unpublish` | Unpublish a post (return to draft) |
| DELETE | `/posts/{post}/image` | Remove featured image |
| POST   | `/categories` | Create a category |
| PUT    | `/categories/{category}` | Update a category |
| DELETE | `/categories/{category}` | Delete a category |
| POST   | `/tags`    | Create a tag |
| PUT    | `/tags/{tag}` | Update a tag |
| DELETE | `/tags/{tag}` | Delete a tag |

## 🧪 Sample cURL Requests

**1. Get all published posts**
```bash
curl http://localhost:8000/api/v1/posts -H "Accept: application/json"
```

**2. Login**
```bash
curl -X POST http://localhost:8000/api/v1/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"email": "john@blog.com", "password": "password123"}'
```
Save the returned `token` for authenticated requests.

**3. Create a post with tags and an image**
```bash
curl -X POST http://localhost:8000/api/v1/posts \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -F "title=My New Post with Image" \
  -F "content=<p>This is a post with a featured image!</p>" \
  -F "category_id=1" \
  -F "tags[]=1" \
  -F "tags[]=2" \
  -F "status=published" \
  -F "featured_image=@/path/to/your/image.jpg"
```

**4. Get my own posts (including drafts)**
```bash
curl http://localhost:8000/api/v1/my-posts \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## 🧩 Key Laravel Concepts Used

| Feature | Implementation |
|---------|----------------|
| Many‑to‑many | `belongsToMany(Tag::class)` with `attach()`, `sync()`, `detach()` |
| File system | `Storage::disk('public')` and `$file->storeAs()` |
| Atomic counters | `$post->increment('views')` |
| Auto‑generation | Model events (`booted()`) for slugs, excerpts, timestamps |
| Accessors | `getReadingTimeAttribute()`, `getFeaturedImageUrlAttribute()` |
| Query scopes | `scopePublished()`, `scopeDraft()` |
| Relationship existence | `whereHas('tags', ...)` for filtering by tag |
| Pagination | `paginate($perPage)` |

## 📦 Project Structure Highlights

- **Models** – `User`, `Post`, `Category`, `Tag` (all with proper relationships)
- **API Resources** – `PostResource`, `CategoryResource`, `TagResource`, `UserResource`
- **Form Requests** – `StorePostRequest`, `UpdatePostRequest` for validation
- **Controllers** – `AuthController`, `PostController`, `CategoryController`, `TagController`
- **Policies** – `PostPolicy` to enforce ownership
