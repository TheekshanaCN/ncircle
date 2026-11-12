-- Migration: faculty separation, Find Members and meetings. Keeps all existing accounts and notes.
-- Run once on an existing database. Fresh installs use schema.sql and seed.sql instead.
SET NAMES utf8mb4;

ALTER TABLE users
    ADD COLUMN skills VARCHAR(255) NULL AFTER bio,
    ADD COLUMN open_to_help TINYINT(1) NOT NULL DEFAULT 1 AFTER skills,
    ADD COLUMN listed_in_directory TINYINT(1) NOT NULL DEFAULT 1 AFTER open_to_help;

ALTER TABLE reports MODIFY target_type ENUM('resource','question','answer','comment','help_request') NOT NULL;

-- Meetings: audio pods become online, and every meeting gets an end time and a place or link.
ALTER TABLE study_sessions MODIFY mode ENUM('hybrid','online','audio','physical') NOT NULL DEFAULT 'hybrid';
UPDATE study_sessions SET mode = 'online' WHERE mode = 'audio';
ALTER TABLE study_sessions
    MODIFY mode ENUM('online','physical','hybrid') NOT NULL DEFAULT 'online',
    ADD COLUMN ends_at DATETIME NULL AFTER starts_at,
    ADD COLUMN location VARCHAR(160) NULL AFTER ends_at,
    ADD COLUMN meeting_url VARCHAR(500) NULL AFTER location;

ALTER TABLE session_attendees ADD COLUMN status ENUM('going','cant') NOT NULL DEFAULT 'going' AFTER user_id;

CREATE TABLE help_requests (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    type ENUM('research','project','tutoring','other') NOT NULL DEFAULT 'research',
    title VARCHAR(160) NOT NULL,
    description TEXT NOT NULL,
    skills VARCHAR(255) NULL,
    status ENUM('open','closed') NOT NULL DEFAULT 'open',
    is_hidden TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_help_status (status, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE help_request_faculties (
    request_id INT UNSIGNED NOT NULL,
    faculty VARCHAR(30) NOT NULL,
    PRIMARY KEY (request_id, faculty),
    FOREIGN KEY (request_id) REFERENCES help_requests(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE help_request_responses (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    request_id INT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    message VARCHAR(500) NOT NULL,
    status ENUM('pending','accepted','declined') NOT NULL DEFAULT 'pending',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_response (request_id, user_id),
    FOREIGN KEY (request_id) REFERENCES help_requests(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Demo content and tidy-up for faculty separation, Find Members and meetings.

-- People in another faculty's group or meeting can no longer open it, so remove them. Owners and hosts stay.
DELETE m FROM group_members m
  JOIN study_groups g ON g.id = m.group_id
  JOIN subjects s ON s.id = g.subject_id
  JOIN users u ON u.id = m.user_id
 WHERE u.faculty <> s.faculty AND u.role = 'student' AND g.owner_id <> m.user_id;
DELETE a FROM session_attendees a
  JOIN study_sessions ss ON ss.id = a.session_id
  JOIN subjects s ON s.id = ss.subject_id
  JOIN users u ON u.id = a.user_id
 WHERE u.faculty <> s.faculty AND u.role = 'student' AND ss.host_id <> a.user_id;

-- Give the original demo meetings proper details.
UPDATE study_sessions SET ends_at = starts_at + INTERVAL 90 MINUTE WHERE ends_at IS NULL;
UPDATE study_sessions SET location = 'Faculty of Computing, Lab 04', meeting_url = 'https://meet.google.com/ncircle-demo-a' WHERE title = 'Database Normalization Problem Solving Sprint';
UPDATE study_sessions SET meeting_url = 'https://meet.google.com/ncircle-demo-b' WHERE title IN ('Midterm Past Paper Speed Run & Verification', 'SQL Joins Warm-up');
UPDATE study_sessions SET meeting_url = 'https://meet.google.com/ncircle-demo-c' WHERE title = 'OSI Security & Packet Encapsulation Review';
UPDATE study_sessions SET mode = 'physical', location = 'Library, Group Room 2' WHERE title = 'CVP Break-even Drill';
UPDATE study_sessions SET meeting_url = 'https://meet.google.com/ncircle-demo-d' WHERE title = 'Laplace Transform Clinic';

INSERT IGNORE INTO subjects (code,name,faculty,year) VALUES
('EN210','Electrical Circuits','Engineering',2),
('EN305','Thermodynamics','Engineering',3),
('SC120','Organic Chemistry','Science',1),
('SC205','Cell Biology','Science',2),
('BM110','Organizational Behaviour','Business',1);

INSERT IGNORE INTO users (name,username,student_id,email,faculty,academic_year,bio,password_hash,email_verified_at,last_seen_at,created_at)
 SELECT 'Pasindu Herath','pasindu_h','21301','pasindu@students.nsbm.ac.lk','Engineering',2,'Circuits lab and thermodynamics problem sets.',password_hash,NOW() - INTERVAL 20 DAY,NOW() - INTERVAL 28 MINUTE,NOW() - INTERVAL 34 DAY FROM users WHERE email = 'kasuni@students.nsbm.ac.lk';
INSERT IGNORE INTO users (name,username,student_id,email,faculty,academic_year,bio,password_hash,email_verified_at,last_seen_at,created_at)
 SELECT 'Hiruni Jayasekara','hiruni_j','21402','hiruni@students.nsbm.ac.lk','Science',2,'Biology notes and lab report reviews.',password_hash,NOW() - INTERVAL 20 DAY,NOW() - INTERVAL 34 MINUTE,NOW() - INTERVAL 37 DAY FROM users WHERE email = 'kasuni@students.nsbm.ac.lk';
INSERT IGNORE INTO users (name,username,student_id,email,faculty,academic_year,bio,password_hash,email_verified_at,last_seen_at,created_at)
 SELECT 'Sahan Gunawardena','sahan_g','21403','sahan@students.nsbm.ac.lk','Science',1,'Chemistry, one reaction at a time.',password_hash,NOW() - INTERVAL 20 DAY,NOW() - INTERVAL 34 MINUTE,NOW() - INTERVAL 37 DAY FROM users WHERE email = 'kasuni@students.nsbm.ac.lk';
INSERT IGNORE INTO users (name,username,student_id,email,faculty,academic_year,bio,password_hash,email_verified_at,last_seen_at,created_at)
 SELECT 'Ishara Dias','ishara_d','21504','ishara@students.nsbm.ac.lk','Business',1,'Organizational behaviour and survey research.',password_hash,NOW() - INTERVAL 20 DAY,NOW() - INTERVAL 22 MINUTE,NOW() - INTERVAL 31 DAY FROM users WHERE email = 'kasuni@students.nsbm.ac.lk';

INSERT INTO resources (user_id,subject_id,category_id,title,description,file_path,original_name,file_type,file_size,pages,status,downloads_count,reviewed_by,reviewed_at,created_at)
 SELECT u.id,s.id,c.id,'Kirchhoff''s Laws Lecture Notes','Node and mesh analysis with worked circuit examples.','seed/sample-note.pdf','sample-note.pdf','pdf',1468006,18,'approved',212,NULL,NOW() - INTERVAL 3 DAY,NOW() - INTERVAL 3 DAY FROM users u, subjects s, categories c WHERE u.email = 'lasith@students.nsbm.ac.lk' AND s.code = 'EN210' AND c.slug = 'lecture-notes';
INSERT INTO resources (user_id,subject_id,category_id,title,description,file_path,original_name,file_type,file_size,pages,status,downloads_count,reviewed_by,reviewed_at,created_at)
 SELECT u.id,s.id,c.id,'Electrical Circuits Past Paper 2024','Solved paper with phasor and transient questions.','seed/sample-note.pdf','sample-note.pdf','pdf',2306867,12,'approved',178,NULL,NOW() - INTERVAL 12 DAY,NOW() - INTERVAL 12 DAY FROM users u, subjects s, categories c WHERE u.email = 'pasindu@students.nsbm.ac.lk' AND s.code = 'EN210' AND c.slug = 'past-paper';
INSERT INTO resources (user_id,subject_id,category_id,title,description,file_path,original_name,file_type,file_size,pages,status,downloads_count,reviewed_by,reviewed_at,created_at)
 SELECT u.id,s.id,c.id,'Thermodynamics Laws Study Guide','First and second law summaries, with cycle diagrams explained.','seed/sample-note.docx','sample-note.docx','docx',1153433,16,'approved',145,NULL,NOW() - INTERVAL 25 DAY,NOW() - INTERVAL 25 DAY FROM users u, subjects s, categories c WHERE u.email = 'malith@students.nsbm.ac.lk' AND s.code = 'EN305' AND c.slug = 'study-guide';
INSERT INTO resources (user_id,subject_id,category_id,title,description,file_path,original_name,file_type,file_size,pages,status,downloads_count,reviewed_by,reviewed_at,created_at)
 SELECT u.id,s.id,c.id,'Fourier Series Quick Guide','Formulas, symmetry shortcuts and common exam traps.','seed/sample-note.pdf','sample-note.pdf','pdf',838860,6,'approved',96,NULL,NOW() - INTERVAL 70 DAY,NOW() - INTERVAL 70 DAY FROM users u, subjects s, categories c WHERE u.email = 'lasith@students.nsbm.ac.lk' AND s.code = 'EN205' AND c.slug = 'study-guide';
INSERT INTO resources (user_id,subject_id,category_id,title,description,file_path,original_name,file_type,file_size,pages,status,downloads_count,reviewed_by,reviewed_at,created_at)
 SELECT u.id,s.id,c.id,'Cell Membrane Transport Notes','Diffusion, osmosis and active transport from weeks 3 and 4.','seed/sample-note.pdf','sample-note.pdf','pdf',1363148,14,'approved',134,NULL,NOW() - INTERVAL 2 DAY,NOW() - INTERVAL 2 DAY FROM users u, subjects s, categories c WHERE u.email = 'hiruni@students.nsbm.ac.lk' AND s.code = 'SC205' AND c.slug = 'lecture-notes';
INSERT INTO resources (user_id,subject_id,category_id,title,description,file_path,original_name,file_type,file_size,pages,status,downloads_count,reviewed_by,reviewed_at,created_at)
 SELECT u.id,s.id,c.id,'Functional Groups Cheat Sheet','Every group you need to recognise, with naming rules.','seed/sample-note.pdf','sample-note.pdf','pdf',524288,2,'approved',201,NULL,NOW() - INTERVAL 9 DAY,NOW() - INTERVAL 9 DAY FROM users u, subjects s, categories c WHERE u.email = 'sahan@students.nsbm.ac.lk' AND s.code = 'SC120' AND c.slug = 'study-guide';
INSERT INTO resources (user_id,subject_id,category_id,title,description,file_path,original_name,file_type,file_size,pages,status,downloads_count,reviewed_by,reviewed_at,created_at)
 SELECT u.id,s.id,c.id,'Applied Statistics Past Paper 2024','Hypothesis testing and regression, fully worked.','seed/sample-note.pdf','sample-note.pdf','pdf',1048576,10,'approved',167,NULL,NOW() - INTERVAL 33 DAY,NOW() - INTERVAL 33 DAY FROM users u, subjects s, categories c WHERE u.email = 'kasuni@students.nsbm.ac.lk' AND s.code = 'SC110' AND c.slug = 'past-paper';
INSERT INTO resources (user_id,subject_id,category_id,title,description,file_path,original_name,file_type,file_size,pages,status,downloads_count,reviewed_by,reviewed_at,created_at)
 SELECT u.id,s.id,c.id,'Mitosis vs Meiosis Comparison','Stage by stage comparison with short exam answers.','seed/sample-note.pptx','sample-note.pptx','pptx',2202009,9,'approved',88,NULL,NOW() - INTERVAL 110 DAY,NOW() - INTERVAL 110 DAY FROM users u, subjects s, categories c WHERE u.email = 'hiruni@students.nsbm.ac.lk' AND s.code = 'SC205' AND c.slug = 'study-guide';
INSERT INTO resources (user_id,subject_id,category_id,title,description,file_path,original_name,file_type,file_size,pages,status,downloads_count,reviewed_by,reviewed_at,created_at)
 SELECT u.id,s.id,c.id,'Motivation Theories Lecture Notes','Maslow, Herzberg and expectancy theory compared.','seed/sample-note.docx','sample-note.docx','docx',943718,11,'approved',142,NULL,NOW() - INTERVAL 5 DAY,NOW() - INTERVAL 5 DAY FROM users u, subjects s, categories c WHERE u.email = 'ishara@students.nsbm.ac.lk' AND s.code = 'BM110' AND c.slug = 'lecture-notes';
INSERT INTO resources (user_id,subject_id,category_id,title,description,file_path,original_name,file_type,file_size,pages,status,downloads_count,reviewed_by,reviewed_at,created_at)
 SELECT u.id,s.id,c.id,'Team Dynamics Study Guide','Group stages, conflict styles and case study pointers.','seed/sample-note.pdf','sample-note.pdf','pdf',1258291,13,'approved',73,NULL,NOW() - INTERVAL 60 DAY,NOW() - INTERVAL 60 DAY FROM users u, subjects s, categories c WHERE u.email = 'sachini@students.nsbm.ac.lk' AND s.code = 'BM110' AND c.slug = 'study-guide';
INSERT INTO resources (user_id,subject_id,category_id,title,description,file_path,original_name,file_type,file_size,pages,status,downloads_count,reviewed_by,reviewed_at,created_at)
 SELECT u.id,s.id,c.id,'Databases Midterm 2025 (Solved)','SQL and ER modelling questions with answers.','seed/sample-note.pdf','sample-note.pdf','pdf',1887436,14,'approved',402,NULL,NOW() - INTERVAL 48 DAY,NOW() - INTERVAL 48 DAY FROM users u, subjects s, categories c WHERE u.email = 'tharushi@students.nsbm.ac.lk' AND s.code = 'CS204' AND c.slug = 'past-paper';
INSERT INTO resources (user_id,subject_id,category_id,title,description,file_path,original_name,file_type,file_size,pages,status,downloads_count,reviewed_by,reviewed_at,created_at)
 SELECT u.id,s.id,c.id,'Sorting Algorithms Lecture Notes','Merge, quick and heap sort traced step by step.','seed/sample-note.pdf','sample-note.pdf','pdf',1572864,22,'approved',388,NULL,NOW() - INTERVAL 78 DAY,NOW() - INTERVAL 78 DAY FROM users u, subjects s, categories c WHERE u.email = 'kavindu@students.nsbm.ac.lk' AND s.code = 'SE201' AND c.slug = 'lecture-notes';
INSERT INTO resources (user_id,subject_id,category_id,title,description,file_path,original_name,file_type,file_size,pages,status,downloads_count,reviewed_by,reviewed_at,created_at)
 SELECT u.id,s.id,c.id,'UML Diagrams Study Guide','Class, sequence and activity diagrams with examples.','seed/sample-note.pptx','sample-note.pptx','pptx',2726297,25,'approved',351,NULL,NOW() - INTERVAL 105 DAY,NOW() - INTERVAL 105 DAY FROM users u, subjects s, categories c WHERE u.email = 'ravindu@students.nsbm.ac.lk' AND s.code = 'SE301' AND c.slug = 'study-guide';
INSERT INTO resources (user_id,subject_id,category_id,title,description,file_path,original_name,file_type,file_size,pages,status,downloads_count,reviewed_by,reviewed_at,created_at)
 SELECT u.id,s.id,c.id,'IP Addressing Lecture Notes','IPv4, IPv6 and subnet masks from week 2.','seed/sample-note.pdf','sample-note.pdf','pdf',1048576,12,'approved',276,NULL,NOW() - INTERVAL 135 DAY,NOW() - INTERVAL 135 DAY FROM users u, subjects s, categories c WHERE u.email = 'dinithi@students.nsbm.ac.lk' AND s.code = 'CY208' AND c.slug = 'lecture-notes';
INSERT INTO resources (user_id,subject_id,category_id,title,description,file_path,original_name,file_type,file_size,pages,status,downloads_count,reviewed_by,reviewed_at,created_at)
 SELECT u.id,s.id,c.id,'Java OOP Basics Guide','Classes, objects and inheritance for first years.','seed/sample-note.pdf','sample-note.pdf','pdf',1153433,16,'approved',244,NULL,NOW() - INTERVAL 140 DAY,NOW() - INTERVAL 140 DAY FROM users u, subjects s, categories c WHERE u.email = 'nimesh@students.nsbm.ac.lk' AND s.code = 'CS101' AND c.slug = 'study-guide';
INSERT INTO resources (user_id,subject_id,category_id,title,description,file_path,original_name,file_type,file_size,pages,status,downloads_count,reviewed_by,reviewed_at,created_at)
 SELECT u.id,s.id,c.id,'Standard Costing Guide','Variances explained with short worked problems.','seed/sample-note.pdf','sample-note.pdf','pdf',1363148,15,'approved',231,NULL,NOW() - INTERVAL 170 DAY,NOW() - INTERVAL 170 DAY FROM users u, subjects s, categories c WHERE u.email = 'nimasha@students.nsbm.ac.lk' AND s.code = 'BM201' AND c.slug = 'study-guide';
INSERT INTO resources (user_id,subject_id,category_id,title,description,file_path,original_name,file_type,file_size,pages,status,downloads_count,reviewed_by,reviewed_at,created_at)
 SELECT u.id,s.id,c.id,'Marketing Principles Past Paper 2025','Essay questions with rubric notes.','seed/sample-note.pdf','sample-note.pdf','pdf',943718,8,'approved',190,NULL,NOW() - INTERVAL 200 DAY,NOW() - INTERVAL 200 DAY FROM users u, subjects s, categories c WHERE u.email = 'sachini@students.nsbm.ac.lk' AND s.code = 'BM102' AND c.slug = 'past-paper';
INSERT INTO resources (user_id,subject_id,category_id,title,description,file_path,original_name,file_type,file_size,pages,status,downloads_count,reviewed_by,reviewed_at,created_at)
 SELECT u.id,s.id,c.id,'ER Modelling Lecture Notes','Entities, relationships and cardinality.','seed/sample-note.pdf','sample-note.pdf','pdf',1677721,20,'approved',330,NULL,NOW() - INTERVAL 198 DAY,NOW() - INTERVAL 198 DAY FROM users u, subjects s, categories c WHERE u.email = 'tharushi@students.nsbm.ac.lk' AND s.code = 'CS204' AND c.slug = 'lecture-notes';
INSERT INTO resources (user_id,subject_id,category_id,title,description,file_path,original_name,file_type,file_size,pages,status,downloads_count,reviewed_by,reviewed_at,created_at)
 SELECT u.id,s.id,c.id,'Python Basics Lecture Notes','Variables, loops and functions from week 1.','seed/sample-note.pdf','sample-note.pdf','pdf',734003,18,'approved',298,NULL,NOW() - INTERVAL 230 DAY,NOW() - INTERVAL 230 DAY FROM users u, subjects s, categories c WHERE u.email = 'kavindu@students.nsbm.ac.lk' AND s.code = 'CS101' AND c.slug = 'lecture-notes';
INSERT INTO resources (user_id,subject_id,category_id,title,description,file_path,original_name,file_type,file_size,pages,status,downloads_count,reviewed_by,reviewed_at,created_at)
 SELECT u.id,s.id,c.id,'Algorithms Midterm 2025','Complexity and recursion questions, solved.','seed/sample-note.pdf','sample-note.pdf','pdf',1468006,9,'approved',265,NULL,NOW() - INTERVAL 232 DAY,NOW() - INTERVAL 232 DAY FROM users u, subjects s, categories c WHERE u.email = 'ravindu@students.nsbm.ac.lk' AND s.code = 'SE201' AND c.slug = 'past-paper';
INSERT INTO resources (user_id,subject_id,category_id,title,description,file_path,original_name,file_type,file_size,pages,status,downloads_count,reviewed_by,reviewed_at,created_at)
 SELECT u.id,s.id,c.id,'TCP vs UDP Guide','When to use each, with packet diagrams.','seed/sample-note.pdf','sample-note.pdf','pdf',629145,5,'approved',219,NULL,NOW() - INTERVAL 262 DAY,NOW() - INTERVAL 262 DAY FROM users u, subjects s, categories c WHERE u.email = 'dinithi@students.nsbm.ac.lk' AND s.code = 'CY208' AND c.slug = 'study-guide';
INSERT INTO resources (user_id,subject_id,category_id,title,description,file_path,original_name,file_type,file_size,pages,status,downloads_count,reviewed_by,reviewed_at,created_at)
 SELECT u.id,s.id,c.id,'Engineering Maths Midterm 2025','Differential equations, with short solutions.','seed/sample-note.pdf','sample-note.pdf','pdf',1258291,8,'approved',120,NULL,NOW() - INTERVAL 258 DAY,NOW() - INTERVAL 258 DAY FROM users u, subjects s, categories c WHERE u.email = 'lasith@students.nsbm.ac.lk' AND s.code = 'EN205' AND c.slug = 'past-paper';
INSERT INTO resources (user_id,subject_id,category_id,title,description,file_path,original_name,file_type,file_size,pages,status,downloads_count,reviewed_by,reviewed_at,created_at)
 SELECT u.id,s.id,c.id,'Sampling Methods Lecture Notes','Random, stratified and cluster sampling.','seed/sample-note.pdf','sample-note.pdf','pdf',734003,9,'approved',104,NULL,NOW() - INTERVAL 285 DAY,NOW() - INTERVAL 285 DAY FROM users u, subjects s, categories c WHERE u.email = 'kasuni@students.nsbm.ac.lk' AND s.code = 'SC110' AND c.slug = 'lecture-notes';
INSERT INTO resources (user_id,subject_id,category_id,title,description,file_path,original_name,file_type,file_size,pages,status,downloads_count,reviewed_by,reviewed_at,created_at)
 SELECT u.id,s.id,c.id,'SQL Query Practice Pack','Sixty practice queries with answers.','seed/sample-note.docx','sample-note.docx','docx',1992294,30,'approved',420,NULL,NOW() - INTERVAL 288 DAY,NOW() - INTERVAL 288 DAY FROM users u, subjects s, categories c WHERE u.email = 'tharushi@students.nsbm.ac.lk' AND s.code = 'CS204' AND c.slug = 'study-guide';

-- Ratings and recent downloads for demo notes, from students in the same faculty.
INSERT IGNORE INTO ratings (resource_id,user_id,value)
 SELECT r.id,u.id,4 + ((r.id + u.id) % 2) FROM resources r JOIN subjects s ON s.id = r.subject_id
   JOIN users u ON u.faculty = s.faculty AND u.role = 'student' AND u.id <> r.user_id
  WHERE r.file_path LIKE 'seed/%' AND r.status = 'approved' AND NOT EXISTS (SELECT 1 FROM ratings x WHERE x.resource_id = r.id);
INSERT INTO downloads (resource_id,user_id,created_at)
 SELECT r.id,u.id,NOW() - INTERVAL ((r.id * u.id * 7) % 27) DAY FROM resources r JOIN subjects s ON s.id = r.subject_id
   JOIN users u ON u.faculty = s.faculty AND u.role = 'student' AND u.id <> r.user_id
  WHERE r.file_path LIKE 'seed/%' AND r.status = 'approved' AND (r.id + u.id) % 3 = 0;

INSERT INTO questions (user_id,subject_id,title,body,views,created_at) SELECT u.id,s.id,'How do I pick between node and mesh analysis in the exam?','Both work, but one always seems longer. Is there a quick way to choose?',58,NOW() - INTERVAL 190 MINUTE FROM users u, subjects s WHERE u.email = 'pasindu@students.nsbm.ac.lk' AND s.code = 'EN210';
INSERT INTO questions (user_id,subject_id,title,body,views,created_at) SELECT u.id,s.id,'What is the easiest way to remember the stages of meiosis?','I keep mixing up prophase I and prophase II.',44,NOW() - INTERVAL 400 MINUTE FROM users u, subjects s WHERE u.email = 'hiruni@students.nsbm.ac.lk' AND s.code = 'SC205';
INSERT INTO questions (user_id,subject_id,title,body,views,created_at) SELECT u.id,s.id,'Which motivation theory should I use for a retail case study?','The case is about part-time staff turnover.',37,NOW() - INTERVAL 700 MINUTE FROM users u, subjects s WHERE u.email = 'ishara@students.nsbm.ac.lk' AND s.code = 'BM110';
INSERT INTO answers (question_id,user_id,body,created_at) SELECT q.id,u.id,'Count the essential nodes and meshes. Choose whichever gives fewer equations.',NOW() - INTERVAL 150 MINUTE FROM users u, questions q WHERE u.email = 'lasith@students.nsbm.ac.lk' AND q.title LIKE 'How do I pick between node%';
INSERT INTO answers (question_id,user_id,body,created_at) SELECT q.id,u.id,'Think of meiosis I as splitting pairs, and meiosis II as a normal mitosis.',NOW() - INTERVAL 300 MINUTE FROM users u, questions q WHERE u.email = 'sahan@students.nsbm.ac.lk' AND q.title LIKE 'What is the easiest way%';

INSERT INTO study_groups (owner_id,subject_id,name,description,max_members,created_at) SELECT u.id,s.id,'Circuits Lab Crew','Lab report checks and circuit problem practice for EN210.',10,NOW() - INTERVAL 6 DAY FROM users u, subjects s WHERE u.email = 'pasindu@students.nsbm.ac.lk' AND s.code = 'EN210';
INSERT INTO group_members (group_id,user_id) SELECT g.id,u.id FROM study_groups g, users u WHERE g.name = 'Circuits Lab Crew' AND u.email IN ('pasindu@students.nsbm.ac.lk','lasith@students.nsbm.ac.lk','malith@students.nsbm.ac.lk');
INSERT INTO study_groups (owner_id,subject_id,name,description,max_members,created_at) SELECT u.id,s.id,'Biology Study Table','Weekly revision of cell biology, with quizzes.',8,NOW() - INTERVAL 6 DAY FROM users u, subjects s WHERE u.email = 'hiruni@students.nsbm.ac.lk' AND s.code = 'SC205';
INSERT INTO group_members (group_id,user_id) SELECT g.id,u.id FROM study_groups g, users u WHERE g.name = 'Biology Study Table' AND u.email IN ('hiruni@students.nsbm.ac.lk','kasuni@students.nsbm.ac.lk','sahan@students.nsbm.ac.lk');
INSERT INTO study_groups (owner_id,subject_id,name,description,max_members,created_at) SELECT u.id,s.id,'OB Case Study Circle','We take apart one organizational behaviour case each week.',10,NOW() - INTERVAL 6 DAY FROM users u, subjects s WHERE u.email = 'ishara@students.nsbm.ac.lk' AND s.code = 'BM110';
INSERT INTO group_members (group_id,user_id) SELECT g.id,u.id FROM study_groups g, users u WHERE g.name = 'OB Case Study Circle' AND u.email IN ('ishara@students.nsbm.ac.lk','sachini@students.nsbm.ac.lk','nimasha@students.nsbm.ac.lk');
INSERT INTO study_sessions (host_id,subject_id,group_id,title,description,mode,starts_at,ends_at,location,meeting_url,capacity) SELECT u.id,s.id,g.id,'Circuits Lab Report Check','Bring your lab sheet and readings.','physical',CURDATE() + INTERVAL 1 DAY + INTERVAL 840 MINUTE,CURDATE() + INTERVAL 1 DAY + INTERVAL 840 MINUTE + INTERVAL 90 MINUTE,'Engineering Building, Lab 2',NULL,8 FROM users u, subjects s, study_groups g WHERE u.email = 'pasindu@students.nsbm.ac.lk' AND s.code = 'EN210' AND g.name = 'Circuits Lab Crew';
INSERT INTO session_attendees (session_id,user_id) SELECT ss.id,u.id FROM study_sessions ss, users u WHERE ss.title = 'Circuits Lab Report Check' AND u.email IN ('pasindu@students.nsbm.ac.lk','lasith@students.nsbm.ac.lk');
INSERT INTO study_sessions (host_id,subject_id,group_id,title,description,mode,starts_at,ends_at,location,meeting_url,capacity) SELECT u.id,s.id,g.id,'Cell Biology Quiz Night','Short quiz, then we go through the answers together.','online',CURDATE() + INTERVAL 2 DAY + INTERVAL 1140 MINUTE,CURDATE() + INTERVAL 2 DAY + INTERVAL 1140 MINUTE + INTERVAL 60 MINUTE,NULL,'https://meet.google.com/ncircle-demo-e',8 FROM users u, subjects s, study_groups g WHERE u.email = 'hiruni@students.nsbm.ac.lk' AND s.code = 'SC205' AND g.name = 'Biology Study Table';
INSERT INTO session_attendees (session_id,user_id) SELECT ss.id,u.id FROM study_sessions ss, users u WHERE ss.title = 'Cell Biology Quiz Night' AND u.email IN ('hiruni@students.nsbm.ac.lk','kasuni@students.nsbm.ac.lk');
INSERT INTO study_sessions (host_id,subject_id,group_id,title,description,mode,starts_at,ends_at,location,meeting_url,capacity) SELECT u.id,s.id,g.id,'Retail Case Discussion','We discuss the staff turnover case from week 5.','hybrid',CURDATE() + INTERVAL 3 DAY + INTERVAL 960 MINUTE,CURDATE() + INTERVAL 3 DAY + INTERVAL 960 MINUTE + INTERVAL 75 MINUTE,'Business Faculty, Room B12','https://meet.google.com/ncircle-demo-f',10 FROM users u, subjects s, study_groups g WHERE u.email = 'ishara@students.nsbm.ac.lk' AND s.code = 'BM110' AND g.name = 'OB Case Study Circle';
INSERT INTO session_attendees (session_id,user_id) SELECT ss.id,u.id FROM study_sessions ss, users u WHERE ss.title = 'Retail Case Discussion' AND u.email IN ('ishara@students.nsbm.ac.lk','sachini@students.nsbm.ac.lk');

UPDATE users SET skills = 'SQL, Python, Data analysis' WHERE email = 'nimesh@students.nsbm.ac.lk';
UPDATE users SET skills = 'Database design, SQL, Tutoring' WHERE email = 'tharushi@students.nsbm.ac.lk';
UPDATE users SET skills = 'Java, C++, Web development' WHERE email = 'kavindu@students.nsbm.ac.lk';
UPDATE users SET skills = 'Algorithms, Python, Machine learning' WHERE email = 'ravindu@students.nsbm.ac.lk';
UPDATE users SET skills = 'Marketing, Surveys, Copywriting' WHERE email = 'sachini@students.nsbm.ac.lk';
UPDATE users SET skills = 'Networking, Security, Wireshark' WHERE email = 'dinithi@students.nsbm.ac.lk';
UPDATE users SET skills = 'Accounting, Excel, Financial analysis' WHERE email = 'nimasha@students.nsbm.ac.lk';
UPDATE users SET skills = 'Mathematics, MATLAB, Circuits' WHERE email = 'lasith@students.nsbm.ac.lk';
UPDATE users SET skills = 'Statistics, R, Lab reports' WHERE email = 'kasuni@students.nsbm.ac.lk';
UPDATE users SET skills = 'Circuit design, Arduino, CAD' WHERE email = 'malith@students.nsbm.ac.lk';
UPDATE users SET skills = 'PCB design, Arduino, Technical writing' WHERE email = 'pasindu@students.nsbm.ac.lk';
UPDATE users SET skills = 'Biology, Lab techniques, Literature review' WHERE email = 'hiruni@students.nsbm.ac.lk';
UPDATE users SET skills = 'Chemistry, Data collection' WHERE email = 'sahan@students.nsbm.ac.lk';
UPDATE users SET skills = 'Market research, Interviews, Presentations' WHERE email = 'ishara@students.nsbm.ac.lk';
UPDATE users SET open_to_help = 0 WHERE email IN ('nimasha@students.nsbm.ac.lk', 'malith@students.nsbm.ac.lk');

INSERT INTO help_requests (user_id,type,title,description,skills,created_at) SELECT id,'research','Need a Business student for a fintech market research project','We are building a student budgeting app and need help designing and running a market survey. About 3 hours a week for 4 weeks.','Market research, Surveys',NOW() - INTERVAL 6 HOUR FROM users WHERE email = 'kavindu@students.nsbm.ac.lk';
INSERT INTO help_request_faculties (request_id,faculty) SELECT id,'Business' FROM help_requests WHERE title = 'Need a Business student for a fintech market research project';
INSERT INTO help_request_responses (request_id,user_id,message,status,created_at) SELECT r.id,u.id,'I have run surveys for two coursework projects and would like to help.','pending',NOW() - INTERVAL 4 HOUR FROM help_requests r, users u WHERE r.title = 'Need a Business student for a fintech market research project' AND u.email = 'ishara@students.nsbm.ac.lk';
INSERT INTO help_requests (user_id,type,title,description,skills,created_at) SELECT id,'project','Looking for a developer to build a campus event website','Our marketing project needs a simple website with sign-ups. Design and content are ready.','Web development',NOW() - INTERVAL 20 HOUR FROM users WHERE email = 'sachini@students.nsbm.ac.lk';
INSERT INTO help_request_faculties (request_id,faculty) SELECT id,'Computing' FROM help_requests WHERE title = 'Looking for a developer to build a campus event website';
INSERT INTO help_request_responses (request_id,user_id,message,status,created_at) SELECT r.id,u.id,'I can build this in PHP or React. Happy to meet this week.','pending',NOW() - INTERVAL 15 HOUR FROM help_requests r, users u WHERE r.title = 'Looking for a developer to build a campus event website' AND u.email = 'kavindu@students.nsbm.ac.lk';
INSERT INTO help_requests (user_id,type,title,description,skills,created_at) SELECT id,'research','Statistics help for a cell biology final-year study','I need someone to check my experimental design and run the analysis in R.','Statistics, R',NOW() - INTERVAL 48 HOUR FROM users WHERE email = 'hiruni@students.nsbm.ac.lk';
INSERT INTO help_request_faculties (request_id,faculty) SELECT id,'Science' FROM help_requests WHERE title = 'Statistics help for a cell biology final-year study';
INSERT INTO help_request_faculties (request_id,faculty) SELECT id,'Computing' FROM help_requests WHERE title = 'Statistics help for a cell biology final-year study';
INSERT INTO help_requests (user_id,type,title,description,skills,created_at) SELECT id,'project','Business partner wanted for an engineering innovation competition','We have a working solar charger prototype and need someone to write the business plan.','Business plan, Financial analysis',NOW() - INTERVAL 90 HOUR FROM users WHERE email = 'pasindu@students.nsbm.ac.lk';
INSERT INTO help_request_faculties (request_id,faculty) SELECT id,'Business' FROM help_requests WHERE title = 'Business partner wanted for an engineering innovation competition';
INSERT INTO help_requests (user_id,type,title,description,skills,created_at) SELECT id,'tutoring','Can someone explain electrical circuits basics for an IoT module?','Our IoT module assumes circuits knowledge that I do not have. One or two sessions would be enough.','Circuits',NOW() - INTERVAL 150 HOUR FROM users WHERE email = 'ravindu@students.nsbm.ac.lk';
INSERT INTO help_request_faculties (request_id,faculty) SELECT id,'Engineering' FROM help_requests WHERE title = 'Can someone explain electrical circuits basics for an IoT module?';
INSERT INTO help_request_responses (request_id,user_id,message,status,created_at) SELECT r.id,u.id,'Happy to do a session this weekend.','accepted',NOW() - INTERVAL 120 HOUR FROM help_requests r, users u WHERE r.title = 'Can someone explain electrical circuits basics for an IoT module?' AND u.email = 'lasith@students.nsbm.ac.lk';
INSERT INTO help_requests (user_id,type,title,description,skills,created_at) SELECT id,'other','Survey respondents needed from all faculties','A five minute survey about study habits for my BM110 assignment.','',NOW() - INTERVAL 300 HOUR FROM users WHERE email = 'ishara@students.nsbm.ac.lk';
