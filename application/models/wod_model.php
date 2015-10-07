<?php
/** 
 * Wod_model
 * 
 * @author Ray Nowell
 *	
 */ 
class Wod_model extends CI_Model {

	function Wod_model()
	{
		parent::__construct();
	}
	
	function get_record_count($member_id = 0)
	{
		
		if ($member_id != 0)
			$this->db->where('member_id', $member_id);
		
		$query = $this->db->get('member_wod');
        return $query->num_rows;
	}

	//Returns a list of box wods applicable to the user's current box that match the search criteria passed
	function search($criteria = '')
	{
		$member_id	=	$this->session->userdata('member_id');
		
		$criteria = $this->db->escape_like_str($criteria);
		
		$sql = "SELECT 
					ifnull(mw.mw_id,0) AS mw_id,
					ifnull(mw.wod_id,0) as wod_id,
					ifnull(bw.bw_id,0) as bw_id,
					CASE 
						WHEN  IFNULL(mw.bw_id,0) <> 0 THEN bw.wod_date
						ELSE mw.wod_date
					END AS wod_date,
					CASE 
						WHEN IFNULL(bw.simple_title,'') <> '' THEN bw.simple_title
						WHEN IFNULL(w.title,'') <> '' THEN w.title
						ELSE custom_title 
					END AS simple_title,
					buy_in,					
					CASE  
						WHEN IFNULL(bw.simple_description,'') <> '' THEN bw.simple_description
						WHEN IFNULL(w.description,'') <> '' THEN w.description
					END AS simple_decription,
					cash_out,
					score,
					mw.note,
					rx
				  FROM 
						member_wod mw
							INNER JOIN 
								member m ON
								  mw.member_id = m.member_id 
							LEFT JOIN
								box_wod bw ON
									bw.bw_id = mw.bw_id 
							LEFT JOIN
								wod w ON
									mw.wod_id = w.wod_id 
				  WHERE
					  mw.member_id = ".$member_id."
						  AND
							(
							w.title LIKE '%".$criteria."%' OR
							w.description LIKE '%".$criteria."%' OR
							bw.buy_in LIKE '%".$criteria."%' OR
							bw.simple_title LIKE '%".$criteria."%' OR
							bw.simple_description LIKE '%".$criteria."%' OR
							bw.cash_out LIKE '%".$criteria."%' OR
							mw.score LIKE '%".$criteria."%' OR
							w.note LIKE '%".$criteria."%' OR
							mw.note LIKE '%".$criteria."%' OR
							mw.custom_title LIKE '%".$criteria."%'
							)
				  ORDER BY
					  wod_date DESC";

		$query = $this->db->query($sql);	
		$result	=	$query->result_array();		
		return $result;
	}
	
	function save_benchmark_wod($data)
	{
		$data['modified_date']	=	date("Y-m-d H:i:s");
		$data['modified_by']	=	$this->session->userdata('display_name');
		
		if (!isset($data['wod_id']))
		{
			$data['created_date'] = date('Y-m-d H:i:s');
			$data['created_by'] = $this->session->userdata('display_name');
			$this->db->insert('wod', $data);
		}
		else
			$this->db->update('wod',	$data, 'wod_id = '.$data['wod_id']);
		
		return array('success'  =>  true);
	}
	
	function save_member_custom_wod($data)
	{
		//This function does the same thing as the one above; just need a good name to refactor
		return $this->save_member_benchmark_wod($data);
	}	
	function save_member_benchmark_wod($data)
	{
		$data['modified_date']	=	date("Y-m-d H:i:s");
		$data['modified_by']	=	$this->session->userdata('display_name');
		
		if (!isset($data['mw_id']))
		{
			$data['created_date'] = date('Y-m-d H:i:s');
			$data['created_by'] = $this->session->userdata('display_name');
			$this->db->insert('member_wod', $data);
		}
		else
			$this->db->update('member_wod',	$data, 'mw_id = '.$data['mw_id']);
		
		return array('success'  =>  true);
	}
	//Returns a simple list of the benchmark WODs completed and a count of how many completed
	function get_member_benchmark_wods($member_id = '') {
		
		$member_id	=	$member_id == '' ? $this->session->userdata('member_id') : $member_id;
		
		$sql	=	"
						SELECT 
							wod_id
							, title
							, category
							, SUM(wod_count) AS wod_count
						FROM
						(
							SELECT 
								w.wod_id ,
								w.title ,
								LEFT(CASE WHEN INSTR(wc.title,'Girl') > 0 THEN 'Girl' ELSE wc.title END,1) AS category ,
								COUNT(w.wod_id) AS wod_count 

							FROM 
								member_wod mw
									INNER JOIN 
										wod w
											ON mw.wod_id = w.wod_id 
												INNER JOIN 
													wod_category wc 
														ON w.wod_category_id = wc.wod_category_id 
							WHERE 
								mw.member_id = ".$member_id." 


							GROUP BY 
								w.wod_id, w.title, category 
							UNION ALL
							SELECT 
								w.wod_id ,
								w.title ,
								LEFT(CASE WHEN INSTR(wc.title,'Girl') > 0 THEN 'Girl' ELSE wc.title END,1) AS category ,
								COUNT(w.wod_id) AS wod_count 
							FROM 
								member_wod mw
									INNER JOIN 
										box_wod bw ON
											mw.bw_id = bw.bw_id
												INNER JOIN
													wod w
														ON bw.wod_id = w.wod_id 
															INNER JOIN 
																wod_category wc 
																	ON w.wod_category_id = wc.wod_category_id 
							WHERE 
								mw.member_id = ".$member_id."


							GROUP BY 
								w.wod_id, w.title, category 
							) groupedBenchmarkWods

							GROUP BY wod_id, title, category

						ORDER BY category, title
						";

		$query = $this->db->query($sql);	
		$result	=	$query->result_array();		
		return $result;
	}
	//Returns a detailed list of a benchmark wod for a member
	//The WOD may have been one the member performed on their own or with their box
	function get_member_benchmark_wod_history($wod_id = 0, $member_id = '')
	{
		$member_id	=	$member_id == '' ? $this->session->userdata('member_id') : $member_id;
		$sql = "SELECT 
					 mw_id
					,mw.bw_id
					,mw.wod_id
					,IFNULL(mw.wod_date,bw.wod_date) AS wod_date 
					,score
					,CASE WHEN IFNULL(mw.bw_id,'') = '' THEN w.score_type 	ELSE bw.score_type 	END AS score_type
					,CASE WHEN IFNULL(mw.bw_id,'') = '' THEN FALSE			ELSE TRUE			END AS wod_at_box
					,mw.rx
				FROM 
					member_wod mw
						LEFT JOIN 
							box_wod bw ON
								mw.bw_id = bw.bw_id
						LEFT JOIN
							wod w
							ON mw.wod_id = w.wod_id
				WHERE 
					member_id = ".$member_id." AND
					(bw.wod_id = ".$wod_id." OR mw.wod_id = ".$wod_id.")
				ORDER BY 
					wod_date desc";
		
		$query = $this->db->query($sql);	
		$result	=	$query->result_array();		
		return $result;

	}
	
	function get_member_custom_wod_history($member_id = '')
	{
		$member_id	=	$member_id == '' ? $this->session->userdata('member_id') : $member_id;
				$sql	=	"
						SELECT
							 mw_id
							,custom_title
							,DATE_FORMAT(wod_date,'%c/%e/%Y') AS wod_date
							,score
						FROM
							member_wod mw 
						WHERE
							ifnull(bw_id,'') = '' AND
							ifnull(wod_id,'') = '' AND
							mw.member_id	= ".$member_id."
				ORDER BY 
					wod_date desc";
		
		$query = $this->db->query($sql);	
		$result	=	$query->result_array();		
		return $result;

	}
	
	function get_member_benchmark_wod($mw_id = 0, $member_id = 0)
	{
		$member_id	=	$member_id == '' ? $this->session->userdata('member_id') : $member_id;

		$sql	=	"
						SELECT 
							 mw.wod_id
							,w.title	AS wod_name
							,description
							,mw.note AS note
							,CASE WHEN wc.title LIKE '%Girl%' THEN 'Girl' ELSE wc.title END	AS category
							,wt.title AS wod_type
							,score_type
							,DATE_FORMAT(wod_date,'%c/%e/%Y') AS wod_date
							,score
							,member_rating
							,rx
							,score_type
						FROM
							member_wod mw 
								INNER JOIN wod w ON 
									mw.wod_id = w.wod_id
								INNER JOIN wod_category wc ON
									w.wod_category_id = wc.wod_category_id
								LEFT JOIN wod_type wt ON
									w.wod_type_id = wt.wod_type_id 
						WHERE
							mw.mw_id		= ".$mw_id." AND
							mw.member_id	= ".$member_id." 
						ORDER BY
							category
							,wod_name";
		
		$query	= $this->db->query($sql);		
		
		$row			=	$query->result_array();

		return array_shift(array_values($row));
	}
	
	function get_member_custom_wod($mw_id = 0, $member_id = 0)
	{
		$member_id	=	$member_id == '' ? $this->session->userdata('member_id') : $member_id;

		$sql	=	"
						SELECT 
							 custom_title
							,note
							,DATE_FORMAT(wod_date,'%c/%e/%Y') AS wod_date
							,score
						FROM
							member_wod mw 
						WHERE
							mw.mw_id		= ".$mw_id." AND
							mw.member_id	= ".$member_id;
		
		$query	= $this->db->query($sql);		
		
		$row			=	$query->result_array();

		return array_shift(array_values($row));
	}

	function get_box_list()
	{		
		$sql =	"
					SELECT 
						  wod_category_id
						, title
					FROM
						wod_category
					ORDER BY 
						title";

		$query = $this->db->query($sql);
		
		if ($query->num_rows() == 0)
			return false;
		
		return	$query->result_array();
	}
	
	function get_member_box_wods($member_id = '') {
		
		$member_id	=	$member_id == '' ? $this->session->userdata('member_id') : $member_id;

		$sql	=	"	SELECT 
							 mw_id
							,mw.bw_id
							,bw.wod_date
							,CASE WHEN ifnull(bw.simple_title,'') = '' THEN 'No name given' ELSE simple_title END AS simple_title
							, CASE WHEN bw.score_type = 'T' 
									THEN 
									(
										CONCAT_WS(':',FLOOR(mw.score / 60),
										LPAD( CAST((mw.score - FLOOR(mw.score / 60) * 60) AS CHAR(2))   ,2,'0'))
									)
									ELSE mw.score
									END									AS score 
						FROM 
							member_wod mw 
								INNER JOIN 
									box_wod bw ON 
										mw.bw_id = bw.bw_id
						WHERE
							mw.member_id = ".$member_id."
						ORDER BY 
							bw.wod_date DESC";

		$query = $this->db->query($sql);	
		$result	=	$query->result_array();		
		return $result;
	}

	function delete_member_wod($delete_id)
	{
		$this->db->where('mw_id', $delete_id);
		//Prevent user from deleting other's data:
		$this->db->where('member_id', $this->session->userdata('member_id'));
		
		$this->db->delete('member_wod'); 
	}

	//Returns a list of the benchmark CrossFit WODs that may apply to the box WOD (Heroes, Girls and Other)
	function get_benchmark_wod_list()
	{
		$sql =	"
					SELECT 
						  wod_id
						, title
						, description
						, score_type
                                                , image_name
					FROM
						wod
					ORDER BY 
						title";

		$query = $this->db->query($sql);
		
		if ($query->num_rows() == 0)
			return false;
		
		return	$query->result_array();	
	}
	function get_wod_type_list()
	{		
		$sql =	"
					SELECT 
						  wod_type_id
						, title
					FROM
						wod_type
					ORDER BY 
						title";

		$query = $this->db->query($sql);
		
		if ($query->num_rows() == 0)
			return false;
		
		return	$query->result_array();
	}
	
	function get_wod_category_list()
	{		
		$sql =	"
					SELECT 
						  wod_category_id
						, title
					FROM
						wod_category
					ORDER BY 
						title";

		$query = $this->db->query($sql);
		
		if ($query->num_rows() == 0)
			return false;
		
		return	$query->result_array();
	}
	
	/*
	 * Returns movements associated with benchmark wods (for Wod Wizard)
	 */
	function get_benchmark_wod_movements()
	{
		$sql	=	"
						SELECT 
							m.movement_id
						   ,m.movement
						FROM 
						   movement m
						ORDER BY 
						   movement
					";

		$query = $this->db->query($sql);
				
		if ($query->num_rows() == 0)
			return FALSE;
		
		return	$query->result_array();

	}
	
	function get_wod_wizard_wods_and_movements()
	{
		$sql	=	"
						SELECT 
							 w.wod_id
							,wc.title 	AS wod_category
							,w.title 	AS wod
							,w.description
							,w.note 
							,w.image_name
							,m.movement_id
							,m.movement
						FROM 
							wod w
								INNER JOIN 
									wod_category wc ON
										w.wod_category_id = wc.wod_category_id
								INNER JOIN 
									wod_movement wm ON
										w.wod_id = wm.wod_id
										INNER JOIN 
											movement m ON
												wm.movement_id = m.movement_id
						ORDER BY 
							wod_category,
							wod
			";
		
		$query = $this->db->query($sql);
				
		if ($query->num_rows() == 0)
			return FALSE;
		
		return	$query->result_array();

	}
	
	function get_benchmark_wod($wod_id	=	'')
	{
		$where_clause	=	$wod_id === '' ? ''	:	'WHERE w.wod_id	=	'.$wod_id;
		$sql	=	"
						SELECT 
							 w.wod_id
							,w.title	AS wod_name
							,description
							,CASE WHEN wc.title LIKE '%Girl%' THEN 'Girl' ELSE wc.title END	AS category
							,wt.title AS wod_type
							,score_type
							#REN NOTE:  The fields below are used in administration functions
							#           I'm sure there's a smarter way to do this,
							#           but I don't want to refactor existing fields NOR
							#           do I want to create another routine that does the same thing 
							#           as this one
							,w.title
							,w.wod_category_id
							,w.note
                                                        ,w.image_name
						FROM 
							wod w
								INNER JOIN wod_category wc ON
									w.wod_category_id = wc.wod_category_id
								LEFT JOIN wod_type wt ON
									w.wod_type_id = wt.wod_type_id "
						.$where_clause."
						ORDER BY
							category
							,wod_name";

		$query = $this->db->query($sql);
				
		if ($query->num_rows() == 0)
			return FALSE;
		
		//IF a single WOD, just return the first ROW
		//Otherwise, send the full list
		if ($wod_id	===	'')
			$return_value	=	$query->result_array();
		else
		{
			$row			=	$query->result_array();
			$return_value	=	array_shift(array_values($row));
		}
		
		
		return	$return_value;
	}
}

/* End of file wod_model.php */
/* Location: ./system/application/models/wod_model.php */