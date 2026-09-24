# ICS 2102 — Web Development Semester Mini Project
## Student Club Website: Innovate Club
**Course:** ICS 2102 — Web Development  
**Semester Mini Project:** 4-Week Progressive Build Plan  
**Theme:** Student Tech & Innovation Society Website  

---

## 📌 Project Overview
**Innovate Club** is a complete, multi-page, responsive website built according to the ICS 2102 curriculum blueprint and design mockup specifications. It follows a green and off-white aesthetic (`#15803D` / `#2E7D32` palette), card-based layout, modern typography, and robust JavaScript interactivity, coupled with server-side PHP processing and MySQL database persistence.

---

## 🗂️ Project File Structure
```
panaxxc/ (student-club/)
│
├── index.html           # Week 1: Home Page (Hero, tagline, pillars, countdown banner)
├── about.html           # Week 2: About Us (Mission, vision, committee cards, HTML table, FAQ accordion)
├── activities.html      # Week 2: Activities & Events (Rendered dynamically via JS arrays & objects)
├── gallery.html         # Week 3: Photo Gallery (2x4 grid, category filtering, interactive lightbox)
├── join.html            # Week 4: Join / Contact (Client validation, PHP endpoint, sidebar info)
├── contact.html         # Direct alias / redirect to join.html
│
├── css/
│   └── style.css        # External stylesheet with custom design tokens, responsive breakpoints
│
├── js/
│   └── script.js        # Core JavaScript: navigation, events engine, lightbox, accordion, validation
│
├── php/
│   ├── join.php         # Handles POST submissions, input sanitization, and database insert
│   ├── process.php      # Alias wrapper for form processing
│   ├── db.php           # PDO database connection with MySQL & automatic SQLite fallback
│   └── database.sql     # Complete MySQL schema and seed data
│
└── images/
    ├── hero-collaboration.jpg  # Hero banner graphic of students collaborating
    ├── president.jpg           # Committee member photo (President)
    ├── vice-president.jpg      # Committee member photo (Vice President)
    ├── secretary.jpg           # Committee member photo (Secretary)
    ├── treasurer.jpg           # Committee member photo (Treasurer)
    ├── logo/
    │   ├── logo.svg            # Official vector club brand logo
    │   └── favicon.svg         # Browser favicon
    └── gallery/
        ├── hackathon-2025.jpg      # Hackathon stage demo
        ├── web-workshop.jpg        # Coding bootcamp lab
        ├── campus-networking.jpg   # Campus lawn meet & greet
        ├── project-showcase.jpg    # Innovation expo booth
        ├── guest-keynote.jpg       # Industry tech trends keynote
        ├── code-sprint.jpg         # Evening pair programming sprint
        ├── robotics-lab.jpg        # Smart hardware & IoT lab
        └── annual-gala.jpg         # Annual celebration & awards
```

---

## 🗓️ 4-Week Milestone Coverage

### Week 1 — Foundation & Home Page (`index.html`, `css/style.css`)
- **Shared Navigation & Header**: Brand logo, horizontal links, green active nav link with underline accent, mobile hamburger drawer.
- **Hero Section**: Large bold headline, one word per line, last word highlighted in green (`Innovate. / Connect. / Inspire.`), collaborative graphic, and CTA buttons.
- **Three Core Pillar Cards**: Community, Growth, and Impact with custom icons, soft grey-green card background (`#F4F7F5`), and hover transitions.
- **Universal Footer**: Solid green bar (`#14532D`), centered copyright, meeting schedule, and social icons.

### Week 2 — About Us & Activities (`about.html`, `activities.html`)
- **About Us**: Club history, mission & vision callout cards, core values grid.
- **Committee Section**: Executive member cards (President, VP, Secretary, Treasurer) and a full **HTML table of committee members** (name, role, course, year, email, office hours) satisfying syllabus grading criteria.
- **Dynamic Activities & Events**:
  - Events data stored in a **JavaScript array of objects** (`clubEventsData`).
  - Rendered dynamically into the DOM using array methods (`filter`), loops (`forEach`), and DOM creation.
  - Tab controls for **Upcoming Events** and **Past Activities**.
  - Category filtering and real-time live search input.
  - Interactive RSVP/Details modal showing event descriptions and remaining seats.

### Week 3 — Gallery & Interactivity (`gallery.html`, `js/script.js`)
- **Gallery Grid**: 8 high-resolution club photos (2 rows x 4 columns) with captions and zoom hover states.
- **Dynamic Category Filter**: Instant switching between All Photos, Workshops, Hackathons, Campus Life, and Keynotes.
- **Interactive Lightbox Modal**:
  - Opens enlarged image with caption on click (`onclick` DOM event).
  - Next and previous image controls.
  - Full keyboard accessibility (`Escape`, `ArrowLeft`, `ArrowRight`).
- **Second Interactive Touch**:
  - Interactive **FAQ Accordion** on `about.html` expanding/collapsing answers on click.
  - Live **Countdown Timer** on `index.html` calculating days, hours, minutes, and seconds.

### Week 4 — Join / Contact, Validation & PHP (`join.html`, `php/`)
- **Join/Contact Form**: Full Name, Email, Phone, Course, Year of Study, Interest Track, Message, and Code of Conduct consent.
- **Sidebar Info**: Official email, phone hotline, campus lab address, meeting hours, and CDN social icons (Facebook, Instagram, Twitter/X, LinkedIn, GitHub).
- **Client-Side Validation (JavaScript)**:
  - Required field checking.
  - Email format validation using regular expressions (`regex`).
  - Phone number digit checks.
  - Minimum message length validation.
  - Inline error feedback with smooth shake animations.
  - Blocks submission until all fields pass validation.
- **PHP Form Processing (`php/join.php`)**:
  - Sanitizes all inputs with `htmlspecialchars`, `trim`, and `filter_var`.
  - Server-side validation safeguarding against tampering.
  - Prepared PDO statements preventing SQL injection.
  - Dual response support: JSON response for asynchronous submission, and a standalone confirmation page for traditional form posts.
- **Database Storage (`php/db.php` & `php/database.sql`)**:
  - MySQL database schema for storing membership applications.
  - Automatic SQLite fallback so the application works seamlessly even if MySQL is not running during local evaluation.

---

## 🚀 How to Run and Test Locally

### Option A: Using Built-in Python Server (Fastest for UI & JS Testing)
Open your terminal in the project directory and run:
```bash
python -m http.server 8000
```
Then navigate to: `http://localhost:8000` in any web browser.

### Option B: Using Node.js HTTP Server
```bash
npx http-server -p 8000
```

### Option C: Using XAMPP / WAMP (Full PHP & MySQL Support)
1. Copy the project folder into your web server's root directory (e.g. `C:\xampp\htdocs\student-club`).
2. Start Apache and MySQL from the XAMPP Control Panel.
3. Open `http://localhost/phpmyadmin` and import `php/database.sql`.
4. Check credentials in `php/db.php` (default `root` with empty password).
5. Visit `http://localhost/student-club` in your browser.

---

## ✅ Submission Checklist Verification
- [x] All 5 pages linked and working (`index.html`, `about.html`, `activities.html`, `gallery.html`, `join.html`)
- [x] Consistent external stylesheet (`css/style.css`) with design tokens
- [x] JavaScript variables, conditionals, loops, functions, arrays of objects, and DOM events
- [x] Form validated client-side with inline error messages before submission
- [x] PHP sanitizes and processes form submissions with DB storage
- [x] Database schema provided (`php/database.sql`) with sample data
- [x] 100% responsive on mobile, tablet, and desktop screens
