<?php
/** 
 * Box_model
 * 
 * @author Ray Nowell
 *	
 */ 
class Box_model extends CI_Model {

	function Box_model()
	{
		parent::__construct();
	}
        
	public function get_box_tiers($box_id = 0)
	{
		$this->db->order_by('tier_order', 'asc');
		$query = $this->db->get_where('box_wod_tier', array('box_id' => $box_id));
		if ($query->num_rows() == 0)
			return false;
		
		return $query->result_array();
	}

	function get_list_of_todays_wods($box_id = 0)
	{
		$member_id	=	$this->session->userdata('member_id');
		$todays_date = date( 'Y-m-d');
		$sql =	"
					SELECT bw.box_id,
						 bw.bw_id
						,CASE WHEN IFNULL(tier_name,'') = '' THEN simple_title
							ELSE (CONCAT_WS(': ',IFNULL(tier_name,''),simple_title)) END AS tier_and_wod
						,CASE WHEN IFNULL(mw.mw_id,0) = 0 THEN 0 ELSE 1 END AS user_saved
					FROM 
						box_wod bw 
							LEFT JOIN 
								box_wod_tier bwt ON 
									bw.bwt_id = bwt.bwt_id
							LEFT JOIN
								member_wod mw ON 
									bw.bw_id = mw.bw_id AND
									mw.member_id = ".$member_id." 
					WHERE 
						bw.wod_date = '".$todays_date."' AND
						bw.box_id = ".$box_id."
					ORDER BY 
						 IFNULL(tier_order,0)
						,simple_title
				";

		$query = $this->db->query($sql);
		
		if ($query->num_rows() == 0)
				return false;
		
		return $query->result_array();
	}
	
	function is_scale_option_rx($scale_option_id = 0)
	{
		$sql =	"
				SELECT 
					  rx
				FROM
					scale_option
									WHERE
											so_id = ".$scale_option_id;


		$query = $this->db->query($sql);

		if ($query->num_rows() == 0)
				return false;

		$result = $query->result_array();
		$row = $result[0];
		$ret_val = $row['rx'];
		return  $ret_val;

	}
    function get_box_class_time_list($bw_id = 0)
	{
		$sql =	"
					SELECT 
						  bct_id
						, `class_time_description`
					FROM
						box_class_time bct
                                        WHERE
                                                bct.box_id = (SELECT box_id from box_wod WHERE bw_id = $bw_id)
					ORDER BY 
						class_time";

		$query = $this->db->query($sql);
		
		if ($query->num_rows() == 0)
			return false;
		
		return	$query->result_array();		
	}
	function get_scale_option_list($scale_id = 0)
	{		
		$sql =	"
					SELECT 
						  so_id
						, `option`
					FROM
						scale_option
                                        WHERE
                                                scale_id = $scale_id
					ORDER BY 
						scale_order,
						`option`";

		$query = $this->db->query($sql);
		
		if ($query->num_rows() == 0)
			return false;
		
		return	$query->result_array();
	}
	
	public function get_box_wod_rating($bw_id = 0)
	{
		//Need the count of total members who have performed this WoD to show percentages of ratings
		//I first tried to do this in one statement, but CI didn't like it.
		//I then started looking into a proc, but decided against it.  just doing it this way.
		$sql = "SELECT COUNT(bw_id) as bw_count FROM member_wod m1 WHERE m1.bw_id=".$bw_id;
		$query = $this->db->query($sql);
		$bw_count_array	=	$query->result_array();
		$bw_count = $bw_count_array[0]['bw_count'];
		$sql	=	"SELECT 
						mw.bw_id,
						CASE WHEN IFNULL(mw.member_rating,0) = 5 THEN '5 - Awesome' 
							 WHEN IFNULL(mw.member_rating,0) = 4 THEN '4 - Fun' 
							 WHEN IFNULL(mw.member_rating,0) = 3 THEN '3 - Ok' 
							 WHEN IFNULL(mw.member_rating,0) = 2 THEN '2 - Meh' 
							 WHEN IFNULL(mw.member_rating,0) = 1 THEN '1 - No way'
							 WHEN IFNULL(mw.member_rating,0) = 0 THEN '0 - Unrated'
							 ELSE IFNULL(mw.member_rating,0) END AS rating,
						COUNT(mw.member_rating) AS votes,
						CONCAT(FORMAT((COUNT(mw.member_rating)) / ".$bw_count." * 100,2),'%') AS percentage
					FROM 
						member_wod mw 
					WHERE 
						mw.bw_id = ".$bw_id."  
					GROUP BY 
						mw.bw_id, mw.member_rating
					ORDER BY 
						rating DESC";

		$query = $this->db->query($sql);
		
		return	$query->result_array();

	}
	
	/*
	 * Inserts or updates a member wod saved from kiosk mode
	 */
	public function save_kiosk_wod($data, $display_name)
	{
		$update_record	=	false;
		$mw_id	=	0;
		$query = $this->db->limit(1)->get_where('member_wod', array('member_id' =>	$data['member_id'],
																	'bw_id'		=>	$data['bw_id']));
		
		if ($query->num_rows() != 0)
		{
			$update_record	=	true;
			$data['modified_date'] = date('Y-m-d H:i:s');
			$data['modified_by'] = $display_name.' - kiosk save';
			$mw_id	=	$query->row()->mw_id;
		}
		
		if ($update_record)
			$this->db->update('member_wod',	$data, 'mw_id = '.$mw_id);
		else
		{
			$data['created_date'] = date('Y-m-d H:i:s');
			$data['created_by'] = $display_name.' - kiosk save';
			$data['modified_date'] = date('Y-m-d H:i:s');
			$data['modified_by'] = $display_name.' - kiosk save';
			$this->db->insert('member_wod', $data);
		}
		
		return array('success'  =>  true);
	}
    
	//Returns basic info for a CrossFit gym
	public function get_box_info($box_id = 0)
	{
		$query = $this->db->limit(1)->get_where('box', array('box_id' => $box_id));
		if ($query->num_rows() == 0)
			return false;
		
		return $query->row();
	}
	
	public function get_power_users($inactive_users = FALSE)
	{
		if ($inactive_users)
		{
			$having_clause = "wod_count > 50 AND 
								max_modified_date
								< (CURDATE() - INTERVAL 21 DAY)";
			$order_by_field = 'max_modified_date';
		}
		else
		{
			$having_clause = "max_modified_date
								BETWEEN (CURDATE() - INTERVAL 21 DAY) AND (CURDATE() + INTERVAL 1 DAY)";
			$order_by_field = 'wod_count';
		}
		$sql	=	"
						SELECT 
							 CONCAT (m.first_name,' ',m.last_name) AS user_name
							,b.box_name
							,MAX(mw.modified_date) AS max_modified_date
							,COUNT(mw.member_id) AS wod_count 
						FROM 
						   member m
							   INNER JOIN
								   member_wod mw ON
									   m.member_id = mw.member_id
							   LEFT JOIN
								   box b ON
									   m.box_id = b.box_id
						GROUP BY 
							 mw.member_id
							,m.first_name
							,m.last_name
							,b.box_name
						HAVING 
						".$having_clause." 
						ORDER BY ".$order_by_field." DESC";
		
		$query = $this->db->query($sql);
		
		return	$query->result_array();
	}
    public function active_member_counts()
	{
		$sql	=	"
                                    SELECT 
                                            'wods' AS active_member_type, count(distinct member_id) as member_count
                                    FROM 
                                            member_wod
                                    WHERE 
                                            created_date BETWEEN (CURDATE() - INTERVAL 21 DAY) AND CURDATE()

                                            UNION

                                    SELECT  
                                            'max' AS active_member_type, count(distinct member_id) as member_count
                                    FROM 
                                            member_max
                                    WHERE 
                                            created_date BETWEEN (CURDATE() - INTERVAL 21 DAY) AND CURDATE()

                                            UNION

                                    SELECT  
                                            'paleo' AS active_member_type, count(distinct member_id) as member_count
                                    FROM 
                                            member_paleo
                                    WHERE 
                                            created_date BETWEEN (CURDATE() - INTERVAL 21 DAY) AND CURDATE()

                                            UNION

                                    SELECT  
                                            'weight_log' AS active_member_type, count(distinct member_id) as member_count
                                    FROM 
                                            member_weight_log
                                    WHERE 
                                            created_date BETWEEN (CURDATE() - INTERVAL 21 DAY) AND CURDATE()

                                    ORDER BY 
                                        member_count desc
					";
		
		$query = $this->db->query($sql);
		
		return	$query->result_array();
	}
	
	public function site_counts()
	{
		$sql	=	"
						SELECT 'Facilities' AS metric, count(*) as the_count FROM box WHERE box_name NOT IN ('other','n/a')
							UNION
						SELECT 'Members' as metric, count(*) as the_count FROM member
							UNION
						SELECT 'WODs' as metric, count(*) as the_count FROM member_wod
							UNION
						SELECT 'Maxes' as metric, count(*) as the_count FROM member_max 
							UNION
						SELECT 'Event Info' as metric, count(*) as the_count FROM member_event_info 
							UNION
						SELECT 'Event WODs' as metric, count(*) as the_count FROM member_event_wod 
							UNION
						SELECT 'Weight Log' as metric, count(*) as the_count FROM member_weight_log
							UNION
						SELECT 'Paleo Meals' as metric, count(*) as the_count FROM member_paleo
					";
		
		$query = $this->db->query($sql);
		
		return	$query->result_array();
	}
	
	public function get_public_box_wods($box_id	=	0, $limit	=	5, $tier = '', $custom_order_by = '')
	{
                $box_id = is_numeric($box_id) ? $box_id : 0;
                
		$tier_where = $tier === '' ? '' : ' AND bwt_id = '.$tier.' ';
		$order_by = $custom_order_by === '' ? 'wod_date DESC' : $custom_order_by;
		$sql = "
				SELECT  
					DATE_FORMAT(wod_date,'%Y') year_value,
					DATE_FORMAT(wod_date,'%M') AS month_year_value,
					DATE_FORMAT(wod_date,'%W, %M %e, %Y') AS wod_date_long_format,
					CASE WHEN LENGTH(TRIM(IFNULL(buy_in,''))) = 0 THEN '' ELSE  CONCAT('<h1>Buy In</h1>'
							,buy_in
						) END AS buy_in,
					CONCAT('<h1>WoD</h1>','<span class=\"wod-title\">',simple_title,'</span><br>', simple_description) AS wod,
					CASE WHEN LENGTH(TRIM(IFNULL(cash_out,''))) = 0 THEN '' ELSE  CONCAT_WS('<br>'
							,'<h1>Cash Out</h1>'
							,cash_out
						) END AS cash_out,
					IFNULL(daily_message,'') AS daily_message,
					IFNULL(image_name, '') AS image_name,
					IFNULL(image_link, '') AS image_link,
					IFNULL(image_caption, '') AS image_caption,
					wod_date AS wod_date_raw,
					buy_in AS buy_in_raw,
					simple_title AS wod_title_raw,
					simple_description AS wod_raw,
					cash_out AS cash_out_raw
				FROM 
					box_wod 
				WHERE 
					box_id = ".$box_id." ".$tier_where."
				ORDER BY 
					 ".$order_by." 
				LIMIT ".$limit;

		$query = $this->db->query($sql);

		if ($query->num_rows() == 0)
			return FALSE;
		
		return	$query->result_array();
	}
	public function get_box_social_media_data($box_id	=	0)
	{
		$sql	=	"
						SELECT
							 sm_package
							,twitter_id
							,facebook_page_id
						FROM 
							box
						WHERE 
							box_id = '".$box_id."'";
		
		$query = $this->db->query($sql);

		if ($query->num_rows() == 0)
			return FALSE;
		
		//Just want to return the first row
		$my_array	=	$query->result_array();
		return	$my_array[0];		
	}
	
	public function get_member_max_count($box_id	=	0)
	{
		$member_id	=	$this->session->userdata('member_id');
		$sql	=	"	
						SELECT 
							 m.member_id
							,CONCAT_WS(' ',first_name,last_name) AS full_name 
							,COUNT(*) AS max_saved_count
						FROM 
							member m INNER JOIN 
								member_max mm
									ON m.member_id = mm.member_id 
						WHERE 
							m.box_id = ".$box_id." 
						GROUP BY 
							member_id
						ORDER BY 
							max_saved_count DESC
					";
		
		$query = $this->db->query($sql);

		if ($query->num_rows() == 0)
			return FALSE;

		return	$query->result_array();

		
	}
	
	public function get_member_wod_count($box_id	=	0)
	{
		$member_id	=	$this->session->userdata('member_id');
		$sql	=	"	
						SELECT 
							 m.member_id
							,CONCAT_WS(' ',first_name,last_name) AS full_name 
							,SUM(IF(IFNULL(mw.bw_id,0) <> 0, 1, 0)) AS box_wod_count
						FROM 
							member m 
								INNER JOIN 
									member_wod mw ON 
										m.member_id = mw.member_id 
											INNER JOIN
												box_wod bw ON
													mw.bw_id = bw.bw_id
						WHERE 
							m.box_id = ".$box_id." AND
							bw.box_id = ".$box_id." 
						GROUP BY 
							member_id
						ORDER BY 
							box_wod_count DESC
					";
		
		$query = $this->db->query($sql);

		if ($query->num_rows() == 0)
			return FALSE;

		return	$query->result_array();

		
	}
	
	public function get_box_wod_rank($bw_id	=	0)
	{
		$member_id	=	$this->session->userdata('member_id');
		$sql	=	"	
						SELECT 
                                                        m.member_id
                                                       ,CONCAT_WS(' ',first_name,CONCAT(LEFT(last_name,1) ,'.')) AS full_name
                                                       ,CASE WHEN IFNULL(bw.score_type,w.score_type) = 'T' 
                                                       THEN 
                                                       (
                                                               CONCAT_WS(':',FLOOR(mw.score / 60),
                                                               LPAD( CAST((mw.score - FLOOR(mw.score / 60) * 60) AS CHAR(2))   ,2,'0'))
                                                       )
                                                       ELSE mw.score
                                                       END AS score
                                                       ,m.box_id
                                                       ,CASE WHEN IFNULL(s.scale_name,'') = ''
                                                        THEN
                                                        (
                                                               'RX/Scaled'
                                                        )
                                                        ELSE s.scale_name
                                                        END AS scale
                                                       ,CASE 
                                                       WHEN IFNULL(s.scale_name,'') = 'No Scale' THEN 'No Scale'
                                                       WHEN IFNULL(so.option,'') = ''
                                                        THEN
                                                        (
                                                               CASE WHEN mw.rx = 1 THEN 'RX' ELSE 'Scaled' END
                                                        )
                                                        ELSE so.`option`
                                                        END AS scale_option
                                                        ,CASE 
                                                        WHEN IFNULL(s.scale_name,'') = 'No Scale' THEN 1
                                                        WHEN IFNULL(so.scale_order,'') = ''
                                                        THEN
                                                        (
                                                               CASE WHEN mw.rx = 1 THEN 1 ELSE 999 END
                                                        )
                                                        ELSE so.scale_order
                                                        END AS scale_order
                                               FROM 
                                                       member m 
                                                               INNER JOIN 
                                                                       member_wod mw ON 
                                                                               m.member_id = mw.member_id 
                                                                       LEFT JOIN
                                                                               scale_option so ON
                                                                                       mw.so_id = so.so_id
                                                                       INNER JOIN
                                                                               box_wod bw ON
                                                                                       bw.bw_id = mw.bw_id
                                                                                               LEFT JOIN
                                                                                                       wod w ON
                                                                                                               bw.wod_id = w.wod_id
                                                                                               LEFT JOIN 
                                                                                                       scale s ON
                                                                                                               bw.scale_id = s.scale_id
						WHERE
									 m.box_id = (SELECT box_id FROM member WHERE member_id = ".$member_id.") AND 
									 bw.bw_id = ".$bw_id."
						ORDER BY
							scale_order,
							#Sort descending when Reps/Round Count or Weight.  Sort ascending for time
							CASE WHEN IFNULL(bw.score_type,w.score_type) <> 'T' THEN CAST(score AS DECIMAL(7,1)) * -1 ELSE CAST(score AS DECIMAL(7,1)) END
					";
		
		$query = $this->db->query($sql);	

		if ($query->num_rows() == 0)
			return false;
		
		return	$query->result_array();

	}

	public function get_box_wods_for_admin()
	{
		$sql	=	"SELECT
							b.box_id
						   ,concat_ws(' - ',box_name,location) AS box
						   ,bw.bw_id
						   ,simple_title
						   ,wod_date 
					  FROM 
						   box b 
							   inner join 
								   box_wod bw on b.box_id = bw.box_id 
					  ORDER BY
						   box, 
						   wod_date desc";
		
		$query = $this->db->query($sql);

		if ($query->num_rows() == 0)
			return FALSE;

		return	$query->result_array();
	}
	
	public function get_member_exercises_for_staff($box_id = 0)
	{
		$sql	=	"	SELECT 
							e.exercise_id
						   ,e.title AS exercise
						   ,COUNT(DISTINCT mm.member_id) AS member_count
					   FROM 
						   exercise e
							   INNER JOIN
								   member_max mm ON
									   e.exercise_id = mm.exercise_id
									   INNER JOIN
										   member m ON
											   mm.member_id = m.member_id

					   WHERE
						   m.box_id = ".$box_id." AND
							IFNULL(mm.max_rep,1) = 1
					GROUP BY 
						e.exercise_id
					ORDER BY
						e.title";
		$query = $this->db->query($sql);

		if ($query->num_rows() == 0)
			return FALSE;

		return	$query->result_array();
		
	}
	public function get_daily_box_wod_details($box_id = 0)
	{
		$sql	=	"SELECT 
                                    CASE WHEN IFNULL(bw.simple_title,'') = '' THEN 'No name given' ELSE simple_title END AS simple_title
                                   ,bw.wod_date
                                   ,CONCAT_WS(' ',first_name,last_name) AS full_name
                                   ,CASE WHEN IFNULL(bw.score_type,w.score_type) = 'T' 
                                           THEN 
                                           (
                                                   CONCAT_WS(':',FLOOR(mw.score / 60),
                                                   LPAD( CAST((mw.score - FLOOR(mw.score / 60) * 60) AS CHAR(2))   ,2,'0'))
                                           )
                                           ELSE mw.score
                                           END AS score
                                   ,bw.bw_id
                                   ,IFNULL(bw.score_type,w.score_type) AS score_type
                                   ,CASE WHEN IFNULL(s.scale_name,'') = ''
                                           THEN
                                           (
                                                  'RX/Scaled'
                                           )
                                           ELSE s.scale_name
                                           END AS scale
                                          ,CASE 
                                           WHEN IFNULL(s.scale_name,'') = 'No Scale' THEN 'No Scale'
                                           WHEN IFNULL(so.option,'') = ''
                                           THEN
                                           (
                                                  CASE WHEN mw.rx = 1 THEN 'RX' ELSE 'Scaled' END
                                           )
                                           ELSE so.`option`
                                           END AS scale_option
                                           ,CASE 
                                           WHEN IFNULL(s.scale_name,'') = 'No Scale' THEN 1
                                           WHEN IFNULL(so.scale_order,'') = ''
                                           THEN
                                           (
                                                  CASE WHEN mw.rx = 1 THEN 1 ELSE 999 END
                                           )
                                           ELSE so.scale_order
                                           END AS scale_order
					FROM 
						member m 
							INNER JOIN 
								member_wod mw ON 
									m.member_id = mw.member_id 
								LEFT JOIN
                                                                               scale_option so ON
                                                                                       mw.so_id = so.so_id
								INNER JOIN
									box_wod bw ON
										bw.bw_id = mw.bw_id
											LEFT JOIN
												wod w ON
													bw.wod_id = w.wod_id
											LEFT JOIN 
                                                                                                       scale s ON
                                                                                                               bw.scale_id = s.scale_id
					WHERE
						bw.box_id = ".$box_id." AND
                                                bw.wod_date BETWEEN (CURDATE() - INTERVAL 31 DAY) AND CURDATE()
					ORDER BY
						wod_date,
						#Every WOD gets returned for the box, (bw_id), so group by that first
						bw_id,
						scale_order,
						#Sort descending when Reps/Round Count or Weight.  Sort ascending for time
						CASE WHEN IFNULL(bw.score_type,w.score_type) <> 'T' THEN CAST(score AS DECIMAL(7,1)) * -1 ELSE CAST(score AS DECIMAL(7,1)) END
                                        
					";
		
		$query = $this->db->query($sql);

		if ($query->num_rows() == 0)
			return FALSE;

		return	$query->result_array();

	
	}
	
	//Get list of box wods for staff by box.  
	public function get_box_wods_for_staff($box_id = 0)
	{
		$sql	=	"SELECT  
						 bw.bw_id
						,bw.wod_date
						,CASE WHEN IFNULL(bw.simple_title,'') = '' THEN 'No name given' ELSE simple_title END AS simple_title
						,IFNULL(bwt.tier_name,'') AS tier_name
					FROM 
						box_wod bw
							LEFT JOIN 
								box_wod_tier bwt ON
									bw.bwt_id = bwt.bwt_id
					WHERE
						bw.box_id = ".$box_id."
					ORDER BY 
						wod_date DESC";

		$query = $this->db->query($sql);

		if ($query->num_rows() == 0)
			return FALSE;

		return	$query->result_array();
	}
	
	public function get_leader_board_for_staff($box_id = 0)
	{
		$sql	=	"
						SELECT 
							 wod_id
							,member_id
							,title AS wod_name
							,full_name
							,score
							,wod_date
						FROM 
							view_box_wod_leaders
						WHERE 
							member_box_id	=	".$box_id." AND 
							box_wod_box_id	=	".$box_id." AND 
							rx = 1
						ORDER BY 
							title, scoreOrder, wod_date DESC, full_name
					   ";
		
		$query = $this->db->query($sql);

		if ($query->num_rows() == 0)
			return FALSE;

		return	$query->result_array();
	}
	//daily results of member wods by box
	public function get_daily_box_wods_for_staff($box_id = 0)
	{
		$sql	=	"SELECT  
						 bw.bw_id
						,bw.wod_date
						,CASE WHEN IFNULL(bw.simple_title,'') = '' THEN 'No name given' ELSE simple_title END AS simple_title
						,COUNT(mw.bw_id) AS member_count
					FROM 
						box_wod bw
							INNER JOIN
								member_wod mw	ON 
									bw.bw_id = mw.bw_id
					WHERE
						bw.box_id = ".$box_id." AND
                                                bw.wod_date BETWEEN (CURDATE() - INTERVAL 31 DAY) AND CURDATE()
					GROUP BY 
						mw.bw_id
					ORDER BY 
						wod_date DESC";

		$query = $this->db->query($sql);

		if ($query->num_rows() == 0)
			return FALSE;

		return	$query->result_array();
	}
	
	public function save_member_box_wod($data)
	{
		//remove existing record first
		//REN NOTE 06/8/2013:  I *think* had a good reason for this at the time, though I don't recall now.
		//                     leaving this code in tact, but think it should be an update where applicable.
		//                     See save on kiosk for more info
		$sql	=	"DELETE FROM member_wod WHERE member_id =".$data['member_id']." AND bw_id = ".$data['bw_id'];
		$query = $this->db->query($sql);
		
		$data['modified_date']	=	date("Y-m-d H:i:s");
		$data['modified_by']	=	'N/A deleted every time';
		$data['created_date'] = date('Y-m-d H:i:s');
		$data['created_by'] = $this->session->userdata('display_name');
		
		//Now Add the record
		$this->db->insert('member_wod', $data);
		return array('success'  =>  true);	
	}
	
	public function get_box_wod($bw_id = 0) 
	{
		$query = $this->db->limit(1)->get_where('box_wod', array('bw_id' => $bw_id));
		if ($query->num_rows() == 0)
			return false;
		
		return $query->row();
	}
	/*
	 * Gets a box wod based on the boxid and current date
	 */
	public function get_box_wod_for_kiosk($box_id = 0) 
	{
		$current_date = date('Y-m-d');
		
		$sql = "SELECT 
					 bw_id
					,buy_in
					,simple_description
					,simple_title
					,cash_out
					,score_type
					,bw_id
					,tier_name
				FROM 
					box_wod bw
						LEFT JOIN box_wod_tier t ON
							bw.bwt_id = t.bwt_id
				WHERE
					bw.wod_date = '".$current_date."' AND
					bw.box_id = ".$box_id." 
				ORDER BY 
					tier_name,
					simple_title";
		
		$query = $this->db->query($sql);
				
		if ($query->num_rows() == 0)
			return FALSE;
		
		return $query->result_array();
	}
	
	public function save_box_wod($data = false, $box_id = 0)
	{
		if($data['box_id'] != $box_id)
		{
			return array(	'success'  =>  false,
							'message'	=>	'Box mismatch');
		}
		
		$data['modified_date']	=	date("Y-m-d H:i:s");
		$data['modified_by']	=	$this->session->userdata('display_name');

		if (!isset($data['bw_id']))
		{
			$save_insert = true;
			if(isset($data['form_uniqid']) && strlen($data['form_uniqid']) > 0)
			{
				//HACK to Prevent duplicate save
				$query = $this->db->get_where('box_wod', array('form_uniqid' => $data['form_uniqid']));
				$save_insert	=	$query->num_rows() > 0 ? false : true;
			}
			
			if ($save_insert)
			{
				$data['created_date'] = date('Y-m-d H:i:s');
				$data['created_by'] = $this->session->userdata('display_name');
				$this->db->insert('box_wod', $data);
			}
			else
			{
				//START AUDIT
				$this->load->model('Audit_model');
				$audit_data['controller']	=	'box_model';
				$audit_data['ip_address']	=	$_SERVER['REMOTE_ADDR'];
				$audit_data['member_id']	=	$this->session->userdata('member_id');
				$audit_data['member_name']	=	$this->session->userdata('display_name');
				$audit_data['short_description']	=	'Suspect Duplicate Save';
				$full_info = '';
				foreach ($data as $key => $value) {
					$value = str_replace("\n", "|", $value);
					$full_info .= 'Key: '.$key.'; Value: '.$value.'\r\n';
				}
				$audit_data['full_info']	=	$full_info;
				$this->Audit_model->save_audit_log($audit_data);
				//END AUDIT
				return array('success'  =>  true, 'suspect_duplicate' => true);
			}
		}
		else
			$this->db->update('box_wod',	$data, 'bw_id = '.$data['bw_id']);
		
		return array('success'  =>  true, 'suspect_duplicate' => false);
	}
	
	//Gets the wod info of the member's box 
	//and the member's saved info (if applicable)
	public function get_member_box_wod($member_id = 0, $box_wod_id = 0)
	{
		$sql	=	"SELECT 
						 bw.box_id
						,bw.bw_id
						,bw.wod_date
						#box_wod.simple_title overrides wod.title; unless simple_title is empty and wod_id is not
						,CASE WHEN (IFNULL(bw.simple_title,'') = '' AND IFNULL(bw.wod_id,0) > 0) THEN w.title ELSE
													CASE WHEN IFNULL(bw.simple_title,'') = '' THEN 'No name given' ELSE simple_title END END AS simple_title
						,buy_in
						#box_wod.simple_description overrides wod.description
						,CASE WHEN (IFNULL(bw.simple_description,'') = '' AND IFNULL(bw.wod_id,0) > 0) THEN w.description ELSE bw.simple_description END AS simple_description
						,CASE WHEN (IFNULL(bw.score_type,'') = '' AND IFNULL(bw.wod_id,0) > 0) THEN w.score_type ELSE IFNULL(bw.score_type,'')END AS score_type
						#box_wod.wod_type_id overrides wod.wod_type_id
						,CASE WHEN (IFNULL(bwwt.title,'') = '' AND IFNULL(bw.wod_id,0) > 0) THEN wt.title ELSE bwwt.title END AS wod_type
						,IFNULL(score,'') AS score
						#box_wod.note overrides wod.note
						,IFNULL(mw.note,'') AS note
						,IFNULL(bct_id,'') AS bct_id
						,IFNULL(rx ,0) AS rx
						,IFNULL(member_rating, -1) AS member_rating						
						,IFNULL(cash_out,'') AS cash_out
						,IFNULL(bw.scale_id,'') AS scale_id
						,IFNULL(s.scale_name,'') AS scale_name
						,IFNULL(mw.so_id,'') AS so_id
						,IFNULL(bwt.tier_name,'') AS tier
					FROM 
						box_wod bw
							LEFT JOIN member_wod mw ON
								bw.bw_id = mw.bw_id AND
								mw.member_id = ".$member_id."
							LEFT JOIN wod w ON
								bw.wod_id = w.wod_id
								LEFT JOIN wod_type wt ON
									w.wod_type_id = wt.wod_type_id
							LEFT JOIN wod_type bwwt ON
								bw.wod_type_id	=	bwwt.wod_type_id
							LEFT JOIN scale s ON
									bw.scale_id = s.scale_id
							LEFT JOIN box_wod_tier bwt ON
									bw.bwt_id = bwt.bwt_id
					WHERE 
						bw.bw_id = ".$box_wod_id."
					GROUP BY
						bw.bw_id, bw.wod_date, simple_title, mw.bw_id
					ORDER BY 
						bw.wod_date DESC";

		$query = $this->db->query($sql);
				
		if ($query->num_rows() == 0)
			return FALSE;
		
		//Just want to return the first row
		$my_array	=	$query->result_array();
		return	$my_array[0];

	}
	
	
	//Returns a list of wods for the specified box.
	//Also returns record for member if member has recored wod
	public function get_box_wod_by_members_box($box_id = 0, $member_id = 0)
	{
		$sql	=	"SELECT 
						 bw.bw_id
						,bw.wod_date
						,CASE WHEN ifnull(bw.simple_title,'') = '' THEN 'No name given' ELSE simple_title END AS simple_title
						,COUNT(mw.bw_id)	AS	recorded_wod
						,IFNULL(bwt.tier_name,'') AS tier_name
					FROM 
						box_wod bw
							LEFT JOIN (SELECT * FROM member_wod WHERE member_id = ".$member_id.") AS mw ON
								bw.bw_id = mw.bw_id 
							LEFT JOIN box_wod_tier bwt ON
								bw.bwt_id = bwt.bwt_id
					WHERE 
						bw.box_id = ".$box_id."
					GROUP BY
						bw.bw_id, bw.wod_date, simple_title, mw.bw_id
					ORDER BY 
						bw.wod_date DESC 
					LIMIT 31 ";
		
		$query = $this->db->query($sql);
				
		if ($query->num_rows() == 0)
			return FALSE;
		
		return	$query->result_array();
	}
	
	//Returns maxes for all members for a box
	public function get_member_max_details($box_id = 0)
	{
		$sql	=	"	SELECT
							e.exercise_id
						   ,e.title AS exercise
						   ,CONCAT_WS(' ',first_name,last_name) AS full_name
						   ,max(max_date) AS max_date
						   ,CASE WHEN e.max_type = 'T' 
							THEN 
							(
								CONCAT_WS(':',FLOOR(`max_value` / 60),
								LPAD( CAST(FLOOR((`max_value` - FLOOR(`max_value` / 60) * 60)) AS CHAR(2))   ,2,'0'))
							)
							ELSE `max_value`
							END AS `max_value`
				   FROM
						member m 
							INNER JOIN
							(	SELECT 	
									 m1.member_id
									,m1.exercise_id
									,DATE_FORMAT(m1.max_date,'%c/%e/%Y')	AS max_date
									,m1.max_value
								FROM 
									(SELECT mm_id, member_id, exercise_id, max_date, IFNULL(max_rep,1) AS max_rep, `max_value` FROM member_max WHERE IFNULL(max_rep,1) = 1) m1
										LEFT JOIN (SELECT mm_id, member_id, exercise_id, max_date, IFNULL(max_rep,1) AS max_rep, `max_value` FROM member_max WHERE IFNULL(max_rep,1) = 1) m2
											ON (m1.max_rep = m2.max_rep AND m1.member_id = m2.member_id AND m1.exercise_id = m2.exercise_id AND m1.max_value < m2.max_value)
								WHERE 
									m2.mm_id IS NULL AND 
										m1.max_rep = 1) reps ON
											m.member_id = reps.member_id 
							INNER JOIN
								exercise e ON
									reps.exercise_id = e.exercise_id
				   WHERE
								m.box_id = ".$box_id."
				GROUP BY
					 exercise_id
					,exercise
					,full_name
					,`max_value`
				ORDER BY
						 exercise
						,CASE WHEN e.max_type = 'T' THEN `max_value` ELSE `max_value` * -1 END
						,max_date
						";

		$query = $this->db->query($sql);
				
		if ($query->num_rows() == 0)
			return FALSE;
		
		return	$query->result_array();
		
	}
	//Returns recently saved activity
	public function get_recenty_activity()
	{
		$sql	=
					"	
						SELECT 
							z.activity_type
							,user_name
							,the_date
							,the_value
							,the_title
						FROM
						(
							(SELECT 
								'New User' AS activity_type
								,CONCAT_WS(' ',first_name,last_name) AS user_name
								,m.created_date AS the_date
								,b.box_name AS the_value 
								,'&nbsp;' AS the_title
							FROM 
								member m 
									LEFT JOIN box b
										ON m.box_id = b.box_id
							WHERE
								user_login NOT IN ('ray','jenn36500')

							ORDER BY 
								m.created_date DESC

							LIMIT 50)

							UNION
							(SELECT 
								'Max' AS activity_type
								,CONCAT_WS(' ',first_name,last_name) AS user_name
								, max_date AS the_date
								, `max_value` AS the_value
								,title AS the_title
							FROM 
								member m 
									INNER JOIN 
										member_max mm
											ON m.member_id = mm.member_id 
												INNER JOIN
													exercise e 
														ON mm.exercise_id = e.exercise_id
							WHERE
								user_login NOT IN ('ray','jenn36500')

							ORDER BY 
								mm.created_date DESC

							LIMIT 50)

							UNION

							(SELECT 
								'Box Wod' AS activity_type
								,CONCAT_WS(' ',first_name,last_name) AS user_name
								, bw.wod_date AS the_date
								, `score` AS the_value
								,simple_title AS the_title
							FROM 
								member m
									INNER JOIN
										member_wod mw
											ON m.member_id = mw.member_id
												INNER JOIN
													box_wod bw
														ON mw.bw_id = bw.bw_id
							WHERE
								user_login NOT IN ('ray','jenn36500')
							ORDER BY 
								mw.created_date DESC
							LIMIT 50


							)

							UNION

							(SELECT 
								'Self Wod' AS activity_type
								,CONCAT_WS(' ',first_name,last_name) AS user_name
								, wod_date AS the_date
								, `score` AS the_value 
								,IFNULL(title,custom_title) AS the_title
							FROM 
								member m
									INNER JOIN
										member_wod mw
											ON m.member_id = mw.member_id
												LEFT JOIN
													wod w
														ON mw.wod_id = w.wod_id
							WHERE
								user_login NOT IN ('ray','jenn36500')
							ORDER BY 
								mw.created_date DESC
							LIMIT 50


							)
							
							UNION 
							(
							SELECT 
								'Weight Log' AS activity_type
								,CONCAT_WS(' ',first_name,last_name) AS user_name
								,mwl.created_date AS the_date
								,mwl.weight_date AS the_value 
								,'New Weight Record' AS the_title
							FROM 
								member m 
									INNER JOIN 
										member_weight_log mwl 
												ON m.member_id = mwl.member_id 
								WHERE
									user_login NOT IN ('ray','jenn36500')

							ORDER BY 
								mwl.created_date DESC
								LIMIT 50
							)

							UNION

							(SELECT 
								'Paleo' AS activity_type
								,CONCAT_WS(' ',first_name,last_name) AS user_name
								,meal_date_time AS the_date
								, 'meal saved' AS the_value 
								,title AS the_title
							FROM 
								member m
									INNER JOIN
										member_paleo mp
											ON m.member_id = mp.member_id
											INNER JOIN
												meal_type mt
													ON mp.meal_type_id = mt.meal_type_id

							WHERE
								user_login NOT IN ('ray','jenn36500')
							ORDER BY 
								mp.created_date DESC
							LIMIT 50

							)
							
							UNION
							
							(SELECT 
								'Event Info' AS activity_type
								,CONCAT_WS(' ',first_name,last_name) AS user_name
								,mei.created_date AS the_date
								, 'event info saved' AS the_value 
								,e.event_name AS the_title
							FROM 
								member m
									INNER JOIN
										member_event_info mei
											ON m.member_id = mei.member_id
											INNER JOIN
												event e
													ON e.event_id = mei.event_id

							WHERE
								user_login NOT IN ('ray','jenn36500')
							ORDER BY 
								mei.created_date DESC
							LIMIT 50

							)
							
							UNION
							
							(SELECT 
								'Event WOD' AS activity_type
								,CONCAT_WS(' ',first_name,last_name) AS user_name
								,mew.created_date AS the_date
								, 'event info saved' AS the_value 
								,ew.simple_title as the_title
							FROM 
								member m
									INNER JOIN
										member_event_wod mew
											ON m.member_id = mew.member_id
											INNER JOIN
												event_wod ew 
													on mew.ew_id = ew.ew_id
														INNER JOIN
														event e
															on e.event_id = ew.event_id
							WHERE
								user_login NOT IN ('ray','jenn36500')
							ORDER BY 
								mew.created_date DESC
							LIMIT 50

							)
						) z
						ORDER BY 
							the_date DESC, activity_type, the_title, user_name
						LIMIT 50
						";
		
		$query = $this->db->query($sql);
				
		if ($query->num_rows() == 0)
			return FALSE;
		
		return	$query->result_array();
	}
	public function get_basic_stats($box_id)
	{
		$sql	=	"SELECT 
							'Member Count'  AS stat_name
						   ,COUNT(*) AS stat_value
					   FROM 
						   member
					   WHERE
						   box_id = ".$box_id."

					   UNION

					   SELECT 
							'Females'  AS stat_name
						   ,COUNT(*) AS stat_value
					   FROM 
						   member
					   WHERE
						   box_id = ".$box_id." AND 
						   gender = 'F'

					   UNION

					   SELECT 
							'Males'  AS stat_name
						   ,COUNT(*) AS stat_value
					   FROM 
						   member
					   WHERE
						   box_id = ".$box_id." AND 
						   gender = 'M'
					   ";
		
		$query = $this->db->query($sql);
				
		if ($query->num_rows() == 0)
			return FALSE;
		
		return	$query->result_array();
	}
	public function is_member_staff($member_id = '')
	{
		$member_id = $member_id === '' ? $this->session->userdata('member_id') : $member_id;
		
		$sql =	"SELECT 
						  box_id
					FROM
						box_staff
					WHERE
						member_id = ".$member_id;

		$query = $this->db->query($sql);
		
		if ($query->num_rows() == 0)
			return FALSE;
		
		$row = $query->row();

		return $row->box_id;
	}
	
	public function set_box_staff($member_id, $box_id)
	{
		//Remove current box assignmet (if spplicable)
		$this->db->delete('box_staff', array('member_id' => $member_id)); 
		
		if ($box_id	=== '-1')
			return true;
		
		//now insert the member as a staff member
		$data = array(
					'member_id'	=>	$member_id ,
					'box_id'	=>	$box_id
		);

		$this->db->insert('box_staff', $data); 
		
		return true;
		
	}
	
	public function get_box_list($real_locations_only	=	FALSE, $active_only = TRUE)
	{		
		$where_clause = '';
		if ($real_locations_only	&&	$active_only)
			$where_clause = ' WHERE super_order = 0 AND active = 1 ';
		elseif ($real_locations_only)
			$where_clause = ' WHERE super_order = 0 ';
		elseif ($active_only)
			$where_clause = ' WHERE active = 1 ';
			
		$sql =	"
					SELECT 
						  box_id
						, CASE WHEN super_order = 0 THEN CONCAT_WS(' - ',box_name,location) ELSE box_name END AS box_name 
					FROM
						box"				   .
				   $where_clause.
				   "
					ORDER BY 
						super_order, box_name";

		$query = $this->db->query($sql);
		
		if ($query->num_rows() == 0)
			return false;
		
		return	$query->result_array();
	}
}

/* End of file box_model.php */
/* Location: ./system/application/models/box_model.php */