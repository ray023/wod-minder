<?php
/** 
 * Rss_model
 * 
 * @author Ray Nowell
 *	
 */ 
class Rss_model extends CI_Model {
	
	function Rss_model()
	{
		parent::__construct();
	}
        
        function get_daily_wod_list($box_id = 0)
        {
            $sql = "SELECT
                        simple_title AS title
                       ,DATE_FORMAT(bw.modified_date,'%a, %d %b %Y %T') AS last_build_date
                       ,DATE_FORMAT(bw.wod_date,'%a, %d %b %Y %T') AS pub_date
                       ,bw.modified_by AS creator
                       ,simple_description as wod_html
                    FROM
                        box_wod bw
                    WHERE
                        box_id = ".$box_id." 
                    ORDER BY 
                        bw.wod_date DESC
                    LIMIT 30 ";
            
            $query = $this->db->query($sql);	
            $result	=	$query->result_array();		
            return $result;
                
        }
	

}

/* End of file rss_model.php */
/* Location: ./application/models/rss_model.php */