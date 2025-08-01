#!/bin/bash

# Service Ticket API Test Script
# Make sure the Laravel server is running on http://127.0.0.1:8001

BASE_URL="http://127.0.0.1:8001/api"
TOKEN=""

echo "🎫 Service Ticket API Testing Script"
echo "====================================="

# Function to login and get token
login() {
    echo "🔐 Logging in..."
    RESPONSE=$(curl -s -X POST "$BASE_URL/auth/login" \
        -H "Content-Type: application/json" \
        -H "Accept: application/json" \
        -d '{
            "email": "admin@almansoori.com",
            "password": "password123"
        }')
    
    TOKEN=$(echo $RESPONSE | jq -r '.data.token')
    
    if [ "$TOKEN" != "null" ] && [ "$TOKEN" != "" ]; then
        echo "✅ Login successful! Token: ${TOKEN:0:20}..."
    else
        echo "❌ Login failed!"
        echo "Response: $RESPONSE"
        exit 1
    fi
}

# Function to test get all service tickets
test_get_all() {
    echo ""
    echo "📋 Getting all service tickets..."
    RESPONSE=$(curl -s -X GET "$BASE_URL/service-tickets" \
        -H "Authorization: Bearer $TOKEN" \
        -H "Accept: application/json")
    
    echo "Response: $RESPONSE" | jq '.'
}

# Function to test create service ticket
test_create() {
    echo ""
    echo "➕ Creating service ticket..."
    RESPONSE=$(curl -s -X POST "$BASE_URL/service-tickets" \
        -H "Authorization: Bearer $TOKEN" \
        -H "Accept: application/json" \
        -H "Content-Type: application/json" \
        -d '{
            "client_id": 2,
            "date": "2025-08-01",
            "status": "In Field to Sign",
            "amount": 1500.00
        }')
    
    echo "Response: $RESPONSE" | jq '.'
    
    # Extract ticket ID for later tests
    TICKET_ID=$(echo $RESPONSE | jq -r '.data.id')
    echo "📝 Created ticket ID: $TICKET_ID"
}

# Function to test get single service ticket
test_get_single() {
    if [ -z "$TICKET_ID" ]; then
        echo "❌ No ticket ID available for single ticket test"
        return
    fi
    
    echo ""
    echo "🔍 Getting service ticket ID: $TICKET_ID..."
    RESPONSE=$(curl -s -X GET "$BASE_URL/service-tickets/$TICKET_ID" \
        -H "Authorization: Bearer $TOKEN" \
        -H "Accept: application/json")
    
    echo "Response: $RESPONSE" | jq '.'
}

# Function to test update service ticket
test_update() {
    if [ -z "$TICKET_ID" ]; then
        echo "❌ No ticket ID available for update test"
        return
    fi
    
    echo ""
    echo "✏️ Updating service ticket ID: $TICKET_ID..."
    RESPONSE=$(curl -s -X PUT "$BASE_URL/service-tickets/$TICKET_ID" \
        -H "Authorization: Bearer $TOKEN" \
        -H "Accept: application/json" \
        -H "Content-Type: application/json" \
        -d '{
            "client_id": 2,
            "date": "2025-08-01",
            "status": "Delivered",
            "amount": 1800.00
        }')
    
    echo "Response: $RESPONSE" | jq '.'
}

# Function to test generate from logs
test_generate_from_logs() {
    echo ""
    echo "🔄 Generating service ticket from logs..."
    RESPONSE=$(curl -s -X POST "$BASE_URL/service-tickets/generate" \
        -H "Authorization: Bearer $TOKEN" \
        -H "Accept: application/json" \
        -H "Content-Type: application/json" \
        -d '{
            "client_id": 2,
            "log_ids": [12],
            "date": "2025-08-01",
            "status": "In Field to Sign",
            "amount": 2000.00
        }')
    
    echo "Response: $RESPONSE" | jq '.'
    
    # Extract generated ticket ID
    GENERATED_TICKET_ID=$(echo $RESPONSE | jq -r '.data.id')
    echo "📝 Generated ticket ID: $GENERATED_TICKET_ID"
}

# Function to test get by client
test_get_by_client() {
    echo ""
    echo "👥 Getting service tickets for client ID: 2..."
    RESPONSE=$(curl -s -X GET "$BASE_URL/service-tickets/client/2" \
        -H "Authorization: Bearer $TOKEN" \
        -H "Accept: application/json")
    
    echo "Response: $RESPONSE" | jq '.'
}

# Function to test delete service ticket
test_delete() {
    if [ -z "$TICKET_ID" ]; then
        echo "❌ No ticket ID available for delete test"
        return
    fi
    
    echo ""
    echo "🗑️ Deleting service ticket ID: $TICKET_ID..."
    RESPONSE=$(curl -s -X DELETE "$BASE_URL/service-tickets/$TICKET_ID" \
        -H "Authorization: Bearer $TOKEN" \
        -H "Accept: application/json")
    
    echo "Response: $RESPONSE" | jq '.'
}

# Main execution
main() {
    login
    test_get_all
    test_create
    test_get_single
    test_update
    test_generate_from_logs
    test_get_by_client
    test_delete
    
    echo ""
    echo "✅ Service Ticket API testing completed!"
}

# Run the main function
main 