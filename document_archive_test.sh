#!/bin/bash

# Document Archive API Test Script
# Make sure the Laravel server is running on http://127.0.0.1:8001

BASE_URL="http://127.0.0.1:8001/api"
TOKEN=""

echo "📁 Document Archive API Testing Script"
echo "======================================"

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

# Function to test get all documents
test_get_all() {
    echo ""
    echo "📋 Getting all documents..."
    RESPONSE=$(curl -s -X GET "$BASE_URL/documents" \
        -H "Authorization: Bearer $TOKEN" \
        -H "Accept: application/json")
    
    echo "Response: $RESPONSE" | jq '.'
}

# Function to test get documents with filters
test_get_with_filters() {
    echo ""
    echo "🔍 Getting documents with filters..."
    RESPONSE=$(curl -s -X GET "$BASE_URL/documents?category=Report&client_id=2&search=test&public_only=true" \
        -H "Authorization: Bearer $TOKEN" \
        -H "Accept: application/json")
    
    echo "Response: $RESPONSE" | jq '.'
}

# Function to test get categories
test_get_categories() {
    echo ""
    echo "📂 Getting document categories..."
    RESPONSE=$(curl -s -X GET "$BASE_URL/documents/categories" \
        -H "Authorization: Bearer $TOKEN" \
        -H "Accept: application/json")
    
    echo "Response: $RESPONSE" | jq '.'
}

# Function to test get stats
test_get_stats() {
    echo ""
    echo "📊 Getting document statistics..."
    RESPONSE=$(curl -s -X GET "$BASE_URL/documents/stats" \
        -H "Authorization: Bearer $TOKEN" \
        -H "Accept: application/json")
    
    echo "Response: $RESPONSE" | jq '.'
}

# Function to test upload document
test_upload() {
    echo ""
    echo "📤 Uploading document..."
    
    # Create a test file
    echo "This is a test document for the archive system." > test_document.txt
    
    RESPONSE=$(curl -s -X POST "$BASE_URL/documents" \
        -H "Authorization: Bearer $TOKEN" \
        -H "Accept: application/json" \
        -F "file=@test_document.txt" \
        -F "title=Test Document" \
        -F "description=This is a test document for the archive system" \
        -F "category=Report" \
        -F "tags[]=test" \
        -F "tags[]=archive" \
        -F "client_id=2" \
        -F "is_public=1")
    
    echo "Response: $RESPONSE" | jq '.'
    
    # Extract document ID for later tests
    DOCUMENT_ID=$(echo $RESPONSE | jq -r '.data.id')
    echo "📝 Created document ID: $DOCUMENT_ID"
    
    # Clean up test file
    rm test_document.txt
}

# Function to test bulk upload
test_bulk_upload() {
    echo ""
    echo "📤 Testing bulk upload..."
    
    # Create test files
    echo "This is test document 1." > test_doc1.txt
    echo "This is test document 2." > test_doc2.txt
    
    RESPONSE=$(curl -s -X POST "$BASE_URL/documents/bulk-upload" \
        -H "Authorization: Bearer $TOKEN" \
        -H "Accept: application/json" \
        -F "files[]=@test_doc1.txt" \
        -F "files[]=@test_doc2.txt" \
        -F "category=Contract" \
        -F "tags[]=bulk" \
        -F "tags[]=test" \
        -F "client_id=2" \
        -F "is_public=0")
    
    echo "Response: $RESPONSE" | jq '.'
    
    # Clean up test files
    rm test_doc1.txt test_doc2.txt
}

# Function to test get single document
test_get_single() {
    if [ -z "$DOCUMENT_ID" ]; then
        echo "❌ No document ID available for single document test"
        return
    fi
    
    echo ""
    echo "🔍 Getting document ID: $DOCUMENT_ID..."
    RESPONSE=$(curl -s -X GET "$BASE_URL/documents/$DOCUMENT_ID" \
        -H "Authorization: Bearer $TOKEN" \
        -H "Accept: application/json")
    
    echo "Response: $RESPONSE" | jq '.'
}

# Function to test update document
test_update() {
    if [ -z "$DOCUMENT_ID" ]; then
        echo "❌ No document ID available for update test"
        return
    fi
    
    echo ""
    echo "✏️ Updating document ID: $DOCUMENT_ID..."
    RESPONSE=$(curl -s -X PUT "$BASE_URL/documents/$DOCUMENT_ID" \
        -H "Authorization: Bearer $TOKEN" \
        -H "Accept: application/json" \
        -H "Content-Type: application/json" \
        -d '{
            "title": "Updated Test Document",
            "description": "This is an updated test document",
            "category": "Manual",
            "tags": ["test", "updated", "manual"],
            "client_id": 2,
            "is_public": false
        }')
    
    echo "Response: $RESPONSE" | jq '.'
}

# Function to test download document
test_download() {
    if [ -z "$DOCUMENT_ID" ]; then
        echo "❌ No document ID available for download test"
        return
    fi
    
    echo ""
    echo "⬇️ Getting download URL for document ID: $DOCUMENT_ID..."
    RESPONSE=$(curl -s -X GET "$BASE_URL/documents/$DOCUMENT_ID/download" \
        -H "Authorization: Bearer $TOKEN" \
        -H "Accept: application/json")
    
    echo "Response: $RESPONSE" | jq '.'
}

# Function to test preview document
test_preview() {
    if [ -z "$DOCUMENT_ID" ]; then
        echo "❌ No document ID available for preview test"
        return
    fi
    
    echo ""
    echo "👁️ Testing preview for document ID: $DOCUMENT_ID..."
    RESPONSE=$(curl -s -X GET "$BASE_URL/documents/$DOCUMENT_ID/preview" \
        -H "Authorization: Bearer $TOKEN" \
        -H "Accept: application/json")
    
    echo "Preview response status: $RESPONSE"
}

# Function to test get by client
test_get_by_client() {
    echo ""
    echo "👥 Getting documents for client ID: 2..."
    RESPONSE=$(curl -s -X GET "$BASE_URL/documents/client/2" \
        -H "Authorization: Bearer $TOKEN" \
        -H "Accept: application/json")
    
    echo "Response: $RESPONSE" | jq '.'
}

# Function to test search documents
test_search() {
    echo ""
    echo "🔍 Searching documents for 'test'..."
    RESPONSE=$(curl -s -X GET "$BASE_URL/documents?search=test" \
        -H "Authorization: Bearer $TOKEN" \
        -H "Accept: application/json")
    
    echo "Response: $RESPONSE" | jq '.'
}

# Function to test filter by category
test_filter_category() {
    echo ""
    echo "📂 Filtering documents by category 'Report'..."
    RESPONSE=$(curl -s -X GET "$BASE_URL/documents?category=Report" \
        -H "Authorization: Bearer $TOKEN" \
        -H "Accept: application/json")
    
    echo "Response: $RESPONSE" | jq '.'
}

# Function to test bulk delete
test_bulk_delete() {
    echo ""
    echo "🗑️ Testing bulk delete..."
    RESPONSE=$(curl -s -X DELETE "$BASE_URL/documents/bulk-delete" \
        -H "Authorization: Bearer $TOKEN" \
        -H "Accept: application/json" \
        -H "Content-Type: application/json" \
        -d '{
            "document_ids": [1, 2, 3]
        }')
    
    echo "Response: $RESPONSE" | jq '.'
}

# Function to test public download
test_public_download() {
    echo ""
    echo "🌐 Testing public download..."
    RESPONSE=$(curl -s -X GET "$BASE_URL/documents/public/download/test_document.txt" \
        -H "Accept: application/json")
    
    echo "Public download response status: $RESPONSE"
}

# Function to test delete document
test_delete() {
    if [ -z "$DOCUMENT_ID" ]; then
        echo "❌ No document ID available for delete test"
        return
    fi
    
    echo ""
    echo "🗑️ Deleting document ID: $DOCUMENT_ID..."
    RESPONSE=$(curl -s -X DELETE "$BASE_URL/documents/$DOCUMENT_ID" \
        -H "Authorization: Bearer $TOKEN" \
        -H "Accept: application/json")
    
    echo "Response: $RESPONSE" | jq '.'
}

# Main execution
main() {
    login
    test_get_all
    test_get_with_filters
    test_get_categories
    test_get_stats
    test_upload
    test_bulk_upload
    test_get_single
    test_update
    test_download
    test_preview
    test_get_by_client
    test_search
    test_filter_category
    test_bulk_delete
    test_public_download
    test_delete
    
    echo ""
    echo "✅ Document Archive API testing completed!"
}

# Run the main function
main 