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

## Next Steps

Session will focus on:
1. ✅ Complete code review (DONE)
2. 🔄 Fix SQL injection vulnerabilities
3. 🔄 Fix authentication/authorization issues
4. 🔄 Add input validation and output escaping
5. 🔄 Test all changes
