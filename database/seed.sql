-- seed.sql: demo data. Import after schema.sql. Logins are listed in docs/DEMO_ACCOUNTS.md.
-- Dates are relative to the import time, so charts and upcoming pods look current after a fresh import.
SET NAMES utf8mb4;

INSERT INTO users (id,name,username,student_id,email,faculty,academic_year,bio,password_hash,role,weekly_goal_minutes,email_verified_at,last_seen_at,created_at) VALUES
(1,'Ncircle Admin','admin','A0001','admin@students.nsbm.ac.lk','Computing',4,'Keeps the campus circle tidy.','$2y$10$vV.XH.KGGlngdkD.AOYNLeS2v6qAHmTkSDh5Ak6ZX1q301UHONz3y','admin',600,NOW() - INTERVAL 31 DAY,NOW() - INTERVAL 1 MINUTE,NOW() - INTERVAL 31 DAY),
(2,'Nimesh Perera','wcnperera','6942','nimesh@students.nsbm.ac.lk','Computing',2,'Second-year computing student. Databases and clean code.','$2y$10$TMB.cIhdOrwM9WERZyPij.oEqrbWSsi5uTcVnPQnzJMgxvp4mges.','student',600,NOW() - INTERVAL 42 DAY,NOW() - INTERVAL 1 MINUTE,NOW() - INTERVAL 42 DAY),
(3,'Tharushi Wijesinghe','tharushi_w','20481','tharushi@students.nsbm.ac.lk','Computing',3,'Database Systems tutor and cheat sheet enthusiast.','$2y$10$TMB.cIhdOrwM9WERZyPij.oEqrbWSsi5uTcVnPQnzJMgxvp4mges.','student',600,NOW() - INTERVAL 53 DAY,NOW() - INTERVAL 3 MINUTE,NOW() - INTERVAL 53 DAY),
(4,'Kavindu Silva','kavindu_s','20517','kavindu@students.nsbm.ac.lk','Computing',2,'Java, C++ and too much coffee.','$2y$10$TMB.cIhdOrwM9WERZyPij.oEqrbWSsi5uTcVnPQnzJMgxvp4mges.','student',600,NOW() - INTERVAL 24 DAY,NOW() - INTERVAL 8 MINUTE,NOW() - INTERVAL 24 DAY),
(5,'Ravindu Liyanage','ravindu_l','20633','ravindu@students.nsbm.ac.lk','Computing',3,'Algorithms and past paper speed runs.','$2y$10$TMB.cIhdOrwM9WERZyPij.oEqrbWSsi5uTcVnPQnzJMgxvp4mges.','student',600,NOW() - INTERVAL 35 DAY,NOW() - INTERVAL 12 MINUTE,NOW() - INTERVAL 35 DAY),
(6,'Sachini Wickramasinghe','sachini_w','20744','sachini@students.nsbm.ac.lk','Business',1,'Marketing and accounting notes.','$2y$10$TMB.cIhdOrwM9WERZyPij.oEqrbWSsi5uTcVnPQnzJMgxvp4mges.','student',600,NOW() - INTERVAL 46 DAY,NOW() - INTERVAL 40 MINUTE,NOW() - INTERVAL 46 DAY),
(7,'Dinithi Fernando','dinithi_f','20802','dinithi@students.nsbm.ac.lk','Computing',2,'Networks, packets and study pods.','$2y$10$TMB.cIhdOrwM9WERZyPij.oEqrbWSsi5uTcVnPQnzJMgxvp4mges.','student',600,NOW() - INTERVAL 57 DAY,NOW() - INTERVAL 5 MINUTE,NOW() - INTERVAL 57 DAY),
(8,'Nimasha Perera','nimasha_p','20915','nimasha@students.nsbm.ac.lk','Business',2,'Management accounting past papers.','$2y$10$TMB.cIhdOrwM9WERZyPij.oEqrbWSsi5uTcVnPQnzJMgxvp4mges.','student',600,NOW() - INTERVAL 28 DAY,NOW() - INTERVAL 200 MINUTE,NOW() - INTERVAL 28 DAY),
(9,'Lasith Weerasinghe','lasith_w','21022','lasith@students.nsbm.ac.lk','Engineering',3,'Happy to help with maths.','$2y$10$TMB.cIhdOrwM9WERZyPij.oEqrbWSsi5uTcVnPQnzJMgxvp4mges.','student',600,NOW() - INTERVAL 39 DAY,NOW() - INTERVAL 14 MINUTE,NOW() - INTERVAL 39 DAY),
(10,'Kasuni Rathnayake','kasuni_r','21134','kasuni@students.nsbm.ac.lk','Science',1,'Applied statistics and lab reports.','$2y$10$TMB.cIhdOrwM9WERZyPij.oEqrbWSsi5uTcVnPQnzJMgxvp4mges.','student',600,NOW() - INTERVAL 33 DAY,NOW() - INTERVAL 60 MINUTE,NOW() - INTERVAL 33 DAY),
(11,'Malith Senanayake','malith_s','21256','malith@students.nsbm.ac.lk','Engineering',2,'Circuits and study groups.','$2y$10$TMB.cIhdOrwM9WERZyPij.oEqrbWSsi5uTcVnPQnzJMgxvp4mges.','student',600,NOW() - INTERVAL 4 DAY,NOW() - INTERVAL 1000 MINUTE,NOW() - INTERVAL 4 DAY);

INSERT INTO subjects (id,code,name,faculty,year) VALUES
(1,'CS204','Database Systems','Computing',2),
(2,'SE201','Algorithms & Data Structures','Computing',2),
(3,'SE301','Software Engineering','Computing',3),
(4,'CY208','Computer Networks','Computing',2),
(5,'CS101','Programming Fundamentals','Computing',1),
(6,'BM102','Marketing Principles','Business',1),
(7,'BM201','Management Accounting','Business',2),
(8,'EN205','Engineering Mathematics','Engineering',2),
(9,'SC110','Applied Statistics','Science',1);

INSERT INTO categories (id,name,plural,slug) VALUES (1,'Lecture Notes','Lecture Notes','lecture-notes'),(2,'Study Guide','Study Guides','study-guide'),(3,'Past Paper','Past Papers','past-paper');

INSERT INTO resources (id,user_id,subject_id,category_id,title,description,file_path,original_name,file_type,file_size,pages,status,reject_reason,downloads_count,reviewed_by,reviewed_at,created_at) VALUES
(1,3,1,2,'Normalization Cheat Sheet — Database Systems','Step-by-step canonical decomposition guide covering 1NF to BCNF with NSBM exam scenarios.','seed/sample-note.pdf','sample-note.pdf','pdf',2516582,24,'approved',NULL,1820,1,NOW() - INTERVAL 1 HOUR,NOW() - INTERVAL 2 HOUR),
(2,4,3,2,'OOP Principles Study Guide','Core OOP patterns in Java & C++ with SOLID paradigms and lecture recap summaries.','seed/sample-note.docx','sample-note.docx','docx',1887436,79,'approved',NULL,1430,1,NOW() - INTERVAL 3 HOUR,NOW() - INTERVAL 4 HOUR),
(3,5,2,3,'2024 Final Exam — Data Structures & Algorithms','Full solved past paper with time complexity analysis and Dijkstra/DFS tree proofs.','seed/sample-note.pdf','sample-note.pdf','pdf',5872025,54,'approved',NULL,1150,1,NOW() - INTERVAL 25 HOUR,NOW() - INTERVAL 26 HOUR),
(4,6,6,1,'Intro to Marketing — Week 3 Slides','Consumer behavior frameworks, SWOT breakdown, and lecture slide annotations.','seed/sample-note.pptx','sample-note.pptx','pptx',4404019,38,'approved',NULL,840,1,NOW() - INTERVAL 49 HOUR,NOW() - INTERVAL 50 HOUR),
(5,7,4,2,'Networking Basics: OSI Model Summary','Layer encapsulation, TCP/UDP headers, and subnetting cheat sheet for exam prep.','seed/sample-note.pdf','sample-note.pdf','pdf',3250585,16,'approved',NULL,980,1,NOW() - INTERVAL 73 HOUR,NOW() - INTERVAL 74 HOUR),
(6,8,7,3,'Management Accounting Past Paper 2023','Standard costing variance analysis and CVP break-even computations with marking scheme.','seed/sample-note.pdf','sample-note.pdf','pdf',2202009,18,'approved',NULL,1030,1,NOW() - INTERVAL 121 HOUR,NOW() - INTERVAL 122 HOUR),
(7,3,1,1,'Week 6 Lecture Notes: Transactions & Concurrency','ACID properties, schedules, and two-phase locking with worked examples.','seed/sample-note.pdf','sample-note.pdf','pdf',1677721,31,'approved',NULL,612,1,NOW() - INTERVAL 7 HOUR,NOW() - INTERVAL 8 HOUR),
(8,4,2,2,'Data Structure & Algorithm Fundamentals (Python)','Stacks, queues, trees and graphs implemented in Python with complexity notes.','seed/sample-note.docx','sample-note.docx','docx',2097152,79,'approved',NULL,560,1,NOW() - INTERVAL 29 HOUR,NOW() - INTERVAL 30 HOUR),
(9,5,3,1,'Agile vs Waterfall: Lecture Summary','Comparison of delivery models with case studies from the SE301 lectures.','seed/sample-note.pptx','sample-note.pptx','pptx',3460300,22,'approved',NULL,430,1,NOW() - INTERVAL 97 HOUR,NOW() - INTERVAL 98 HOUR),
(10,7,4,3,'Computer Networks Midterm 2024 (Solved)','Subnetting, routing tables and congestion control, fully worked.','seed/sample-note.pdf','sample-note.pdf','pdf',1258291,12,'approved',NULL,705,1,NOW() - INTERVAL 149 HOUR,NOW() - INTERVAL 150 HOUR),
(11,2,5,2,'Java Basics: Loops and Arrays','Beginner-friendly guide with practice problems for first-year labs.','seed/sample-note.pdf','sample-note.pdf','pdf',943718,20,'approved',NULL,288,1,NOW() - INTERVAL 55 HOUR,NOW() - INTERVAL 56 HOUR),
(12,9,8,1,'Engineering Mathematics: Laplace Transforms','Definitions, tables and solved examples from the Year 2 lectures.','seed/sample-note.pdf','sample-note.pdf','pdf',2831155,40,'approved',NULL,352,1,NOW() - INTERVAL 19 HOUR,NOW() - INTERVAL 20 HOUR),
(13,10,9,2,'Applied Statistics Formula Sheet','Distributions, hypothesis tests and regression formulas on two pages.','seed/sample-note.pdf','sample-note.pdf','pdf',629145,2,'approved',NULL,498,1,NOW() - INTERVAL 43 HOUR,NOW() - INTERVAL 44 HOUR),
(14,6,7,1,'Budgeting and Forecasting Lecture Notes','Flexible budgets and variance reporting from week 8.','seed/sample-note.docx','sample-note.docx','docx',1153433,15,'approved',NULL,214,1,NOW() - INTERVAL 169 HOUR,NOW() - INTERVAL 170 HOUR),
(15,8,6,3,'Marketing Principles Past Paper 2024','Essay questions with marking rubric notes.','seed/sample-note.pdf','sample-note.pdf','pdf',1468006,10,'approved',NULL,377,1,NOW() - INTERVAL 199 HOUR,NOW() - INTERVAL 200 HOUR),
(16,9,8,3,'Engineering Maths Final 2023','Past paper with short solutions.','seed/sample-note.pdf','sample-note.pdf','pdf',1992294,9,'approved',NULL,256,1,NOW() - INTERVAL 259 HOUR,NOW() - INTERVAL 260 HOUR),
(17,5,1,3,'2024 Final Exam Past Paper (Solved) — Databases','ER modelling, SQL and normalization questions with solutions.','seed/sample-note.pdf','sample-note.pdf','pdf',3145728,24,'approved',NULL,890,1,NOW() - INTERVAL 299 HOUR,NOW() - INTERVAL 300 HOUR),
(18,2,1,2,'SQL Joins Visual Guide','Inner, outer and self joins explained with small diagrams.','seed/sample-note.pptx','sample-note.pptx','pptx',1572864,14,'approved',NULL,640,1,NOW() - INTERVAL 13 HOUR,NOW() - INTERVAL 14 HOUR),
(19,7,3,2,'Design Patterns Quick Reference','Creational, structural and behavioural patterns in one guide.','seed/sample-note.pdf','sample-note.pdf','pdf',1363148,12,'approved',NULL,520,1,NOW() - INTERVAL 109 HOUR,NOW() - INTERVAL 110 HOUR),
(20,10,9,1,'Probability Lecture Notes: Week 2','Conditional probability and Bayes with worked examples.','seed/sample-note.pdf','sample-note.pdf','pdf',838860,11,'approved',NULL,180,1,NOW() - INTERVAL 5 HOUR,NOW() - INTERVAL 6 HOUR),
(21,4,4,1,'Routing Algorithms Lecture Recap','Link state and distance vector compared, with exam tips.','seed/sample-note.docx','sample-note.docx','docx',1048576,9,'pending',NULL,0,NULL,NULL,NOW() - INTERVAL 3 HOUR),
(22,8,7,2,'CVP Analysis Study Guide','Contribution margin and break-even worked problems.','seed/sample-note.pdf','sample-note.pdf','pdf',1782579,13,'pending',NULL,0,NULL,NULL,NOW() - INTERVAL 5 HOUR),
(23,2,2,1,'Graph Traversal Notes: BFS and DFS','My notes on traversal order, with small examples.','seed/sample-note.pdf','sample-note.pdf','pdf',734003,8,'pending',NULL,0,NULL,NULL,NOW() - INTERVAL 1 HOUR),
(24,9,8,2,'Scanned Maths Notes','Handwritten notes from the Year 2 lectures.','seed/sample-note.pdf','sample-note.pdf','pdf',5033164,30,'rejected','Pages are too blurry to read. Please rescan and upload again.',0,1,NOW() - INTERVAL 89 HOUR,NOW() - INTERVAL 90 HOUR);

INSERT INTO ratings (resource_id,user_id,value) VALUES
(1,2,5),
(1,4,5),
(1,5,5),
(1,6,5),
(1,7,5),
(1,8,5),
(1,9,5),
(1,10,5),
(1,11,4),
(2,2,5),
(2,3,5),
(2,5,5),
(2,6,5),
(2,7,5),
(2,8,5),
(2,9,5),
(2,10,4),
(2,11,4),
(3,2,5),
(3,3,5),
(3,4,5),
(3,6,5),
(3,7,5),
(3,8,5),
(3,9,4),
(3,10,4),
(3,11,4),
(4,2,5),
(4,3,5),
(4,4,5),
(4,5,5),
(4,7,5),
(4,8,4),
(4,9,4),
(4,10,4),
(4,11,4),
(5,2,5),
(5,3,5),
(5,4,5),
(5,5,5),
(5,6,5),
(5,8,5),
(5,9,5),
(5,10,5),
(5,11,4),
(6,2,5),
(6,3,5),
(6,4,5),
(6,5,5),
(6,6,5),
(6,7,5),
(6,9,5),
(6,10,4),
(6,11,4),
(7,2,5),
(7,4,5),
(7,5,5),
(7,6,5),
(7,7,4),
(7,8,4),
(7,9,4),
(7,10,4),
(7,11,4),
(8,2,5),
(8,3,5),
(8,5,5),
(8,6,5),
(8,7,4),
(8,8,4),
(8,9,4),
(8,10,4),
(8,11,4),
(9,2,5),
(9,3,4),
(9,4,4),
(9,6,4),
(9,7,4),
(9,8,4),
(9,9,4),
(9,10,4),
(9,11,4),
(10,2,5),
(10,3,5),
(10,4,5),
(10,5,5),
(10,6,5),
(10,8,4),
(10,9,4),
(10,10,4),
(10,11,4),
(11,3,5),
(11,4,5),
(11,5,4),
(11,6,4),
(11,7,4),
(11,8,4),
(11,9,4),
(11,10,4),
(11,11,4),
(12,2,5),
(12,3,5),
(12,4,5),
(12,5,4),
(12,6,4),
(12,7,4),
(12,8,4),
(12,10,4),
(12,11,4),
(13,2,5),
(13,3,5),
(13,4,5),
(13,5,5),
(13,6,5),
(13,7,5),
(13,8,4),
(13,9,4),
(13,11,4),
(14,2,4),
(14,3,4),
(14,4,4),
(14,5,4),
(14,7,4),
(14,8,4),
(14,9,4),
(14,10,4),
(14,11,3),
(15,2,4),
(15,3,4),
(15,4,4),
(15,5,4),
(15,6,4),
(15,7,4),
(15,9,4),
(15,10,4),
(15,11,4),
(16,2,4),
(16,3,4),
(16,4,4),
(16,5,4),
(16,6,4),
(16,7,4),
(16,8,4),
(16,10,3),
(16,11,3),
(17,2,5),
(17,3,5),
(17,4,5),
(17,6,5),
(17,7,4),
(17,8,4),
(17,9,4),
(17,10,4),
(17,11,4),
(18,3,5),
(18,4,5),
(18,5,5),
(18,6,5),
(18,7,5),
(18,8,4),
(18,9,4),
(18,10,4),
(18,11,4),
(19,2,5),
(19,3,5),
(19,4,5),
(19,5,5),
(19,6,4),
(19,8,4),
(19,9,4),
(19,10,4),
(19,11,4),
(20,2,5),
(20,3,4),
(20,4,4),
(20,5,4),
(20,6,4),
(20,7,4),
(20,8,4),
(20,9,4),
(20,11,4);

INSERT INTO comments (resource_id,user_id,body,created_at) VALUES
(1,4,'This saved me before the midterm. The BCNF example is very clear.',NOW() - INTERVAL 20 MINUTE),
(1,5,'Could you add one more example for 3NF decomposition?',NOW() - INTERVAL 60 MINUTE),
(1,2,'Great summary, thanks for sharing!',NOW() - INTERVAL 90 MINUTE),
(2,3,'The SOLID section is excellent.',NOW() - INTERVAL 100 MINUTE),
(3,7,'Question 4''s Dijkstra trace helped a lot.',NOW() - INTERVAL 300 MINUTE),
(5,2,'Exactly what I needed for the CY208 quiz.',NOW() - INTERVAL 500 MINUTE),
(6,6,'Very clear variance tables.',NOW() - INTERVAL 800 MINUTE),
(7,4,'Good concurrency examples.',NOW() - INTERVAL 120 MINUTE),
(18,3,'Nice diagrams.',NOW() - INTERVAL 200 MINUTE);

INSERT INTO bookmarks (user_id,resource_id) VALUES (2,1),(2,3),(2,5),(3,2),(4,1);

INSERT INTO downloads (resource_id,user_id,created_at) VALUES (1,2,NOW() - INTERVAL 1 DAY),(3,2,NOW() - INTERVAL 2 DAY),(5,4,NOW() - INTERVAL 3 HOUR);

INSERT INTO questions (id,user_id,subject_id,title,body,views,solved_at,created_at) VALUES
(1,5,1,'What is the concrete difference between 2NF and 3NF with a real NSBM exam example?','I keep mixing up partial and transitive dependencies. Can someone show this with a past paper question?',210,NOW() - INTERVAL 34 MINUTE,NOW() - INTERVAL 45 MINUTE),
(2,4,2,'How do I choose between BFS and DFS for shortest path in an unweighted vs weighted graph?','The lecture said BFS works for unweighted graphs, but what should I use once edges have weights?',96,NULL,NOW() - INTERVAL 120 MINUTE),
(3,6,7,'Is there a verified past paper marking guide for Management Accounting 2023?','I found the paper but no marking guide. Does anyone have a verified one?',142,NULL,NOW() - INTERVAL 1500 MINUTE),
(4,7,4,'Why does TCP need a three-way handshake instead of two steps?','Our CY208 lecturer asked this in class and I could not explain it well.',88,NOW() - INTERVAL 2865 MINUTE,NOW() - INTERVAL 2900 MINUTE),
(5,2,3,'When should I use the Strategy pattern instead of simple inheritance?','I understand both, but not how to decide in a real design.',64,NULL,NOW() - INTERVAL 4400 MINUTE),
(6,10,9,'How do I decide between a t-test and a z-test for the lab report?','The sample size is 28 and the population variance is unknown.',51,NOW() - INTERVAL 5982 MINUTE,NOW() - INTERVAL 6000 MINUTE),
(7,9,8,'Any tips for remembering Laplace transform pairs in the exam?','There are too many to memorize. What do people actually focus on?',47,NULL,NOW() - INTERVAL 8000 MINUTE),
(8,3,1,'Is denormalization ever acceptable in a coursework database design?','My design is slow on reports, and I wonder if marks are lost for denormalizing.',73,NULL,NOW() - INTERVAL 9500 MINUTE);

INSERT INTO answers (id,question_id,user_id,body,is_accepted,created_at) VALUES
(1,1,3,'2NF removes partial dependencies on part of a composite key. 3NF then removes transitive dependencies between non-key columns. In the 2023 paper, StudentID+ModuleCode to StudentName breaks 2NF, while ModuleCode to LecturerID to LecturerRoom breaks 3NF.',1,NOW() - INTERVAL 34 MINUTE),
(2,1,2,'A quick check: if a non-key column depends on another non-key column, that is your 3NF problem.',0,NOW() - INTERVAL 30 MINUTE),
(3,2,5,'Use BFS for unweighted graphs since each edge costs the same. Once edges have weights, use Dijkstra if they are non-negative.',0,NOW() - INTERVAL 100 MINUTE),
(4,2,7,'If there are negative weights, look at Bellman-Ford. DFS does not give shortest paths in general.',0,NOW() - INTERVAL 80 MINUTE),
(5,4,7,'Two steps would only confirm one direction. The third step confirms the server''s sequence number, so both sides know the other can send and receive.',1,NOW() - INTERVAL 2865 MINUTE),
(6,5,3,'Use Strategy when the behaviour needs to change at runtime, or when subclasses would multiply for every combination.',0,NOW() - INTERVAL 3000 MINUTE),
(7,6,9,'With an unknown population variance and n = 28, use the t-test. With large samples the two become nearly the same.',1,NOW() - INTERVAL 5982 MINUTE),
(8,7,3,'Learn the five or six common pairs and the shift theorems. Most others come from those.',0,NOW() - INTERVAL 6000 MINUTE),
(9,8,5,'It is fine if you justify it. Explain the performance reason and show the normalized design first.',0,NOW() - INTERVAL 7000 MINUTE),
(10,8,4,'I kept a materialized summary table in mine and got full marks for the explanation.',0,NOW() - INTERVAL 6500 MINUTE),
(11,8,2,'Keep the normalized design in your report and explain the trade-off.',0,NOW() - INTERVAL 1300 MINUTE),
(12,5,4,'Also consider composition first, then Strategy follows naturally.',0,NOW() - INTERVAL 1000 MINUTE),
(13,7,6,'Group them by shift rules rather than by function.',0,NOW() - INTERVAL 2500 MINUTE),
(14,8,7,'Agree, justify it clearly in the report.',0,NOW() - INTERVAL 4200 MINUTE),
(15,4,8,'The SYN and ACK numbers are what make it reliable.',0,NOW() - INTERVAL 1450 MINUTE),
(16,2,9,'Dijkstra is usually what the marking scheme expects.',0,NOW() - INTERVAL 60 MINUTE),
(17,6,10,'Check the assumptions section of the lab sheet too.',0,NOW() - INTERVAL 5600 MINUTE),
(18,1,4,'This example is what finally made it click, thanks.',0,NOW() - INTERVAL 20 MINUTE);

INSERT INTO answer_votes (answer_id,user_id) VALUES (1,2),(1,4),(1,5),(1,6),(1,7),(1,8),(1,9),(1,10),(1,11),(2,3),(2,4),(3,2),(3,4),(3,7),(4,2),(4,3),(5,2),(5,3),(5,4),(5,5),(7,2),(7,3);

INSERT INTO study_groups (id,owner_id,subject_id,name,description,max_members,created_at) VALUES
(1,3,1,'CS204 Exam Sprint Crew','Weekly normalization and SQL drills before the CS204 final.',12,NOW() - INTERVAL 3 DAY),
(2,5,2,'Algorithms Speed Run','We race through past paper graph and DP questions, then compare answers.',10,NOW() - INTERVAL 6 DAY),
(3,7,4,'Packet Pushers','Subnetting practice, Wireshark labs and CY208 revision.',8,NOW() - INTERVAL 9 DAY),
(4,6,7,'Accounting Study Circle','Variance analysis and CVP problems for BM201.',15,NOW() - INTERVAL 12 DAY),
(5,9,8,'Maths Survival Group','Laplace, Fourier and everything else in EN205.',6,NOW() - INTERVAL 15 DAY),
(6,10,9,'Stats Lab Partners','Lab report reviews and hypothesis testing practice.',8,NOW() - INTERVAL 18 DAY);

INSERT INTO group_members (group_id,user_id) VALUES (1,3),(1,2),(1,4),(1,5),(1,7),(1,10),(2,5),(2,2),(2,7),(3,7),(3,4),(3,2),(3,3),(4,6),(4,8),(5,9),(5,11),(6,10);

INSERT INTO group_posts (group_id,user_id,body,created_at) VALUES (1,3,'Tonight we cover BCNF decomposition. Bring last year''s question 3.',NOW() - INTERVAL 200 MINUTE),(1,2,'I will be there. I also uploaded my SQL joins guide.',NOW() - INTERVAL 150 MINUTE),(1,4,'Can we do transactions next week?',NOW() - INTERVAL 60 MINUTE),(3,7,'Lab 4 subnetting answers are up on the wall tomorrow.',NOW() - INTERVAL 400 MINUTE);

INSERT INTO study_sessions (id,host_id,subject_id,group_id,title,description,mode,starts_at,capacity) VALUES
(1,3,1,1,'Database Normalization Problem Solving Sprint','Lab 04 (Faculty of Computing) + WebRTC Voice sync','hybrid',CURDATE() + INTERVAL 1 DAY + INTERVAL 930 MINUTE,10),
(2,5,2,NULL,'Midterm Past Paper Speed Run & Verification','Interactive whiteboard & live code sandbox','online',CURDATE() + INTERVAL 1 DAY + INTERVAL 1020 MINUTE,15),
(3,7,4,3,'OSI Security & Packet Encapsulation Review','Muted silent study with 10-min Q&A breaks','online',CURDATE() + INTERVAL 2 DAY + INTERVAL 600 MINUTE,8),
(4,6,7,4,'CVP Break-even Drill','Library group room 2, bring a calculator','hybrid',CURDATE() + INTERVAL 3 DAY + INTERVAL 840 MINUTE,12),
(5,9,8,5,'Laplace Transform Clinic','Online lounge, short worked examples','online',CURDATE() + INTERVAL 4 DAY + INTERVAL 990 MINUTE,8),
(6,3,1,1,'SQL Joins Warm-up','Quick practice before the lab','online',NOW() - INTERVAL 1 HOUR,10);

INSERT INTO session_attendees (session_id,user_id) VALUES (1,3),(1,4),(1,5),(1,7),(1,9),(1,10),(1,11),(1,8),(2,5),(2,7),(2,2),(2,3),(2,4),(2,6),(2,8),(2,9),(2,10),(2,11),(2,1),(3,7),(3,4),(3,3),(3,5),(3,9),(3,10),(4,6),(4,8),(5,9),(5,11),(6,3),(6,2),(6,4);

INSERT INTO study_logs (user_id,minutes,note,logged_on) VALUES
(2,75,'Normalization practice',CURDATE() - INTERVAL 0 DAY),
(2,60,'CS204 past paper',CURDATE() - INTERVAL 1 DAY),
(2,90,'Graph algorithms',CURDATE() - INTERVAL 2 DAY),
(2,45,'OOP revision',CURDATE() - INTERVAL 3 DAY),
(2,120,'Group sprint',CURDATE() - INTERVAL 5 DAY),
(2,60,'SQL joins',CURDATE() - INTERVAL 6 DAY),
(2,50,'Networks reading',CURDATE() - INTERVAL 7 DAY),
(2,40,'Lab prep',CURDATE() - INTERVAL 8 DAY),
(2,70,'Past papers',CURDATE() - INTERVAL 9 DAY),
(2,35,'Reading',CURDATE() - INTERVAL 10 DAY),
(2,60,'Revision',CURDATE() - INTERVAL 12 DAY);

INSERT INTO notifications (user_id,icon,title,body,link,read_at,created_at) VALUES
(2,'patch-check','Your note was approved','SQL Joins Visual Guide','/resources/18',NULL,NOW() - INTERVAL 30 MINUTE),
(2,'chat-left-text','Tharushi Wijesinghe answered your question','When should I use the Strategy pattern instead of simple inheritance?','/forum/5',NULL,NOW() - INTERVAL 300 MINUTE),
(2,'people','Kavindu Silva joined your group','CS204 Exam Sprint Crew','/groups/1',NOW(),NOW() - INTERVAL 1500 MINUTE),
(3,'chat-dots','Kavindu Silva commented on your note','Normalization Cheat Sheet — Database Systems','/resources/1',NULL,NOW() - INTERVAL 20 MINUTE),
(9,'x-circle','Your note was not approved','Pages are too blurry to read. Please rescan and upload again.','/resources/24',NULL,NOW() - INTERVAL 5000 MINUTE);

INSERT INTO reports (reporter_id,target_type,target_id,reason,created_at) VALUES (4,'resource',16,'This looks copied from a lecturer''s private material.',NOW() - INTERVAL 5 HOUR),(7,'answer',8,'This is not an answer, please review.',NOW() - INTERVAL 2 HOUR);

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
