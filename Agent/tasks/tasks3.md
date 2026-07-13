# PRD --- Enterprise Login Page Redesign

**Version:** 1.0\
**Status:** Ready for Design & Development\
**Priority:** High\
**Platform:** Web (Desktop, Tablet, Mobile)\
**Design Goal:** Premium Enterprise Authentication Experience

## 1. Overview

Redesign the current login page into a modern enterprise authentication
screen suitable for a Pension Fund Management System.

### Goals

-   Security
-   Professionalism
-   Trust
-   Simplicity
-   Corporate identity

The interface should resemble enterprise financial software used daily
by banks, ERP systems, and pension institutions.

## 2. Visual Style

References: - Stripe Dashboard - Clerk Authentication - Vercel -
Linear - Notion - Microsoft Fluent - IBM Carbon - Material Design 3 -
shadcn/ui

## 3. Color Palette

### Primary

-   #1E3A8A

### Secondary

-   #2563EB
-   #3B82F6

### Background

-   #F8FAFC
-   #F1F5F9
-   #EEF2FF

### Text

-   Primary: #0F172A
-   Secondary: #334155
-   Muted: #64748B

### Border

-   #E2E8F0

## 4. Layout

Desktop split layout: - Left: 35% decorative panel - Right: 65% centered
login card

### Left Panel

-   No logo
-   No company name
-   No marketing copy
-   Soft blue gradients
-   Abstract wave shapes
-   Thin architectural/financial line illustrations
-   Large whitespace

### Right Panel

Centered login card (max-width 460px).

## 5. Login Card

-   White background
-   Radius: 20px
-   Soft shadow
-   Padding: 48px

Header: - Shield/Lock icon - Title: Masuk - Subtitle: Masukkan
kredensial Anda untuk melanjutkan.

## 6. Form

Fields: - Email - Password - Password visibility toggle - Remember me -
Forgot password

Primary button: - Full width - Height: 52px - Background: #1E3A8A

Bottom: - Register link - Privacy notice

## 7. Responsive

Desktop: - Split layout

Tablet: - Smaller left panel

Mobile: - Hide decorative panel - Card width 90%

## 8. Accessibility

-   WCAG AA
-   Keyboard navigation
-   ARIA labels
-   Visible focus states

## 9. Tech Stack

-   Tailwind CSS
-   shadcn/ui
-   TypeScript
-   React or Vue
-   Framer Motion
-   Lucide Icons
-   Zod
-   React Hook Form / Vee Validate

## 10. Acceptance Criteria

-   Enterprise-grade appearance
-   Uses Dapense blue palette
-   No logo or brand text
-   Responsive
-   Accessible
-   Production-ready

------------------------------------------------------------------------

# AI Design Prompt

Design a premium enterprise-grade login page for a Pension Fund
Management System with a modern SaaS dashboard aesthetic. Use a clean,
minimal, corporate style that communicates trust, security, and
professionalism. Apply the existing Dapense blue color palette (#1E3A8A,
#2563EB, #3B82F6) with soft gray backgrounds (#F8FAFC, #F1F5F9). Do not
include any logo, brand name, hero text, or marketing content.

Use a 35/65 split layout with an abstract decorative left panel and a
centered login card on the right. Include a shield icon, title "Masuk",
subtitle, email/password fields, remember me, forgot password, primary
button, register link, and privacy notice.

The final design should look comparable to Stripe, Clerk, Vercel,
Microsoft Fluent, IBM Carbon, and Notion authentication experiences.
