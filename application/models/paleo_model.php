<?php
/** 
 * Paleo_model
 * 
 * @author Ray Nowell
 *	
 */ 
class Paleo_model extends CI_Model {
	
	function Paleo_model()
	{
		parent::__construct();
	}
	
	function save_member_paleo_meal($data)
	{
		$data['modified_date']	=	date("Y-m-d H:i:s");
		$data['modified_by']	=	$this->session->userdata('display_name');

		if (!isset($data['mp_id']))
		{
			$data['created_date'] = date('Y-m-d H:i:s');
			$data['created_by'] = $this->session->userdata('display_name');
			$this->db->insert('member_paleo', $data);
		}
		else
			$this->db->update('member_paleo',	$data, 'mp_id = '.$data['mp_id']);
		
		return array('success'  =>  true);		
	}
	
	function get_member_paleo_meal_history($meal_date	=	'1900-01-01')
	{
		$member_id	=	$this->session->userdata('member_id');

		$sql	=	"
						SELECT 
							 mp_id
							,member_id
							,DATE_FORMAT(meal_date_time,'%c/%e/%Y') AS meal_date
							,DATE_FORMAT(meal_date_time,'%h:%i %p') AS meal_time
							,DATE_FORMAT(mp.meal_date_time,'%k')		AS meal_time_for_form
							,protein
							,veggie_or_fruit
							,fat
							,note
							,IFNULL(image_name,'') AS image_name
							,mp.meal_type_id
							,mt.title AS meal_type
							,mp.meal_type_id
						FROM
							member_paleo mp
								LEFT JOIN
									meal_type mt ON mp.meal_type_id = mt.meal_type_id
						WHERE
							member_id = ".$member_id." ".
						"AND DATE(mp.meal_date_time) ='".$meal_date."' ".
						"ORDER BY
							 meal_date_time DESC
							,display_order
						";
		$query		= $this->db->query($sql);	
		$ret_val	=	$query->result_array();

		return $ret_val;
		
	}
	
	/*
	 * When user clicks on Paleo History button, the options they are given derive from this sql query
	 */
	function get_member_paleo_select_list($member_id = '')
	{
		$member_id	=	$member_id == '' ? $this->session->userdata('member_id') : $member_id;
		
		$sql	=	"
						SELECT 
							 DATE_FORMAT(meal_date_time,'%W, %c/%e/%Y') AS meal_date
							,DATE_FORMAT(meal_date_time,'%Y-%c-%e') AS meal_day
							,COUNT(*) AS meal_count
							,MAX(mp_id) AS mp_id  #not brilliant, but will do for now
						FROM
							member_paleo mp
								LEFT JOIN
									meal_type mt ON mp.meal_type_id = mt.meal_type_id
						WHERE
							member_id = ".$member_id." ".
						"GROUP BY 
							meal_date DESC
						ORDER BY
							meal_date_time DESC
					";
		
		$query = $this->db->query($sql);	

		return $query->result_array();;
	}
	
	/*
	 * Gets a single meal or a full history
	 */
	function get_member_paleo_meal($mp_id = '', $member_id = '') 
	{
		
		$member_id	=	$member_id == '' ? $this->session->userdata('member_id') : $member_id;
		
		$sql	=	"
						SELECT 
							 mp_id
							,member_id
							,DATE_FORMAT(meal_date_time,'%c/%e/%Y') AS meal_date
							,DATE_FORMAT(meal_date_time,'%H') AS meal_time
							,protein
							,veggie_or_fruit
							,fat
							,note
							,IFNULL(image_name,'') AS image_name
							,mp.meal_type_id
							,mt.title AS meal_type
						FROM
							member_paleo mp
								LEFT JOIN
									meal_type mt ON mp.meal_type_id = mt.meal_type_id
						WHERE
							member_id = ".$member_id." ".
						($mp_id	===	'' ? '' : "AND mp_id =".$mp_id." ").
						"ORDER BY
							 meal_date_time DESC
							,display_order
						";

		$query = $this->db->query($sql);	
		//IF a single Paleo Meal, just return the first ROW
		//Otherwise, send the full list
		$return_value	=	false;
		if ($mp_id	===	'')
			$return_value	=	$query->result_array();
		else
		{
			$row			=	$query->result_array();
			$return_value	=	array_shift(array_values($row));
		}


		return $return_value;
	}
	
	function delete_paleo_meal($delete_id	=	0)
	{
		$this->db->where('mp_id', $delete_id);
		//Prevent user from deleting other's data:
		$this->db->where('member_id', $this->session->userdata('member_id'));
		
		$this->db->delete('member_paleo'); 
	}
	
	function get_meal_type_list()
	{		
		$sql =	"
					SELECT 
						  meal_type_id
						, title
					FROM
						meal_type
					ORDER BY 
						display_order";

		$query = $this->db->query($sql);
		
		if ($query->num_rows() == 0)
			return false;
		
		return	$query->result_array();
	}
	
	function get_previous_meal($meal_date	=	'1900-01-01')
	{
		$member_id	=	$this->session->userdata('member_id');
		$sql	=	"	SELECT 
							DATE_FORMAT(meal_date_time,'%c/%e/%Y') AS meal_date 
						FROM 
							member_paleo m 
						WHERE 
							DATE(m.meal_date_time) < '".$meal_date."' AND member_id = ".$member_id."    ORDER BY meal_date_time DESC LIMIT 1";
		$query = $this->db->query($sql);
		
		if ($query->num_rows() == 0)
			return false;

		$row			=	$query->result_array();
		return	$row[0];

	}
	function get_next_meal($meal_date	=	'1900-01-01')
	{
		$member_id	=	$this->session->userdata('member_id');
		$sql	=	"	SELECT 
							DATE_FORMAT(meal_date_time,'%c/%e/%Y') AS meal_date 
						FROM 
							member_paleo m 
						WHERE 
							DATE(m.meal_date_time) > '".$meal_date."' AND member_id = ".$member_id."  ORDER BY meal_date_time LIMIT 1";
				$query = $this->db->query($sql);
		
		if ($query->num_rows() == 0)
			return false;

		$row			=	$query->result_array();
		return	$row[0];
	}
}

/* End of file paleo_model.php */
/* Location: ./application/models/paleo_model.php */