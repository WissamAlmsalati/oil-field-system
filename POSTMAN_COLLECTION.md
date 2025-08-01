# Postman Collection - Almansoori Petroleum Services API

## Environment Setup

First, create a new Environment in Postman with these variables:

```
baseUrl: http://127.0.0.1:8001/api
token: (will be set after login)
```

---

## 🔐 Authentication Endpoints

### 1. POST Login
**URL:** `{{baseUrl}}/auth/login`
**Method:** POST
**Headers:**
```
Content-Type: application/json
```
**Body (raw JSON):**
```json
{
  "email": "admin@almansoori.com",
  "password": "password123"
}
```
**Test Script (to save token):**
```javascript
pm.test("Login successful", function () {
    pm.response.to.have.status(200);
    var jsonData = pm.response.json();
    if (jsonData.success && jsonData.data.token) {
        pm.environment.set("token", jsonData.data.token);
    }
});
```

### 2. GET Current User
**URL:** `{{baseUrl}}/auth/me`
**Method:** GET
**Headers:**
```
Authorization: Bearer {{token}}
```

### 3. POST Register (Admin Only)
**URL:** `{{baseUrl}}/auth/register`
**Method:** POST
**Headers:**
```
Authorization: Bearer {{token}}
Content-Type: application/json
```
**Body (raw JSON):**
```json
{
  "name": "New User",
  "email": "newuser@almansoori.com",
  "password": "password123",
  "password_confirmation": "password123",
  "role": "User"
}
```

### 4. POST Logout
**URL:** `{{baseUrl}}/auth/logout`
**Method:** POST
**Headers:**
```
Authorization: Bearer {{token}}
```

---

## 👥 Clients Management

### 1. GET All Clients
**URL:** `{{baseUrl}}/clients`
**Method:** GET
**Headers:**
```
Authorization: Bearer {{token}}
```
**Query Params:**
```
per_page: 10
```

### 2. POST Create Client (with file upload)
**URL:** `{{baseUrl}}/clients`
**Method:** POST
**Headers:**
```
Authorization: Bearer {{token}}
```
**Body (form-data):**
```
name: ADNOC
logo: [Choose File - image file]
contacts[0][name]: Ahmed Ali
contacts[0][email]: ahmed@adnoc.ae
contacts[0][phone]: +971501234567
contacts[0][position]: Project Manager
contacts[1][name]: Sara Mohamed
contacts[1][email]: sara@adnoc.ae
contacts[1][phone]: +971509876543
contacts[1][position]: Technical Lead
```

### 3. POST Create Client (JSON only)
**URL:** `{{baseUrl}}/clients`
**Method:** POST
**Headers:**
```
Authorization: Bearer {{token}}
Content-Type: application/json
```
**Body (raw JSON):**
```json
{
  "name": "Emirates National Oil Company",
  "contacts": [
    {
      "name": "Ahmed Al Mansoori",
      "email": "ahmed.almansoori@enoc.com",
      "phone": "+971501234567",
      "position": "Operations Manager"
    },
    {
      "name": "Fatima Al Zaabi",
      "email": "fatima.alzaabi@enoc.com",
      "phone": "+971509876543",
      "position": "Project Coordinator"
    }
  ]
}
```

### 4. PUT Update Client
**URL:** `{{baseUrl}}/clients/1`
**Method:** PUT
**Headers:**
```
Authorization: Bearer {{token}}
Content-Type: application/json
```
**Body (raw JSON):**
```json
{
  "name": "ADNOC - Updated Name",
  "contacts": [
    {
      "name": "Updated Contact Name",
      "email": "updated@adnoc.ae",
      "phone": "+971501111111",
      "position": "Senior Manager"
    }
  ]
}
```

### 5. DELETE Client
**URL:** `{{baseUrl}}/clients/1`
**Method:** DELETE
**Headers:**
```
Authorization: Bearer {{token}}
```

---

## 📋 Sub-Agreements Management

### 1. GET All Sub-Agreements
**URL:** `{{baseUrl}}/sub-agreements`
**Method:** GET
**Headers:**
```
Authorization: Bearer {{token}}
```

### 2. GET Sub-Agreements by Client
**URL:** `{{baseUrl}}/sub-agreements/client/1`
**Method:** GET
**Headers:**
```
Authorization: Bearer {{token}}
```

### 3. POST Create Sub-Agreement
**URL:** `{{baseUrl}}/sub-agreements`
**Method:** POST
**Headers:**
```
Authorization: Bearer {{token}}
Content-Type: application/json
```
**Body (raw JSON):**
```json
{
  "client_id": 1,
  "name": "Drilling Services Contract 2025",
  "amount": 500000.00,
  "balance": 500000.00,
  "start_date": "2025-01-01",
  "end_date": "2025-12-31"
}
```

### 4. PUT Update Sub-Agreement
**URL:** `{{baseUrl}}/sub-agreements/1`
**Method:** PUT
**Headers:**
```
Authorization: Bearer {{token}}
Content-Type: application/json
```
**Body (raw JSON):**
```json
{
  "name": "Updated Drilling Services Contract 2025",
  "amount": 600000.00,
  "balance": 550000.00,
  "end_date": "2026-01-31"
}
```

### 5. DELETE Sub-Agreement
**URL:** `{{baseUrl}}/sub-agreements/1`
**Method:** DELETE
**Headers:**
```
Authorization: Bearer {{token}}
```

---

## 🛠️ Call-Out Jobs Management

### 1. GET All Call-Out Jobs
**URL:** `{{baseUrl}}/call-out-jobs`
**Method:** GET
**Headers:**
```
Authorization: Bearer {{token}}
```

### 2. GET Call-Out Jobs by Client
**URL:** `{{baseUrl}}/call-out-jobs/client/1`
**Method:** GET
**Headers:**
```
Authorization: Bearer {{token}}
```

### 3. POST Create Call-Out Job
**URL:** `{{baseUrl}}/call-out-jobs`
**Method:** POST
**Headers:**
```
Authorization: Bearer {{token}}
Content-Type: application/json
```
**Body (raw JSON):**
```json
{
  "client_id": 1,
  "job_name": "Emergency Well Intervention RW-123",
  "work_order_number": "WO-2025-001",
  "start_date": "2025-07-27",
  "end_date": "2025-07-30",
  "documents": []
}
```

### 4. PUT Update Call-Out Job
**URL:** `{{baseUrl}}/call-out-jobs/1`
**Method:** PUT
**Headers:**
```
Authorization: Bearer {{token}}
Content-Type: application/json
```
**Body (raw JSON):**
```json
{
  "job_name": "Updated Emergency Well Intervention",
  "end_date": "2025-08-01"
}
```

### 5. DELETE Call-Out Job
**URL:** `{{baseUrl}}/call-out-jobs/1`
**Method:** DELETE
**Headers:**
```
Authorization: Bearer {{token}}
```

---

## 📊 Daily Service Logs Management

### 1. GET All Daily Logs
**URL:** `{{baseUrl}}/daily-logs`
**Method:** GET
**Headers:**
```
Authorization: Bearer {{token}}
```

### 2. GET Daily Logs by Client
**URL:** `{{baseUrl}}/daily-logs/client/1`
**Method:** GET
**Headers:**
```
Authorization: Bearer {{token}}
```

### 3. POST Create Daily Service Log
**URL:** `{{baseUrl}}/daily-logs`
**Method:** POST
**Headers:**
```
Authorization: Bearer {{token}}
Content-Type: application/json
```
**Body (raw JSON):**
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
      "hours": 8,
      "signature": "Ahmed_signature_base64"
    },
    {
      "name": "Mohamed Hassan",
      "position": "Rig Operator",
      "hours": 8,
      "signature": "Mohamed_signature_base64"
    }
  ],
  "equipment_used": [
    {
      "name": "Drilling Rig DR-101",
      "type": "Heavy Equipment",
      "hours": 8,
      "condition": "Good"
    },
    {
      "name": "Mud Pump MP-205",
      "type": "Pumping Equipment",
      "hours": 6,
      "condition": "Excellent"
    }
  ],
  "almansoori_rep": {
    "name": "Khalid Al Mansoori",
    "position": "Site Supervisor",
    "signature": "khalid_signature_base64"
  },
  "mog_approval_1": {
    "name": "Inspector Ahmad",
    "badge": "MOG-001",
    "signature": "inspector1_signature_base64"
  },
  "mog_approval_2": {
    "name": "Inspector Salem",
    "badge": "MOG-002",
    "signature": "inspector2_signature_base64"
  }
}
```

### 4. PUT Update Daily Service Log
**URL:** `{{baseUrl}}/daily-logs/1`
**Method:** PUT
**Headers:**
```
Authorization: Bearer {{token}}
Content-Type: application/json
```
**Body (raw JSON):**
```json
{
  "field": "Updated Ruwais Field",
  "personnel": [
    {
      "name": "Updated Ahmed Ali",
      "position": "Senior Drilling Engineer",
      "hours": 10
    }
  ]
}
```

### 5. POST Generate Excel for Daily Log
**URL:** `{{baseUrl}}/daily-logs/1/generate-excel`
**Method:** POST
**Headers:**
```
Authorization: Bearer {{token}}
```

### 6. DELETE Daily Service Log
**URL:** `{{baseUrl}}/daily-logs/1`
**Method:** DELETE
**Headers:**
```
Authorization: Bearer {{token}}
```

---

## 🎫 Service Tickets Management

### 1. GET All Service Tickets
**URL:** `{{baseUrl}}/service-tickets`
**Method:** GET
**Headers:**
```
Authorization: Bearer {{token}}
```

### 2. GET Service Tickets by Client
**URL:** `{{baseUrl}}/service-tickets/client/1`
**Method:** GET
**Headers:**
```
Authorization: Bearer {{token}}
```

### 3. POST Create Service Ticket
**URL:** `{{baseUrl}}/service-tickets`
**Method:** POST
**Headers:**
```
Authorization: Bearer {{token}}
Content-Type: application/json
```
**Body (raw JSON):**
```json
{
  "client_id": 1,
  "sub_agreement_id": 1,
  "call_out_job_id": null,
  "date": "2025-07-27",
  "status": "In Field to Sign",
  "amount": 25000.00,
  "related_log_ids": [1, 2],
  "documents": []
}
```

### 4. PUT Update Service Ticket
**URL:** `{{baseUrl}}/service-tickets/1`
**Method:** PUT
**Headers:**
```
Authorization: Bearer {{token}}
Content-Type: application/json
```
**Body (raw JSON):**
```json
{
  "status": "Delivered",
  "amount": 27000.00
}
```

### 5. POST Generate Service Tickets from Logs
**URL:** `{{baseUrl}}/service-tickets/generate`
**Method:** POST
**Headers:**
```
Authorization: Bearer {{token}}
Content-Type: application/json
```
**Body (raw JSON):**
```json
{
  "log_ids": [1, 2, 3],
  "client_id": 1,
  "sub_agreement_id": 1
}
```

### 6. DELETE Service Ticket
**URL:** `{{baseUrl}}/service-tickets/1`
**Method:** DELETE
**Headers:**
```
Authorization: Bearer {{token}}
```

---

## 🔧 Ticket Issues Management

### 1. GET All Ticket Issues
**URL:** `{{baseUrl}}/ticket-issues`
**Method:** GET
**Headers:**
```
Authorization: Bearer {{token}}
```

### 2. GET Issues by Ticket
**URL:** `{{baseUrl}}/ticket-issues/ticket/1`
**Method:** GET
**Headers:**
```
Authorization: Bearer {{token}}
```

### 3. POST Create Ticket Issue
**URL:** `{{baseUrl}}/ticket-issues`
**Method:** POST
**Headers:**
```
Authorization: Bearer {{token}}
Content-Type: application/json
```
**Body (raw JSON):**
```json
{
  "ticket_id": 1,
  "description": "Missing signature from client representative on page 2",
  "status": "Open",
  "remarks": "Contacted client representative Ahmed Ali via phone on 27/07/2025. Scheduled meeting for signature on 28/07/2025.",
  "date_reported": "2025-07-27"
}
```

### 4. PUT Update Ticket Issue
**URL:** `{{baseUrl}}/ticket-issues/1`
**Method:** PUT
**Headers:**
```
Authorization: Bearer {{token}}
Content-Type: application/json
```
**Body (raw JSON):**
```json
{
  "status": "In Progress",
  "remarks": "Client representative confirmed availability for signature on 28/07/2025 at 10:00 AM"
}
```

### 5. DELETE Ticket Issue
**URL:** `{{baseUrl}}/ticket-issues/1`
**Method:** DELETE
**Headers:**
```
Authorization: Bearer {{token}}
```

---

## 📈 Dashboard APIs

### 1. GET Dashboard Stats
**URL:** `{{baseUrl}}/dashboard/stats`
**Method:** GET
**Headers:**
```
Authorization: Bearer {{token}}
```

### 2. GET Recent Activities
**URL:** `{{baseUrl}}/dashboard/recent-activities`
**Method:** GET
**Headers:**
```
Authorization: Bearer {{token}}
```

---

## 📁 Documents Management

### 1. GET All Documents
**URL:** `{{baseUrl}}/documents`
**Method:** GET
**Headers:**
```
Authorization: Bearer {{token}}
```

### 2. GET Documents by Client
**URL:** `{{baseUrl}}/documents/client/1`
**Method:** GET
**Headers:**
```
Authorization: Bearer {{token}}
```

### 3. GET Download Document
**URL:** `{{baseUrl}}/documents/download/1`
**Method:** GET
**Headers:**
```
Authorization: Bearer {{token}}
```

### 4. DELETE Document
**URL:** `{{baseUrl}}/documents/1`
**Method:** DELETE
**Headers:**
```
Authorization: Bearer {{token}}
```

---

## 👤 Users Management (Admin Only)

### 1. GET All Users
**URL:** `{{baseUrl}}/users`
**Method:** GET
**Headers:**
```
Authorization: Bearer {{token}}
```

### 2. POST Create User
**URL:** `{{baseUrl}}/users`
**Method:** POST
**Headers:**
```
Authorization: Bearer {{token}}
Content-Type: application/json
```
**Body (raw JSON):**
```json
{
  "name": "Manager User",
  "email": "manager2@almansoori.com",
  "password": "password123",
  "password_confirmation": "password123",
  "role": "Manager"
}
```

### 3. PUT Update User
**URL:** `{{baseUrl}}/users/1`
**Method:** PUT
**Headers:**
```
Authorization: Bearer {{token}}
Content-Type: application/json
```
**Body (raw JSON):**
```json
{
  "name": "Updated Admin User",
  "role": "Admin"
}
```

### 4. DELETE User
**URL:** `{{baseUrl}}/users/1`
**Method:** DELETE
**Headers:**
```
Authorization: Bearer {{token}}
```

---

## 🧪 Test Sequence

Recommended testing order:

1. **Login** with admin credentials
2. **Create Client** with contacts
3. **Create Sub-Agreement** for the client
4. **Create Call-Out Job** for the client
5. **Create Daily Service Log** for the client
6. **Create Service Ticket** from the logs
7. **Create Ticket Issue** if needed
8. **Test Dashboard Stats**

---

## 🔍 Common Test Scripts

Add these to your Postman tests:

**For Success Responses:**
```javascript
pm.test("Status code is 200", function () {
    pm.response.to.have.status(200);
});

pm.test("Response has success true", function () {
    var jsonData = pm.response.json();
    pm.expect(jsonData.success).to.eql(true);
});
```

**For Creation (201):**
```javascript
pm.test("Status code is 201", function () {
    pm.response.to.have.status(201);
});

pm.test("Response contains data", function () {
    var jsonData = pm.response.json();
    pm.expect(jsonData.data).to.exist;
});
```

---

## 📝 Notes

- All endpoints require authentication except `/auth/login`
- Admin role required for user management
- File uploads use `multipart/form-data`
- Regular data uses `application/json`
- Server runs on `http://127.0.0.1:8001`

اتبع هذا الدليل خطوة بخطوة وستتمكن من اختبار جميع الـ APIs بنجاح! 🚀
