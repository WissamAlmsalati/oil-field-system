# 📁 Document Archive System

A comprehensive document management system for the Almansoori Petroleum Services backend, providing secure file storage, categorization, and access control.

## 🚀 Features

### Core Features
- **Document Upload**: Single and bulk file uploads with metadata
- **File Management**: Secure storage with organized directory structure
- **Categorization**: 10 predefined categories (Contract, Invoice, Report, etc.)
- **Tagging System**: Flexible tagging for easy organization
- **Client Association**: Link documents to specific clients
- **Access Control**: Public/private document visibility
- **Download Tracking**: Monitor document download statistics

### Advanced Features
- **Bulk Operations**: Upload and delete multiple documents at once
- **Document Preview**: In-browser preview for supported file types
- **Public Downloads**: Share documents without authentication
- **Expiry Management**: Set and track document expiration dates
- **Search & Filtering**: Advanced search with multiple filter options
- **Statistics Dashboard**: Comprehensive usage analytics
- **File Versioning**: Update documents with new file versions

### Security Features
- **Authentication Required**: All operations require valid API token
- **File Validation**: Strict file type and size validation
- **Secure Storage**: Files stored in organized, secure directory structure
- **Access Logging**: Track all document access and modifications
- **Public Access Control**: Selective public document sharing

## 📋 Supported File Types

| Category | Extensions | Max Size |
|----------|------------|----------|
| Documents | pdf, doc, docx, txt | 10MB |
| Spreadsheets | xls, xlsx | 10MB |
| Presentations | ppt, pptx | 10MB |
| Images | jpg, jpeg, png, gif | 10MB |
| Archives | zip, rar | 10MB |

## 🏗️ Database Schema

### Documents Table
```sql
CREATE TABLE documents (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    file_size BIGINT NOT NULL,
    file_type VARCHAR(50) NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    category ENUM('Contract','Invoice','Report','Certificate','License','Manual','Procedure','Policy','Form','Other') DEFAULT 'Other',
    tags JSON NULL,
    client_id BIGINT NULL,
    uploaded_by BIGINT NOT NULL,
    is_public BOOLEAN DEFAULT FALSE,
    download_count INT DEFAULT 0,
    expiry_date DATE NULL,
    metadata JSON NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE SET NULL,
    FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE CASCADE,
    
    INDEX idx_category_client (category, client_id),
    INDEX idx_file_type_uploader (file_type, uploaded_by),
    INDEX idx_public_created (is_public, created_at),
    INDEX idx_expiry_date (expiry_date)
);
```

## 🔧 Setup Instructions

### 1. Database Migration
```bash
php artisan migrate
```

### 2. Storage Configuration
Ensure your storage is properly configured in `config/filesystems.php`:
```php
'public' => [
    'driver' => 'local',
    'root' => storage_path('app/public'),
    'url' => env('APP_URL').'/storage',
    'visibility' => 'public',
],
```

### 3. Create Storage Link
```bash
php artisan storage:link
```

### 4. Directory Structure
The system will automatically create the following directory structure:
```
storage/app/public/
└── documents/
    └── YYYY/
        └── MM/
            └── DD/
                └── [timestamp]_[random].ext
```

## 📚 API Endpoints

### Authentication Required Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/documents` | Get all documents with pagination and filters |
| GET | `/api/documents/categories` | Get available document categories |
| GET | `/api/documents/stats` | Get document statistics |
| GET | `/api/documents/{id}` | Get single document details |
| GET | `/api/documents/client/{clientId}` | Get documents by client |
| POST | `/api/documents` | Upload single document |
| POST | `/api/documents/bulk-upload` | Upload multiple documents |
| PUT | `/api/documents/{id}` | Update document metadata |
| DELETE | `/api/documents/{id}` | Delete single document |
| DELETE | `/api/documents/bulk-delete` | Delete multiple documents |
| GET | `/api/documents/{id}/download` | Get download URL |
| GET | `/api/documents/{id}/download-direct` | Download file directly |
| GET | `/api/documents/{id}/preview` | Preview document in browser |

### Public Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/documents/public/download/{filename}` | Download public documents |

## 🔍 Query Parameters

### Document Listing Filters
- `page`: Page number for pagination
- `per_page`: Items per page (default: 15)
- `sort_by`: Sort field (default: created_at)
- `sort_order`: Sort direction - asc/desc (default: desc)
- `category`: Filter by category
- `client_id`: Filter by client ID
- `search`: Search in title, description, or filename
- `public_only`: Show only public documents
- `expired_only`: Show only expired documents
- `not_expired`: Show only non-expired documents

## 📊 Response Format

### Success Response
```json
{
  "success": true,
  "data": {
    // Response data
  },
  "message": "Operation completed successfully"
}
```

### Error Response
```json
{
  "success": false,
  "message": "Error description"
}
```

## 🧪 Testing

### Using the Test Script
```bash
# Make the script executable
chmod +x document_archive_test.sh

# Run the test script
./document_archive_test.sh
```

### Using Postman
1. Import the `Document_Archive_Postman_Collection.json`
2. Set environment variables:
   - `base_url`: `http://127.0.0.1:8001/api`
   - `token`: (will be set after login)
3. Run the "Login" request first to get the token
4. Test all endpoints

### Manual Testing with cURL
```bash
# Login and get token
curl -X POST http://127.0.0.1:8001/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email": "admin@almansoori.com", "password": "password123"}'

# Upload document
curl -X POST http://127.0.0.1:8001/api/documents \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -F "file=@document.pdf" \
  -F "title=Sample Document" \
  -F "category=Report" \
  -F "client_id=2"

# Get documents
curl -X GET http://127.0.0.1:8001/api/documents \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## 🔒 Security Considerations

### File Upload Security
- File type validation using MIME type checking
- File size limits (10MB per file)
- Secure file naming with timestamps and random strings
- Organized directory structure to prevent path traversal

### Access Control
- All endpoints require authentication except public downloads
- Public documents are explicitly marked and validated
- Client association for document access control
- Download tracking for audit purposes

### Data Protection
- File metadata stored separately from file content
- Secure file paths not exposed in responses
- Database transactions for bulk operations
- Proper error handling without information leakage

## 📈 Performance Optimization

### Database Indexes
- Composite indexes on frequently queried fields
- Index on expiry date for efficient filtering
- Index on public status and creation date

### File Storage
- Organized directory structure for efficient file access
- File size tracking for storage monitoring
- Automatic cleanup of orphaned files

### Caching Strategy
- Document statistics can be cached
- Category lists are static and cacheable
- Recent uploads can be cached with short TTL

## 🚨 Error Handling

### Common Error Scenarios
- **File not found**: 404 response with clear message
- **Invalid file type**: 400 response with supported types
- **File too large**: 400 response with size limit
- **Unauthorized access**: 401 response
- **Client not found**: 404 response when filtering by client
- **Database errors**: 500 response with generic message

### Validation Rules
- Title: Required, max 255 characters
- Description: Optional, max 1000 characters
- Category: Required, must be from predefined list
- Tags: Optional array of strings, max 50 characters each
- File: Required for upload, max 10MB, supported types only
- Expiry date: Optional, must be future date

## 🔄 Maintenance

### Regular Tasks
- Monitor storage usage and clean up old files
- Review expired documents for deletion
- Update document categories as needed
- Backup document metadata regularly

### Monitoring
- Track upload/download statistics
- Monitor storage space usage
- Check for failed uploads
- Review access patterns

## 📝 Changelog

### Version 1.0.0 (Current)
- Initial release with core document management
- Bulk upload and delete operations
- Document preview functionality
- Public download capability
- Comprehensive API documentation
- Test scripts and Postman collection

## 🤝 Contributing

When contributing to the document archive system:

1. Follow Laravel coding standards
2. Add proper error handling
3. Include validation for all inputs
4. Write tests for new features
5. Update documentation
6. Consider security implications

## 📞 Support

For issues or questions regarding the document archive system:

1. Check the API documentation
2. Review the test scripts
3. Check server logs for errors
4. Verify storage permissions
5. Ensure database migrations are up to date

---

**Document Archive System** - Part of Almansoori Petroleum Services Backend 