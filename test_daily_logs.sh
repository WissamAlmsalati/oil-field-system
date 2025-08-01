#!/bin/bash

# Daily Service Logs API Test Script
# Make sure to replace YOUR_TOKEN with your actual token

TOKEN="15|jKEFDbM8Ekkv3b9Y9xf2Y1AfSeemi9CAR1r3cBQt4d8d86c1"
BASE_URL="http://127.0.0.1:8001/api"

echo "🧪 Testing Daily Service Logs API"
echo "=================================="

# 1. Get all logs
echo -e "\n1️⃣ Getting all daily logs..."
curl -s -X GET "$BASE_URL/daily-logs" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json" | jq '.'

# 2. Create a new log
echo -e "\n2️⃣ Creating a new daily log..."
CREATE_RESPONSE=$(curl -s -X POST "$BASE_URL/daily-logs" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "client_id": 2,
    "field": "Test Field",
    "well": "TEST-001",
    "contract": "CT-TEST-001",
    "job_no": "JOB-TEST-001",
    "date": "2025-07-27",
    "personnel": [
      {
        "name": "Test User",
        "position": "Engineer",
        "hours": 8
      }
    ],
    "equipment_used": [
      {
        "name": "Test Equipment",
        "hours": 8
      }
    ],
    "almansoori_rep": [
      {
        "name": "Test Rep",
        "position": "Supervisor"
      }
    ]
  }')

echo "$CREATE_RESPONSE" | jq '.'

# Extract the log ID from the response
LOG_ID=$(echo "$CREATE_RESPONSE" | jq -r '.data.id')
echo -e "\n📝 Created log with ID: $LOG_ID"

# 3. Get the specific log
echo -e "\n3️⃣ Getting log with ID $LOG_ID..."
curl -s -X GET "$BASE_URL/daily-logs/$LOG_ID" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json" | jq '.'

# 4. Generate Excel
echo -e "\n4️⃣ Generating Excel for log $LOG_ID..."
curl -s -X POST "$BASE_URL/daily-logs/$LOG_ID/generate-excel" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json" | jq '.'

# 5. Get client logs
echo -e "\n5️⃣ Getting logs for client 2..."
curl -s -X GET "$BASE_URL/daily-logs/client/2" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json" | jq '.'

# 6. Update the log
echo -e "\n6️⃣ Updating log $LOG_ID..."
curl -s -X PUT "$BASE_URL/daily-logs/$LOG_ID" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "client_id": 2,
    "field": "Updated Test Field",
    "well": "TEST-001",
    "contract": "CT-TEST-001",
    "job_no": "JOB-TEST-001",
    "date": "2025-07-27",
    "personnel": [
      {
        "name": "Updated Test User",
        "position": "Senior Engineer",
        "hours": 10
      }
    ]
  }' | jq '.'

# 7. Download file
echo -e "\n7️⃣ Getting download URL for log $LOG_ID..."
curl -s -X GET "$BASE_URL/daily-logs/$LOG_ID/download/excel" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json" | jq '.'

# 8. Delete the log
echo -e "\n8️⃣ Deleting log $LOG_ID..."
curl -s -X DELETE "$BASE_URL/daily-logs/$LOG_ID" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json" | jq '.'

echo -e "\n✅ All tests completed!" 