# API Documentation - Almansoori Petroleum Services Backend

## Base URL
```
http://127.0.0.1:8001/api
```

## Response Format

### Success Response
```json
{
  "success": true,
  "data": {
    // البيانات المطلوبة
  },
  "message": "Operation completed successfully",
  "pagination": {  // للصفحات فقط
    "page": 1,
    "limit": 10,
    "total": 100,
    "totalPages": 10
  }
}
```

### Error Response
```json
{
  "success": false,
  "error": {
    "code": "ERROR_CODE",
    "message": "Error description",
    "details": [  // للـ validation errors فقط
      {
        "field": "email",
        "message": "Invalid email format"
      }
    ]
  }
}
```

---

## 🔐 Authentication APIs

### 1. Login
**POST** `/auth/login`

**Headers:**
```
Content-Type: application/json
```

**Request Body:**
```json
{
  "email": "admin@almansoori.com",
  "password": "password123"
}
```

**Response (200):**
```json
{
  "success": true,
  "data": {
    "user": {
      "id": 1,
      "name": "Admin User",
      "email": "admin@almansoori.com",
      "role": "Admin",
      "avatar_url": null,
      "created_at": "2025-07-26T22:57:32.000000Z",
      "updated_at": "2025-07-26T22:57:32.000000Z"
    },
    "token": "1|abc123...",
    "token_type": "Bearer"
  },
  "message": "Login successful"
}
```

**CURL Example:**
```bash
curl -X POST http://127.0.0.1:8001/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@almansoori.com",
    "password": "password123"
  }'
```

### 2. Get Current User
**GET** `/auth/me`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Admin User",
    "email": "admin@almansoori.com",
    "role": "Admin",
    "avatar_url": null
  },
  "message": "User data retrieved successfully"
}
```

**CURL Example:**
```bash
curl -X GET http://127.0.0.1:8001/api/auth/me \
  -H "Authorization: Bearer 1|abc123..."
```

### 3. Register New User (Admin Only)
**POST** `/auth/register`

**Headers:**
```
Authorization: Bearer {admin_token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "name": "New User",
  "email": "newuser@almansoori.com",
  "password": "password123",
  "password_confirmation": "password123",
  "role": "User",
  "avatar_url": "https://example.com/avatar.jpg"
}
```

### 4. Logout
**POST** `/auth/logout`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "success": true,
  "message": "Logged out successfully"
}
```

---

## 👥 Clients Management APIs

### 1. Get All Clients
**GET** `/clients`

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `per_page` (optional): Number of items per page (default: 10)

**Response (200):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "ADNOC",
      "logo_url": "http://127.0.0.1:8001/storage/logos/logo1.jpg",
      "logo_file_path": "logos/logo1.jpg",
      "created_at": "2025-07-26T22:57:32.000000Z",
      "updated_at": "2025-07-26T22:57:32.000000Z",
      "contacts": [
        {
          "id": 1,
          "client_id": 1,
          "name": "Ahmed Ali",
          "email": "ahmed@adnoc.ae",
          "phone": "+971501234567",
          "position": "Project Manager"
        }
      ]
    }
  ],
  "message": "Clients retrieved successfully",
  "pagination": {
    "page": 1,
    "limit": 10,
    "total": 1,
    "totalPages": 1
  }
}
```

**CURL Example:**
```bash
curl -X GET "http://127.0.0.1:8001/api/clients?per_page=5" \
  -H "Authorization: Bearer 1|abc123..."
```

### 2. Create New Client
**POST** `/clients`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: multipart/form-data
```

**Request Body (FormData):**
```
name: ADNOC
logo: [file] (optional - image file)
contacts[0][name]: Ahmed Ali
contacts[0][email]: ahmed@adnoc.ae
contacts[0][phone]: +971501234567
contacts[0][position]: Project Manager
contacts[1][name]: Sara Mohamed
contacts[1][email]: sara@adnoc.ae
contacts[1][phone]: +971509876543
contacts[1][position]: Technical Lead
```

**CURL Example:**
```bash
curl -X POST http://127.0.0.1:8001/api/clients \
  -H "Authorization: Bearer 1|abc123..." \
  -F "name=ADNOC" \
  -F "logo=@/path/to/logo.jpg" \
  -F "contacts[0][name]=Ahmed Ali" \
  -F "contacts[0][email]=ahmed@adnoc.ae" \
  -F "contacts[0][phone]=+971501234567" \
  -F "contacts[0][position]=Project Manager"
```

### 3. Update Client
**PUT** `/clients/{id}`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: multipart/form-data
```

**Request Body:** Same as Create Client

### 4. Delete Client
**DELETE** `/clients/{id}`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "success": true,
  "message": "Client deleted successfully"
}
```

---

## 📋 Sub-Agreements Management APIs

### 1. Get All Sub-Agreements
**GET** `/sub-agreements`

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `per_page` (optional): Number of items per page

### 2. Get Sub-Agreements by Client
**GET** `/sub-agreements/client/{clientId}`

**Headers:**
```
Authorization: Bearer {token}
```

### 3. Create Sub-Agreement
**POST** `/sub-agreements`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: multipart/form-data
```

**Request Body:**
```json
{
  "client_id": 1,
  "name": "Drilling Services Contract 2025",
  "amount": 500000.00,
  "balance": 500000.00,
  "start_date": "2025-01-01",
  "end_date": "2025-12-31",
  "agreement_file": "[file]" // PDF file
}
```

**CURL Example:**
```bash
curl -X POST http://127.0.0.1:8001/api/sub-agreements \
  -H "Authorization: Bearer 1|abc123..." \
  -F "client_id=1" \
  -F "name=Drilling Services Contract 2025" \
  -F "amount=500000.00" \
  -F "balance=500000.00" \
  -F "start_date=2025-01-01" \
  -F "end_date=2025-12-31" \
  -F "agreement_file=@/path/to/agreement.pdf"
```

---

## 🛠️ Call-Out Jobs Management APIs

### 1. Get All Call-Out Jobs
**GET** `/call-out-jobs`

### 2. Get Call-Out Jobs by Client
**GET** `/call-out-jobs/client/{clientId}`

### 3. Create Call-Out Job
**POST** `/call-out-jobs`

**Request Body:**
```json
{
  "client_id": 1,
  "job_name": "Emergency Well Intervention",
  "work_order_number": "WO-2025-001",
  "start_date": "2025-07-27",
  "end_date": "2025-07-30",
  "documents": ["file1.pdf", "file2.pdf"] // Array of uploaded files
}
```

---

## 📊 Daily Service Logs APIs

### 1. Get All Daily Logs
**GET** `/daily-logs`

### 2. Get Daily Logs by Client
**GET** `/daily-logs/client/{clientId}`

### 3. Create Daily Service Log
**POST** `/daily-logs`

**Request Body:**
```json
{
  "client_id": 1,
  "field": "Ruwais Field",
  "well": "RW-123",
  "contract": "CT-2025-001",
  "job_no": "JOB-001",
  "date": "2025-07-27",
  "linked_job_id": "1", // ID of SubAgreement or CallOutJob
  "personnel": [
    {
      "name": "Ahmed Ali",
      "position": "Drilling Engineer",
      "hours": 8
    }
  ],
  "equipment_used": [
    {
      "name": "Drilling Rig",
      "type": "Heavy Equipment",
      "hours": 8
    }
  ],
  "almansoori_rep": {
    "name": "Mohamed Hassan",
    "signature": "base64_signature_string"
  },
  "mog_approval_1": {
    "name": "Inspector 1",
    "signature": "base64_signature_string"
  },
  "mog_approval_2": {
    "name": "Inspector 2",
    "signature": "base64_signature_string"
  }
}
```

### 4. Generate Excel for Daily Log
**POST** `/daily-logs/{id}/generate-excel`

**Response:** Excel file download

---

## 🎫 Service Tickets APIs

### 1. Get All Service Tickets
**GET** `/service-tickets`

### 2. Get Service Tickets by Client
**GET** `/service-tickets/client/{clientId}`

### 3. Create Service Ticket
**POST** `/service-tickets`

**Request Body:**
```json
{
  "client_id": 1,
  "sub_agreement_id": 1, // Optional
  "call_out_job_id": null, // Optional
  "date": "2025-07-27",
  "status": "In Field to Sign", // Options: "In Field to Sign", "Issue", "Delivered", "Invoiced"
  "amount": 25000.00,
  "related_log_ids": [1, 2, 3], // Array of DailyServiceLog IDs
  "documents": ["ticket1.pdf", "invoice1.pdf"]
}
```

### 4. Generate Service Tickets from Logs
**POST** `/service-tickets/generate`

**Request Body:**
```json
{
  "log_ids": [1, 2, 3],
  "client_id": 1,
  "sub_agreement_id": 1
}
```

---

## 🔧 Ticket Issues APIs

### 1. Get All Ticket Issues
**GET** `/ticket-issues`

### 2. Get Issues by Ticket
**GET** `/ticket-issues/ticket/{ticketId}`

### 3. Create Ticket Issue
**POST** `/ticket-issues`

**Request Body:**
```json
{
  "ticket_id": 1,
  "description": "Missing signature from client representative",
  "status": "Open", // Options: "Open", "In Progress", "Resolved"
  "remarks": "Contacted client on 27/07/2025",
  "date_reported": "2025-07-27"
}
```

---

## 📈 Dashboard APIs

### 1. Get Dashboard Stats
**GET** `/dashboard/stats`

**Response Example:**
```json
{
  "success": true,
  "data": {
    "total_clients": 5,
    "active_agreements": 12,
    "pending_tickets": 8,
    "this_month_revenue": 150000.00,
    "open_issues": 3
  }
}
```

### 2. Get Recent Activities
**GET** `/dashboard/recent-activities`

---

## 📁 Documents Management APIs

### 1. Get All Documents
**GET** `/documents`

### 2. Get Documents by Client
**GET** `/documents/client/{clientId}`

### 3. Download Document
**GET** `/documents/download/{fileId}`

### 4. Delete Document
**DELETE** `/documents/{fileId}`

---

## 👤 Users Management APIs (Admin Only)

### 1. Get All Users
**GET** `/users`

**Headers:**
```
Authorization: Bearer {admin_token}
```

### 2. Create User
**POST** `/users`

### 3. Update User
**PUT** `/users/{id}`

### 4. Delete User
**DELETE** `/users/{id}`

---

## Error Codes Reference

| Code | Description |
|------|-------------|
| `INVALID_CREDENTIALS` | خطأ في بيانات الدخول |
| `UNAUTHORIZED` | غير مصرح |
| `FORBIDDEN` | لا توجد صلاحيات كافية |
| `VALIDATION_ERROR` | خطأ في التحقق من البيانات |
| `NOT_FOUND` | البيانات غير موجودة |
| `CREATE_ERROR` | خطأ في الإنشاء |
| `UPDATE_ERROR` | خطأ في التحديث |
| `DELETE_ERROR` | خطأ في الحذف |

---

## Testing with Postman

1. **Import Collection:** يمكنك إنشاء Postman Collection بهذه endpoints
2. **Environment Variables:**
   - `base_url`: http://127.0.0.1:8001/api
   - `token`: سيتم تعيينها بعد Login

3. **Authentication Flow:**
   - Login → احفظ الـ token
   - استخدم الـ token في جميع الطلبات الأخرى

هل تريد مني إنشاء Postman Collection جاهزة أم تريد المزيد من التفاصيل حول أي endpoint معين؟
