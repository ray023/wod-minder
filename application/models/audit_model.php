<?php
/** 
 * Audit
 * This class handles model work necessary for logging in user
 * 
 * @author Ray Nowell
 *	
 */ 
class Audit_model extends CI_Model {

	function Audit_model()
	{
		parent::__construct();
			$this->load->library('encrypt');
	}	
	
	
	public function save_audit_log($data)
	{
		//Don't log me
		if (array_key_exists('member_id', $data) && $data['member_id']	==	15)
				   return;
		
		$this->db->insert('audit_log', $data);
		return TRUE;
	}
	
	public function get_audit_log()
	{
		$sql	=	"SELECT 
						DATE_FORMAT(log_date,'%d-%b %H:%i') as log_date
					  , short_description
					  , REPLACE(full_info,'\'','') AS full_info
					  , m.member_id
					  , b.box_abbreviation
					  ,IFNULL(CONCAT(first_name,' ',last_name),'') AS member_name
					FROM 
					  audit_log al 
						  LEFT JOIN 
							  member m ON 
								  al.member_id = m.member_id
									LEFT JOIN 
										box b ON 
											m.box_id = b.box_id
					ORDER BY 
					  al.log_date DESC";

		$query = $this->db->query($sql);
		
		if ($query->num_rows() == 0)
				return false;
		
		return $query->result_array();		
	}
}
/* End of file audit_model.php */
/* Location: ./system/application/models/audit_model.php */
