CREATE DEFINER=`root`@`localhost` FUNCTION `fetch_error_desc`(in_err_cd CHAR(5)) RETURNS varchar(300) CHARSET latin1
BEGIN
	DECLARE v_xcpt_desc		VARCHAR(300);

    SELECT xcpt_desc
    INTO v_xcpt_desc
    FROM exceptions
    WHERE xcpt_cd = in_err_cd;
    
	IF v_xcpt_desc IS NOT NULL AND v_xcpt_desc > ' ' THEN
		RETURN v_xcpt_desc;
	END IF;
	RETURN 'Error Occurred';
END