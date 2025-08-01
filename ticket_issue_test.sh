#!/bin/bash

# Ticket Issue API Test Script
# Make sure the Laravel server is running on http://127.0.0.1:8001

BASE_URL="http://127.0.0.1:8001/api"
TOKEN=""

echo "🚨 Ticket Issue API Testing Script"
echo "=================================="

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

# Function to test get all ticket issues
test_get_all() {
    echo ""
    echo "📋 Getting all ticket issues..."
    RESPONSE=$(curl -s -X GET "$BASE_URL/ticket-issues" \
        -H "Authorization: Bearer $TOKEN" \
        -H "Accept: application/json")
    
    echo "Response: $RESPONSE" | jq '.'
}

# Function to test create ticket issue
test_create() {
    echo ""
    echo "➕ Creating ticket issue..."
    RESPONSE=$(curl -s -X POST "$BASE_URL/ticket-issues" \
        -H "Authorization: Bearer $TOKEN" \
        -H "Accept: application/json" \
        -H "Content-Type: application/json" \
        -d '{
            "ticket_id": 1,
            "description": "Missing signature from client representative",
            "status": "Open",
            "remarks": "Contacted client on 27/07/2025",
            "date_reported": "2025-08-01"
        }')
    
    echo "Response: $RESPONSE" | jq '.'
    
    # Extract issue ID for later tests
    ISSUE_ID=$(echo $RESPONSE | jq -r '.data.id')
    echo "📝 Created issue ID: $ISSUE_ID"
}

# Function to test get single ticket issue
test_get_single() {
    if [ -z "$ISSUE_ID" ]; then
        echo "❌ No issue ID available for single issue test"
        return
    fi
    
    echo ""
    echo "🔍 Getting ticket issue ID: $ISSUE_ID..."
    RESPONSE=$(curl -s -X GET "$BASE_URL/ticket-issues/$ISSUE_ID" \
        -H "Authorization: Bearer $TOKEN" \
        -H "Accept: application/json")
    
    echo "Response: $RESPONSE" | jq '.'
}

# Function to test update ticket issue
test_update() {
    if [ -z "$ISSUE_ID" ]; then
        echo "❌ No issue ID available for update test"
        return
    fi
    
    echo ""
    echo "✏️ Updating ticket issue ID: $ISSUE_ID..."
    RESPONSE=$(curl -s -X PUT "$BASE_URL/ticket-issues/$ISSUE_ID" \
        -H "Authorization: Bearer $TOKEN" \
        -H "Accept: application/json" \
        -H "Content-Type: application/json" \
        -d '{
            "ticket_id": 1,
            "description": "Missing signature from client representative",
            "status": "In Progress",
            "remarks": "Client agreed to sign tomorrow",
            "date_reported": "2025-08-01"
        }')
    
    echo "Response: $RESPONSE" | jq '.'
}

# Function to test get by ticket
test_get_by_ticket() {
    echo ""
    echo "🎫 Getting ticket issues for ticket ID: 1..."
    RESPONSE=$(curl -s -X GET "$BASE_URL/ticket-issues/ticket/1" \
        -H "Authorization: Bearer $TOKEN" \
        -H "Accept: application/json")
    
    echo "Response: $RESPONSE" | jq '.'
}

# Function to test delete ticket issue
test_delete() {
    if [ -z "$ISSUE_ID" ]; then
        echo "❌ No issue ID available for delete test"
        return
    fi
    
    echo ""
    echo "🗑️ Deleting ticket issue ID: $ISSUE_ID..."
    RESPONSE=$(curl -s -X DELETE "$BASE_URL/ticket-issues/$ISSUE_ID" \
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
    test_get_by_ticket
    test_delete
    
    echo ""
    echo "✅ Ticket Issue API testing completed!"
}

# Run the main function
main 