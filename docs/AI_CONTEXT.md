# AI Context

**Project:** Developer Ruhban

**Version:** 1.0

**Purpose:** Persistent AI Development Context

---

# Project Overview

You are assisting in the development of **Developer Ruhban**, a production-grade Public Developer Knowledge Platform.

This is **not** a traditional portfolio website.

It is a scalable knowledge platform where the owner publishes:

* Projects
* Blogs
* Tutorials
* AI Prompts
* Code Snippets
* Tools
* Websites
* Applications
* Reviews
* Resources
* Developer Notes
* Experiments
* Thoughts
* Problem → Solution Articles
* Downloads
* Videos
* Roadmaps
* Cheat Sheets
* Collections
* Recommendations

The project is expected to grow for many years without requiring architectural redesign.

---

# Primary Goal

Build a modern, fast, scalable, secure, and SEO-first knowledge platform that demonstrates engineering expertise while serving as a valuable public resource.

Every development decision should prioritize:

* Maintainability
* Performance
* Scalability
* Security
* User Experience
* SEO
* Reusability

---

# Technology Stack

## Backend

* PHP 8.3+
* Apache
* MySQL 8+

## Frontend

* HTML5
* CSS3
* Bootstrap 5
* Vanilla JavaScript

## Libraries

* CKEditor 5
* Prism.js
* SweetAlert2
* Chart.js
* SortableJS
* Cropper.js

## Hosting

* Shared Apache Hosting
* Linux
* cPanel Compatible

Do not introduce technologies outside this stack unless explicitly requested.

---

# Architecture

The project follows a custom MVC architecture.

Application Flow

Request

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

Rules

* Controllers handle requests only.
* Services contain business logic.
* Repositories communicate with the database.
* Views never access the database.
* Business logic never belongs in controllers or views.

Always follow the architecture defined in **ARCHITECTURE.md**.

---

# Database

Follow **DATABASE.md**.

Important principles:

* Universal Posts architecture
* One Posts table
* Content Types
* Post Meta
* Categories
* Tags
* SEO
* Media
* Collections

Never invent new database tables unless absolutely necessary.

Prefer extending existing structures.

---

# Content Model

Follow **CONTENT_MODEL.md**.

Everything is a Post.

Each Post belongs to one Content Type.

Examples

* Blog
* Project
* Tutorial
* Review
* Tool
* Prompt
* Snippet
* Resource
* Website
* App
* Thought
* Experiment

Content-specific fields belong in metadata.

---

# Design System

Follow **DESIGN_SYSTEM.md**.

Requirements

* Modern
* Minimal
* Professional
* Developer-focused
* Responsive
* Component-based
* Dark Mode
* Light Mode
* Accessibility-first

Never invent new design patterns if reusable components already exist.

---

# SEO

Follow **SEO_GUIDE.md**.

Always implement:

* Semantic HTML
* Schema.org
* JSON-LD
* Open Graph
* Twitter Cards
* Breadcrumbs
* XML Sitemap
* Canonical URLs
* Proper Metadata
* Internal Linking
* Accessible HTML
* Clean URLs

SEO is a core requirement.

---

# Security

Every feature must include:

* CSRF Protection
* XSS Protection
* SQL Injection Prevention
* Input Validation
* Output Escaping
* Password Hashing
* Secure Sessions
* Permission Checks

Never bypass security for convenience.

---

# Performance

Optimize every feature.

Requirements

* Efficient SQL
* Pagination
* Lazy Loading
* Optimized Images
* WebP Support
* Caching
* Minimal JavaScript
* Reusable Components

Always consider Core Web Vitals.

---

# Coding Standards

Generate production-quality code.

Requirements

* Modular
* Readable
* Documented
* Reusable
* Maintainable

Avoid:

* Duplicate logic
* Hardcoded values
* Large functions
* Tight coupling
* Dead code

---

# UI Principles

The interface should be:

* Clean
* Consistent
* Predictable
* Content-focused
* Fast

Every component must support:

* Responsive layouts
* Keyboard accessibility
* Dark mode
* Light mode

---

# Development Rules

Always:

* Follow existing architecture.
* Reuse existing components.
* Extend existing systems instead of replacing them.
* Maintain backward compatibility.
* Write secure code.
* Optimize for performance.
* Write maintainable code.
* Use meaningful naming.
* Keep functions focused on a single responsibility.
* Comment only when necessary to explain complex logic.

Never:

* Rewrite completed modules unless explicitly requested.
* Introduce breaking architectural changes.
* Duplicate functionality.
* Ignore existing conventions.
* Hardcode credentials, URLs, or configuration.
* Bypass validation or authorization.

---

# AI Behavior

When generating code:

1. Understand the requested feature.
2. Review the existing architecture.
3. Reuse existing components.
4. Keep the solution modular.
5. Consider scalability.
6. Consider security.
7. Consider SEO.
8. Consider accessibility.
9. Consider performance.
10. Generate production-ready code only.

If additional files are required, generate them.

If a feature affects multiple modules, update all affected modules consistently.

Never generate placeholder code unless explicitly requested.

---

# Session Instructions

Treat every conversation as a continuation of this project.

Do not redesign the architecture.

Do not replace existing systems without a clear technical reason.

Assume previous phases are already completed unless told otherwise.

Before writing code:

* Understand the objective.
* Follow all project documents.
* Preserve consistency.

---

# Reference Documents

Always follow these documents in order of priority:

1. PRD.md
2. ARCHITECTURE.md
3. DATABASE.md
4. CONTENT_MODEL.md
5. DESIGN_SYSTEM.md
6. SEO_GUIDE.md
7. AI_CONTEXT.md

If a conflict exists:

PRD.md has the highest authority.

---

# Final Objective

The final product should be a long-term, production-ready Public Developer Knowledge Platform capable of supporting thousands of content items, millions of page views, and years of continuous development while remaining fast, maintainable, secure, and SEO-friendly.

Every decision should move the project toward that goal.
