CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_experts`(IN in_topic_id INT,
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

		SET v_msg_txt = 'FETCH EXPERTS IN A TOPIC';

		select t1.topic_id,
			   t1.topic_desc,
			   sum(t3.up_votes) as total_upvotes,
			   sum(t3.down_votes) as total_downvotes,
			   (sum(t3.up_votes)-sum(t3.down_votes)) as expertise_score,
			   t4.user_id,
               t4.disp_name,
               t4.up_votes,
               t4.down_votes,
               t4.shrt_bio,
               t4.pro_img_url
		from topics t1
		inner join questions t2
		   on t1.topic_id = t2.topic_id
		   and t1.parent_topic <> 0
		inner join answers t3
		   on t3.qstn_id = t2.qstn_id
		inner join users t4
		   on t4.user_id = t3.posted_by
		where t1.topic_id = in_topic_id
		group by t1.topic_id,t1.topic_desc,t4.user_id,t4.disp_name,t4.up_votes,t4.down_votes,t4.shrt_bio,t4.pro_img_url
		order by t1.topic_id,expertise_score desc;
			
	END P2;
END
