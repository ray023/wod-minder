<?php
/** 
 * Event_model
 * 
 * @author Ray Nowell
 *	
 */ 
class Event_model extends CI_Model {

	function Event_model()
	{
		parent::__construct();
	}
	
	function save_member_event_wod($data = null)
	{
		$sql	=	"DELETE FROM member_event_wod WHERE member_id =".$data['member_id']." AND ew_id = ".$data['ew_id'];
		$query = $this->db->query($sql);
		
		$data['modified_date']	=	date("Y-m-d H:i:s");
		$data['modified_by']	=	'N/A deleted every time';
		$data['created_date'] = date('Y-m-d H:i:s');
		$data['created_by'] = $this->session->userdata('display_name');
		
		$this->db->insert('member_event_wod', $data);
		return array('success'  =>  true);	
	}
	
	function get_member_event_wod($ew_id = 0, $member_id = 0)
	{
		$sql = "
				SELECT 
					    mew_id
					  , ew_id
					  , member_id
					  , score
					  , remainder
					  , rank
					  , member_rating
					  , note
				FROM
					member_event_wod mew
				WHERE
					ew_id = ".$ew_id." AND 
					member_id = ".$member_id;
		$query = $this->db->query($sql);
				
		if ($query->num_rows() == 0)
			return FALSE;
		
		$row	=	$query->result_array();
		return array_shift(array_values($row));
		
	}
	function get_event_wod_for_member($event_id = 0, $member_id = 0)
	{
		$sql = "
				SELECT 
					  ew.ew_id
					, simple_title 
					, COUNT(mew.ew_id) AS recorded_wod
				FROM
					event_wod ew
						LEFT JOIN
							member_event_wod mew ON
								ew.ew_id = mew.ew_id AND
								member_id = ".$member_id."
				WHERE
					ew.event_id = ".$event_id."
				GROUP BY 
					  ew.simple_title
					, mew.ew_id";
		
		$query = $this->db->query($sql);
				
		if ($query->num_rows() == 0)
			return FALSE;
		

		return $query->result_array();
	}
	
	function get_member_event_info($event_id, $member_id)
	{
		$query = $this->db->limit(1)->get_where('member_event_info', array(	'event_id' => $event_id, 
																			'member_id' => $member_id));
		if ($query->num_rows() == 0)
			return false;
		
		$row = $query->result_array();
		
		return array_shift(array_values($row));
	}

	function save_member_event_info($data = null)
	{
		$sql	=	"DELETE FROM member_event_info WHERE member_id =".$data['member_id']." AND event_id = ".$data['event_id'];
		$query = $this->db->query($sql);
		
		$data['modified_date']	=	date("Y-m-d H:i:s");
		$data['modified_by']	=	'N/A deleted every time';
		$data['created_date'] = date('Y-m-d H:i:s');
		$data['created_by'] = $this->session->userdata('display_name');
		
		//Now Add the record
		$this->db->insert('member_event_info', $data);
		return array('success'  =>  true);	
	}
	
	function get_events_with_wods()
	{
		$sql = "SELECT 
					 CASE WHEN IFNULL(e.hosting_box_id,'') = '' THEN e.host_name ELSE b.box_name END AS hosting_entity
					,e.event_name
					,e.event_id
					,COUNT(ew.event_id) AS wod_count
					,MIN(ew.ew_id) AS ew_id_single
				FROM 
					`event` e
						LEFT JOIN
							box b ON
								e.hosting_box_id = b.box_id
						INNER JOIN
							event_wod ew ON
							e.event_id = ew.event_id
				GROUP BY 
					ew.event_id";
		
		$query = $this->db->query($sql);
				
		if ($query->num_rows() == 0)
			return FALSE;
		
		$return_value	=	$query->result_array();
		
		return	$return_value;
	}
	
	function get_event_wods_for_events_with_multiple_wods()
	{
		$sql	=	'SELECT 
						 ew_id
						,event_id
						,simple_title
					FROM 
						event_wod
					WHERE 
						event_id IN
							(SELECT event_id FROM event_wod GROUP BY event_id HAVING COUNT(event_id) > 1)';
		
		$query = $this->db->query($sql);
				
		if ($query->num_rows() == 0)
			return FALSE;
		
		$return_value	=	$query->result_array();
		
		return	$return_value;
	}
	
	function get_event_wod($ew_id = 0, $event_id = '')
	{
		$where_clause	=	'';
		if ($event_id != '')
			$where_clause	=	'WHERE e.event_id	=	'.$event_id;
		else
			$where_clause	=	'WHERE ew.ew_id	=	'.$ew_id;
		
		$sql	=	"
						SELECT 
							 ew.ew_id
							,e.event_id
							,ew.wod_id
							,ew.score_type
							,ew.remainder_name
							,ew.es_id
							,DATE_FORMAT(wod_date,'%m/%d/%Y') AS wod_date
							,simple_title
							,simple_description
							,ew.note
							,ew.team_wod
							,ew.result_hyperlink
							,es.scale_name
						FROM 
							event e
								INNER JOIN
									event_wod ew ON
										e.event_id = ew.event_id
											LEFT JOIN
												event_scale es ON
													ew.es_id = es.es_id								 "
						.$where_clause."
						ORDER BY
							ew.simple_title";

		$query = $this->db->query($sql);
				
		if ($query->num_rows() == 0)
			return FALSE;
		
		//IF a single Event WOD, just return the first ROW
		//Otherwise, send the full list
		if ($ew_id	===	'')
			$return_value	=	$query->result_array();
		else
		{
			$row			=	$query->result_array();
			$return_value	=	array_shift(array_values($row));
		}
		
		
		return	$return_value;
	}
	
	function get_event($event_id = '', $member_id = 0)
	{
		$where_clause	=	$event_id === '' ? ''	:	'WHERE e.event_id	=	'.$event_id;
		$sql	=	"
						SELECT 
							 e.event_id
							,CASE WHEN IFNULL(e.hosting_box_id,'') = '' THEN e.host_name ELSE b.box_name END AS hosting_entity
							,b.box_abbreviation
							,DATE_FORMAT(e.start_date,'%m/%d/%Y') AS start_date
							,e.start_date AS start_date_mysql_format
							,event_name
							,e.es_id
							,hosting_box_id
							,host_name
							,is_team_event
							,duration
							,publish
							,e.result_hyperlink
							,event_main_hyperlink
							,facebook_page
							,twitter_account
							,e.note AS event_note
							,IFNULL(COUNT(mei.mei_id),0) + IFNULL(COUNT(mew.mew_id),0) AS recorded_event
						FROM 
							event e
								LEFT JOIN
									box b ON
										e.hosting_box_id = b.box_id
								LEFT JOIN 
									(SELECT * FROM member_event_info WHERE member_id = ".$member_id.") AS mei ON
									e.event_id = mei.event_id 
								LEFT JOIN 
									event_wod ew ON 
										e.event_id = ew.event_id
									LEFT JOIN 
										(SELECT * FROM member_event_wod WHERE member_id = ".$member_id.") AS mew ON 
											ew.ew_id = mew.ew_id  "
						.$where_clause."
						GROUP BY
							e.event_id
						  , hosting_entity
						  , start_date
						  , start_date_mysql_format
						  , event_name
						  , e.es_id
						  , hosting_box_id
						  , host_name
						  , is_team_event
						  , duration
						  , publish
						  , result_hyperlink
						  , event_main_hyperlink
						  , facebook_page
						  , twitter_account	
						  , event_note
						ORDER BY
							start_date_mysql_format DESC
							,event_name";

		$query = $this->db->query($sql);
				
		if ($query->num_rows() == 0)
			return FALSE;
		
		//IF a single Event, just return the first ROW
		//Otherwise, send the full list
		if ($event_id	===	'')
			$return_value	=	$query->result_array();
		else
		{
			$row			=	$query->result_array();
			$return_value	=	array_shift(array_values($row));
		}
		
		
		return	$return_value;
	}
	
	function save_event($data)
	{
		$data['modified_date']	=	date("Y-m-d H:i:s");
		$data['modified_by']	=	$this->session->userdata('display_name');
		
		if (!isset($data['event_id']))
		{
			$data['created_date'] = date('Y-m-d H:i:s');
			$data['created_by'] = $this->session->userdata('display_name');
			$this->db->insert('event', $data);
		}
		else
			$this->db->update('event',	$data, 'event_id = '.$data['event_id']);
		
		return array('success'  =>  true);
	}
	
	function save_event_wod($data)
	{
		$data['modified_date']	=	date("Y-m-d H:i:s");
		$data['modified_by']	=	$this->session->userdata('display_name');
		
		if (!isset($data['ew_id']))
		{
			$data['created_date'] = date('Y-m-d H:i:s');
			$data['created_by'] = $this->session->userdata('display_name');
			$this->db->insert('event_wod', $data);
		}
		else
			$this->db->update('event_wod',	$data, 'ew_id = '.$data['ew_id']);
		
		return array('success'  =>  true);
	}
	
	function get_event_scale_list()
	{		
		$sql =	"
					SELECT 
						  es_id
						, scale_name
					FROM
						event_scale
					";

		$query = $this->db->query($sql);
		
		if ($query->num_rows() == 0)
			return false;
		
		return	$query->result_array();
	}
	
	function get_event_scale_option_list($scale_id = 0)
	{		
		$sql =	"
					SELECT 
						  eso_id
						, `scale_option`
					FROM
						event_scale_option
                                        WHERE
                                                es_id = $scale_id
					ORDER BY 
						scale_order,
						`scale_option`";

		$query = $this->db->query($sql);
		
		if ($query->num_rows() == 0)
			return false;
		
		return	$query->result_array();
	}
	
	
}

/* End of file event_model.php */
/* Location: ./system/application/models/event_model.php */