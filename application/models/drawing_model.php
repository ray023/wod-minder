<?php
/** 
 * Drawing model
 * 
 * @author Ray Nowell
 *	
 */ 
class Drawing_model extends CI_Model {
    
        function get_5000_wod_giveaway_members()
	{
		$sql	=	"	
                                    SELECT 
                                             gender
                                            ,m.member_id
                                            ,CONCAT(first_name,' ', LEFT(last_name,1),'.') AS member_name 
                                            ,b.box_name
                                            ,count(mw.mw_id) as wod_count
                                    FROM 
                                            member m 
                                                    INNER JOIN 
                                                            member_wod mw ON m.member_id = mw.member_id
                                                    INNER JOIN box b ON m.box_id = b.box_id
                                    WHERE 
                                            m.member_id NOT IN (15,16) 
                                                    AND
                                            DATEDIFF('2013-07-28',mw.created_date) <= 0
                                    GROUP BY
                                             gender
                                            ,m.member_id
                                            ,CONCAT(first_name,' ', LEFT(last_name,1),'.')
                                            ,b.box_name
                                    ORDER BY 
                                      gender,
                                      member_name
				
					";
		
		$query = $this->db->query($sql);	

		if ($query->num_rows() == 0)
			return false;
		
		return	$query->result_array();

	}

}

/* End of file exercise_model.php */
/* Location: ./system/application/models/drawing_model.php */