# ERD - Social Network of Recipes

```mermaid
erDiagram
    USERS {
        INT id PK
        VARCHAR username UK
        VARCHAR email UK
        VARCHAR password
        TIMESTAMP created_at
    }

    RECIPES {
        INT id PK
        INT user_id FK
        VARCHAR title
        TEXT description
        TEXT ingredients
        TEXT instructions
        VARCHAR image_path
        TIMESTAMP created_at
    }

    LIKES {
        INT id PK
        INT user_id FK
        INT recipe_id FK
        TIMESTAMP created_at
    }

    COMMENTS {
        INT id PK
        INT user_id FK
        INT recipe_id FK
        TEXT comment_text
        TIMESTAMP created_at
    }

    USERS ||--o{ RECIPES : "uploads"
    USERS ||--o{ LIKES : "gives"
    USERS ||--o{ COMMENTS : "writes"
    RECIPES ||--o{ LIKES : "receives"
    RECIPES ||--o{ COMMENTS : "has"
```

## Relationships

| Relationship | Type | Description |
|---|---|---|
| USERS → RECIPES | 1:N | Ένας χρήστης ανεβάζει πολλές συνταγές |
| USERS → LIKES | 1:N | Ένας χρήστης κάνει πολλά likes |
| USERS → COMMENTS | 1:N | Ένας χρήστης γράφει πολλά σχόλια |
| RECIPES → LIKES | 1:N | Μία συνταγή λαμβάνει πολλά likes |
| RECIPES → COMMENTS | 1:N | Μία συνταγή έχει πολλά σχόλια |
| USERS ↔ RECIPES (via LIKES) | M:N | Πολλοί χρήστες κάνουν like σε πολλές συνταγές |
| USERS ↔ RECIPES (via COMMENTS) | M:N | Πολλοί χρήστες σχολιάζουν πολλές συνταγές |

## Constraints

- `likes.UNIQUE(user_id, recipe_id)` — Αποτρέπει διπλό like
- `ON DELETE CASCADE` — Διαγραφή χρήστη → διαγράφονται recipes, likes, comments
- `ON DELETE CASCADE` — Διαγραφή recipe → διαγράφονται likes, comments
