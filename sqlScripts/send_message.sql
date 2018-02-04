CREATE DEFINER=`root`@`localhost` PROCEDURE `send_message`(IN `in_sender` VARCHAR(50), IN `in_recipient` VARCHAR(50), IN `in_msg_txt` VARCHAR(50000), OUT `out_err_cd` CHAR(5), OUT `out_err_desc` VARCHAR(500))
    NO SQL
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
		
        SET v_msg_txt = 'Validate inputs';
		
        SET in_msg_txt = TRIM(in_msg_txt);
        
        IF LENGTH(in_msg_txt) = 0 THEN
			SET out_err_cd = '00001';
            SET out_err_desc = 'Message is empty';
		ELSE
			SET v_msg_txt = 'Insert into messages table';
			INSERT INTO messages
			(sender_id,
			 recipient_id,
			 msg_text,
			 view_ind,
			 created_ts)
			VALUES
			(in_sender,
			 in_recipient,
			 in_msg_txt,
			 'N',
			 CURRENT_TIMESTAMP);
        END IF;
        
	END P2;
END
