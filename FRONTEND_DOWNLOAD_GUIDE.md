# Frontend Download Guide - Daily Service Logs

## 🚨 **IMPORTANT: Use These URLs to Avoid 403 Forbidden Errors**

### **Problem:**
- Direct access to `/storage/` URLs requires authentication
- Frontend gets 403 Forbidden when trying to download files

### **Solution:**
Use the **public download URLs** provided by the API instead of direct storage URLs.

---

## **Available Download URLs**

### **1. Public Download URL (Recommended for Frontend)**
```javascript
// Use this URL for frontend downloads
const publicDownloadUrl = "http://127.0.0.1:8001/download/{filename}";
```
- ✅ **No authentication required**
- ✅ **CORS enabled**
- ✅ **Works in browsers**
- ✅ **Inline display** (opens in browser)

### **2. Force Download URL (Forced Download)**
```javascript
// Use this URL to force file download
const forceDownloadUrl = "http://127.0.0.1:8001/download-file/{filename}";
```
- ✅ **No authentication required**
- ✅ **CORS enabled**
- ✅ **Forces download** (saves to computer)

### **3. Storage URL (Avoid This)**
```javascript
// ❌ DON'T USE THIS - Requires authentication
const storageUrl = "http://127.0.0.1:8001/storage/daily_logs/excel/{filename}";
```

---

## **How to Get the Correct URLs**

### **Step 1: Generate Excel File**
```javascript
const response = await fetch('/api/daily-logs/12/generate-excel', {
  method: 'POST',
  headers: {
    'Authorization': 'Bearer ' + token,
    'Accept': 'application/json',
    'Content-Type': 'application/json'
  }
});

const data = await response.json();
```

### **Step 2: Use the Correct URL**
```javascript
// ✅ CORRECT - Use public download URL
const downloadUrl = data.data.public_download_url;
// Example: "http://127.0.0.1:8001/download/daily_service_log_DSL-000010_2025-08-01_22-04-02.xlsx"

// ✅ ALTERNATIVE - Use force download URL
const forceDownloadUrl = data.data.force_download_url;
// Example: "http://127.0.0.1:8001/download-file/daily_service_log_DSL-000010_2025-08-01_22-04-02.xlsx"

// ❌ WRONG - Don't use storage URL
const wrongUrl = data.data.download_url;
// This will give 403 Forbidden
```

---

## **Frontend Implementation Examples**

### **Example 1: Open in New Tab**
```javascript
// Generate Excel and open in new tab
const generateAndOpen = async (logId) => {
  try {
    // Generate Excel
    const response = await fetch(`/api/daily-logs/${logId}/generate-excel`, {
      method: 'POST',
      headers: {
        'Authorization': 'Bearer ' + token,
        'Accept': 'application/json'
      }
    });
    
    const data = await response.json();
    
    if (data.success) {
      // Open in new tab
      window.open(data.data.public_download_url, '_blank');
    }
  } catch (error) {
    console.error('Error:', error);
  }
};
```

### **Example 2: Force Download**
```javascript
// Generate Excel and force download
const generateAndDownload = async (logId) => {
  try {
    // Generate Excel
    const response = await fetch(`/api/daily-logs/${logId}/generate-excel`, {
      method: 'POST',
      headers: {
        'Authorization': 'Bearer ' + token,
        'Accept': 'application/json'
      }
    });
    
    const data = await response.json();
    
    if (data.success) {
      // Force download
      const link = document.createElement('a');
      link.href = data.data.force_download_url;
      link.download = data.data.file_name;
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
    }
  } catch (error) {
    console.error('Error:', error);
  }
};
```

### **Example 3: React Component**
```jsx
import React, { useState } from 'react';

const DownloadButton = ({ logId, token }) => {
  const [loading, setLoading] = useState(false);

  const handleDownload = async () => {
    setLoading(true);
    try {
      // Generate Excel
      const response = await fetch(`/api/daily-logs/${logId}/generate-excel`, {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${token}`,
          'Accept': 'application/json'
        }
      });
      
      const data = await response.json();
      
      if (data.success) {
        // Use public download URL
        window.open(data.data.public_download_url, '_blank');
      }
    } catch (error) {
      console.error('Download failed:', error);
    } finally {
      setLoading(false);
    }
  };

  return (
    <button 
      onClick={handleDownload} 
      disabled={loading}
    >
      {loading ? 'Generating...' : 'Download Excel'}
    </button>
  );
};
```

---

## **API Response Structure**

When you call the generate Excel endpoint, you get this response:

```json
{
  "success": true,
  "data": {
    "file_path": "daily_logs/excel/daily_service_log_DSL-000010_2025-08-01_22-04-02.xlsx",
    "file_name": "daily_service_log_DSL-000010_2025-08-01_22-04-02.xlsx",
    "download_url": "http://127.0.0.1:8001/storage/daily_logs/excel/daily_service_log_DSL-000010_2025-08-01_22-04-02.xlsx",
    "public_download_url": "http://127.0.0.1:8001/download/daily_service_log_DSL-000010_2025-08-01_22-04-02.xlsx",
    "force_download_url": "http://127.0.0.1:8001/download-file/daily_service_log_DSL-000010_2025-08-01_22-04-02.xlsx"
  },
  "message": "Excel file generated successfully"
}
```

**Use:**
- `public_download_url` for opening in browser
- `force_download_url` for forcing download
- **Don't use** `download_url` (storage URL)

---

## **Testing the URLs**

### **Test Public Download:**
```bash
curl -I "http://127.0.0.1:8001/download/daily_service_log_DSL-000010_2025-08-01_22-04-02.xlsx"
```

### **Test Force Download:**
```bash
curl -I "http://127.0.0.1:8001/download-file/daily_service_log_DSL-000010_2025-08-01_22-04-02.xlsx"
```

Both should return `HTTP/1.1 200 OK` with proper headers.

---

## **Common Issues & Solutions**

### **Issue: 403 Forbidden**
**Cause:** Using the storage URL instead of public download URL
**Solution:** Use `public_download_url` or `force_download_url`

### **Issue: CORS Error**
**Cause:** Missing CORS headers
**Solution:** The public routes already include CORS headers

### **Issue: File Not Found**
**Cause:** File doesn't exist or wrong filename
**Solution:** Generate the Excel file first, then use the returned URL

---

## **Summary**

1. **Always generate Excel first** using the API endpoint
2. **Use `public_download_url`** for frontend downloads
3. **Use `force_download_url`** for forced downloads
4. **Never use `download_url`** (storage URL) in frontend
5. **Test URLs** to ensure they work before implementing

This approach will completely eliminate the 403 Forbidden errors! 🚀 