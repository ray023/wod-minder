<?php
/** 
 * Weight_model
 * 
 * @author Ray Nowell
 *	
 */ 
class Weight_model extends CI_Model {
	
	function Weight_model()
	{
		parent::__construct();
	}
	
	function save_member_weight($data)
	{
		$data['modified_date']	=	date("Y-m-d H:i:s");
		$data['modified_by']	=	$this->session->userdata('display_name');

		if (!isset($data['mwl_id']))
		{
			$data['created_date'] = date('Y-m-d H:i:s');
			$data['created_by'] = $this->session->userdata('display_name');
			$this->db->insert('member_weight_log', $data);
		}
		else
			$this->db->update('member_weight_log',	$data, 'mwl_id = '.$data['mwl_id']);
		
		return array('success'  =>  true);
	}
	
	function get_record_count($member_id = 0)
	{
		
		if ($member_id != 0)
			$this->db->where('member_id', $member_id);
		
		$query = $this->db->get('member_weight_log');
        return $query->num_rows;
	}
	
	function get_member_weight_history($mwl_id = '', $member_id	=	0)
	{
		$member_id	=	$this->session->userdata('member_id');

		$sql	=	"
						SELECT 
							 mwl_id
							,member_id
							,DATE_FORMAT(weight_date,'%c/%e/%Y') AS weight_date
							,weight
							,bmi
							,body_fat_percentage
							,how_i_feel
							,note
							,IFNULL(image_name,'') AS image_name
						FROM
							member_weight_log mwl
						WHERE
							member_id = ".$member_id." ".
						($mwl_id	===	'' ? '' : "AND mwl_id =".$mwl_id." ").
						"ORDER BY
							 mwl.weight_date DESC
						";
		
		$query		= $this->db->query($sql);	

		$return_value	=	false;
		if ($mwl_id	===	'')
			$return_value	=	$query->result_array();
		else
		{
			$row			=	$query->result_array();
			$return_value	=	array_shift(array_values($row));
		}


		return $return_value;
	}
	
	function delete_weight($delete_id	=	0)
	{
		$this->db->where('mwl_id', $delete_id);
		//Prevent user from deleting other's data:
		$this->db->where('member_id', $this->session->userdata('member_id'));
		
		$this->db->delete('member_weight_log'); 
	}
}

/* End of file waleo_model.php */
/* Location: ./application/models/waleo_model.php */