# PRD: Debug Raw HTML Rendering on Sidebar Navigation

**Version:** 1.0
**Priority:** Critical (P1)
**Goal:** Identify and fix the root cause of sidebar navigation rendering raw HTML instead of the expected application page.

---

# Role

You are a Senior Laravel, Blade, JavaScript, and Browser Debugging Engineer.

Your responsibility is **not to immediately write code**.

Your responsibility is to perform a complete investigation, identify the actual root cause with evidence, and only then implement the smallest possible fix.

Work like a senior engineer debugging a production system.

Never guess.

Never rewrite large portions of the application unless absolutely necessary.

---

# Problem Statement

When clicking a sidebar menu item (for example:

- Journaling
    - Kas Masuk
    - Kas Keluar
    - Bank Masuk
    - Bank Keluar
    - Memorial
    - Memorial Penutup

),

the application does **not** render the normal page.

Instead it displays raw HTML content, showing:

- links
- bullet lists
- menu items
- HTML page structure

instead of rendering the Blade layout.

This behavior indicates that somewhere between the click event and the browser rendering, the response is handled incorrectly.

---

# Objectives

Determine exactly why this happens.

Follow the execution flow from:

User Click

↓

JavaScript

↓

HTTP Request

↓

Laravel Route

↓

Controller

↓

Blade View

↓

Response

↓

Browser Rendering

Find the exact point where the application fails.

---

# Investigation Scope

Inspect the entire navigation system.

Including but not limited to:

## Laravel

- routes/web.php
- routes/*.php
- Controllers
- Middleware
- Blade Layouts
- Blade Components
- Blade Includes
- View Composers

---

## Frontend

Inspect:

resources/js/

resources/views/

public/js/

public/assets/

Look for:

- fetch()
- axios
- $.ajax()
- $.get()
- $.post()
- $.load()
- XMLHttpRequest
- Livewire
- Inertia
- Alpine
- Vue
- React

---

## Sidebar

Inspect:

- sidebar.blade.php
- navigation.blade.php
- menu.blade.php
- components/sidebar/*
- layouts/sidebar/*
- layouts/app.blade.php

Verify:

- href
- onclick
- data-url
- data-target
- custom attributes

---

## Event Listeners

Search entire project for:

preventDefault

addEventListener

onclick

delegate

navigation

router

pushState

history.pushState

replaceState

pjax

turbo

swup

---

## DOM Rendering

Search for:

innerHTML

outerHTML

textContent

innerText

append

prepend

replaceChildren

DOMParser

createElement

insertAdjacentHTML

document.write

---

## AJAX Rendering

Verify:

Does JavaScript expect:

JSON

but receive

HTML?

Or:

Does it receive HTML but insert it incorrectly?

---

## Browser Debugging

Inspect:

Console

Network

Elements

Sources

Application

Verify:

- HTTP status
- Response body
- Content-Type
- Request headers
- Response headers
- Redirects
- Caching

---

# Required Verification Checklist

Determine whether:

☐ Route exists

☐ Route name is correct

☐ URL generated correctly

☐ Middleware redirects unexpectedly

☐ Controller returns Blade

☐ Controller returns JSON

☐ AJAX request expects JSON

☐ AJAX request receives HTML

☐ HTML inserted via textContent

☐ HTML inserted via innerText

☐ HTML escaped accidentally

☐ Blade layout rendered twice

☐ Layout missing

☐ CSS not loaded

☐ JS crashes before rendering

☐ Duplicate event listeners

☐ Multiple sidebar initializations

☐ PreventDefault blocks navigation

☐ Response Content-Type incorrect

☐ Browser receives full HTML document

☐ Browser receives partial Blade

☐ Browser receives plain text

---

# Mandatory Debug Workflow

Follow these steps exactly.

## Step 1

Locate sidebar component.

Determine how menu items are generated.

---

## Step 2

Locate click handler.

Determine whether navigation is:

- Browser navigation
- AJAX
- SPA routing
- Livewire
- Inertia

---

## Step 3

Trace request.

Record:

Method

URL

Headers

Payload

---

## Step 4

Trace Laravel.

Route

↓

Middleware

↓

Controller

↓

View

↓

Response

---

## Step 5

Inspect browser response.

Determine:

Expected response

Actual response

---

## Step 6

Identify the first divergence.

Example:

Click

↓

AJAX

↓

Controller returns Blade

↓

JavaScript inserts Blade as text

↓

Raw HTML displayed

Root cause found.

---

## Step 7

Only now implement the fix.

No guessing.

No rewrites.

---

# Deliverables

Produce a complete report.

---

# 1. Root Cause

Explain precisely:

- Why HTML is displayed.
- Which component caused it.
- Why it failed.

---

# 2. Evidence

For every issue provide:

File

Line number

Function

Reason

Example:

resources/js/sidebar.js:82

Uses textContent instead of innerHTML.

---

# 3. Execution Trace

Document:

Click

↓

Event

↓

Request

↓

Route

↓

Controller

↓

Blade

↓

Browser

↓

Failure

---

# 4. Fix Strategy

Explain:

Why the fix works.

Why it is the safest option.

Why alternatives were rejected.

---

# 5. Code Changes

Show:

Before

↓

After

Only minimal changes.

Avoid unnecessary refactoring.

---

# 6. Regression Analysis

Check whether the fix affects:

- Sidebar
- Routing
- AJAX
- Layout
- CSS
- Authentication
- Middleware
- Livewire
- Inertia
- Browser history

---

# 7. Validation

After implementing:

Verify:

✓ Dashboard works

✓ Journaling works

✓ Kas Masuk

✓ Kas Keluar

✓ Bank Masuk

✓ Bank Keluar

✓ Memorial

✓ Memorial Penutup

✓ Browser Back

✓ Browser Refresh

✓ CSS loaded

✓ JavaScript loaded

✓ No Console Errors

✓ No Network Errors

✓ No Raw HTML Rendering

---

# Acceptance Criteria

The task is complete only if:

- No sidebar menu displays raw HTML.
- All pages render correctly.
- Blade layouts render normally.
- CSS and JavaScript load successfully.
- Navigation works using the intended architecture.
- No console errors.
- No network errors.
- Root cause is documented.
- Minimal code changes are made.
- All affected routes are regression tested.

---

# Engineering Principles

- Evidence over assumptions.
- Root cause over symptoms.
- Minimal changes over rewrites.
- Maintainability over quick hacks.
- Performance over unnecessary abstractions.
- Preserve existing architecture unless it is fundamentally incorrect.

---

# Success Criteria

The final solution should:

- Eliminate raw HTML rendering.
- Preserve existing functionality.
- Avoid introducing regressions.
- Clearly document the debugging process.
- Provide confidence in the diagnosis and fix.

**Task is not complete until the exact root cause has been identified, verified, fixed, and regression tested.**
