# SEO Guide

**Project:** Developer Ruhban

**Version:** 1.0

**Status:** Draft

---

# 1. Purpose

This document defines the SEO architecture and standards for the Developer Ruhban platform.

SEO is a core feature of the platform, not an afterthought.

Every page, module, and component must follow this guide.

The objective is to build a website that is:

- Crawlable
- Indexable
- Fast
- Structured
- Helpful
- Accessible
- Scalable
- Content-rich
- AI Search Friendly

---

# 2. SEO Philosophy

The platform should focus on:

- User-first content
- Helpful content
- Topical authority
- Semantic search
- Content quality
- Excellent user experience

Avoid:

- Keyword stuffing
- Hidden text
- Duplicate content
- Thin content
- Spam pages
- AI-generated filler
- Manipulative SEO techniques

---

# 3. URL Structure

URLs must be:

- Human readable
- Short
- Descriptive
- Lowercase
- Hyphen separated

Good

/blog/php-routing-system

/tools/photopea

/projects/wows-audio-platform

/tutorials/flutter-firebase-auth

Avoid

?id=25

/post123

/blog/1234

---

# 4. Slug Rules

Every slug must:

- Be unique
- Use lowercase
- Use hyphens
- Contain primary keywords
- Avoid stop words where possible

Never change published slugs.

If changed:

Create a 301 redirect.

---

# 5. Site Structure

The website hierarchy should be:

Home

↓

Content Type

↓

Category

↓

Content

Example

Home

↓

Tutorials

↓

Flutter

↓

Flutter Authentication Guide

---

# 6. Content Types

Each content type should have its own optimized layout.

Examples

Projects

Blogs

Tutorials

Reviews

Tools

Apps

Resources

Prompts

Code Snippets

Downloads

Collections

Thoughts

---

# 7. Metadata

Every page must support:

SEO Title

Meta Description

Canonical URL

Robots Directive

Open Graph

Twitter Card

Structured Data

Featured Image

No page should be published without metadata.

---

# 8. Title Rules

Titles should:

Be unique

Contain the main keyword naturally

Be descriptive

Avoid clickbait

Prefer clarity over cleverness

---

# 9. Meta Description Rules

Should:

Summarize the page

Encourage clicks

Be unique

Match page content

Avoid duplication

---

# 10. Heading Structure

Use proper heading hierarchy.

H1

↓

H2

↓

H3

↓

H4

Never skip heading levels unnecessarily.

Every page should have one H1.

---

# 11. Internal Linking

Every article should link to:

Related tutorials

Related projects

Related tools

Related reviews

Related resources

Parent categories

Collections

Internal links should feel natural.

---

# 12. Categories

Categories represent broad topics.

Examples

PHP

Flutter

AI

Linux

Backend

Frontend

Databases

AWS

Security

Categories should be evergreen.

---

# 13. Tags

Tags describe specific concepts.

Examples

JWT

REST API

Redis

Docker

Bootstrap

Authentication

Caching

Do not create unnecessary tags.

---

# 14. Breadcrumbs

Every content page should display breadcrumbs.

Example

Home

>

Tutorials

>

Flutter

>

State Management

Use structured data for breadcrumbs.

---

# 15. Structured Data

Every page should include appropriate Schema.org markup.

Supported schemas

WebSite

Organization

Person

Article

BlogPosting

HowTo

FAQ

BreadcrumbList

SoftwareApplication

Review

WebPage

CollectionPage

ItemList

VideoObject

Code

SearchAction

---

# 16. XML Sitemap

Automatically generate:

Main Sitemap

Post Sitemap

Category Sitemap

Tag Sitemap

Image Sitemap

Video Sitemap

Update automatically.

---

# 17. robots.txt

Allow:

Public content

Block:

Admin

Login

Temporary files

Cache

Private uploads

System directories

---

# 18. Canonical URLs

Every page must define:

Canonical URL

Prevent duplicate indexing.

---

# 19. Pagination

Support:

Previous

Next

Page Numbers

Canonical handling

Avoid duplicate pagination issues.

---

# 20. Images

Every image must include:

Alt Text

Title (optional)

Caption (when helpful)

Responsive sizes

WebP support

Lazy Loading

Descriptive filenames

Example

php-routing-guide.webp

Not

IMG_001.webp

---

# 21. Videos

Embedded videos should include:

Title

Description

Thumbnail

Structured Data

Transcript (if available)

---

# 22. Open Graph

Generate automatically.

Include:

Title

Description

Image

Type

URL

Site Name

---

# 23. Twitter Cards

Automatically generate.

Include:

Title

Description

Image

Creator

---

# 24. RSS Feed

Support RSS for:

Blogs

Tutorials

Projects

Reviews

News

---

# 25. Search

Internal search should support:

Autocomplete

Suggestions

Categories

Tags

Recent Searches

Popular Searches

Search should be crawl-friendly.

---

# 26. Performance

SEO depends on performance.

Requirements:

Fast loading

Optimized queries

Compression

Caching

Image optimization

Lazy loading

Minimal JavaScript

Excellent Core Web Vitals

---

# 27. Accessibility

Accessibility improves SEO.

Requirements:

Semantic HTML

Keyboard Navigation

ARIA Labels

Alt Text

Readable Typography

High Contrast

Logical Focus Order

---

# 28. Mobile Optimization

Mobile-first design.

Requirements

Responsive layout

Touch-friendly

Readable text

Fast loading

No horizontal scrolling

---

# 29. Content Quality

Every published page should:

Solve a real problem

Be accurate

Be original

Contain examples

Include references when appropriate

Be updated over time

Avoid thin content

---

# 30. Related Content

Automatically display:

Related Articles

Related Projects

Related Tools

Related Reviews

Related Resources

Based on:

Category

Tags

Content Type

---

# 31. Author Authority

Every article should display:

Author

Bio

Expertise

Published Date

Updated Date

Related Content

---

# 32. Social Sharing

Support sharing to:

LinkedIn

X

Facebook

WhatsApp

Telegram

Email

Copy Link

---

# 33. 404 Strategy

404 pages should:

Explain the error

Suggest search

Show popular content

Show recent content

Never leave users stranded.

---

# 34. Redirect Strategy

Support:

301 Permanent

302 Temporary

410 Gone

Automatically log broken URLs.

---

# 35. Content Freshness

Support:

Updated Date

Revision History

Recently Updated

Last Reviewed

Encourage regular content maintenance.

---

# 36. AI & Semantic Search Optimization

Content should be optimized for:

Natural language queries

Question-based searches

Conversational search

Entity-based search

Helpful summaries

Clear headings

Logical structure

Well-defined terminology

Avoid writing solely for keywords.

Write for understanding.

---

# 37. Core Web Vitals

Target:

Excellent Largest Contentful Paint (LCP)

Excellent Interaction to Next Paint (INP)

Excellent Cumulative Layout Shift (CLS)

Minimize render-blocking resources.

Optimize fonts and images.

---

# 38. Technical SEO

Every page should have:

Canonical URL

Clean HTML

Proper HTTP status

Structured data

Optimized assets

Valid sitemap inclusion

Robots directives

Correct metadata

---

# 39. Analytics & Monitoring

Track:

Page Views

Organic Traffic

Search Queries

Top Landing Pages

Bounce Rate

Scroll Depth

Popular Searches

404 Errors

Broken Links

Downloads

---

# 40. SEO Rules

- Every page must target a clear user intent.
- Every page must have unique metadata.
- Never publish duplicate content.
- Every image requires descriptive alt text.
- Every page must use semantic HTML.
- Every content item must belong to at least one category.
- Every content item should include relevant internal links.
- Never create orphan pages.
- Optimize for users first and search engines second.
- Focus on topical authority rather than individual keywords.
- Keep content updated as technologies evolve.
- SEO decisions must always align with usability and long-term maintainability.