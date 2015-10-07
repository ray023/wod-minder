<?php
/** 
 * Scale_model
 * 
 * @author Ray Nowell
 *	
 */ 
class Scale_model extends CI_Model {

	function Scale_model()
	{
		parent::__construct();
	}
	
        function get_scale_list($box_id = 0)
        {
            $is_admin = $box_id === 'ADMIN';
            
            $sql =	"
                            SELECT 
                                      scale_id
                                    , s.box_id
                                    , scale_name
                                    , ifnull(b.box_name,'all boxes') as box_name
                            FROM
                                    scale s LEFT JOIN box b on s.box_id = b.box_id ";
            if (!$is_admin)
            $sql .= "
                            WHERE
                                s.box_id in (0,".$box_id.")
                            ORDER BY 
                                     s.box_id DESC
                                    ,scale_name";

		$query = $this->db->query($sql);
		
		if ($query->num_rows() == 0)
			return false;
		
		return	$query->result_array();	
    
        }
}

/* End of file blog_model.php */
/* Location: ./system/application/models/blog_model.php */