# Database Design Document

**Project:** Developer Ruhban

**Version:** 1.0

**Status:** Draft

---

# 1. Purpose

This document defines the complete database architecture for the Developer Ruhban platform.

The database is designed to be:

- Scalable
- Normalized
- Flexible
- Easy to maintain
- SEO-friendly
- Future-proof

The system must support thousands of content items without requiring schema redesign.

---

# 2. Database Engine

Database

MySQL 8+

Character Set

utf8mb4

Collation

utf8mb4_unicode_ci

Storage Engine

InnoDB

Timezone

UTC

---

# 3. Database Design Principles

The database follows:

- Third Normal Form (3NF)
- Foreign Key Constraints
- Soft Deletes
- UUID-ready architecture
- Audit Fields
- Indexed Queries
- Minimal Data Duplication

---

# 4. Core Database Philosophy

Everything is Content.

Instead of creating tables like:

blogs

projects

tools

reviews

snippets

prompts

the system uses a single universal content table.

Each record has a Content Type.

Example

Post

↓

Content Type

↓

Blog

Tutorial

Tool

Project

Review

Prompt

Website

App

Resource

Video

Download

Experiment

Thought

This dramatically simplifies development.

---

# 5. Core Tables

## Users

Stores user accounts.

Fields

- id
- role_id
- username
- name
- email
- password
- avatar
- bio
- website
- github
- linkedin
- twitter
- is_active
- email_verified_at
- last_login
- created_at
- updated_at
- deleted_at

---

## Roles

Examples

Admin

Editor

Author

Visitor

---

## Permissions

Granular permissions.

---

## Role Permissions

Many-to-many relationship.

---

## Content Types

Examples

Blog

Project

Tutorial

Tool

Website

App

Review

Snippet

Prompt

Video

Resource

Download

Collection

Thought

Experiment

Roadmap

Cheat Sheet

---

## Posts

This is the heart of the platform.

Fields

- id
- content_type_id
- author_id
- title
- slug
- excerpt
- content
- featured_image
- status
- visibility
- reading_time
- published_at
- created_at
- updated_at
- deleted_at

Status

Draft

Published

Scheduled

Archived

Visibility

Public

Private

Unlisted

---

## Post Meta

Stores extra fields for different content types.

Examples

Github URL

Demo URL

Price

Difficulty

Rating

Version

AI Model

Platform

Download URL

API Endpoint

Framework

Technology

Key

meta_key

Value

meta_value

---

## Categories

Supports nested categories.

Fields

- id
- parent_id
- name
- slug
- description
- icon
- featured_image

---

## Tags

Fields

- id
- name
- slug

---

## Post Tags

Many-to-many.

---

## Post Categories

Many-to-many.

---

## Media

Stores uploaded files.

Fields

- id
- uploader_id
- filename
- original_name
- path
- mime_type
- extension
- file_size
- width
- height
- alt_text
- created_at

---

## Comments

Nested comments.

Supports replies.

Status

Pending

Approved

Spam

Rejected

---

## Likes

Tracks likes.

---

## Bookmarks

Stores saved posts.

---

## Views

Tracks page views.

Includes

IP

Device

Browser

Country

Date

---

## Collections

User-created collections.

Example

Best AI Tools

Flutter Resources

PHP Tips

---

## Collection Items

Maps posts to collections.

---

## Downloads

Tracks download count.

---

## SEO

One record per post.

Fields

- meta_title
- meta_description
- canonical_url
- robots
- schema_type
- og_title
- og_description
- og_image

---

## Redirects

Stores

301

302

410

Redirects.

---

## Search Logs

Stores user searches.

Useful for analytics.

---

## Newsletter Subscribers

Stores emails.

---

## Contact Messages

Stores contact form submissions.

---

## Notifications

Admin notifications.

---

## Settings

Application configuration.

Site Name

SMTP

Logo

Analytics

Theme

Timezone

Social Links

SEO Defaults

---

## Audit Logs

Tracks every important action.

Login

Delete

Update

Publish

Upload

Permission Change

---

# 6. Relationships

User

↓

Posts

↓

Categories

↓

Tags

↓

Media

↓

Comments

↓

SEO

↓

Views

↓

Likes

↓

Bookmarks

Everything connects back to Posts.

---

# 7. Indexing Strategy

Indexes should exist on:

slug

published_at

status

content_type_id

category_id

author_id

tag_id

email

username

created_at

---

# 8. Soft Deletes

The following tables use soft deletes.

Users

Posts

Media

Categories

Collections

This allows recovery.

---

# 9. Data Integrity

Always enforce:

Foreign Keys

Unique Slugs

Unique Emails

Unique Usernames

Cascade Deletes where appropriate

Transactions for critical operations

---

# 10. Future Expansion

The schema must support future modules without redesign.

Examples

Forum

Marketplace

AI Assistant

PWA

Public API

Plugins

Themes

Knowledge Graph

Teams

Badges

Achievements

---

# 11. Database Rules

Never duplicate data.

Never store calculated values unless necessary.

Always use foreign keys.

Always index searchable columns.

Never hardcode IDs.

Use many-to-many relationships where appropriate.

Keep tables normalized.

Store reusable metadata inside Post Meta.

Everything published on the website should originate from the Posts table.

This architecture should remain valid for many years without structural changes.