<?php
/** 
 * Kiosk model
 * 
 * @author Ray Nowell
 *	
 */ 
class Kiosk_model extends CI_Model {
	
	function get_wod_results_for_kiosk($bw_id = 0, $gender = '')
	{
		$sql	=	"	 SELECT 
							mw.bw_id
						   ,CONCAT(first_name,' ',LEFT(last_name,1),'.') AS member_name
						   ,gender
						   ,CASE WHEN bw.score_type = 'T' 
								  THEN 
								  (
									  CONCAT_WS(':',FLOOR(mw.score / 60),
									  LPAD( CAST((mw.score - FLOOR(mw.score / 60) * 60) AS CHAR(2))   ,2,'0'))
								  )
								  ELSE mw.score
								  END AS score
						   ,rx
						   ,member_rating 
					   FROM 
						   member m 
							   INNER JOIN 
								   member_wod mw 	ON
									   m.member_id = mw.member_id
								   INNER JOIN box_wod bw ON
									   mw.bw_id = bw.bw_id
					   WHERE 
						   m.gender = '".$gender."' AND
						   mw.bw_id = '".$bw_id."' 
					   ORDER BY
							bw_id
						   ,gender
						   ,rx DESC
						   ,member_name
						   ,CASE WHEN bw.score_type = 'T' THEN score * -1 ELSE score END
						";
		
		
		$query = $this->db->query($sql);	
		
		if ($query->num_rows() == 0)
			return false;
		
		return $query->result_array();
		
	}
    
	function check_dates($box_id = 0)
	{
		$sql	=	"	SELECT 
							DATE_FORMAT(CASE WHEN created_date > modified_date 
								THEN created_date 
								ELSE modified_date 
							END ,'%Y,%m,%d,%h,%i,%s') AS box_wod_date
						FROM 
						   box_wod 
						WHERE 
							box_id = ".$box_id." AND 
							wod_date = CURDATE()
						ORDER BY 
							box_wod_date DESC 
						LIMIT 1
					";
		
		$query = $this->db->query($sql);	

		if ($query->num_rows() == 0)
			return false;
		
		$temp1_array = $query->result_array();
		$first_array_item = $temp1_array[0];
		$box_wod_date	=	$first_array_item['box_wod_date'];
		
		$sql	=	"	SELECT 
							DATE_FORMAT(CASE WHEN mw.created_date > mw.modified_date 
								THEN mw.created_date 
								ELSE mw.modified_date 
							END ,'%Y,%m,%d,%h,%i,%s') AS member_wod_date
						 FROM 
							member_wod mw
						WHERE 
							bw_id IN
							(
								SELECT 
									bw_id
								 FROM 
									box_wod 
								WHERE  
									box_id = ".$box_id." AND 
									wod_date = CURDATE()
							)
						ORDER BY 
							member_wod_date DESC
						LIMIT 1
					";
		
		$query = $this->db->query($sql);	

		$member_wod_date = FALSE;
		if ($query->num_rows() > 0)
		{
			$temp1_array = $query->result_array();
			$first_array_item = $temp1_array[0];
			$member_wod_date	=	$first_array_item['member_wod_date'];
		}
		
		return $box_wod_date.'|'.$member_wod_date;

	}

}

/* End of file exercise_model.php */
/* Location: ./system/application/models/kiosk_model.php */