CREATE DEFINER=`root`@`localhost` PROCEDURE `updt_bookmarks`( IN in_user_id VARCHAR(50),
															  IN in_post_id INTEGER,
															  IN in_set_flag SMALLINT,
															  OUT out_err_cd CHAR(5),
															  OUT out_err_desc VARCHAR(500))
BEGIN
DECLARE v_msg_txt VARCHAR(200) DEFAULT ' '; 
DECLARE v_bkmrk_cnt			INTEGER;

DECLARE EXIT HANDLER FOR SQLEXCEPTION
P1: BEGIN
	ROLLBACK;
	GET DIAGNOSTICS CONDITION 1 @err_no=MYSQL_ERRNO,@err_txt=MESSAGE_TEXT;
			select @err_no,@err_txt
			INTO out_err_cd,out_err_desc;

	SET out_err_desc = 'ERROR IN STATEMENT - '|| v_msg_txt || out_err_desc;
        
END P1;

    SET out_err_cd = '00000';
    SET out_err_desc = 'Successful';

	P2: BEGIN
		
        IF in_set_flag = 0 THEN
			SET v_msg_txt = 'CHECK IF USER ALREADY BOOKMARKED THE POST'; 
            SELECT COUNT(1)
            INTO v_bkmrk_cnt
            FROM bookmarks
            WHERE user_id = in_user_id
              AND post_id = in_post_id
              AND post_type = 'Q';
            
            IF v_bkmrk_cnt > 0 THEN
				SET out_err_cd = '00010';
				LEAVE P2;
            END IF;
            
            SET v_msg_txt = 'ADD BOOKMARKS'; 
            INSERT INTO bookmarks
				(user_id
                ,post_id
                ,post_type
                )
			VALUES
				(in_user_id
                ,in_post_id
                ,'Q'
                );
                
        ELSEIF in_set_flag = 1 THEN
			SET v_msg_txt = 'CHECK IF USER ALREADY REMOVED THE BOOKMARK'; 
            SELECT COUNT(1)
            INTO v_bkmrk_cnt
            FROM bookmarks
            WHERE user_id = in_user_id
              AND post_id = in_post_id
              AND post_type = 'Q';
            
            IF v_bkmrk_cnt = 0 THEN
				SET out_err_cd = '00011';
				LEAVE P2;
            END IF;
            
            SET v_msg_txt = 'REMOVE BOOKMARK'; 
            DELETE FROM bookmarks
            WHERE user_id = in_user_id
              AND post_id = in_post_id
              AND post_type = 'Q';
              
        END IF;
	END P2;
	IF out_err_cd <> '00000' THEN
		SET v_msg_txt = 'Get error desc from fetch_err_desc';
		SET out_err_desc = fetch_error_desc(out_err_cd);
	END IF;
END
