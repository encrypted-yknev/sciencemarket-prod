CREATE DEFINER=`root`@`localhost` PROCEDURE `add_user_interests`(IN in_tag_list VARCHAR(5000),
																 IN in_user_id  VARCHAR(50),
																 OUT out_err_cd CHAR(5),
																 OUT out_err_desc VARCHAR(500))
BEGIN
	DECLARE v_msg_txt 	VARCHAR(200) DEFAULT ' ';
	DECLARE v_tag_list	VARCHAR(5000);
    DECLARE v_tag_name	VARCHAR(50);
    DECLARE v_tag_id	INT;
	DECLARE v_tag_count INT			 DEFAULT 0;
    
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
	P1: BEGIN
		GET DIAGNOSTICS CONDITION 1 @err_no=MYSQL_ERRNO,@err_txt=MESSAGE_TEXT;
        select @err_no,@err_txt
        INTO out_err_cd,out_err_desc;
		
		SET out_err_desc = 'ERROR IN STATEMENT '||v_msg_txt;
        select @err_no,@err_txt;
	END P1;
	
    SET v_msg_txt = 'DROP TEMP TABLE';
    DROP TEMPORARY TABLE IF EXISTS
		TEMP_TAG_LIST;
        
	SET v_msg_txt = 'CREATE TEMP TABLE';
    CREATE TEMPORARY TABLE TEMP_TAG_LIST(
			TAG_ID		INT,
			TAG_NAME	VARCHAR(50)
            ) ;
            
    SET out_err_cd = '00000';
    SET out_err_desc = 'Successful';
    
	P2: BEGIN
        SET v_msg_txt = 'CHECK TAGS TABLE';
		SET v_tag_list = in_tag_list;
        WHILE CHAR_LENGTH(v_tag_list) > 0 DO
        
			SET v_tag_name = TRIM(SUBSTRING_INDEX(v_tag_list,',',1));
                        
            SET v_msg_txt = 'CHECK IF DUPLICATE TAG EXISTS';
            SELECT COUNT(1)
            INTO v_tag_count
            FROM TEMP_TAG_LIST
            WHERE tag_name = v_tag_name;
            
            IF v_tag_count = 0 THEN
				SET v_msg_txt = 'INSERT INTO TEMP TABLE';            
				INSERT INTO TEMP_TAG_LIST
				(TAG_NAME)
				VALUES
				(v_tag_name);
            END IF;
            
            SET v_tag_list = TRIM(SUBSTRING(v_tag_list,CHAR_LENGTH(v_tag_name)+2));
        
        END WHILE;
		
        SET v_msg_txt = 'INSERT INTO TAGS FOR NEW INTERESTS';
        INSERT INTO tags
        (tag_name,created_by)
        ( SELECT t1.tag_name, in_user_id
          FROM TEMP_TAG_LIST t1
          WHERE NOT EXISTS
		(SELECT 1 FROM tags t2 WHERE t2.tag_name=t1.tag_name ));
        
        SET v_msg_txt = 'UPDATE TAG_ID IN TEMP TABLE';
        UPDATE TEMP_TAG_LIST t1
        SET t1.tag_id=(SELECT t2.tag_id FROM tags t2 WHERE t2.tag_name = t1.tag_name);
        
        
        SET v_msg_txt = 'REMOVE ALL USER INTERESTS FROM USER_TAGS';
        DELETE FROM user_tags
        WHERE user_id = in_user_id;
        
        SET v_msg_txt = 'INSERT INTO USER_TAGS TABLE IF NOT EXISTS';
        
        INSERT INTO user_tags
        (user_id,tag_id)
        (
			SELECT in_user_id,
				   tag_id
			FROM TEMP_TAG_LIST
        );
                
        
		SET v_msg_txt = 'DROP TEMP_TAG_LIST TABLE';
        DROP TABLE TEMP_TAG_LIST;
        
        SET v_msg_txt = 'RETURN FINAL INTEREST LIST';
        SELECT t1.tag_name
        FROM tags t1
        INNER JOIN user_tags t2
        ON t1.tag_id = t2.tag_id
        WHERE t2.user_id = in_user_id;
        
	END P2;
END
