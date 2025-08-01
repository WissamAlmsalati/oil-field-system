# 🚀 Almansoori Petroleum Services - API Guide

## Server Information
- **Base URL:** `http://127.0.0.1:8001/api`
- **Authentication:** Bearer Token (Laravel Sanctum)

## 🔐 Test Credentials

```
Admin User:
Email: admin@almansoori.com
Password: password123

Manager User:
Email: manager@almansoori.com
Password: password123

Regular User:
Email: user@almansoori.com
Password: password123
```

---

## 🎯 Quick Start Guide

### Step 1: Login and Get Token

**Request:**
```bash
curl -X POST http://127.0.0.1:8001/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@almansoori.com","password":"password123"}'
```

**Response:**
```json
{
  "success": true,
  "data": {
    "user": {...},
    "token": "1|abcd1234...",
    "token_type": "Bearer"
  }
}
```

### Step 2: Use Token for Authenticated Requests

**Example - Get All Clients:**
```bash
curl -X GET http://127.0.0.1:8001/api/clients \
  -H "Authorization: Bearer 1|abcd1234..."
```

---

## 📋 Core API Endpoints

### 🔐 Authentication

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| POST | `/auth/login` | User login | ❌ |
| POST | `/auth/logout` | User logout | ✅ |
| GET | `/auth/me` | Get current user | ✅ |
| POST | `/auth/register` | Register user (Admin only) | ✅ Admin |

### 👥 Clients Management

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| GET | `/clients` | Get all clients | ✅ |
| POST | `/clients` | Create new client | ✅ |
| PUT | `/clients/{id}` | Update client | ✅ |
| DELETE | `/clients/{id}` | Delete client | ✅ |

### 📋 Sub-Agreements

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| GET | `/sub-agreements` | Get all agreements | ✅ |
| GET | `/sub-agreements/client/{id}` | Get by client | ✅ |
| POST | `/sub-agreements` | Create agreement | ✅ |
| PUT | `/sub-agreements/{id}` | Update agreement | ✅ |
| DELETE | `/sub-agreements/{id}` | Delete agreement | ✅ |

### 🛠️ Call-Out Jobs

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| GET | `/call-out-jobs` | Get all jobs | ✅ |
| GET | `/call-out-jobs/client/{id}` | Get by client | ✅ |
| POST | `/call-out-jobs` | Create job | ✅ |
| PUT | `/call-out-jobs/{id}` | Update job | ✅ |
| DELETE | `/call-out-jobs/{id}` | Delete job | ✅ |

### 📊 Daily Service Logs

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| GET | `/daily-logs` | Get all logs | ✅ |
| GET | `/daily-logs/client/{id}` | Get by client | ✅ |
| POST | `/daily-logs` | Create log | ✅ |
| PUT | `/daily-logs/{id}` | Update log | ✅ |
| DELETE | `/daily-logs/{id}` | Delete log | ✅ |
| POST | `/daily-logs/{id}/generate-excel` | Generate Excel | ✅ |

### 🎫 Service Tickets

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| GET | `/service-tickets` | Get all tickets | ✅ |
| GET | `/service-tickets/client/{id}` | Get by client | ✅ |
| POST | `/service-tickets` | Create ticket | ✅ |
| PUT | `/service-tickets/{id}` | Update ticket | ✅ |
| DELETE | `/service-tickets/{id}` | Delete ticket | ✅ |
| POST | `/service-tickets/generate` | Generate from logs | ✅ |

---

## 🔥 Examples

### Create Client with Logo and Contacts

```bash
curl -X POST http://127.0.0.1:8001/api/clients \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -F "name=ADNOC" \
  -F "logo=@logo.jpg" \
  -F "contacts[0][name]=Ahmed Ali" \
  -F "contacts[0][email]=ahmed@adnoc.ae" \
  -F "contacts[0][phone]+971501234567" \
  -F "contacts[0][position]=Project Manager"
```

### Create Sub-Agreement

```bash
curl -X POST http://127.0.0.1:8001/api/sub-agreements \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "client_id": 1,
    "name": "Drilling Services 2025",
    "amount": 500000.00,
    "balance": 500000.00,
    "start_date": "2025-01-01",
    "end_date": "2025-12-31"
  }'
```

### Create Daily Service Log

```bash
curl -X POST http://127.0.0.1:8001/api/daily-logs \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "client_id": 1,
    "field": "Ruwais Field",
    "well": "RW-123",
    "contract": "CT-2025-001",
    "job_no": "JOB-001",
    "date": "2025-07-27",
    "personnel": [
      {"name":"Ahmed Ali","position":"Engineer","hours":8}
    ],
    "equipment_used": [
      {"name":"Drilling Rig","type":"Heavy","hours":8}
    ]
  }'
```

---

## 📝 Response Format

**Success Response:**
```json
{
  "success": true,
  "data": { /* Your data here */ },
  "message": "Operation successful",
  "pagination": { /* For paginated results */ }
}
```

**Error Response:**
```json
{
  "success": false,
  "error": {
    "code": "ERROR_CODE",
    "message": "Error description"
  }
}
```

---

## 🛠️ Testing Tools

### Using cURL (Command Line)
The examples above show how to use cURL for testing.

### Using Postman
1. Create new collection
2. Set environment variable: `base_url = http://127.0.0.1:8001/api`
3. Set authorization header: `Bearer {{token}}`
4. Import the endpoints from this documentation

### Using JavaScript/Fetch
```javascript
// Login
const loginResponse = await fetch('http://127.0.0.1:8001/api/auth/login', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({
    email: 'admin@almansoori.com',
    password: 'password123'
  })
});

const loginData = await loginResponse.json();
const token = loginData.data.token;

// Get clients
const clientsResponse = await fetch('http://127.0.0.1:8001/api/clients', {
  headers: {
    'Authorization': `Bearer ${token}`
  }
});

const clients = await clientsResponse.json();
```

---

## 🔧 Development Notes

- Server runs on: `http://127.0.0.1:8001`
- Database: SQLite (development)
- File uploads stored in: `storage/app/public/`
- API routes defined in: `routes/api.php`
- All endpoints return JSON responses
- Pagination available on list endpoints with `?per_page=N`

---

## 📞 Support

إذا واجهت أي مشاكل أو احتجت مساعدة إضافية، تواصل معي!
