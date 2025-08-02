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

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `page` (optional): Page number for pagination
- `per_page` (optional): Items per page (default: 15)

**Response (200):**
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "log_number": "DSL-000001",
        "client_id": 1,
        "field": "Ruwais Field",
        "well": "RW-123",
        "contract": "CT-2025-001",
        "job_no": "JOB-001",
        "date": "2025-07-27",
        "linked_job_id": "1",
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
            "hours": 8
          }
        ],
        "almansoori_rep": [
          {
            "name": "Mohamed Hassan",
            "position": "Field Supervisor"
          }
        ],
        "mog_approval_1": {
          "name": "Inspector 1",
          "signature": "base64_signature_string",
          "date": "2025-07-27"
        },
        "mog_approval_2": {
          "name": "Inspector 2",
          "signature": "base64_signature_string",
          "date": "2025-07-27"
        },
        "excel_file_path": null,
        "excel_file_name": null,
        "pdf_file_path": null,
        "pdf_file_name": null,
        "created_at": "2025-07-27T10:00:00.000000Z",
        "updated_at": "2025-07-27T10:00:00.000000Z",
        "client": {
          "id": 1,
          "name": "ADNOC",
          "email": "contact@adnoc.ae"
        }
      }
    ],
    "total": 1,
    "per_page": 15
  },
  "message": "Daily service logs retrieved successfully"
}
```

### 2. Get Daily Log by ID
**GET** `/daily-logs/{id}`

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
    "log_number": "DSL-000001",
    "client_id": 1,
    "field": "Ruwais Field",
    "well": "RW-123",
    "contract": "CT-2025-001",
    "job_no": "JOB-001",
    "date": "2025-07-27",
    "linked_job_id": "1",
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
        "hours": 8
      }
    ],
    "almansoori_rep": [
      {
        "name": "Mohamed Hassan",
        "position": "Field Supervisor"
      }
    ],
    "mog_approval_1": {
      "name": "Inspector 1",
      "signature": "base64_signature_string",
      "date": "2025-07-27"
    },
    "mog_approval_2": {
      "name": "Inspector 2",
      "signature": "base64_signature_string",
      "date": "2025-07-27"
    },
    "excel_file_path": null,
    "excel_file_name": null,
    "pdf_file_path": null,
    "pdf_file_name": null,
    "created_at": "2025-07-27T10:00:00.000000Z",
    "updated_at": "2025-07-27T10:00:00.000000Z",
    "client": {
      "id": 1,
      "name": "ADNOC",
      "email": "contact@adnoc.ae"
    }
  },
  "message": "Daily service log retrieved successfully"
}
```

### 3. Get Daily Logs by Client
**GET** `/daily-logs/client/{clientId}`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "log_number": "DSL-000001",
      "client_id": 1,
      "field": "Ruwais Field",
      "well": "RW-123",
      "contract": "CT-2025-001",
      "job_no": "JOB-001",
      "date": "2025-07-27",
      "linked_job_id": "1",
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
          "hours": 8
        }
      ],
      "almansoori_rep": [
        {
          "name": "Mohamed Hassan",
          "position": "Field Supervisor"
        }
      ],
      "mog_approval_1": {
        "name": "Inspector 1",
        "signature": "base64_signature_string",
        "date": "2025-07-27"
      },
      "mog_approval_2": {
        "name": "Inspector 2",
        "signature": "base64_signature_string",
        "date": "2025-07-27"
      },
      "excel_file_path": null,
      "excel_file_name": null,
      "pdf_file_path": null,
      "pdf_file_name": null,
      "created_at": "2025-07-27T10:00:00.000000Z",
      "updated_at": "2025-07-27T10:00:00.000000Z",
      "client": {
        "id": 1,
        "name": "ADNOC",
        "email": "contact@adnoc.ae"
      }
    }
  ],
  "message": "Client daily service logs retrieved successfully"
}
```

### 4. Create Daily Service Log
**POST** `/daily-logs`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: multipart/form-data
```

**Request Body:**
```json
{
  "client_id": 1,
  "field": "Ruwais Field",
  "well": "RW-123",
  "contract": "CT-2025-001",
  "job_no": "JOB-001",
  "date": "2025-07-27",
  "linked_job_id": "1",
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
      "hours": 8
    }
  ],
  "almansoori_rep": [
    {
      "name": "Mohamed Hassan",
      "position": "Field Supervisor"
    }
  ],
  "mog_approval_1": {
    "name": "Inspector 1",
    "signature": "base64_signature_string",
    "date": "2025-07-27"
  },
  "mog_approval_2": {
    "name": "Inspector 2",
    "signature": "base64_signature_string",
    "date": "2025-07-27"
  },
  "excel_file": "file", // Optional - Excel file upload
  "pdf_file": "file"    // Optional - PDF file upload
}
```

**Response (201):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "log_number": "DSL-000001",
    "client_id": 1,
    "field": "Ruwais Field",
    "well": "RW-123",
    "contract": "CT-2025-001",
    "job_no": "JOB-001",
    "date": "2025-07-27",
    "linked_job_id": "1",
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
        "hours": 8
      }
    ],
    "almansoori_rep": [
      {
        "name": "Mohamed Hassan",
        "position": "Field Supervisor"
      }
    ],
    "mog_approval_1": {
      "name": "Inspector 1",
      "signature": "base64_signature_string",
      "date": "2025-07-27"
    },
    "mog_approval_2": {
      "name": "Inspector 2",
      "signature": "base64_signature_string",
      "date": "2025-07-27"
    },
    "excel_file_path": "daily_logs/excel/excel_1234567890_file.xlsx",
    "excel_file_name": "excel_1234567890_file.xlsx",
    "pdf_file_path": "daily_logs/pdf/pdf_1234567890_file.pdf",
    "pdf_file_name": "pdf_1234567890_file.pdf",
    "created_at": "2025-07-27T10:00:00.000000Z",
    "updated_at": "2025-07-27T10:00:00.000000Z",
    "client": {
      "id": 1,
      "name": "ADNOC",
      "email": "contact@adnoc.ae"
    }
  },
  "message": "Daily service log created successfully"
}
```

### 5. Update Daily Service Log
**PUT** `/daily-logs/{id}`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: multipart/form-data
```

**Request Body:** Same as Create Daily Service Log

**Response (200):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "log_number": "DSL-000001",
    "client_id": 1,
    "field": "Updated Field",
    "well": "RW-123",
    "contract": "CT-2025-001",
    "job_no": "JOB-001",
    "date": "2025-07-27",
    "linked_job_id": "1",
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
        "hours": 8
      }
    ],
    "almansoori_rep": [
      {
        "name": "Mohamed Hassan",
        "position": "Field Supervisor"
      }
    ],
    "mog_approval_1": {
      "name": "Inspector 1",
      "signature": "base64_signature_string",
      "date": "2025-07-27"
    },
    "mog_approval_2": {
      "name": "Inspector 2",
      "signature": "base64_signature_string",
      "date": "2025-07-27"
    },
    "excel_file_path": "daily_logs/excel/excel_1234567890_file.xlsx",
    "excel_file_name": "excel_1234567890_file.xlsx",
    "pdf_file_path": "daily_logs/pdf/pdf_1234567890_file.pdf",
    "pdf_file_name": "pdf_1234567890_file.pdf",
    "created_at": "2025-07-27T10:00:00.000000Z",
    "updated_at": "2025-07-27T10:00:00.000000Z",
    "client": {
      "id": 1,
      "name": "ADNOC",
      "email": "contact@adnoc.ae"
    }
  },
  "message": "Daily service log updated successfully"
}
```

### 6. Delete Daily Service Log
**DELETE** `/daily-logs/{id}`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "success": true,
  "message": "Daily service log deleted successfully"
}
```

### 7. Generate Excel for Daily Log
**POST** `/daily-logs/{id}/generate-excel`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "success": true,
  "data": {
    "file_path": "daily_logs/excel/daily_service_log_DSL-000001_2025-07-27_10-00-00.xlsx",
    "file_name": "daily_service_log_DSL-000001_2025-07-27_10-00-00.xlsx",
    "download_url": "http://127.0.0.1:8001/storage/daily_logs/excel/daily_service_log_DSL-000001_2025-07-27_10-00-00.xlsx",
    "public_download_url": "http://127.0.0.1:8001/download/daily_service_log_DSL-000001_2025-07-27_10-00-00.xlsx"
  },
  "message": "Excel file generated successfully"
}
```

### 8. Download File URL
**GET** `/daily-logs/{id}/download/{type}`

**Headers:**
```
Authorization: Bearer {token}
```

**Parameters:**
- `type`: `excel` or `pdf`

**Response (200):**
```json
{
  "success": true,
  "data": {
    "download_url": "http://127.0.0.1:8001/storage/daily_logs/excel/file.xlsx",
    "file_name": "file.xlsx"
  },
  "message": "File download URL generated successfully"
}
```

### 9. Direct File Download
**GET** `/daily-logs/{id}/download-file/{type}`

**Headers:**
```
Authorization: Bearer {token}
```

**Parameters:**
- `type`: `excel` or `pdf`

**Response:** Direct file download with proper headers

**CURL Examples:**

**Create Daily Service Log:**
```bash
curl -X POST http://127.0.0.1:8001/api/daily-logs \
  -H "Authorization: Bearer {token}" \
  -F "client_id=1" \
  -F "field=Ruwais Field" \
  -F "well=RW-123" \
  -F "contract=CT-2025-001" \
  -F "job_no=JOB-001" \
  -F "date=2025-07-27" \
  -F "personnel[0][name]=Ahmed Ali" \
  -F "personnel[0][position]=Drilling Engineer" \
  -F "personnel[0][hours]=8" \
  -F "equipment_used[0][name]=Drilling Rig" \
  -F "equipment_used[0][hours]=8" \
  -F "almansoori_rep[0][name]=Mohamed Hassan" \
  -F "almansoori_rep[0][position]=Field Supervisor"
```

**Generate Excel:**
```bash
curl -X POST http://127.0.0.1:8001/api/daily-logs/1/generate-excel \
  -H "Authorization: Bearer {token}"
```

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

## 🎫 Service Tickets Management APIs

### 1. Get All Service Tickets
**GET** `/service-tickets`

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
```

**Response (200):**
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "ticket_number": "ST-000001",
        "client_id": 2,
        "sub_agreement_id": null,
        "call_out_job_id": null,
        "date": "2025-08-01T00:00:00.000000Z",
        "status": "In Field to Sign",
        "amount": "1500.00",
        "related_log_ids": null,
        "documents": null,
        "created_at": "2025-08-01T22:17:10.000000Z",
        "updated_at": "2025-08-01T22:17:10.000000Z",
        "client": {
          "id": 2,
          "name": "test company"
        },
        "sub_agreement": null
      }
    ],
    "per_page": 15,
    "total": 1
  },
  "message": "Service tickets retrieved successfully"
}
```

### 2. Create Service Ticket
**POST** `/service-tickets`

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

**Request Body:**
```json
{
  "client_id": 2,
  "sub_agreement_id": null,
  "call_out_job_id": null,
  "date": "2025-08-01",
  "status": "In Field to Sign",
  "amount": 1500.00,
  "related_log_ids": [1, 2, 3],
  "documents": [
    {
      "name": "Invoice",
      "file_path": "/path/to/file.pdf",
      "file_type": "pdf",
      "upload_date": "2025-08-01"
    }
  ]
}
```

**Response (201):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "ticket_number": "ST-000001",
    "client_id": 2,
    "date": "2025-08-01T00:00:00.000000Z",
    "status": "In Field to Sign",
    "amount": "1500.00",
    "client": {
      "id": 2,
      "name": "test company"
    }
  },
  "message": "Service ticket created successfully"
}
```

### 3. Get Service Ticket by ID
**GET** `/service-tickets/{id}`

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
```

**Response (200):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "ticket_number": "ST-000001",
    "client_id": 2,
    "date": "2025-08-01T00:00:00.000000Z",
    "status": "In Field to Sign",
    "amount": "1500.00",
    "client": {
      "id": 2,
      "name": "test company"
    },
    "sub_agreement": null,
    "call_out_job": null,
    "ticket_issues": []
  },
  "message": "Service ticket retrieved successfully"
}
```

### 4. Update Service Ticket
**PUT** `/service-tickets/{id}`

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

**Request Body:**
```json
{
  "client_id": 2,
  "date": "2025-08-01",
  "status": "Delivered",
  "amount": 1800.00
}
```

**Response (200):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "ticket_number": "ST-000001",
    "client_id": 2,
    "date": "2025-08-01T00:00:00.000000Z",
    "status": "Delivered",
    "amount": "1800.00",
    "client": {
      "id": 2,
      "name": "test company"
    }
  },
  "message": "Service ticket updated successfully"
}
```

### 5. Delete Service Ticket
**DELETE** `/service-tickets/{id}`

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
```

**Response (200):**
```json
{
  "success": true,
  "message": "Service ticket deleted successfully"
}
```

### 6. Get Service Tickets by Client
**GET** `/service-tickets/client/{clientId}`

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
```

**Response (200):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "ticket_number": "ST-000001",
      "client_id": 2,
      "date": "2025-08-01T00:00:00.000000Z",
      "status": "In Field to Sign",
      "amount": "1500.00",
      "client": {
        "id": 2,
        "name": "test company"
      }
    }
  ],
  "message": "Client service tickets retrieved successfully"
}
```

### 7. Generate Service Ticket from Logs
**POST** `/service-tickets/generate`

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

**Request Body:**
```json
{
  "client_id": 2,
  "log_ids": [12, 13, 14],
  "sub_agreement_id": null,
  "call_out_job_id": null,
  "date": "2025-08-01",
  "status": "In Field to Sign",
  "amount": 2000.00
}
```

**Response (201):**
```json
{
  "success": true,
  "data": {
    "id": 2,
    "ticket_number": "ST-000002",
    "client_id": 2,
    "date": "2025-08-01T00:00:00.000000Z",
    "status": "In Field to Sign",
    "amount": "2000.00",
    "client": {
      "id": 2,
      "name": "test company"
    }
  },
  "message": "Service ticket generated from logs successfully"
}
```

**CURL Examples:**

```bash
# Get all service tickets
curl -X GET http://127.0.0.1:8001/api/service-tickets \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"

# Create service ticket
curl -X POST http://127.0.0.1:8001/api/service-tickets \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "client_id": 2,
    "date": "2025-08-01",
    "status": "In Field to Sign",
    "amount": 1500.00
  }'

# Generate from logs
curl -X POST http://127.0.0.1:8001/api/service-tickets/generate \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "client_id": 2,
    "log_ids": [12],
    "date": "2025-08-01",
    "status": "In Field to Sign",
    "amount": 2000.00
  }'
```

---

## 🚨 Ticket Issues Management APIs

### 1. Get All Ticket Issues
**GET** `/ticket-issues`

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
```

**Response (200):**
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "ticket_id": 1,
        "description": "Missing signature from client representative",
        "status": "In Progress",
        "remarks": "Client agreed to sign tomorrow",
        "date_reported": "2025-08-01T00:00:00.000000Z",
        "created_at": "2025-08-01T22:36:55.000000Z",
        "updated_at": "2025-08-01T22:38:47.000000Z",
        "ticket": {
          "id": 1,
          "ticket_number": "ST-000001",
          "client_id": 2,
          "date": "2025-08-01T00:00:00.000000Z",
          "status": "Delivered",
          "amount": "1800.00"
        }
      }
    ],
    "per_page": 15,
    "total": 1
  },
  "message": "Ticket issues retrieved successfully"
}
```

### 2. Create Ticket Issue
**POST** `/ticket-issues`

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

**Request Body:**
```json
{
  "ticket_id": 1,
  "description": "Missing signature from client representative",
  "status": "Open",
  "remarks": "Contacted client on 27/07/2025",
  "date_reported": "2025-08-01"
}
```

**Response (201):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "ticket_id": 1,
    "description": "Missing signature from client representative",
    "status": "Open",
    "remarks": "Contacted client on 27/07/2025",
    "date_reported": "2025-08-01T00:00:00.000000Z",
    "ticket": {
      "id": 1,
      "ticket_number": "ST-000001"
    }
  },
  "message": "Ticket issue created successfully"
}
```

### 3. Get Ticket Issue by ID
**GET** `/ticket-issues/{id}`

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
```

**Response (200):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "ticket_id": 1,
    "description": "Missing signature from client representative",
    "status": "In Progress",
    "remarks": "Client agreed to sign tomorrow",
    "date_reported": "2025-08-01T00:00:00.000000Z",
    "ticket": {
      "id": 1,
      "ticket_number": "ST-000001",
      "client_id": 2,
      "date": "2025-08-01T00:00:00.000000Z",
      "status": "Delivered",
      "amount": "1800.00"
    }
  },
  "message": "Ticket issue retrieved successfully"
}
```

### 4. Update Ticket Issue
**PUT** `/ticket-issues/{id}`

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

**Request Body:**
```json
{
  "ticket_id": 1,
  "description": "Missing signature from client representative",
  "status": "In Progress",
  "remarks": "Client agreed to sign tomorrow",
  "date_reported": "2025-08-01"
}
```

**Response (200):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "ticket_id": 1,
    "description": "Missing signature from client representative",
    "status": "In Progress",
    "remarks": "Client agreed to sign tomorrow",
    "date_reported": "2025-08-01T00:00:00.000000Z",
    "ticket": {
      "id": 1,
      "ticket_number": "ST-000001"
    }
  },
  "message": "Ticket issue updated successfully"
}
```

### 5. Delete Ticket Issue
**DELETE** `/ticket-issues/{id}`

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
```

**Response (200):**
```json
{
  "success": true,
  "message": "Ticket issue deleted successfully"
}
```

### 6. Get Ticket Issues by Ticket
**GET** `/ticket-issues/ticket/{ticketId}`

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
```

**Response (200):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "ticket_id": 1,
      "description": "Missing signature from client representative",
      "status": "In Progress",
      "remarks": "Client agreed to sign tomorrow",
      "date_reported": "2025-08-01T00:00:00.000000Z",
      "ticket": {
        "id": 1,
        "ticket_number": "ST-000001",
        "client_id": 2,
        "date": "2025-08-01T00:00:00.000000Z",
        "status": "Delivered",
        "amount": "1800.00"
      }
    }
  ],
  "message": "Ticket issues retrieved successfully"
}
```

**CURL Examples:**

```bash
# Get all ticket issues
curl -X GET http://127.0.0.1:8001/api/ticket-issues \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"

# Create ticket issue
curl -X POST http://127.0.0.1:8001/api/ticket-issues \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "ticket_id": 1,
    "description": "Missing signature from client representative",
    "status": "Open",
    "remarks": "Contacted client on 27/07/2025",
    "date_reported": "2025-08-01"
  }'

# Get issues by ticket
curl -X GET http://127.0.0.1:8001/api/ticket-issues/ticket/1 \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
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

## 📁 Document Archive Management APIs

### 1. Get All Documents
**GET** `/documents`

**Query Parameters:**
- `page` (optional): Page number for pagination
- `per_page` (optional): Number of items per page (default: 15)
- `sort_by` (optional): Field to sort by (default: created_at)
- `sort_order` (optional): Sort order - asc/desc (default: desc)
- `category` (optional): Filter by category
- `client_id` (optional): Filter by client ID
- `search` (optional): Search in title, description, or filename
- `public_only` (optional): Show only public documents
- `expired_only` (optional): Show only expired documents
- `not_expired` (optional): Show only non-expired documents

**Response Example:**
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "title": "Sample Document",
        "description": "This is a sample document",
        "file_name": "sample.pdf",
        "file_size": 1024000,
        "file_size_human": "1.00 MB",
        "file_type": "pdf",
        "mime_type": "application/pdf",
        "category": "Report",
        "tags": ["sample", "test"],
        "client_id": 2,
        "is_public": true,
        "download_count": 5,
        "expiry_date": "2025-12-31",
        "is_expired": false,
        "created_at": "2025-08-01T12:00:00.000000Z",
        "updated_at": "2025-08-01T12:00:00.000000Z",
        "client": {
          "id": 2,
          "name": "Client Name"
        },
        "uploaded_by": {
          "id": 1,
          "name": "Admin User"
        }
      }
    ],
    "total": 1,
    "per_page": 15
  },
  "message": "Documents retrieved successfully"
}
```

### 2. Get Document Categories
**GET** `/documents/categories`

**Response Example:**
```json
{
  "success": true,
  "data": {
    "Contract": "Contracts and Agreements",
    "Invoice": "Invoices and Billing",
    "Report": "Reports and Analytics",
    "Certificate": "Certificates and Licenses",
    "License": "Licenses and Permits",
    "Manual": "Manuals and Guides",
    "Procedure": "Procedures and Policies",
    "Policy": "Policies and Standards",
    "Form": "Forms and Templates",
    "Other": "Other Documents"
  },
  "message": "Document categories retrieved successfully"
}
```

### 3. Get Document Statistics
**GET** `/documents/stats`

**Response Example:**
```json
{
  "success": true,
  "data": {
    "total_documents": 25,
    "total_size": 52428800,
    "total_downloads": 150,
    "by_category": [
      {
        "category": "Report",
        "count": 10
      },
      {
        "category": "Contract",
        "count": 8
      }
    ],
    "by_file_type": [
      {
        "file_type": "pdf",
        "count": 15
      },
      {
        "file_type": "docx",
        "count": 5
      }
    ],
    "recent_uploads": [...],
    "expired_documents": 3,
    "public_documents": 12,
    "storage_usage": {
      "total_size_human": "50.00 MB",
      "average_file_size": "2.00 MB"
    }
  },
  "message": "Document statistics retrieved successfully"
}
```

### 4. Upload Single Document
**POST** `/documents`

**Headers:**
```
Content-Type: multipart/form-data
Authorization: Bearer {token}
```

**Form Data:**
- `file` (required): Document file (max 10MB)
- `title` (required): Document title
- `description` (optional): Document description
- `category` (required): Document category
- `tags[]` (optional): Array of tags
- `client_id` (optional): Client ID
- `is_public` (optional): Public access (true/false)
- `expiry_date` (optional): Expiry date (YYYY-MM-DD)

**Response Example:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "title": "Sample Document",
    "file_name": "sample.pdf",
    "file_size": 1024000,
    "file_size_human": "1.00 MB",
    "category": "Report",
    "tags": ["sample", "test"],
    "is_public": true,
    "download_count": 0,
    "created_at": "2025-08-01T12:00:00.000000Z"
  },
  "message": "Document uploaded successfully"
}
```

### 5. Bulk Upload Documents
**POST** `/documents/bulk-upload`

**Headers:**
```
Content-Type: multipart/form-data
Authorization: Bearer {token}
```

**Form Data:**
- `files[]` (required): Array of document files (max 10MB each)
- `category` (required): Document category for all files
- `tags[]` (optional): Array of tags for all files
- `client_id` (optional): Client ID for all files
- `is_public` (optional): Public access for all files

**Response Example:**
```json
{
  "success": true,
  "data": {
    "uploaded": [
      {
        "id": 1,
        "title": "Document 1",
        "file_name": "doc1.pdf"
      },
      {
        "id": 2,
        "title": "Document 2",
        "file_name": "doc2.pdf"
      }
    ],
    "failed": []
  },
  "message": "Bulk upload completed. 2 documents uploaded successfully."
}
```

### 6. Get Single Document
**GET** `/documents/{id}`

**Response Example:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "title": "Sample Document",
    "description": "This is a sample document",
    "file_name": "sample.pdf",
    "file_path": "documents/2025/08/01/1234567890_abc123.pdf",
    "file_size": 1024000,
    "file_size_human": "1.00 MB",
    "file_type": "pdf",
    "mime_type": "application/pdf",
    "category": "Report",
    "tags": ["sample", "test"],
    "client_id": 2,
    "is_public": true,
    "download_count": 5,
    "expiry_date": "2025-12-31",
    "is_expired": false,
    "created_at": "2025-08-01T12:00:00.000000Z",
    "updated_at": "2025-08-01T12:00:00.000000Z",
    "client": {
      "id": 2,
      "name": "Client Name"
    },
    "uploaded_by": {
      "id": 1,
      "name": "Admin User"
    }
  },
  "message": "Document retrieved successfully"
}
```

### 7. Update Document
**PUT** `/documents/{id}`

**Headers:**
```
Content-Type: application/json
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "title": "Updated Document Title",
  "description": "Updated description",
  "category": "Manual",
  "tags": ["updated", "manual"],
  "client_id": 2,
  "is_public": false,
  "expiry_date": "2025-12-31"
}
```

### 8. Update Document with File
**PUT** `/documents/{id}`

**Headers:**
```
Content-Type: multipart/form-data
Authorization: Bearer {token}
```

**Form Data:**
- `file` (optional): New document file
- `title` (optional): Document title
- `description` (optional): Document description
- `category` (optional): Document category
- `tags[]` (optional): Array of tags
- `client_id` (optional): Client ID
- `is_public` (optional): Public access

### 9. Get Documents by Client
**GET** `/documents/client/{clientId}`

**Response Example:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "Client Document",
      "file_name": "client_doc.pdf",
      "category": "Contract",
      "created_at": "2025-08-01T12:00:00.000000Z"
    }
  ],
  "message": "Client documents retrieved successfully"
}
```

### 10. Get Download URL
**GET** `/documents/{id}/download`

**Response Example:**
```json
{
  "success": true,
  "data": {
    "download_url": "http://127.0.0.1:8001/storage/documents/2025/08/01/1234567890_abc123.pdf",
    "file_name": "sample.pdf",
    "file_size": "1.00 MB",
    "mime_type": "application/pdf"
  },
  "message": "Download URL generated successfully"
}
```

### 11. Download Document Direct
**GET** `/documents/{id}/download-direct`

Downloads the file directly with proper headers.

### 12. Preview Document
**GET** `/documents/{id}/preview`

Previews the document in browser (supports PDF, images, text files).

### 13. Delete Single Document
**DELETE** `/documents/{id}`

**Response Example:**
```json
{
  "success": true,
  "message": "Document deleted successfully"
}
```

### 14. Bulk Delete Documents
**DELETE** `/documents/bulk-delete`

**Headers:**
```
Content-Type: application/json
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "document_ids": [1, 2, 3]
}
```

**Response Example:**
```json
{
  "success": true,
  "data": {
    "deleted_count": 3,
    "failed_deletions": []
  },
  "message": "Bulk deletion completed. 3 documents deleted successfully."
}
```

### 15. Public Document Download
**GET** `/documents/public/download/{filename}`

Downloads public documents without authentication.

**CURL Examples:**

```bash
# Get all documents
curl -X GET http://127.0.0.1:8001/api/documents \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"

# Upload document
curl -X POST http://127.0.0.1:8001/api/documents \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json" \
  -F "file=@document.pdf" \
  -F "title=Sample Document" \
  -F "category=Report" \
  -F "client_id=2"

# Bulk upload
curl -X POST http://127.0.0.1:8001/api/documents/bulk-upload \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json" \
  -F "files[]=@doc1.pdf" \
  -F "files[]=@doc2.pdf" \
  -F "category=Contract"

# Download document
curl -X GET http://127.0.0.1:8001/api/documents/1/download \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"

# Public download
curl -X GET http://127.0.0.1:8001/api/documents/public/download/sample.pdf
```

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
