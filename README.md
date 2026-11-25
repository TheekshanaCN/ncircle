<p align="center">
  <a href="https://ncircle.me"><img src="public/assets/img/logo.svg" alt="Ncircle logo" width="340"></a>
</p>

<h3 align="center">Study smarter, together.</h3>

<p align="center">The study community for NSBM students: share notes, ask questions,<br>form study groups and find the right people to work with.</p>

<p align="center">
  <img alt="PHP 8.1+" src="https://img.shields.io/badge/PHP%208.1+-777BB4?style=for-the-badge&logo=php&logoColor=white">
  <img alt="MySQL" src="https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white">
  <img alt="Bootstrap 5.3" src="https://img.shields.io/badge/Bootstrap%205.3-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white">
  <img alt="JavaScript" src="https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black">
  <img alt="HTML5" src="https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white">
  <br>
  <img alt="CSS" src="https://img.shields.io/badge/CSS-1572B6?style=for-the-badge&logo=css&logoColor=white">
  <img alt="Resend" src="https://img.shields.io/badge/Resend-000000?style=for-the-badge&logo=resend&logoColor=white">
  <img alt="Figma" src="https://img.shields.io/badge/Figma-F24E1E?style=for-the-badge&logo=figma&logoColor=white">
  <img alt="Notion" src="https://img.shields.io/badge/Notion-191919?style=for-the-badge&logo=notion&logoColor=white">
</p>

<p align="center">
  <a href="https://ncircle.me"><b>🌐 Live website</b></a> &nbsp;•&nbsp;
  <a href="https://www.figma.com/design/jgbT62qa7rqlf6dg9fErZ8/Ncircle?node-id=62-5"><b>🎨 Figma design</b></a> &nbsp;•&nbsp;
  <a href="docs/DATABASE.md"><b>🗄️ Database design</b></a> &nbsp;•&nbsp;
  <a href="#-getting-started"><b>🚀 Run it locally</b></a>
</p>

<p align="center">
  <img src="docs/screenshots/mock-two.webp" alt="Ncircle landing page in the dark theme" width="100%">
</p>

## 📖 Contents

- [About](#-about)
- [Features](#-features)
- [Screenshots](#-screenshots)
- [Tech stack](#-tech-stack)
- [Architecture](#-architecture)
- [Database](#-database)
- [Security](#-security)
- [Getting started](#-getting-started)
- [Testing](#-testing)
- [Deployment](#-deployment)
- [Team](#-team)

## 💡 About

Study material at university is usually scattered across chat groups and personal drives. **Ncircle** brings it into one place, where admins review every note and only people with an NSBM student email can join.

It is our **Web Development module group project (Group 3)** at NSBM Green University. The backend is written in plain PHP with **no framework**: we built the router, MVC core, authentication and validation ourselves.

## ✨ Features

| | Feature | What students can do |
|:-:|---|---|
| 📚 | **Notes vault** | Share PDF, DOCX and PPTX notes. Filter, rate, bookmark, comment and download |
| 💬 | **Discussion forum** | Ask, answer, upvote and accept answers, with images, files, emoji and GIFs |
| 👥 | **Study groups** | Create and join groups by subject, and post on a private group wall |
| 📅 | **Meetings** | Schedule online, physical or hybrid sessions with limited seats |
| 🔎 | **Find Members** | Post help requests and find peers by skill, across all faculties |
| 📈 | **Dashboard and study space** | Set a weekly goal, log study time, and follow live charts |
| 🔔 | **Notifications** | Hear about answers, approvals, meetings and responses |
| 🛡️ | **Admin panel** | Approve notes, moderate reports, manage users and subjects, export CSV |
| 🌗 | **Light and dark themes** | Responsive pages that also respect reduced-motion settings |

## 📸 Screenshots

### 🖥️ Desktop

<table align="center">
  <tr>
    <td align="center" width="50%"><img src="docs/screenshots/dashboard.webp" alt="Dashboard" width="420"><br><b>Dashboard</b></td>
    <td align="center" width="50%"><img src="docs/screenshots/notes.webp" alt="Browse notes" width="420"><br><b>Browse notes</b></td>
  </tr>
  <tr>
    <td align="center" width="50%"><img src="docs/screenshots/forum.webp" alt="Discussion forum" width="420"><br><b>Discussion forum</b></td>
    <td align="center" width="50%"><img src="docs/screenshots/thread.webp" alt="Forum thread" width="420"><br><b>Forum thread</b></td>
  </tr>
  <tr>
    <td align="center" width="50%"><img src="docs/screenshots/groups.webp" alt="Study groups" width="420"><br><b>Study groups</b></td>
    <td align="center" width="50%"><img src="docs/screenshots/meetings.webp" alt="Meetings" width="420"><br><b>Meetings</b></td>
  </tr>
  <tr>
    <td align="center" width="50%"><img src="docs/screenshots/members.webp" alt="Find Members" width="420"><br><b>Find Members</b></td>
    <td align="center" width="50%"><img src="docs/screenshots/study-space.webp" alt="Personal study space" width="420"><br><b>Personal study space</b></td>
  </tr>
  <tr>
    <td align="center" width="50%"><img src="docs/screenshots/login.webp" alt="Sign in" width="420"><br><b>Sign in</b></td>
    <td align="center" width="50%"><img src="docs/screenshots/admin.webp" alt="Admin overview" width="420"><br><b>Admin overview</b></td>
  </tr>
</table>

### 📱 Mobile

<table align="center">
  <tr>
    <td align="center" width="33%"><img src="docs/screenshots/mobile-dashboard.webp" alt="Dashboard" width="250"><br><b>Dashboard</b></td>
    <td align="center" width="33%"><img src="docs/screenshots/mobile-notes.webp" alt="Browse notes" width="250"><br><b>Browse notes</b></td>
    <td align="center" width="33%"><img src="docs/screenshots/mobile-forum.webp" alt="Discussion forum" width="250"><br><b>Discussion forum</b></td>
  </tr>
</table>

<p align="center"><sub>Screenshots use made-up demo data. A light theme is available too.</sub></p>

## 🧰 Tech stack

| Layer | Technology |
|---|---|
| **Frontend** | HTML5, CSS with custom properties, vanilla JavaScript, Bootstrap 5.3.3, Bootstrap Icons 1.13.2 |
| **Backend** | PHP 8.1+, custom MVC, no framework and no Composer packages |
| **Database** | MySQL or MariaDB through PDO |
| **Email** | [Resend](https://resend.com) API |
| **GIFs** | [GIPHY](https://developers.giphy.com) API, called from the server |
| **Design** | Figma, with self-hosted DM Sans, Inter and Carattere fonts |
| **Hosting** | Ubuntu server with Nginx |
| **Planning** | Notion |
| **Version control** | Git and GitHub, with feature branches and pull requests |

## 🏗 Architecture

Every request enters through one front controller and follows the same path:

```mermaid
flowchart LR
    B([Browser]) --> I["public/index.php<br>front controller"]
    I --> R["Router"]
    R --> M["Middleware<br>CSRF, auth, guest, admin"]
    M --> C["Controller"]
    C --> S["Services<br>mail, uploads, stats"]
    C --> MO["Models"]
    S --> MO
    MO --> D[("MySQL")]
    S --> X["Resend and GIPHY"]
    S --> F[("storage/<br>outside web root")]
    C --> V["View, layout, components"]
    V --> B
```

**How a student account is verified**

```mermaid
sequenceDiagram
    actor Student
    participant App as Ncircle
    participant DB as MySQL
    participant Mail as Resend
    Student->>App: Register with an NSBM email
    App->>DB: Save account (unverified) and hashed token
    App->>Mail: Send verification email
    Mail-->>Student: Email with a 24-hour link
    Student->>App: Open the link
    App->>DB: Check token, mark verified, delete token
    App-->>Student: Ready to sign in
```

<details>
<summary><b>📁 Project structure</b></summary>

```text
studycircle/
├── app/
│   ├── Controllers/     Request handlers, with Admin/ for the admin panel
│   ├── Core/            Router, Database, Auth, Session, CSRF, Validator
│   ├── Helpers/         Global helper functions
│   ├── Middleware/      Auth, guest, admin and CSRF checks
│   ├── Models/          All SQL queries
│   └── Services/        Mail, uploads, file checks, notifications, stats
├── config/              App, database and upload settings
├── database/            Schema, seed data and migrations
├── docs/                Database design, demo accounts, screenshots
├── public/              Web root: index.php and assets only
├── routes/web.php       Every URL, and who can open it
├── storage/             Uploads, logs and cache, not public
├── tests/smoke.php      End-to-end tests
└── views/               Pages, layouts, partials, components, emails
```

Shared components are shown at `/dev/styleguide` when `APP_DEBUG=true`. Colours, fonts and radii live in `public/assets/css/theme.css`.

</details>

## 🗄 Database

**25 tables** in five modules. This diagram shows the core tables and their main links. **[See the full database design](docs/DATABASE.md)** for every table, column and key.

```mermaid
erDiagram
    users ||--o{ resources : shares
    users ||--o{ questions : asks
    users ||--o{ study_groups : owns
    users ||--o{ help_requests : posts
    categories ||--o{ resources : groups
    subjects ||--o{ resources : covers
    subjects ||--o{ questions : covers
    subjects ||--o{ study_groups : covers
    resources ||--o{ comments : receives
    resources ||--o{ ratings : receives
    questions ||--o{ answers : receives
    study_groups ||--o{ group_members : has
    study_groups |o--o{ study_sessions : schedules
    help_requests ||--o{ help_request_responses : receives
    users {
        int id PK
        varchar email UK
        varchar student_id UK
        enum role
    }
    subjects {
        int id PK
        varchar code UK
        varchar faculty
    }
    resources {
        int id PK
        int user_id FK
        int subject_id FK
        int category_id FK
        enum status
    }
    questions {
        int id PK
        int user_id FK
        int subject_id FK
        datetime solved_at
    }
    study_groups {
        int id PK
        int owner_id FK
        int subject_id FK
        smallint max_members
    }
    help_requests {
        int id PK
        int user_id FK
        enum type
        enum status
    }

```

| Module | Tables |
|---|---|
| Accounts | `users` `user_tokens` `login_attempts` `notifications` `study_logs` |
| Notes | `subjects` `categories` `resources` `ratings` `downloads` `comments` `bookmarks` |
| Forum | `questions` `answers` `answer_votes` `attachments` |
| Groups and meetings | `study_groups` `group_members` `group_posts` `study_sessions` `session_attendees` |
| Find Members and moderation | `help_requests` `help_request_faculties` `help_request_responses` `reports` |

## 🔐 Security

| Risk | Protection |
|---|---|
| SQL injection | PDO prepared statements with emulation turned off |
| XSS | Escaped output and a nonce-based Content Security Policy |
| CSRF | Token checked automatically on every POST route |
| Stolen passwords | `password_hash()`, with sign-in limited to 5 failed tries per 15 minutes |
| Session attacks | HttpOnly and SameSite cookies, with the session ID renewed at sign-in |
| Fake accounts | NSBM student emails only, confirmed by a single-use link |
| Dangerous uploads | Checked by content, given random names and stored outside the web root |
| Clickjacking and sniffing | `X-Frame-Options`, `X-Content-Type-Options` and referrer headers |
| Wrong access | Role, ownership and faculty checks on the server |

## 🚀 Getting started

**You need:** PHP 8.1+ with `pdo_mysql`, `fileinfo`, `mbstring`, `zip` and `curl`, plus MySQL or MariaDB.

```bash
# 1. Clone
git clone https://github.com/TheekshanaCN/studycircle.git
cd studycircle

# 2. Configure: fill in your database details
cp .env.example .env

# 3. Create the database and import it (schema.sql wipes existing tables)
mysql -u <user> -p -e "CREATE DATABASE ncircle CHARACTER SET utf8mb4"
mysql -u <user> -p ncircle < database/schema.sql
mysql -u <user> -p ncircle < database/seed.sql

# 4. Start
php -d upload_max_filesize=10M -d post_max_size=22M -S localhost:8000 -t public public/index.php
```

Set `DB_NAME=ncircle` in `.env`, open <http://localhost:8000> and sign in with an account from [docs/DEMO_ACCOUNTS.md](docs/DEMO_ACCOUNTS.md).

<details>
<summary><b>⚙️ Environment variables</b></summary>

| Variable | Purpose |
|---|---|
| `APP_NAME`, `APP_ENV` | Site name and environment |
| `APP_DEBUG` | Shows error details and dev pages. **Must be `false` when live** |
| `APP_URL` | Base address for links in emails when debug is off |
| `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS` | Database connection |
| `MAIL_DRIVER` | `log` writes to `storage/logs/mail.log`, `resend` sends real email, `mail` uses PHP `mail()` |
| `RESEND_API_KEY` | Needed when `MAIL_DRIVER=resend` |
| `MAIL_FROM_ADDRESS`, `MAIL_FROM_NAME` | Sender. The domain must be verified at Resend |
| `GIPHY_API_KEY` | Optional. Without it, the GIF button is hidden |

</details>

<details>
<summary><b>🔄 Updating an existing database</b></summary>

Back up first, then run each new file in `database/migrations/` once, in date order:

```bash
mysqldump -u <user> -p <database> > backup.sql
mysql -u <user> -p <database> < database/migrations/2026-11-25-profile-photos.sql
```

</details>

## 🧪 Testing

The end-to-end suite makes **483 checks** over real HTTP, covering access control, uploads, XSS, CSRF, moderation and more.

Always use a separate test database with freshly imported `schema.sql` and `seed.sql`, because the tests create accounts and content.

```bash
# Terminal 1
MAIL_DRIVER=log DB_NAME=ncircle_test php -d upload_max_filesize=10M -d post_max_size=22M -S 127.0.0.1:8770 -t public public/index.php

# Terminal 2
php tests/smoke.php http://127.0.0.1:8770
```

Values set on the command line override `.env`, and `APP_DEBUG` must be `true`. To include the GIF checks, also run `php -S 127.0.0.1:8799 tests/giphy-mock.php` and start the server with `GIPHY_API_KEY=test-secret-key GIPHY_API_BASE=http://127.0.0.1:8799/v1`.

## 👥 Team

**Group 3, NSBM Green University**

| | Member | GitHub | Contribution |
|:-:|---|---|---|
| <img src="https://avatars.githubusercontent.com/TheekshanaCN?s=120" alt="TheekshanaCN" width="60" height="60"> | **TheekshanaCN** | [@TheekshanaCN](https://github.com/TheekshanaCN) | MVC core, database, admin panel, email, tests |
| <img src="https://avatars.githubusercontent.com/wkravindulakshan?s=120" alt="wkravindulakshan" width="60" height="60"> | **wkravindulakshan** | [@wkravindulakshan](https://github.com/wkravindulakshan) | Design system, layouts, shared components |
| <img src="https://avatars.githubusercontent.com/Terashasith?s=120" alt="Terasha Sithmi" width="60" height="60"> | **Terasha Sithmi** | [@Terashasith](https://github.com/Terashasith) | Notes vault, ratings, landing metrics |
| <img src="https://avatars.githubusercontent.com/Kaleimagan-s?s=120" alt="Kaleimagan-s" width="60" height="60"> | **Kaleimagan-s** | [@Kaleimagan-s](https://github.com/Kaleimagan-s) | Sign in, register, verification, reset |
| <img src="https://avatars.githubusercontent.com/Thashu25?s=120" alt="Thashu25" width="60" height="60"> | **Thashu25** | [@Thashu25](https://github.com/Thashu25) | Study groups, meetings, landing structure |
| <img src="https://avatars.githubusercontent.com/amaJayasekara?s=120" alt="amaJayasekara" width="60" height="60"> | **amaJayasekara** | [@amaJayasekara](https://github.com/amaJayasekara) | Forum, composer with files and GIFs |
| <img src="https://avatars.githubusercontent.com/SanduniCW?s=120" alt="SanduniCW" width="60" height="60"> | **SanduniCW** | [@SanduniCW](https://github.com/SanduniCW) | Find Members, profiles, study space |
| <img src="https://avatars.githubusercontent.com/nimthara-TN?s=120" alt="nimthara-TN" width="60" height="60"> | **nimthara-TN** | [@nimthara-TN](https://github.com/nimthara-TN) | Dashboard, notifications, search, info pages |


<div align="center">
  <br>
  <b>Made by Group 3 at NSBM Green University</b><br>
  <a href="https://ncircle.me">ncircle.me</a>
</div>
