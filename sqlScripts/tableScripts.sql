CREATE TABLE `testDb`.`users` ( `user_id` VARCHAR(50) NOT NULL , `disp_name` VARCHAR(200) NOT NULL , `encrypt_pwd` CHAR(40) NOT NULL , `email_addr` CHAR(50) NOT NULL , `ph_num` BIGINT NOT NULL , `age` SMALLINT NOT NULL , `location` VARCHAR(100) NOT NULL , `description` VARCHAR(2000) NULL , `pro_img_url` CHAR(200) NULL , `status` CHAR(1) NOT NULL DEFAULT 'A' , `up_votes` SMALLINT NOT NULL DEFAULT '0' , `down_votes` SMALLINT NOT NULL DEFAULT '0' , `created_ts` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() , `last_access_ts` TIMESTAMP NULL DEFAULT NULL , PRIMARY KEY (`user_id`)) ENGINE = InnoDB


CREATE TABLE `testdb`.`questions` ( `qstn_id` CHAR(5) NOT NULL AUTO_INCREMENT , `qstn_titl` VARCHAR(3000) NOT NULL , `qstn_desc` LONGBLOB NOT NULL , `qstn_status` CHAR(1) NOT NULL , `topic_id` CHAR(5) NOT NULL , `posted_by` VARCHAR(50) NOT NULL , `created_ts` TIMESTAMP NOT NULL , `last_updt_ts` TIMESTAMP on update CURRENT_TIMESTAMP() NOT NULL DEFAULT CURRENT_TIMESTAMP() , PRIMARY KEY (`qstn_id`(5))) ENGINE = InnoDB;

CREATE TABLE `testdb`.`topics` ( `topic_id` CHAR(5) NOT NULL , `topic_code` CHAR(50) NOT NULL , `topic_desc` VARCHAR(500) NOT NULL , `created_ts` TIMESTAMP NOT NULL , `last_updt_ts` TIMESTAMP on update CURRENT_TIMESTAMP() NOT NULL , PRIMARY KEY (`topic_id`(5))) ENGINE = InnoDB;

CREATE TABLE `testdb`.`answers` ( `ans_id` CHAR(5) NOT NULL , `ans_desc` VARCHAR(50000) NOT NULL , `qstn_id` CHAR(5) NOT NULL , `posted_by` VARCHAR(50) NOT NULL , `created_ts` TIMESTAMP NOT NULL , `last_updt_ts` TIMESTAMP on update CURRENT_TIMESTAMP() NOT NULL , PRIMARY KEY (`ans_id`(5)), INDEX (`qstn_id`(5))) ENGINE = InnoDB;


CREATE TABLE `testdb`.`tags` ( `tag_id` INT NOT NULL AUTO_INCREMENT , `tag_name` VARCHAR(50) NOT NULL , `created_ts` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() , `last_updt_ts` TIMESTAMP on update CURRENT_TIMESTAMP() NOT NULL , PRIMARY KEY (`tag_id`), UNIQUE `tag_unique` (`tag_name`(50))) ENGINE = InnoDB;

CREATE TABLE `testdb`.`qstn_tags` ( `qstn_id` INT NOT NULL , `tag_id` INT NOT NULL , `created_by` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() , `last_updt_by` TIMESTAMP on update CURRENT_TIMESTAMP() NOT NULL ) ENGINE = InnoDB;

ALTER TABLE `qstn_tags` ADD CONSTRAINT `fk_qstn_id` FOREIGN KEY (`qstn_id`) REFERENCES `questions`(`qstn_id`) ON DELETE RESTRICT ON UPDATE RESTRICT; 
ALTER TABLE `qstn_tags` ADD CONSTRAINT `fk_tag_id` FOREIGN KEY (`tag_id`) REFERENCES `tags`(`tag_id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

CREATE TABLE `testdb`.`user_tags` ( `user_id` VARCHAR(50) NOT NULL , `tag_id` INT NOT NULL , `created_by` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() , `last_updt_by` TIMESTAMP on update CURRENT_TIMESTAMP() NOT NULL ) ENGINE = InnoDB;

ALTER TABLE `user_tags` ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE RESTRICT ON UPDATE RESTRICT; 
ALTER TABLE `user_tags` ADD CONSTRAINT `fk_tag2_id` FOREIGN KEY (`tag_id`) REFERENCES `tags`(`tag_id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

CREATE TABLE `testdb`.`comments` ( `comment_id` INT NOT NULL AUTO_INCREMENT , `comment_desc` VARCHAR(5000) NOT NULL , `ans_id` INT NOT NULL , `posted_by` VARCHAR(50) NOT NULL , `created_ts` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() , `last_updt_ts` TIMESTAMP on update CURRENT_TIMESTAMP() NOT NULL , PRIMARY KEY (`comment_id`)) ENGINE = InnoDB;

ALTER TABLE `comments` ADD CONSTRAINT `fk_ans_id` FOREIGN KEY (`ans_id`) REFERENCES `answers`(`ans_id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

CREATE TABLE `testdb`.`user_posts_votes` ( `vote_id` INT NOT NULL AUTO_INCREMENT,  `user_id` VARCHAR(50) NOT NULL , `post_id` INT NOT NULL , `post_type` CHAR(1) NOT NULL , `vote_type` CHAR(1) NOT NULL , PRIMARY KEY (`vote_id`)) ENGINE = InnoDB;

ALTER TABLE `user_posts_votes` ADD CONSTRAINT `userid_vote_fk` FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

CREATE TABLE `testdb`.`notifications` ( `notify_id` INT NOT NULL AUTO_INCREMENT , `notify_text` VARCHAR(200) NOT NULL , `user_id` VARCHAR(50) NOT NULL , `view_ts` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() , `created_ts` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() , `view_flag` INT NOT NULL , PRIMARY KEY (`notify_id`)) ENGINE = InnoDB;

ALTER TABLE `notifications` ADD CONSTRAINT `fk_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

CREATE TABLE `testdb`.`followers` ( `user_id` VARCHAR(50) NOT NULL , `following_user_id` VARCHAR(50) NOT NULL , `created_ts` TIMESTAMP NOT NULL , `last_updt_ts` TIMESTAMP on update CURRENT_TIMESTAMP() NOT NULL ) ENGINE = InnoDB;

ALTER TABLE `followers` ADD CONSTRAINT `user_fk_follower` FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE RESTRICT ON UPDATE RESTRICT; ALTER TABLE `followers` ADD CONSTRAINT `fluser_fk_follower` FOREIGN KEY (`following_user_id`) REFERENCES `users`(`user_id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

alter table followers add constraint uidx_followers unique (user_id,following_user_id);

ALTER TABLE `followers` ADD `follow_id` INT NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY (`follow_id`);