# Demo accounts

Created by `database/seed.sql`. For local demos only: change or remove them before going live.

| Role | Email | Student ID | Password |
|---|---|---|---|
| Admin | admin@students.nsbm.ac.lk | A0001 | `Admin@123` |
| Student (main demo) | nimesh@students.nsbm.ac.lk | 6942 | `Student@123` |
| Student | tharushi@students.nsbm.ac.lk | 20481 | `Student@123` |
| Student | kavindu@students.nsbm.ac.lk | 20517 | `Student@123` |
| Student | ravindu@students.nsbm.ac.lk | 20633 | `Student@123` |
| Student (Business) | sachini@students.nsbm.ac.lk | 20744 | `Student@123` |
| Student (Engineering) | lasith@students.nsbm.ac.lk | 21022 | `Student@123` |
| Student (Science) | hiruni@students.nsbm.ac.lk | 21402 | `Student@123` |

Every other seeded student uses `Student@123` too. All names and notes are made up.

Seeded notes all download one of three small files from `database/seed-files/`, so the file size shown on a demo note is not the real size of that file.

Dates in the seed are relative to when you import it. Re-import `schema.sql` and `seed.sql` to refresh the charts and upcoming pods. This wipes all data.
