# Database design

Ncircle uses **25 InnoDB tables** in `utf8mb4`, defined in [`database/schema.sql`](../database/schema.sql).

- **PK** primary key, **FK** foreign key, **UK** unique.
- Each table shows every column once, in its own section. A table from another section appears as a name only.
- `attachments` and `reports` point at several kinds of content (`owner_type` + `owner_id`, `target_type` + `target_id`), so that link is checked in PHP and has no foreign key.

## Accounts

```mermaid
erDiagram
    users ||--o{ user_tokens : user_id
    users ||--o{ notifications : user_id
    users ||--o{ study_logs : user_id
    users {
        int id PK
        varchar name
        varchar username UK
        varchar student_id UK
        varchar email UK
        varchar faculty
        tinyint academic_year
        varchar bio
        varchar avatar
        varchar skills
        tinyint open_to_help
        tinyint listed_in_directory
        varchar password_hash
        enum role
        enum status
        smallint weekly_goal_minutes
        datetime email_verified_at
        datetime last_seen_at
        datetime created_at
    }
    user_tokens {
        int id PK
        int user_id FK
        enum type
        char token_hash UK
        datetime expires_at
        datetime created_at
    }
    login_attempts {
        int id PK
        varchar identifier
        varchar ip
        datetime attempted_at
    }
    notifications {
        int id PK
        int user_id FK
        varchar icon
        varchar title
        varchar body
        varchar link
        datetime read_at
        datetime created_at
    }
    study_logs {
        int id PK
        int user_id FK
        smallint minutes
        varchar note
        date logged_on
        datetime created_at
    }
```

## Notes

```mermaid
erDiagram
    users ||--o{ resources : user_id
    subjects ||--o{ resources : subject_id
    categories ||--o{ resources : category_id
    users |o--o{ resources : reviewed_by
    resources ||--o{ ratings : resource_id
    users ||--o{ ratings : user_id
    resources ||--o{ downloads : resource_id
    users ||--o{ downloads : user_id
    resources ||--o{ comments : resource_id
    users ||--o{ comments : user_id
    users ||--o{ bookmarks : user_id
    resources ||--o{ bookmarks : resource_id
    subjects {
        int id PK
        varchar code UK
        varchar name
        varchar faculty
        tinyint year
        tinyint is_active
    }
    categories {
        int id PK
        varchar name
        varchar plural
        varchar slug UK
    }
    resources {
        int id PK
        int user_id FK
        int subject_id FK
        int category_id FK
        varchar title
        text description
        varchar file_path
        varchar original_name
        varchar file_type
        int file_size
        smallint pages
        enum status
        varchar reject_reason
        int downloads_count
        int reviewed_by FK
        datetime reviewed_at
        datetime created_at
    }
    ratings {
        int resource_id PK, FK
        int user_id PK, FK
        tinyint value
        datetime created_at
    }
    downloads {
        int id PK
        int resource_id FK
        int user_id FK
        datetime created_at
    }
    comments {
        int id PK
        int resource_id FK
        int user_id FK
        text body
        tinyint is_hidden
        datetime created_at
    }
    bookmarks {
        int user_id PK, FK
        int resource_id PK, FK
        datetime created_at
    }
```

## Forum and attachments

```mermaid
erDiagram
    users ||--o{ questions : user_id
    subjects ||--o{ questions : subject_id
    questions ||--o{ answers : question_id
    users ||--o{ answers : user_id
    answers ||--o{ answer_votes : answer_id
    users ||--o{ answer_votes : user_id
    users ||--o{ attachments : user_id
    questions {
        int id PK
        int user_id FK
        int subject_id FK
        varchar title
        text body
        int views
        datetime solved_at
        tinyint is_hidden
        datetime created_at
    }
    answers {
        int id PK
        int question_id FK
        int user_id FK
        text body
        tinyint is_accepted
        tinyint is_hidden
        datetime created_at
    }
    answer_votes {
        int answer_id PK, FK
        int user_id PK, FK
    }
    attachments {
        int id PK
        enum owner_type
        int owner_id
        int user_id FK
        enum kind
        varchar original_name
        varchar stored_name
        varchar mime
        int size
        smallint width
        smallint height
        varchar giphy_id
        datetime created_at
    }
```

## Study groups and meetings

```mermaid
erDiagram
    users ||--o{ study_groups : owner_id
    subjects ||--o{ study_groups : subject_id
    study_groups ||--o{ group_members : group_id
    users ||--o{ group_members : user_id
    study_groups ||--o{ group_posts : group_id
    users ||--o{ group_posts : user_id
    users ||--o{ study_sessions : host_id
    subjects ||--o{ study_sessions : subject_id
    study_groups |o--o{ study_sessions : group_id
    study_sessions ||--o{ session_attendees : session_id
    users ||--o{ session_attendees : user_id
    study_groups {
        int id PK
        int owner_id FK
        int subject_id FK
        varchar name
        varchar description
        smallint max_members
        datetime created_at
    }
    group_members {
        int group_id PK, FK
        int user_id PK, FK
        datetime joined_at
    }
    group_posts {
        int id PK
        int group_id FK
        int user_id FK
        text body
        tinyint is_hidden
        datetime created_at
    }
    study_sessions {
        int id PK
        int host_id FK
        int subject_id FK
        int group_id FK
        varchar title
        varchar description
        enum mode
        datetime starts_at
        datetime ends_at
        varchar location
        varchar meeting_url
        smallint capacity
        datetime created_at
    }
    session_attendees {
        int session_id PK, FK
        int user_id PK, FK
        enum status
        datetime joined_at
    }
```

## Find Members and moderation

```mermaid
erDiagram
    users ||--o{ help_requests : user_id
    help_requests ||--o{ help_request_faculties : request_id
    help_requests ||--o{ help_request_responses : request_id
    users ||--o{ help_request_responses : user_id
    users ||--o{ reports : reporter_id
    users |o--o{ reports : resolved_by
    help_requests {
        int id PK
        int user_id FK
        enum type
        varchar title
        text description
        varchar skills
        enum status
        tinyint is_hidden
        datetime created_at
    }
    help_request_faculties {
        int request_id PK, FK
        varchar faculty PK
    }
    help_request_responses {
        int id PK
        int request_id FK
        int user_id FK
        varchar message
        enum status
        datetime created_at
    }
    reports {
        int id PK
        int reporter_id FK
        enum target_type
        int target_id
        varchar reason
        enum status
        int resolved_by FK
        datetime resolved_at
        datetime created_at
    }
```

## Design rules

| Rule | How it is enforced |
|---|---|
| One rating, bookmark, vote or membership per person | Composite primary keys |
| One report per person per item, one response per request | Unique keys |
| Deleting an account removes that person's own content | `ON DELETE CASCADE` |
| Moderation history survives a removed admin | `ON DELETE SET NULL` on `reviewed_by` and `resolved_by` |
| A meeting survives its group being deleted | `ON DELETE SET NULL` on `group_id` |
| Seats and member limits cannot be oversold | Row locks (`SELECT ... FOR UPDATE`) inside transactions |
| Tokens are useless if the database leaks | Only a SHA-256 hash is stored, with an expiry |

## Migrations

Run new files in [`database/migrations/`](../database/migrations) once, in date order. They keep existing data. Importing `schema.sql` **wipes** it.
