CREATE DEFINER=`root`@`localhost` PROCEDURE `update_user`(IN in_old_user VARCHAR(50),
								IN in_new_user VARCHAR(50),
                                OUT out_err_cd CHAR(5),
                                OUT out_err_desc VARCHAR(500))
BEGIN
	DECLARE v_msg_txt 	VARCHAR(200) DEFAULT ' ';
	
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	P1: BEGIN
		ROLLBACK;
		GET DIAGNOSTICS CONDITION 1 @err_no=MYSQL_ERRNO,@err_txt=MESSAGE_TEXT;
SELECT @err_no, @err_txt INTO out_err_cd , out_err_desc;
		/* SET out_err_cd = MYSQL_ERRNO; */
		SET out_err_desc = 'ERROR IN STATEMENT - '|| v_msg_txt || out_err_desc;
        
	END P1;
	
    SET out_err_cd = '00000';
    SET out_err_desc = 'Successful';
	
	P2: BEGIN
		
        SET v_msg_txt = 'UPDATE USERS TABLE';
		
		UPDATE users 
SET 
    user_id = in_new_user
WHERE
    user_id = in_old_user;
        
	END P2;
END