<?php
/** 
 * Staff_model
 * 
 * @author Ray Nowell
 *	
 */ 
class Staff_model extends CI_Model {

	function Staff_model()
	{
		parent::__construct();
	}
        
		
	public function save_staff_training_log($data)
	{
		
		$data['modified_date']	=	date("Y-m-d H:i:s");
		$data['modified_by']	=	$this->session->userdata('display_name');

		if (!isset($data['bstl_id']))
		{
			$data['created_date'] = date('Y-m-d H:i:s');
			$data['created_by'] = $this->session->userdata('display_name');
			$this->db->insert('box_staff_training_log', $data);
		}
		else
			$this->db->update('box_staff_training_log',	$data, 'bstl_id = '.$data['bstl_id']);
		
		return array('success'  =>  true);
	}
	
	function get_staff_training_log_history($bstl_id = '', $member_id	=	0)
	{
		$member_id	=	$this->session->userdata('member_id');

		$sql	=	"
						SELECT 
							  bstl.bstl_id
							 ,DATE_FORMAT(training_date,'%c/%e/%Y') AS training_date
							 ,bct.class_time_description
						FROM
							box_staff_training_log bstl	
								LEFT JOIN
									box_class_time bct ON
										bstl.bct_id = bct.bct_id
						WHERE
							member_id = ".$member_id." ".
						($bstl_id	===	'' ? '' : "AND bstl_id =".$bstl_id." ").
						"ORDER BY
							 bstl.training_date DESC,
							 bct.class_time DESC
						";
		
		$query		= $this->db->query($sql);	

		$return_value	=	false;
		if ($bstl_id	===	'')
			$return_value	=	$query->result_array();
		else
		{
			$row			=	$query->result_array();
			$return_value	=	array_shift(array_values($row));
		}


		return $return_value;
	}
	
	function delete_training_log($delete_id	=	0)
	{
		$this->db->where('bstl_id', $delete_id);
		//Prevent user from deleting other's data:
		$this->db->where('member_id', $this->session->userdata('member_id'));
		
		$this->db->delete('box_staff_training_log'); 
	}
	
	function get_class_time_list($box_id = 0)
	{
		$sql =	"
					SELECT 
						  bct_id
						, `class_time_description`
					FROM
						box_class_time bct
                                        WHERE
                                                bct.box_id = $box_id
					ORDER BY 
						class_time";

		$query = $this->db->query($sql);
		
		if ($query->num_rows() == 0)
			return false;
		
		return	$query->result_array();		
	}
	
	public function get_staff_training_log($bstl_id = 0) 
	{
            $member_id	=	$this->session->userdata('member_id');
            $sql	=   "
                         SELECT 
                             bstl_id
                            ,member_id
                            ,box_id
                            ,bct_id
                            ,DATE_FORMAT(training_date,'%c/%e/%Y') AS training_date
                            ,class_size
                            ,note
                            FROM
                                    box_staff_training_log 
                            WHERE
                                    member_id = ".$member_id." ".
                                    "AND bstl_id =".$bstl_id;
		$query = $this->db->query($sql);
		if ($query->num_rows() == 0)
			return false;
		
		return $query->row();
	}
	/*
	 * Gets a box wod based on the boxid and current date
	 */
	
}

/* End of file box_model.php */
/* Location: ./system/application/models/box_model.php */