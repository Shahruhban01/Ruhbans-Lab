# Content Model Document

**Project:** Developer Ruhban

**Version:** 1.0

**Status:** Draft

---

# 1. Purpose

This document defines every content type available in the platform.

Instead of creating different systems for blogs, tools, projects, reviews, tutorials, etc., the platform uses one universal content model.

Every published item is a **Post**.

Each post belongs to a **Content Type**.

This allows the platform to scale indefinitely without redesigning the CMS.

---

# 2. Universal Content Model

Every content type inherits the following base fields.

## Base Fields

- Title
- Slug
- Short Description (Excerpt)
- Content (Rich Text / Markdown)
- Featured Image
- Gallery
- Author
- Categories
- Tags
- Status
- Visibility
- SEO Metadata
- Featured Flag
- Published Date
- Reading Time
- Created At
- Updated At

These fields are available for every content type.

---

# 3. Content Types

## 3.1 Project

Purpose

Showcase software projects.

Additional Fields

- GitHub Repository
- Live Demo
- Tech Stack
- Architecture
- Features
- Screenshots
- Timeline
- Lessons Learned
- Current Status
- License

Display

Project Page

Portfolio

Related Projects

---

## 3.2 Blog

Purpose

Long-form technical articles.

Additional Fields

- Reading Difficulty
- Reading Time
- Table of Contents
- Series
- Featured Quote

Display

Article Layout

Related Articles

Author Box

---

## 3.3 Tutorial

Purpose

Step-by-step educational guides.

Additional Fields

- Difficulty
- Estimated Duration
- Prerequisites
- Required Software
- Steps
- Result
- Downloads

Display

Tutorial Layout

Progress Sections

Navigation

---

## 3.4 Problem → Solution

Purpose

Document bugs and their solutions.

Additional Fields

- Problem
- Environment
- Cause
- Solution
- Commands
- References
- Related Problems

Display

Problem Card

Solution Card

Copy Buttons

---

## 3.5 Tool

Purpose

Recommend online tools.

Additional Fields

- Official Website
- Category
- Platform
- Pricing
- Free Plan
- Alternatives
- Pros
- Cons
- Rating

Display

Tool Card

Comparison Section

---

## 3.6 Website

Purpose

Useful websites.

Additional Fields

- URL
- Category
- Screenshot
- Description
- Rating
- Alternatives

---

## 3.7 Application

Purpose

Desktop or Mobile applications.

Additional Fields

- Platform
- Download URL
- Current Version
- Operating System
- Price
- License
- Developer

---

## 3.8 Review

Purpose

Review software, hardware, books, hosting, courses, etc.

Additional Fields

- Rating
- Advantages
- Disadvantages
- Verdict
- Alternatives

Display

Rating Card

Comparison Table

---

## 3.9 AI Prompt

Purpose

Store reusable prompts.

Additional Fields

- AI Model
- Prompt Category
- Prompt Text
- Variables
- Example Output
- Copy Button

---

## 3.10 Code Snippet

Purpose

Reusable code.

Additional Fields

- Programming Language
- Framework
- Filename
- Code
- Explanation
- Copy Button

Display

Syntax Highlighting

---

## 3.11 Resource

Purpose

Useful learning resources.

Additional Fields

- Resource Type
- Author
- Publisher
- URL
- Price
- Level

Examples

Books

Courses

Documentation

Repositories

Videos

PDFs

---

## 3.12 Video

Purpose

Embed educational videos.

Additional Fields

- YouTube URL
- Duration
- Channel
- Thumbnail

---

## 3.13 Download

Purpose

Distribute downloadable files.

Additional Fields

- File
- File Size
- Version
- Changelog
- License

---

## 3.14 Developer Note

Purpose

Personal technical notes.

Additional Fields

- Topic
- Summary
- References

---

## 3.15 Thought

Purpose

Engineering opinions and ideas.

Additional Fields

- Topic
- Summary

---

## 3.16 Experiment

Purpose

Document experiments.

Additional Fields

- Objective
- Method
- Result
- Conclusion
- Lessons

---

## 3.17 Collection

Purpose

Group multiple posts together.

Examples

Best AI Tools

Flutter Resources

Backend Roadmaps

Linux Commands

Each collection references multiple posts.

---

## 3.18 Recommendation

Purpose

Recommend products, services, or software.

Additional Fields

- Recommendation Type
- Reason
- Alternatives
- Rating

---

## 3.19 Roadmap

Purpose

Learning paths.

Additional Fields

- Difficulty
- Estimated Time
- Milestones
- Resources

---

## 3.20 Cheat Sheet

Purpose

Quick reference pages.

Additional Fields

- Topic
- Commands
- Examples

---

# 4. Common Behaviors

Every content type supports:

- Categories
- Tags
- Search
- SEO
- Featured Image
- Social Sharing
- Reading Time
- Author
- Breadcrumbs
- Related Content
- Comments (optional)
- View Counter
- Likes (future)
- Bookmarks (future)

---

# 5. Status Workflow

Every content item supports:

Draft

↓

Review

↓

Scheduled

↓

Published

↓

Archived

↓

Deleted

---

# 6. Visibility

Every content item can be:

- Public
- Private
- Unlisted

---

# 7. SEO Support

Every content item supports:

- SEO Title
- Meta Description
- Canonical URL
- Open Graph
- Twitter Card
- Structured Data
- Breadcrumbs
- Clean URL

---

# 8. Search Support

Every content type is searchable by:

- Title
- Description
- Content
- Tags
- Categories
- Author
- Content Type

Search should support:

- Filters
- Sorting
- Suggestions
- Autocomplete

---

# 9. Relationships

A content item may have:

- Multiple Categories
- Multiple Tags
- Multiple Images
- Multiple Downloads
- Multiple References
- Multiple Related Posts

---

# 10. Future Content Types

The architecture should allow adding new content types without database redesign.

Examples

- Podcast
- Webinar
- Newsletter
- Changelog
- API Documentation
- Interview Experience
- Case Study
- Product Launch
- Community Post

---

# 11. Content Rules

- Every published item is a Post.
- Every Post belongs to one Content Type.
- Every Content Type extends the Universal Content Model.
- Content-specific fields are stored using metadata.
- Every content type is searchable.
- Every content type supports SEO.
- Every content type follows the same publishing workflow.
- Every content type should be reusable and extensible.
- New content types should never require changes to the core architecture.