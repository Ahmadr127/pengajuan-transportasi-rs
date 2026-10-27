# SIPETRANS REST API

## Base URL
`http://localhost:8000/api`

## Authentication
1. `POST /api/auth/login` dengan body `{ "username", "password" }`.
2. Response berisi `data.token`. Simpan ke environment variable `token`.
3. Semua request protected menggunakan header `Authorization: Bearer {{token}}`.

## Import ke Postman
1. Import `postman/SIPETRANS_API.postman_collection.json`.
2. Import `postman/SIPETRANS_API.postman_environment.json`.
3. Pilih environment "SIPETRANS Local".
4. Jalankan request "Login", lalu copy token ke variable `token` (atau gunakan test script).

## Response Format
Sukses:
```json
{ "success": true, "message": "OK", "data": { ... }, "meta": { "pagination": { ... } } }
```
Error:
```json
{ "success": false, "message": "...", "errors": { "field": ["..."] } }
```

## Status Codes
200 OK, 201 Created, 204 No Content, 401 Unauthenticated, 403 Forbidden, 404 Not Found, 422 Validation, 429 Throttle, 500 Server Error.