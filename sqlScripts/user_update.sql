CREATE DEFINER=`root`@`localhost` PROCEDURE `user_update`(IN in_user_id VARCHAR(50),
														  IN in_request_type INTEGER,
														  IN in_user_dtls LONGTEXT,
														  OUT out_err_cd CHAR(5),
														  OUT out_err_desc VARCHAR(500))
BEGIN
DECLARE v_msg_txt VARCHAR(200) DEFAULT ' '; 
DECLARE v_user_dtls_json	JSON;
DECLARE v_user_id	VARCHAR(50);
DECLARE v_user 		VARCHAR(50);
DECLARE v_user_mob	BIGINT;
DECLARE v_user_name VARCHAR(200);
DECLARE v_user_mail CHAR(50);
DECLARE v_user_location		VARCHAR(100);
DECLARE v_user_desc			VARCHAR(2000);
DECLARE v_user_shrt_bio		VARCHAR(100);
DECLARE v_user_dob			DATE;
DECLARE v_user_interest		VARCHAR(5000);
DECLARE v_user_old_p		CHAR(32);
DECLARE v_user_new_p		CHAR(32);
DECLARE v_user_cnf_p		CHAR(32);
DECLARE v_user_enc_pwd		CHAR(32);
DECLARE v_enc_pwd			CHAR(32);
DECLARE v_user_actv_chk		INTEGER;
DECLARE v_user_cnt			INTEGER;

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

		SET v_msg_txt = 'CAST VARCHAR TO JSON DATATYPE'; 
		SET v_user_dtls_json=CAST(in_user_dtls as JSON);
		
		IF IN_REQUEST_TYPE = 1 THEN
			SET v_msg_txt = 'FETCH USER DETAILS VALUES FROM JSON'; 
			SELECT v_user_dtls_json->>'$.request_dtls.user',
				   v_user_dtls_json->>'$.request_dtls.name',
				   v_user_dtls_json->>'$.request_dtls.mob',
				   v_user_dtls_json->>'$.request_dtls.mail',
				   v_user_dtls_json->>'$.request_dtls.location',
				   v_user_dtls_json->>'$.request_dtls.desc',
				   v_user_dtls_json->>'$.request_dtls.shrt_bio',
				   v_user_dtls_json->>'$.request_dtls.dob'	
							
			INTO v_user,
				 v_user_name,
				 v_user_mob,
				 v_user_mail,
				 v_user_location,
				 v_user_desc,
				 v_user_shrt_bio,
				 v_user_dob;
			
            IF v_user <> in_user_id THEN
            
				SET v_msg_txt = 'CHECK IF USER ALREADY EXISTS'; 
                SELECT COUNT(1)
                INTO v_user_cnt
                FROM users
                WHERE user_id = v_user;
                
                IF v_user_cnt > 0 THEN
					SET out_err_cd='00002';
                    LEAVE P2;
                END IF;
                
				SET v_msg_txt = 'UPDATE USER_ID IF CHANGED'; 
				UPDATE users
				SET user_id = v_user
				WHERE user_id = in_user_id;
                
            END IF;
            
			SET v_msg_txt = 'UPDATE USERS TABLE'; 
			UPDATE users
			SET disp_name = v_user_name,
				ph_num = v_user_mob,
				email_addr = v_user_mail,
				location = v_user_location,
		 	    description = v_user_desc,
			    shrt_bio = v_user_shrt_bio,
                dob=v_user_dob
			WHERE user_id = v_user; 
		
		ELSEIF IN_REQUEST_TYPE = 2 THEN
			SET v_msg_txt = 'Fetch Interest list from JSON';
			SELECT v_user_dtls_json->>'$.request_dtls.interest_list'
			INTO v_user_interest;
			
			SET v_msg_txt = 'CALL add_user_interests'; 
			CALL add_user_interests(v_user_interest,
									in_user_id,
									out_err_cd,
									out_err_desc);	
			
			IF out_err_cd <> '00000' THEN
				LEAVE P2;
			END IF;
			
		ELSEIF IN_REQUEST_TYPE = 3 THEN
			SET v_msg_txt = 'Fetch Interest list from JSON';
			SELECT v_user_dtls_json->>'$.request_dtls.old_pwd',
				   v_user_dtls_json->>'$.request_dtls.new_pwd',
				   v_user_dtls_json->>'$.request_dtls.confirm_pwd'
			INTO v_user_old_p,
				 v_user_new_p,
				 v_user_cnf_p;
			
            IF (v_user_old_p IS NOT NULL AND v_user_old_p > ' ') AND 
               (v_user_old_p IS NOT NULL AND v_user_old_p > ' ') AND 
               (v_user_old_p IS NOT NULL AND v_user_old_p > ' ') THEN
               
				SET v_msg_txt = 'Fetch encrypt pwd from DB';
				SELECT encrypt_pwd 
				INTO v_enc_pwd
				FROM users
				WHERE user_id = in_user_id;
				
				SET v_msg_txt = 'Compare pwd from DB';
				IF v_user_old_p <> v_enc_pwd THEN
					SET out_err_cd = '00004';
					LEAVE P2;
				END IF;
				
				SET v_msg_txt = 'Compare new and confirm pwd';
				IF v_user_new_p <> v_user_cnf_p THEN
					SET out_err_cd = '00003';
					LEAVE P2;
				END IF;
				
				SET v_msg_txt = 'Update users with updated pwd';
				UPDATE users
				SET encrypt_pwd = v_user_new_p
				WHERE user_id = in_user_id;
			
            ELSE
				SET out_err_cd = '00009';
                LEAVE P2;
                
            END IF;
			
		ELSEIF IN_REQUEST_TYPE = 4 THEN
			SET v_msg_txt = 'Fetch Interest list from JSON';
			SELECT v_user_dtls_json->>'$.request_dtls.encrypt_pwd'
			INTO v_user_enc_pwd;
			
			SET v_msg_txt = 'Fetch encrypt pwd from DB';
			SELECT encrypt_pwd 
			INTO v_enc_pwd
			FROM users
			WHERE user_id = in_user_id;
			
			SET v_msg_txt = 'Compare pwd from DB';
			IF v_user_enc_pwd <> v_enc_pwd THEN
				SET out_err_cd = '00004';
				LEAVE P2;
			END IF;
			
			SET v_msg_txt = 'Validate user account';
            
			SELECT count(1)
			INTO v_user_actv_chk
			FROM users
			WHERE user_id = in_user_id
			  AND status = 'I';
			
			IF v_user_actv_chk > 0 THEN
				SET out_err_cd = '00008';
				LEAVE P2;
			END IF;

			SET v_msg_txt = 'Deactivate user account';
			UPDATE users
			SET status = 'I'
			WHERE user_id = in_user_id;
			
		ELSE
		
			SET out_err_cd = '00007';
				LEAVE P2;
				
		END IF;
		
	END P2;
	IF out_err_cd <> '00000' THEN
		SET v_msg_txt = 'Get error desc from fetch_err_desc';
		SET out_err_desc = fetch_error_desc(out_err_cd);
	END IF;
END
