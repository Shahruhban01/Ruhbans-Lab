# Design System

**Project:** Developer Ruhban

**Version:** 1.0

**Status:** Draft

---

# 1. Purpose

This document defines the complete visual language for the Developer Ruhban platform.

The design system ensures every page, component, and feature follows the same visual identity, interaction patterns, spacing rules, and accessibility standards.

Every UI element must follow this document.

---

# 2. Design Philosophy

The platform should feel:

- Modern
- Minimal
- Premium
- Professional
- Developer-focused
- Content-first
- Fast
- Clean
- Consistent
- Accessible

The design should prioritize readability over decoration.

Users should always focus on the content, not unnecessary visual effects.

---

# 3. Design Principles

Always follow:

- Consistency
- Simplicity
- Reusability
- Accessibility
- Responsive Design
- Performance
- Visual Hierarchy
- Predictability

Avoid:

- Over-designed interfaces
- Excessive animations
- Bright distracting colors
- Inconsistent spacing
- Inconsistent typography

---

# 4. Theme Support

The platform must support:

- Light Theme
- Dark Theme

Theme switching should happen instantly without page reload.

User preference should persist.

System preference should be detected automatically.

---

# 5. Color System

Primary

Used for:

- Buttons
- Links
- Active Navigation
- Focus States

Secondary

Used for:

- Supporting UI

Success

Warnings

Errors

Information

Neutral Colors

Background

Surface

Border

Muted Text

Primary Text

Secondary Text

Never hardcode colors.

Use CSS Variables.

Example

--color-primary

--color-background

--color-text

--color-border

etc.

---

# 6. Typography

Use one font family.

Hierarchy

Display

H1

H2

H3

H4

H5

H6

Body

Small

Caption

Code

Rules

Maximum readability

Consistent line height

Consistent spacing

Never skip heading hierarchy.

---

# 7. Layout System

Maximum Content Width

Container Width

Sidebar Width

Navbar Height

Footer Height

Content Padding

Section Spacing

All spacing should use an 8px spacing scale.

Example

4

8

16

24

32

40

48

64

80

96

---

# 8. Grid System

Desktop

Tablet

Mobile

Responsive breakpoints

Extra Small

Small

Medium

Large

Extra Large

Never create layouts that only work on desktop.

---

# 9. Border Radius

Small

Medium

Large

Rounded

Use the same radius throughout the application.

---

# 10. Shadows

Use subtle shadows.

Levels

Small

Medium

Large

Hover

Modal

Dropdown

Avoid heavy shadows.

---

# 11. Buttons

Button Types

Primary

Secondary

Outline

Ghost

Danger

Success

Warning

Info

Sizes

Small

Medium

Large

States

Default

Hover

Focus

Disabled

Loading

Rules

Every button should have:

Hover state

Focus state

Disabled state

Loading state

---

# 12. Forms

Components

Input

Textarea

Select

Checkbox

Radio

Toggle

Date Picker

Search

Password

Rules

Labels always visible.

Validation messages below fields.

Required fields marked clearly.

Support keyboard navigation.

---

# 13. Cards

Card Types

Content Card

Project Card

Tool Card

Review Card

Resource Card

Collection Card

Dashboard Card

Cards should have:

Consistent spacing

Consistent padding

Optional actions

Hover interaction

---

# 14. Navigation

Navbar

Logo

Primary Navigation

Search

Theme Toggle

Profile Menu

Sidebar

Collapsible

Icons

Nested Menus

Active Indicator

---

# 15. Tables

Features

Sorting

Filtering

Pagination

Responsive

Sticky Header

Empty State

Loading State

---

# 16. Badges

Status

Category

Tag

Technology

Difficulty

Version

Featured

Recommended

---

# 17. Alerts

Types

Success

Warning

Danger

Information

Each alert should have:

Icon

Title

Description

Dismiss Button

---

# 18. Modals

Support

Confirmation

Forms

Media Preview

Delete Actions

Large Content

Rules

Keyboard Accessible

ESC closes modal

Overlay click configurable

---

# 19. Dropdowns

Support

Single Select

Multi Select

Searchable

Grouped

Keyboard Navigation

---

# 20. Tabs

Horizontal

Vertical

Scrollable

Accessible

---

# 21. Accordions

Used for:

FAQ

Documentation

Long Content

---

# 22. Breadcrumbs

Every content page should include breadcrumbs.

Example

Home

↓

Projects

↓

AI

↓

Project Name

---

# 23. Pagination

Support

Previous

Next

Page Numbers

First

Last

Mobile Friendly

---

# 24. Search

Universal Search

Autocomplete

Suggestions

Recent Searches

Popular Searches

Filters

Search should be accessible from every page.

---

# 25. Icons

Use one icon library only.

Icons should remain visually consistent.

Do not mix icon styles.

---

# 26. Code Blocks

Features

Syntax Highlighting

Copy Button

Language Label

Line Numbers

Wrap Toggle

Dark Theme Support

---

# 27. Images

Support

Responsive Images

Lazy Loading

WebP

Lightbox

Captions

Alt Text

---

# 28. Empty States

Every page without data should display:

Illustration

Message

Action Button

Never show blank pages.

---

# 29. Loading States

Support

Skeleton Loading

Progress Bar

Spinner

Placeholder Cards

Avoid blocking the UI.

---

# 30. Error States

404

403

500

Network Error

Validation Errors

Friendly Messages

Retry Button

---

# 31. Animations

Use subtle animations only.

Support

Hover

Fade

Slide

Expand

Collapse

Loading

Avoid long animations.

Keep interactions fast.

---

# 32. Accessibility

Follow WCAG guidelines.

Requirements

Semantic HTML

Keyboard Navigation

Visible Focus States

ARIA Labels

High Contrast

Readable Fonts

Proper Heading Order

Screen Reader Support

---

# 33. Responsive Design

Every page must support:

Desktop

Laptop

Tablet

Mobile

No horizontal scrolling.

Touch-friendly interactions.

---

# 34. Component Library

Reusable Components

Navbar

Sidebar

Footer

Button

Card

Badge

Avatar

Modal

Dropdown

Tooltip

Pagination

Breadcrumb

Table

Accordion

Tabs

Search Box

Toast

Alert

Form Controls

Code Block

Comment Card

Tag

Category Chip

Author Box

Social Share

Reading Progress

Table of Contents

Theme Switch

Loading Skeleton

Every page should be built from these reusable components.

---

# 35. Interaction Rules

Buttons must provide immediate feedback.

Forms must validate clearly.

Links must indicate hover.

Clickable elements must have cursor changes.

Dangerous actions must require confirmation.

---

# 36. Content Layout

Long-form content should include:

Hero Section

Metadata

Table of Contents

Main Content

Related Articles

Author Box

Comments (optional)

Share Section

Previous / Next Navigation

---

# 37. Dashboard Design

Dashboard should include:

Statistics Cards

Recent Activity

Charts

Quick Actions

Latest Content

Storage Usage

System Status

Responsive Widgets

---

# 38. Design Rules

- Use reusable components only.
- Never hardcode styles inside HTML.
- Use design tokens through CSS variables.
- Maintain consistent spacing.
- Keep typography consistent.
- Every component must support dark mode.
- Every component must be responsive.
- Every interaction must provide visual feedback.
- Prioritize readability over visual complexity.
- Design for long-term scalability, not individual pages.