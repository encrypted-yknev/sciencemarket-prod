CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_user_dtls`(IN in_user_id VARCHAR(50),
                                OUT out_err_cd CHAR(5),
                                OUT out_err_desc VARCHAR(500))
BEGIN
	DECLARE v_msg_txt 	VARCHAR(200) DEFAULT ' '; 
	
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
		
        SET v_msg_txt = 'FETCH USER DETAILS FOR USER_ID - '||in_user_id;
		
		SELECT * 
        FROM users
		WHERE user_id = in_user_id;
        
	END P2;
END
