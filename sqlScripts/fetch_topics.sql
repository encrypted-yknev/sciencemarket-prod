CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_topics`(IN in_topic_id INT,
															 OUT out_err_cd CHAR(5),
															 OUT out_err_desc VARCHAR(500))
BEGIN
DECLARE v_msg_txt VARCHAR(200) DEFAULT ' '; 

DECLARE EXIT HANDLER FOR SQLEXCEPTION
	P1: BEGIN
	GET DIAGNOSTICS CONDITION 1 @err_no=MYSQL_ERRNO,@err_txt=MESSAGE_TEXT;
			select @err_no,@err_txt
			INTO out_err_cd,out_err_desc;

	SET out_err_desc = 'ERROR IN STATEMENT - '|| v_msg_txt ;
			
	END P1;

    SET out_err_cd = '00000';
    SET out_err_desc = 'Successful';

	P2: BEGIN
		
        IF in_topic_id = 0 THEN
        
			SET v_msg_txt = 'FETCH PARENT TOPICS';
			SELECT topic_id,
				   topic_code,
				   topic_desc,
				   created_ts,
				   last_updt_ts
			FROM topics
			WHERE parent_topic = 0;
        
        ELSEIF in_topic_id < 0 THEN
			
            SET v_msg_txt = 'FETCH ALL CHILD TOPICS';
			SELECT topic_id,
				   topic_code,
				   topic_desc,
				   created_ts,
				   last_updt_ts
			FROM topics
			WHERE parent_topic <> 0;
            
        ELSEIF in_topic_id > 0 THEN
        
			SET v_msg_txt = 'FETCH CHILD TOPICS';
			SELECT topic_id,
				   topic_code,
				   topic_desc,
				   created_ts,
				   last_updt_ts
			FROM topics
			WHERE parent_topic = in_topic_id;
		
        END IF;
			
	END P2;
END
