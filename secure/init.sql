BEGIN TRANSACTION;

CREATE TABLE users (
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	username TEXT NOT NULL UNIQUE,
	password TEXT NOT NULL
);

INSERT INTO users (id,username,password) VALUES (1,'chimesmaster1','$2y$10$0Pr3WpfaRW41H2L2.zuoaelxidfW.IVnelep07C8RpdnHG8KPKJPu'); -- password: chimes1
INSERT INTO users (id,username,password) VALUES (2,'chimesmaster2','$2y$10$lU0U.h1FgCFSc.cimZuSs.8cNchy2crPxmnDgOutRCLBPDB4Hpike'); -- password: chimes2

CREATE TABLE images (
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	user_id INTEGER NOT NULL,
	file_name TEXT NOT NULL,
    file_ext TEXT NOT NULL,
    description TEXT
);

-- SOURCE: Personal photographs
INSERT INTO images (id,user_id,file_name,file_ext,description) VALUES (1,1,'1.jpg','jpg','Cornell Arts Quad in Spring');
INSERT INTO images (id,user_id,file_name,file_ext,description) VALUES (2,1,'2.jpg','jpg','Cornell Arts Quad in Autumn snow');
INSERT INTO images (id,user_id,file_name,file_ext,description) VALUES (3,1,'3.jpg','jpg','Cornell Arts Quad in Winter snow');
INSERT INTO images (id,user_id,file_name,file_ext,description) VALUES (4,1,'4.jpg','jpg','Cornell Libe Slope in Summer');
INSERT INTO images (id,user_id,file_name,file_ext,description) VALUES (5,1,'5.jpg','jpg','Cornell Arts Quad in Spring rain');
INSERT INTO images (id,user_id,file_name,file_ext,description) VALUES (6,1,'6.jpg','jpg','Cornell Arts Quad in Winter snow');
INSERT INTO images (id,user_id,file_name,file_ext,description) VALUES (7,1,'7.jpg','jpg','Cornell Libe Slope in Summer');
INSERT INTO images (id,user_id,file_name,file_ext,description) VALUES (8,1,'8.jpg','jpg','Cornell Ho Plaza in Summer');
INSERT INTO images (id,user_id,file_name,file_ext,description) VALUES (9,1,'9.jpg','jpg','Cornell Libe Slope in Autumn');
INSERT INTO images (id,user_id,file_name,file_ext,description) VALUES (10,1,'10.jpg','jpg','Cornell Ho Plaza in Autumn');

CREATE TABLE tags (
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	tag TEXT NOT NULL UNIQUE
);

INSERT INTO tags (id,tag) VALUES (1,'Spring');
INSERT INTO tags (id,tag) VALUES (2,'Autumn');
INSERT INTO tags (id,tag) VALUES (3,'Winter');
INSERT INTO tags (id,tag) VALUES (4,'Summer');
INSERT INTO tags (id,tag) VALUES (5,'Arts Quad');
INSERT INTO tags (id,tag) VALUES (6,'Libe Slope');
INSERT INTO tags (id,tag) VALUES (7,'Ho Plaza');
INSERT INTO tags (id,tag) VALUES (8,'Cayuga Lake');

CREATE TABLE image_tags (
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	image_id INTEGER NOT NULL,
	tag_id INTEGER NOT NULL
);

INSERT INTO image_tags (image_id,tag_id) VALUES (1,1);
INSERT INTO image_tags (image_id,tag_id) VALUES (1,5);
INSERT INTO image_tags (image_id,tag_id) VALUES (2,2);
INSERT INTO image_tags (image_id,tag_id) VALUES (2,5);
INSERT INTO image_tags (image_id,tag_id) VALUES (3,3);
INSERT INTO image_tags (image_id,tag_id) VALUES (3,5);
INSERT INTO image_tags (image_id,tag_id) VALUES (4,6);
INSERT INTO image_tags (image_id,tag_id) VALUES (4,8);
INSERT INTO image_tags (image_id,tag_id) VALUES (4,4);
INSERT INTO image_tags (image_id,tag_id) VALUES (5,5);
INSERT INTO image_tags (image_id,tag_id) VALUES (5,1);
INSERT INTO image_tags (image_id,tag_id) VALUES (6,5);
INSERT INTO image_tags (image_id,tag_id) VALUES (6,3);
INSERT INTO image_tags (image_id,tag_id) VALUES (7,6);
INSERT INTO image_tags (image_id,tag_id) VALUES (7,8);
INSERT INTO image_tags (image_id,tag_id) VALUES (7,4);
INSERT INTO image_tags (image_id,tag_id) VALUES (8,7);
INSERT INTO image_tags (image_id,tag_id) VALUES (9,6);
INSERT INTO image_tags (image_id,tag_id) VALUES (9,2);
INSERT INTO image_tags (image_id,tag_id) VALUES (9,8);
INSERT INTO image_tags (image_id,tag_id) VALUES (10,2);
INSERT INTO image_tags (image_id,tag_id) VALUES (10,7);

CREATE TABLE sessions (
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	user_id INTEGER NOT NULL,
	session TEXT NOT NULL UNIQUE
);

COMMIT;
