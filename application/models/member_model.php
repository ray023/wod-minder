<?php
/** 
 * Member_model
 * 
 * @author Ray Nowell
 *	
 */ 
class Member_model extends CI_Model {

	function Member_model()
	{
		parent::__construct();
	}
	function delete_member_history($delete_id)
	{
		$this->db->where('mm_id', $delete_id);
		//Prevent user from deleting other's data:
		$this->db->where('member_id', $this->session->userdata('member_id'));
		
		$this->db->delete('member_max'); 
	}
	
	function update_member($data)
	{
		if (!isset($data['member_id']))
			$data['member_id']	=	$this->session->userdata('member_id');
		
		$this->db->where('member_id',$data['member_id']);
		$this->db->update('member',$data);
		
		$query	=	$this->db->limit(1)->get_where('member', array('member_id' => $data['member_id']));
		$row			=	$query->result_array();
		$member_row	=	array_shift(array_values($row));
		return array(	'success'		=>  true,
						'site_admin'	=>	false,
						'member_id'		=>	$member_row['member_id'],
						'display_name'	=>	$member_row['first_name'],
						'member_box_id'	=>	$member_row['box_id'],
						'email'			=>	$member_row['email'],
						//'user_login'	=>	$member_row['user_login'],
					);		
	}
	
	function get_member($member_id = '')
	{
		$member_id	=	$member_id == '' ? $this->session->userdata('member_id') : $member_id;
		
		$query = $this->db->limit(1)->get_where('member', array('member_id`' => $member_id));
		if ($query->num_rows() == 0)
			return false;
		
		return $query->row(); 
	}
	
	function get_all_members() {
		$query = $this->db->get('member');

		return $query->result_array();
	}
	function get_email_wod_data($member_id	=	'')
	{
		$member_id	=	$member_id	==	'' ? $this->session->userdata('member_id') : $member_id;
		$sql	=	"
						SELECT 
							m.first_name
							,DATE_FORMAT(mw.wod_date,'%c/%e/%Y') AS wod_date
							, CASE WHEN w.score_type = 'T' 
								THEN 
								(
									CONCAT_WS(':',FLOOR(mw.score / 60),
									LPAD( CAST((mw.score - FLOOR(mw.score / 60) * 60) AS CHAR(2))   ,2,'0'))
								)
								ELSE mw.score
								END AS score
							,mw.rx
							,member_rating
							,w.title
							,CASE w.score_type WHEN 'T' THEN 'Time' WHEN 'R' THEN 'Rep' WHEN 'I' THEN 'Integer' WHEN 'W' THEN 'Weight' ELSE 'Other' END AS score_type
							,wc.title AS category
							,'Yes' AS benchmark_wod
							,'No'  AS box_wod
							,mw.note
						FROM 
							member m 
								INNER JOIN 
									member_wod mw ON
										m.member_id = mw.member_id
										INNER JOIN 
											wod w ON
												mw.wod_id = w.wod_id
													INNER JOIN
														wod_category wc ON
															w.wod_category_id = wc.wod_category_id
						WHERE 
							m.member_id	=	".$member_id."

						UNION

						SELECT 
							m.first_name
							,DATE_FORMAT(bw.wod_date,'%c/%e/%Y') AS wod_date
							, CASE WHEN IFNULL(w.score_type,bw.score_type) = 'T' 
								THEN 
								(
									CONCAT_WS(':',FLOOR(mw.score / 60),
									LPAD( CAST((mw.score - FLOOR(mw.score / 60) * 60) AS CHAR(2))   ,2,'0'))
								)
								ELSE mw.score
								END AS score
							,mw.rx
							,member_rating
							,CASE WHEN IFNULL(bw.simple_title,'') = '' THEN 'No Name Given' ELSE bw.simple_title END AS title
							,CASE IFNULL(w.score_type,bw.score_type) WHEN 'T' THEN 'Time' WHEN 'R' THEN 'Rep' WHEN 'I' THEN 'Integer' WHEN 'W' THEN 'Weight' ELSE 'Other' END AS score_type
							,IFNULL(wc.title,'N/A') AS category
							,CASE WHEN IFNULL(bw.wod_id,'') = '' THEN 'NO' ELSE 'YES' END AS benchmark_wod
							,'YES'  AS box_wod
							,mw.note
						FROM 
							member m 
								INNER JOIN 
									member_wod mw ON
										m.member_id = mw.member_id
											INNER JOIN
												box_wod bw ON mw.bw_id = bw.bw_id
												LEFT JOIN 
													wod w ON
														bw.wod_id = w.wod_id
															LEFT JOIN
																wod_category wc ON
																	w.wod_category_id = wc.wod_category_id
						WHERE 
							m.member_id	=	".$member_id."
						ORDER BY 
							wod_date
				";
		
		$query = $this->db->query($sql);	
		$result	=	$query->result_array();		
		return $result;	
	}
	function get_email_max_data($member_id = '')
	{
		$member_id	=	$member_id == '' ? $this->session->userdata('member_id') : $member_id;
			$sql	=	"
							SELECT 
								m.first_name 
								,e.title AS exercise
								,DATE_FORMAT(mm.max_date,'%c/%e/%Y')	AS max_date
								, CASE WHEN e.max_type = 'T' 
									THEN 
									(
										CONCAT_WS(':',FLOOR(mm.max_value / 60),
										LPAD( CAST(FLOOR((mm.`max_value` - FLOOR(mm.`max_value` / 60) * 60)) AS CHAR(2))   ,2,'0'))
									)
									ELSE mm.max_value
									END									AS `max_value`
								,IFNULL(max_rep,'N/A') AS max_rep
								,CASE e.max_type WHEN 'T' THEN 'Time' WHEN 'R' THEN 'Rep' WHEN 'W' THEN 'Weight' ELSE 'Other' END AS max_type
							FROM 
								member m 
									INNER JOIN 
										member_max mm ON
											m.member_id = mm.member_id
												INNER JOIN
													exercise e ON
														mm.exercise_id = e.exercise_id
							WHERE 
								mm.member_id = ".$member_id."
							ORDER BY
								exercise, max_date DESC
							";

		$query = $this->db->query($sql);	
		$result	=	$query->result_array();		
		return $result;	
	}

	function get_email_paleo_data($member_id = '')
	{
		$member_id	=	$member_id == '' ? $this->session->userdata('member_id') : $member_id;
			$sql	=	"
							SELECT 
								 DATE_FORMAT(meal_date_time,'%c/%e/%Y') AS meal_date
								,DATE_FORMAT(meal_date_time,'%H') AS meal_time
								,mt.title AS meal_type
								,protein
								,veggie_or_fruit
								,fat
								,note
							FROM 
								member_paleo mp
									INNER JOIN 
										meal_type mt ON
											mp.meal_type_id = mt.meal_type_id
							WHERE 
								mp.member_id = ".$member_id."
							ORDER BY
								meal_date_time DESC
								,display_order
							";

		$query = $this->db->query($sql);	
		$result	=	$query->result_array();		
		return $result;	
	}
	
	function get_member_summary_info($member_id	=	0)
	{
		$sql	=	"
						select 'IsBoxStaff' AS MyCategory, count(member_id) AS TheCount from box_staff where member_id = ".$member_id." 
							UNION
						select 'Max Count' AS MyCategory, count(member_id) AS TheCount from member_max where member_id = ".$member_id." 
							UNION
						select 'Weight Log' AS MyCategory, count(member_id) AS TheCount from member_weight_log where member_id = ".$member_id." 
							UNION
						select 'WOD COUNT' AS MyCategory, count(member_id) AS TheCount from member_wod where member_id = ".$member_id." ;
					";
		
		$query = $this->db->query($sql);	
		$result	=	$query->result_array();		
		return $result;
		
	}
	function erase_members_existance($member_id	=	0)
	{
		$this->db->delete('box_staff'			, array('member_id' => $member_id)); 
		$this->db->delete('member_max'			, array('member_id' => $member_id)); 
		$this->db->delete('member_weight_log'	, array('member_id' => $member_id)); 
		$this->db->delete('member_wod'			, array('member_id' => $member_id)); 
		$this->db->delete('member'				, array('member_id' => $member_id)); 
		$this->db->delete('member_event_info'	, array('member_id' => $member_id)); 
		$this->db->delete('member_event_wod'	, array('member_id' => $member_id)); 

		return true;
	}
}

/* End of file member_model.php */
/* Location: ./system/application/models/member_model.php */