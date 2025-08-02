# User Management System Documentation

## Overview

The User Management System for Almansoori Petroleum Services Portal provides comprehensive user authentication, authorization, and management capabilities. The system supports role-based access control with three user roles: Admin, Manager, and User.

## Features

### 🔐 Authentication
- User login with email and password
- JWT token-based authentication using Laravel Sanctum
- Secure logout with token revocation
- Current user profile retrieval

### 👥 User Management (Admin Only)
- Create, read, update, and delete users
- Role assignment and management
- Avatar upload and management
- User statistics and analytics
- Bulk user operations
- Activity logging for all user actions

### 👤 Profile Management
- Personal profile viewing and updating
- Avatar upload for profile pictures
- Password change functionality
- Email verification status

### 🛡️ Security Features
- Password hashing using Laravel's built-in hashing
- Role-based middleware protection
- Token-based API authentication
- Activity logging for audit trails
- Prevention of last admin deletion

## User Roles

### Admin
- Full system access
- Can manage all users
- Can create, update, and delete any user
- Can reset user passwords
- Can view user activity logs
- Can perform bulk operations

### Manager
- Limited administrative access
- Can view user lists (read-only)
- Can manage their own profile
- Can access most system features

### User
- Basic access
- Can manage their own profile
- Can access assigned features
- Limited system access

## API Endpoints

### Authentication Endpoints

#### POST /api/auth/login
Login with email and password.

**Request Body:**
```json
{
    "email": "user@example.com",
    "password": "password123"
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "user@example.com",
            "role": "Admin",
            "avatar_url": null,
            "email_verified_at": "2025-08-01T10:00:00.000000Z",
            "created_at": "2025-08-01T10:00:00.000000Z",
            "updated_at": "2025-08-01T10:00:00.000000Z"
        },
        "token": "1|abc123...",
        "token_type": "Bearer"
    },
    "message": "Login successful"
}
```

#### POST /api/auth/logout
Logout and revoke current token.

**Headers:** `Authorization: Bearer {token}`

#### GET /api/auth/me
Get current authenticated user information.

**Headers:** `Authorization: Bearer {token}`

#### POST /api/auth/register
Register a new user (Admin only).

**Headers:** `Authorization: Bearer {token}`

**Request Body:**
```json
{
    "name": "John Doe",
    "email": "john.doe@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "Manager"
}
```

### User Management Endpoints (Admin Only)

#### GET /api/users
Get all users with pagination and filters.

**Query Parameters:**
- `page`: Page number (default: 1)
- `per_page`: Items per page (default: 15)
- `search`: Search by name or email
- `role`: Filter by role (Admin, Manager, User)
- `status`: Filter by status (active, inactive)

**Headers:** `Authorization: Bearer {token}`

#### GET /api/users/stats
Get user statistics and analytics.

**Headers:** `Authorization: Bearer {token}`

**Response:**
```json
{
    "success": true,
    "data": {
        "total_users": 25,
        "active_users": 20,
        "inactive_users": 5,
        "by_role": [
            {"role": "Admin", "count": 3},
            {"role": "Manager", "count": 7},
            {"role": "User", "count": 15}
        ],
        "recent_registrations": [...],
        "users_with_avatars": 12,
        "users_without_avatars": 13
    },
    "message": "User statistics retrieved successfully"
}
```

#### GET /api/users/roles
Get available user roles and descriptions.

**Headers:** `Authorization: Bearer {token}`

#### POST /api/users
Create a new user.

**Headers:** `Authorization: Bearer {token}`

**Request Body (Form Data):**
- `name`: User's full name
- `email`: User's email address
- `password`: User's password
- `password_confirmation`: Password confirmation
- `role`: User role (Admin, Manager, User)
- `avatar`: Profile picture file (optional)

#### GET /api/users/{id}
Get specific user details.

**Headers:** `Authorization: Bearer {token}`

#### PUT /api/users/{id}
Update user information.

**Headers:** `Authorization: Bearer {token}`

**Request Body (Form Data):**
- `name`: User's full name
- `email`: User's email address
- `role`: User role
- `avatar`: Profile picture file (optional)

#### POST /api/users/{id}/reset-password
Reset user password (Admin only).

**Headers:** `Authorization: Bearer {token}`

**Request Body:**
```json
{
    "new_password": "newpassword123",
    "new_password_confirmation": "newpassword123"
}
```

#### GET /api/users/{id}/activity-log
Get user activity log.

**Headers:** `Authorization: Bearer {token}`

#### DELETE /api/users/{id}
Delete user.

**Headers:** `Authorization: Bearer {token}`

#### POST /api/users/bulk-delete
Bulk delete multiple users.

**Headers:** `Authorization: Bearer {token}`

**Request Body:**
```json
{
    "user_ids": [2, 3, 4]
}
```

### Profile Management Endpoints

#### GET /api/profile
Get current user's profile.

**Headers:** `Authorization: Bearer {token}`

#### PUT /api/profile
Update current user's profile.

**Headers:** `Authorization: Bearer {token}`

**Request Body (Form Data):**
- `name`: User's full name
- `email`: User's email address
- `avatar`: Profile picture file (optional)

#### POST /api/profile/change-password
Change current user's password.

**Headers:** `Authorization: Bearer {token}`

**Request Body:**
```json
{
    "current_password": "oldpassword123",
    "new_password": "newpassword123",
    "new_password_confirmation": "newpassword123"
}
```

## File Upload

### Avatar Upload
- Supported formats: JPEG, PNG, JPG, GIF
- Maximum file size: 2MB
- Files are stored in `storage/app/public/avatars/YYYY/MM/DD/`
- Automatic file naming with timestamp and random string

### File Storage
- Uses Laravel's public disk
- Files are accessible via `/storage/` URL
- Automatic cleanup when user is deleted or avatar is updated

## Error Handling

### Common Error Responses

#### 401 Unauthorized
```json
{
    "success": false,
    "error": {
        "code": "INVALID_CREDENTIALS",
        "message": "Invalid email or password"
    }
}
```

#### 403 Forbidden
```json
{
    "success": false,
    "message": "Access denied. Insufficient permissions."
}
```

#### 404 Not Found
```json
{
    "success": false,
    "message": "User not found"
}
```

#### 422 Validation Error
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "email": ["The email field is required."],
        "password": ["The password must be at least 8 characters."]
    }
}
```

## Security Considerations

### Password Security
- Passwords are hashed using Laravel's `Hash::make()`
- Minimum password length: 8 characters
- Password confirmation required for changes

### Token Security
- JWT tokens are used for API authentication
- Tokens are automatically revoked on logout
- Tokens are revoked when password is changed

### Role Protection
- Admin-only endpoints are protected by `role:Admin` middleware
- Users cannot access endpoints they don't have permission for
- Last admin user cannot be deleted

### File Upload Security
- File type validation
- File size limits
- Secure file naming
- Automatic cleanup

## Activity Logging

The system automatically logs all user activities including:
- User creation, updates, and deletion
- Profile changes
- Password changes
- Login/logout events
- Role assignments

Activity logs can be accessed via the `/api/users/{id}/activity-log` endpoint.

## Testing

### Postman Collection
Import the `User_Management_Postman_Collection.json` file into Postman to test all endpoints.

### Environment Variables
Set up the following environment variables in Postman:
- `base_url`: Your API base URL (e.g., `http://127.0.0.1:8001`)
- `auth_token`: Authentication token (automatically set after login)

### Test Flow
1. Login with admin credentials
2. Copy the token from the response
3. Set the `auth_token` environment variable
4. Test all other endpoints

## Database Schema

### Users Table
```sql
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('Admin', 'Manager', 'User') DEFAULT 'User',
    avatar_url VARCHAR(255) NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

## Dependencies

- Laravel 10.x
- Laravel Sanctum (for API authentication)
- Spatie Permission (for role management)
- Spatie Activity Log (for activity logging)

## Installation and Setup

1. Ensure all dependencies are installed:
```bash
composer install
```

2. Run database migrations:
```bash
php artisan migrate
```

3. Seed the database with admin user:
```bash
php artisan db:seed --class=AdminSeeder
```

4. Create storage link:
```bash
php artisan storage:link
```

5. Start the development server:
```bash
php artisan serve --host=127.0.0.1 --port=8001
```

## Default Admin Credentials

After running the seeder, you can login with:
- Email: `admin@almansoori.com`
- Password: `password123`

## Support

For technical support or questions about the User Management System, please refer to the main API documentation or contact the development team. 