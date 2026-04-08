# InPursuit WordPress Plugin - Code Review Summary

**Date:** April 8, 2026
**Status:** Critical security vulnerabilities found - requires immediate remediation

---

## Critical Vulnerabilities Found

### 1. SQL Injection (CRITICAL)
- **Affected Files:** All files in `/db/` directory
- **Issue:** User input concatenated directly into SQL queries without `$wpdb->prepare()`
- **Impact:** Complete database compromise
- **Files:**
  - `db/class-inpursuit-db-comment.php:44-58` - Search parameter unescaped
  - `db/class-inpursuit-db-comments-category.php:60` - Category name unescaped
  - `db/class-inpursuit-db-member.php` - ID list interpolation
  - `db/class-inpursuit-db-event-member-relation.php:17` - Direct string concat

### 2. Privilege Escalation on Registration (CRITICAL)
- **File:** `rest-authentication/class-inpursuit-rest-authentication.php`
- **Issue:** Anyone can register as administrator with `__return_true` permission
- **Impact:** Complete access to the system

### 3. Weak Credential Encoding (CRITICAL)
- **File:** `rest-authentication/class-inpursuit-rest-authentication.php`
- **Issue:** Base64 (encoding, not encryption) used for credential handling
- **Impact:** Credentials trivially reversible

### 4. IDOR - Attendance Modification (HIGH)
- **File:** `rest-api/class-inpursuit-rest-member.php:107-128`
- **Issue:** No ownership checks on event attendance changes
- **Impact:** Users can modify any member's attendance

### 5. Stored XSS in Comments (HIGH)
- **File:** `db/class-inpursuit-db-comment.php`
- **Issue:** Unsanitized user input stored and returned in REST responses
- **Impact:** Account hijacking, data theft

---

## High Severity Issues

| Issue | Location | Impact |
|-------|----------|--------|
| No rate limiting on auth endpoints | `rest-authentication/` | Brute force attacks |
| Most endpoints use `__return_true` permission | All REST files | Unauthorized access |
| No CSRF/nonce validation | All REST endpoints | CSRF attacks |
| Analytics/map/settings fully public | `rest-api/class-inpursuit-rest-custom.php` | Data exposure |
| Version uses `time()` function | `InPursuit.php:13` | Breaks all caching |
| Vue 2 and Axios 0.21 EOL | `package.json` | Known vulnerabilities |

---

## Code Quality Issues

- Commented-out CORS code in main plugin (lines 88-111)
- Debug `test()` method left in base class
- No error handling (no try/catch blocks)
- Extensive code duplication across DB classes
- No inline documentation
- Inconsistent permission callback implementation

---

## Remediation Priority

1. **IMMEDIATE:** Fix all SQL injection vulnerabilities using `$wpdb->prepare()`
2. **IMMEDIATE:** Fix privilege escalation - remove auto admin role from registration
3. **IMMEDIATE:** Gate sensitive endpoints with proper permission checks
4. **HIGH:** Add rate limiting to authentication endpoints
5. **HIGH:** Fix credential handling (remove base64 encoding)
6. **HIGH:** Add CSRF/nonce validation
7. **MEDIUM:** Update Vue 2 → Vue 3, Axios 0.21 → latest
8. **MEDIUM:** Refactor duplicate code
9. **LOW:** Add error handling and logging

---

## Files to Review/Fix

```
Priority 1 - SQL Injection:
- db/class-inpursuit-db.php
- db/class-inpursuit-db-comment.php
- db/class-inpursuit-db-member.php
- db/class-inpursuit-db-event.php
- db/class-inpursuit-db-event-member-relation.php
- db/class-inpursuit-db-member-dates.php
- db/class-inpursuit-db-comments-category.php
- db/class-inpursuit-db-user.php

Priority 2 - Authentication:
- rest-authentication/class-inpursuit-rest-authentication.php
- rest-authentication/class-inpursuit-rest-otp-auth.php

Priority 3 - REST API:
- rest-api/class-inpursuit-rest-custom.php
- rest-api/class-inpursuit-rest-member.php
- rest-api/class-inpursuit-rest-comment.php
```

---

## Architecture Assessment

**Strengths:**
- Good use of singleton + inheritance pattern
- Proper separation of DB/REST/Admin layers
- Custom tables for comments/attendance (good decision)
- Using WordPress Application Passwords for API auth

**Weaknesses:**
- Query building with string concatenation instead of parameterized queries
- Inconsistent permission callback pattern
- No validation/sanitization layer
- Missing error handling throughout

---

## Completed Fixes

### 1. ✅ SQL Injection Vulnerabilities (FIXED)
- All DB queries now use `$wpdb->prepare()` with parameterized placeholders
- All user input type-cast with `intval()` for numeric values
- LIKE clauses escaped with `$wpdb->esc_like()`
- Array parameters properly handled in IN clauses
- 7 database files updated
- **Files:** db/class-inpursuit-db-*.php

### 2. ✅ Sensitive Endpoints Gated (FIXED)
- History endpoint requires login
- Analytics endpoint requires login
- Map/regions endpoints require login
- Special-dates endpoint requires login
- Comments endpoints require login
- Settings endpoint requires login
- **Files:** rest-api/class-inpursuit-rest-*.php

## Next Priority Fixes

1. 🔴 CRITICAL: Fix authentication/authorization issues
   - Remove auto-admin role assignment on registration
   - Add email verification and admin approval
   - Replace base64 encoding with proper encryption
   - Add rate limiting to auth endpoints

2. 🟠 HIGH: Fix IDOR vulnerabilities
   - Add ownership checks for attendance modifications
   - Verify user can modify specific posts

3. 🟠 HIGH: Output escaping
   - Escape comments to prevent stored XSS
   - Escape all REST API responses

4. 🟡 MEDIUM: Add input validation
   - Validate all form inputs
   - Sanitize all taxonomy operations

5. 🟡 MEDIUM: Add nonce validation
   - Implement CSRF protection on state-changing operations
