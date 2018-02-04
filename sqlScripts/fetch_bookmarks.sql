CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_bookmarks`( IN in_user_id VARCHAR(50),
															  IN in_start_row INTEGER,
															  IN in_record_count INTEGER,
                                                              IN in_first_call SMALLINT,
                                                              OUT out_record_count	SMALLINT,
															  OUT out_err_cd CHAR(5),
															  OUT out_err_desc VARCHAR(500))
BEGIN
DECLARE v_msg_txt VARCHAR(200) DEFAULT ' '; 
DECLARE v_end_row			INTEGER;
DECLARE v_record_count		INTEGER;

DECLARE EXIT HANDLER FOR SQLEXCEPTION
P1: BEGIN
	GET DIAGNOSTICS CONDITION 1 @err_no=MYSQL_ERRNO,@err_txt=MESSAGE_TEXT;
			select @err_no,@err_txt
			INTO out_err_cd,out_err_desc;

	SET out_err_desc = 'ERROR IN STATEMENT - '|| v_msg_txt;
        
END P1;

    SET out_err_cd = '00000';
    SET out_err_desc = 'Successful';
	SET out_record_count = 0;
	P2: BEGIN
		
        SET v_msg_txt = 'GET TOTAL RECORD COUNT';
		SELECT count(1)
		INTO v_record_count
		FROM bookmarks 
		WHERE user_id=in_user_id
		AND post_type='Q';
		
        SET out_record_count = v_record_count - in_start_row + 1;
        IF out_record_count < 10 THEN
			SET v_end_row = in_start_row + out_record_count - 1;
		ELSE
			SET v_end_row = in_start_row + in_record_count - 1;
		END IF;
        
        SELECT * FROM
		(SELECT t1.bkmrk_id,
			   t1.user_id,
			   t1.post_id,
               t2.qstn_titl,
               t2.qstn_desc,
			   t1.post_type,
			   t1.created_ts,
			   t1.last_updt_ts,
			   @row:=@row+1 as rnum

		FROM (SELECT @row:=0) t,
		bookmarks t1
        INNER JOIN questions t2
        ON t1.post_id = t2.qstn_id
		WHERE t1.user_id=in_user_id
		AND post_type='Q') z
		WHERE z.rnum BETWEEN in_start_row and v_end_row;
        
	END P2;

END
