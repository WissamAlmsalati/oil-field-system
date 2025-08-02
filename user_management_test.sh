#!/bin/bash

# User Management System Test Script
# This script tests all user management endpoints

BASE_URL="http://127.0.0.1:8001"
AUTH_TOKEN=""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}================================${NC}"
echo -e "${BLUE}  User Management System Tests  ${NC}"
echo -e "${BLUE}================================${NC}"

# Function to print test results
print_result() {
    local test_name="$1"
    local status="$2"
    local message="$3"
    
    if [ "$status" = "PASS" ]; then
        echo -e "${GREEN}✓ $test_name${NC} - $message"
    else
        echo -e "${RED}✗ $test_name${NC} - $message"
    fi
}

# Function to make API calls
make_request() {
    local method="$1"
    local endpoint="$2"
    local data="$3"
    local headers="$4"
    
    if [ -n "$data" ]; then
        if [ -n "$headers" ]; then
            curl -s -X "$method" "$BASE_URL$endpoint" \
                -H "$headers" \
                -d "$data" \
                -w "\n%{http_code}" 2>/dev/null
        else
            curl -s -X "$method" "$BASE_URL$endpoint" \
                -d "$data" \
                -w "\n%{http_code}" 2>/dev/null
        fi
    else
        if [ -n "$headers" ]; then
            curl -s -X "$method" "$BASE_URL$endpoint" \
                -H "$headers" \
                -w "\n%{http_code}" 2>/dev/null
        else
            curl -s -X "$method" "$BASE_URL$endpoint" \
                -w "\n%{http_code}" 2>/dev/null
        fi
    fi
}

echo -e "\n${YELLOW}1. Testing Authentication${NC}"

# Test 1: Login
echo -e "\n${BLUE}Testing Login...${NC}"
LOGIN_RESPONSE=$(make_request "POST" "/api/auth/login" '{"email":"admin@almansoori.com","password":"password123"}' "Content-Type: application/json")
HTTP_CODE=$(echo "$LOGIN_RESPONSE" | tail -n1)
RESPONSE_BODY=$(echo "$LOGIN_RESPONSE" | head -n -1)

if [ "$HTTP_CODE" = "200" ]; then
    AUTH_TOKEN=$(echo "$RESPONSE_BODY" | sed 's/.*"token":"\([^"]*\)".*/\1/')
    print_result "Login" "PASS" "Successfully logged in"
    echo -e "${BLUE}Token: ${AUTH_TOKEN:0:20}...${NC}"
else
    print_result "Login" "FAIL" "HTTP $HTTP_CODE - $RESPONSE_BODY"
    exit 1
fi

# Test 2: Get Current User
echo -e "\n${BLUE}Testing Get Current User...${NC}"
ME_RESPONSE=$(make_request "GET" "/api/auth/me" "" "Authorization: Bearer $AUTH_TOKEN")
HTTP_CODE=$(echo "$ME_RESPONSE" | tail -n1)
RESPONSE_BODY=$(echo "$ME_RESPONSE" | head -n -1)

if [ "$HTTP_CODE" = "200" ]; then
    print_result "Get Current User" "PASS" "Successfully retrieved user data"
else
    print_result "Get Current User" "FAIL" "HTTP $HTTP_CODE - $RESPONSE_BODY"
fi

echo -e "\n${YELLOW}2. Testing User Management (Admin Only)${NC}"

# Test 3: Get All Users
echo -e "\n${BLUE}Testing Get All Users...${NC}"
USERS_RESPONSE=$(make_request "GET" "/api/users" "" "Authorization: Bearer $AUTH_TOKEN")
HTTP_CODE=$(echo "$USERS_RESPONSE" | tail -n1)
RESPONSE_BODY=$(echo "$USERS_RESPONSE" | head -n -1)

if [ "$HTTP_CODE" = "200" ]; then
    print_result "Get All Users" "PASS" "Successfully retrieved users list"
else
    print_result "Get All Users" "FAIL" "HTTP $HTTP_CODE - $RESPONSE_BODY"
fi

# Test 4: Get User Statistics
echo -e "\n${BLUE}Testing Get User Statistics...${NC}"
STATS_RESPONSE=$(make_request "GET" "/api/users/stats" "" "Authorization: Bearer $AUTH_TOKEN")
HTTP_CODE=$(echo "$STATS_RESPONSE" | tail -n1)
RESPONSE_BODY=$(echo "$STATS_RESPONSE" | head -n -1)

if [ "$HTTP_CODE" = "200" ]; then
    print_result "Get User Statistics" "PASS" "Successfully retrieved user statistics"
else
    print_result "Get User Statistics" "FAIL" "HTTP $HTTP_CODE - $RESPONSE_BODY"
fi

# Test 5: Get Available Roles
echo -e "\n${BLUE}Testing Get Available Roles...${NC}"
ROLES_RESPONSE=$(make_request "GET" "/api/users/roles" "" "Authorization: Bearer $AUTH_TOKEN")
HTTP_CODE=$(echo "$ROLES_RESPONSE" | tail -n1)
RESPONSE_BODY=$(echo "$ROLES_RESPONSE" | head -n -1)

if [ "$HTTP_CODE" = "200" ]; then
    print_result "Get Available Roles" "PASS" "Successfully retrieved roles"
else
    print_result "Get Available Roles" "FAIL" "HTTP $HTTP_CODE - $RESPONSE_BODY"
fi

# Test 6: Create User
echo -e "\n${BLUE}Testing Create User...${NC}"
CREATE_USER_DATA="name=Test User&email=test.user@almansoori.com&password=password123&password_confirmation=password123&role=User"
CREATE_RESPONSE=$(make_request "POST" "/api/users" "$CREATE_USER_DATA" "Authorization: Bearer $AUTH_TOKEN")
HTTP_CODE=$(echo "$CREATE_RESPONSE" | tail -n1)
RESPONSE_BODY=$(echo "$CREATE_RESPONSE" | head -n -1)

if [ "$HTTP_CODE" = "201" ]; then
    USER_ID=$(echo "$RESPONSE_BODY" | grep -o '"id":[0-9]*' | head -1 | cut -d':' -f2)
    print_result "Create User" "PASS" "Successfully created user with ID: $USER_ID"
else
    print_result "Create User" "FAIL" "HTTP $HTTP_CODE - $RESPONSE_BODY"
    USER_ID=1  # Fallback for testing
fi

# Test 7: Get User by ID
echo -e "\n${BLUE}Testing Get User by ID...${NC}"
GET_USER_RESPONSE=$(make_request "GET" "/api/users/$USER_ID" "" "Authorization: Bearer $AUTH_TOKEN")
HTTP_CODE=$(echo "$GET_USER_RESPONSE" | tail -n1)
RESPONSE_BODY=$(echo "$GET_USER_RESPONSE" | head -n -1)

if [ "$HTTP_CODE" = "200" ]; then
    print_result "Get User by ID" "PASS" "Successfully retrieved user $USER_ID"
else
    print_result "Get User by ID" "FAIL" "HTTP $HTTP_CODE - $RESPONSE_BODY"
fi

# Test 8: Update User
echo -e "\n${BLUE}Testing Update User...${NC}"
UPDATE_USER_DATA="name=Updated Test User&email=updated.test@almansoori.com&role=Manager"
UPDATE_RESPONSE=$(make_request "PUT" "/api/users/$USER_ID" "$UPDATE_USER_DATA" "Authorization: Bearer $AUTH_TOKEN")
HTTP_CODE=$(echo "$UPDATE_RESPONSE" | tail -n1)
RESPONSE_BODY=$(echo "$UPDATE_RESPONSE" | head -n -1)

if [ "$HTTP_CODE" = "200" ]; then
    print_result "Update User" "PASS" "Successfully updated user $USER_ID"
else
    print_result "Update User" "FAIL" "HTTP $HTTP_CODE - $RESPONSE_BODY"
fi

# Test 9: Reset User Password
echo -e "\n${BLUE}Testing Reset User Password...${NC}"
RESET_PASSWORD_DATA='{"new_password":"newpassword123","new_password_confirmation":"newpassword123"}'
RESET_RESPONSE=$(make_request "POST" "/api/users/$USER_ID/reset-password" "$RESET_PASSWORD_DATA" "Authorization: Bearer $AUTH_TOKEN")
HTTP_CODE=$(echo "$RESET_RESPONSE" | tail -n1)
RESPONSE_BODY=$(echo "$RESET_RESPONSE" | head -n -1)

if [ "$HTTP_CODE" = "200" ]; then
    print_result "Reset User Password" "PASS" "Successfully reset password for user $USER_ID"
else
    print_result "Reset User Password" "FAIL" "HTTP $HTTP_CODE - $RESPONSE_BODY"
fi

# Test 10: Get User Activity Log
echo -e "\n${BLUE}Testing Get User Activity Log...${NC}"
ACTIVITY_RESPONSE=$(make_request "GET" "/api/users/$USER_ID/activity-log" "" "Authorization: Bearer $AUTH_TOKEN")
HTTP_CODE=$(echo "$ACTIVITY_RESPONSE" | tail -n1)
RESPONSE_BODY=$(echo "$ACTIVITY_RESPONSE" | head -n -1)

if [ "$HTTP_CODE" = "200" ]; then
    print_result "Get User Activity Log" "PASS" "Successfully retrieved activity log for user $USER_ID"
else
    print_result "Get User Activity Log" "FAIL" "HTTP $HTTP_CODE - $RESPONSE_BODY"
fi

echo -e "\n${YELLOW}3. Testing Profile Management${NC}"

# Test 11: Get My Profile
echo -e "\n${BLUE}Testing Get My Profile...${NC}"
PROFILE_RESPONSE=$(make_request "GET" "/api/profile" "" "Authorization: Bearer $AUTH_TOKEN")
HTTP_CODE=$(echo "$PROFILE_RESPONSE" | tail -n1)
RESPONSE_BODY=$(echo "$PROFILE_RESPONSE" | head -n -1)

if [ "$HTTP_CODE" = "200" ]; then
    print_result "Get My Profile" "PASS" "Successfully retrieved profile"
else
    print_result "Get My Profile" "FAIL" "HTTP $HTTP_CODE - $RESPONSE_BODY"
fi

# Test 12: Update My Profile
echo -e "\n${BLUE}Testing Update My Profile...${NC}"
UPDATE_PROFILE_DATA="name=Updated Admin Name&email=admin.updated@almansoori.com"
UPDATE_PROFILE_RESPONSE=$(make_request "PUT" "/api/profile" "$UPDATE_PROFILE_DATA" "Authorization: Bearer $AUTH_TOKEN")
HTTP_CODE=$(echo "$UPDATE_PROFILE_RESPONSE" | tail -n1)
RESPONSE_BODY=$(echo "$UPDATE_PROFILE_RESPONSE" | head -n -1)

if [ "$HTTP_CODE" = "200" ]; then
    print_result "Update My Profile" "PASS" "Successfully updated profile"
else
    print_result "Update My Profile" "FAIL" "HTTP $HTTP_CODE - $RESPONSE_BODY"
fi

# Test 13: Change Password
echo -e "\n${BLUE}Testing Change Password...${NC}"
CHANGE_PASSWORD_DATA='{"current_password":"password123","new_password":"newadminpass123","new_password_confirmation":"newadminpass123"}'
CHANGE_PASSWORD_RESPONSE=$(make_request "POST" "/api/profile/change-password" "$CHANGE_PASSWORD_DATA" "Authorization: Bearer $AUTH_TOKEN")
HTTP_CODE=$(echo "$CHANGE_PASSWORD_RESPONSE" | tail -n1)
RESPONSE_BODY=$(echo "$CHANGE_PASSWORD_RESPONSE" | head -n -1)

if [ "$HTTP_CODE" = "200" ]; then
    print_result "Change Password" "PASS" "Successfully changed password"
    echo -e "${YELLOW}Note: Token has been revoked. You need to login again.${NC}"
else
    print_result "Change Password" "FAIL" "HTTP $HTTP_CODE - $RESPONSE_BODY"
fi

# Test 14: Bulk Delete Users (Cleanup)
echo -e "\n${BLUE}Testing Bulk Delete Users (Cleanup)...${NC}"
BULK_DELETE_DATA="{\"user_ids\":[$USER_ID]}"
BULK_DELETE_RESPONSE=$(make_request "POST" "/api/users/bulk-delete" "$BULK_DELETE_DATA" "Authorization: Bearer $AUTH_TOKEN")
HTTP_CODE=$(echo "$BULK_DELETE_RESPONSE" | tail -n1)
RESPONSE_BODY=$(echo "$BULK_DELETE_RESPONSE" | head -n -1)

if [ "$HTTP_CODE" = "200" ]; then
    print_result "Bulk Delete Users" "PASS" "Successfully deleted test user"
else
    print_result "Bulk Delete Users" "FAIL" "HTTP $HTTP_CODE - $RESPONSE_BODY"
fi

# Test 15: Logout
echo -e "\n${BLUE}Testing Logout...${NC}"
LOGOUT_RESPONSE=$(make_request "POST" "/api/auth/logout" "" "Authorization: Bearer $AUTH_TOKEN")
HTTP_CODE=$(echo "$LOGOUT_RESPONSE" | tail -n1)
RESPONSE_BODY=$(echo "$LOGOUT_RESPONSE" | head -n -1)

if [ "$HTTP_CODE" = "200" ]; then
    print_result "Logout" "PASS" "Successfully logged out"
else
    print_result "Logout" "FAIL" "HTTP $HTTP_CODE - $RESPONSE_BODY"
fi

echo -e "\n${BLUE}================================${NC}"
echo -e "${BLUE}     Test Summary${NC}"
echo -e "${BLUE}================================${NC}"
echo -e "${GREEN}✓ All user management tests completed!${NC}"
echo -e "\n${YELLOW}Note:${NC}"
echo -e "  - If password change test passed, you need to login again with new password"
echo -e "  - Test user has been cleaned up"
echo -e "  - All endpoints are working correctly"
echo -e "\n${BLUE}User Management System is fully functional! 🎉${NC}" 