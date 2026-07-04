# Architecture Document

**Project:** Developer Ruhban

**Version:** 1.0

**Status:** Draft

---

# 1. Purpose

This document defines the complete software architecture for the Developer Ruhban platform.

Its purpose is to ensure that every future feature, module, and line of code follows the same architectural principles, coding standards, and project structure.

This document acts as the single source of truth for all backend and frontend development.

---

# 2. Architecture Philosophy

The platform is designed with the following goals:

- Simplicity
- Scalability
- Maintainability
- Security
- Performance
- SEO-first
- Content-first
- Shared hosting compatibility
- Modular development
- Easy future expansion

Every architectural decision should support these goals.

---

# 3. System Overview

The application is a custom-built MVC web application running on Apache and MySQL.

The system consists of five major layers:

1. Presentation Layer
2. Application Layer
3. Domain Layer
4. Data Layer
5. Infrastructure Layer

Each layer has a single responsibility.

---

# 4. Technology Stack

Backend

- PHP 8.3+
- Apache
- MySQL 8+

Frontend

- HTML5
- CSS3
- Bootstrap 5
- Vanilla JavaScript

Libraries

- CKEditor 5
- Prism.js
- SweetAlert2
- Chart.js
- SortableJS
- Cropper.js

Hosting

- Apache Shared Hosting
- Linux
- cPanel Compatible

---

# 5. Architectural Principles

The project follows these principles:

- MVC Architecture
- Separation of Concerns
- DRY (Don't Repeat Yourself)
- KISS (Keep It Simple)
- Reusable Components
- Configuration over Hardcoding
- Convention over Configuration
- Single Responsibility Principle
- Progressive Enhancement
- Defensive Programming

---

# 6. High-Level Architecture

User

↓

Apache

↓

Router

↓

Middleware

↓

Controller

↓

Service

↓

Repository

↓

Database

↓

Response

↓

View

The Controller should never directly communicate with the database.

All business logic belongs inside Services.

Database access belongs inside Repositories.

---

# 7. Folder Structure

```
/
│
├── app/
│   ├── Controllers/
│   ├── Models/
│   ├── Services/
│   ├── Repositories/
│   ├── Middleware/
│   ├── Helpers/
│   ├── Core/
│   ├── Views/
│   ├── Policies/
│   ├── Traits/
│   ├── Events/
│   ├── Exceptions/
│   └── Validators/
│
├── config/
├── database/
├── public/
├── storage/
├── uploads/
├── cache/
├── logs/
├── routes/
├── assets/
│   ├── css/
│   ├── js/
│   ├── images/
│   └── fonts/
│
├── docs/
└── vendor/
```

---

# 8. MVC Responsibilities

## Controllers

Controllers should:

- Receive requests
- Validate input
- Call services
- Return responses

Controllers must NOT:

- Write SQL
- Contain business logic
- Generate HTML manually

---

## Services

Services contain:

- Business logic
- Calculations
- Rules
- Workflows

Services should never render HTML.

---

## Repositories

Repositories handle:

- SQL Queries
- Database reads
- Database writes

Repositories should not contain business rules.

---

## Models

Models represent database entities.

Models should remain lightweight.

---

## Views

Views should only display data.

Views must never perform database queries.

Views should not contain business logic.

---

# 9. Routing

Routing must support:

GET

POST

PUT

PATCH

DELETE

Routes should be grouped.

Example:

Admin

Authentication

API

Frontend

Media

Search

SEO

---

# 10. Middleware

Middleware examples:

Authentication

Guest

Admin

Editor

Rate Limiter

CSRF

Maintenance

Request Logger

Permission Checker

---

# 11. Authentication

Authentication should support:

Login

Logout

Forgot Password

Remember Me

Email Verification

Password Reset

Role-Based Access

Roles:

Admin

Editor

Author

Visitor

---

# 12. Authorization

Permissions should be granular.

Examples:

Create Posts

Delete Posts

Publish Posts

Manage Users

Manage Media

Manage SEO

Manage Settings

Permissions must never be hardcoded.

---

# 13. Configuration

All configuration belongs inside config/.

Never hardcode:

Database credentials

URLs

Email addresses

API keys

Storage paths

Upload limits

---

# 14. Database Layer

Database communication should always use:

Prepared Statements

Parameterized Queries

Transactions where necessary

Indexes for performance

Foreign Keys

Repositories are the only layer allowed to communicate with MySQL.

---

# 15. Validation

Validation should exist in two places.

Frontend

Backend

Backend validation is mandatory.

Frontend validation is optional for user experience only.

---

# 16. File Uploads

Allowed:

Images

PDF

ZIP

Videos

Documents

Requirements:

File validation

Extension validation

MIME validation

Unique filenames

Directory organization

Image optimization

---

# 17. Error Handling

Errors should never expose:

SQL

Server paths

Stack traces

Sensitive information

All errors should be logged.

Users should receive friendly error messages.

---

# 18. Logging

Log:

Errors

Authentication

Failed Login

Uploads

Deletes

Updates

System Events

Logs should be stored separately from uploads.

---

# 19. Caching

Support:

Query Cache

File Cache

Page Cache

Configuration Cache

Caching should be optional.

---

# 20. Security

Requirements:

Prepared Statements

CSRF Protection

XSS Protection

Password Hashing

Secure Sessions

Rate Limiting

Input Sanitization

Output Escaping

Secure Cookies

HTTPS Support

No sensitive data should ever be exposed.

---

# 21. Performance

Always:

Lazy Load Images

Optimize SQL

Minify Assets

Compress Responses

Use Pagination

Optimize Images

Reduce HTTP Requests

Avoid Duplicate Queries

---

# 22. Coding Standards

Use:

Meaningful names

Small methods

Reusable functions

Consistent formatting

No duplicated logic

No magic numbers

No hardcoded values

Document complex code.

---

# 23. Naming Conventions

Classes

PascalCase

Methods

camelCase

Variables

camelCase

Constants

UPPER_CASE

Database

snake_case

URLs

kebab-case

---

# 24. SEO Architecture

SEO must be built into the architecture.

Support:

Clean URLs

Meta Manager

Canonical URLs

Schema.org

JSON-LD

XML Sitemap

Image Sitemap

robots.txt

Breadcrumbs

Open Graph

Twitter Cards

RSS

Internal Linking

Search-friendly HTML

---

# 25. Scalability

The system should support:

100,000+ articles

Millions of page views

Thousands of media files

Future REST API

PWA

AI features

Community features

without architectural redesign.

---

# 26. Future Expansion

Architecture should support future modules without modification.

Examples:

Forum

Marketplace

AI Search

Plugins

Themes

Mobile App

Public API

Teams

Knowledge Graph

---

# 27. Non-Negotiable Rules

- Never bypass Services.
- Never query the database from Views.
- Never place business logic inside Controllers.
- Never hardcode configuration values.
- Never duplicate functionality.
- Every module must be reusable.
- Every page must be responsive.
- Every feature must be secure.
- Every feature must be SEO-friendly.
- Every database query must be optimized.
- Every new module must follow this architecture.