<?php
/** 
 * Exercise_model
 * 
 * @author Ray Nowell
 *	
 */ 
class Exercise_model extends CI_Model {

	function Exercise_model()
	{
		parent::__construct();
	}
	
	function get_record_count($member_id = 0)
	{
		
		if ($member_id != 0)
			$this->db->where('member_id', $member_id);
		
		$query = $this->db->get('member_max');
        return $query->num_rows;
	}
	
	function save_member_max($data)
	{
		$data['member_id']	=	$this->session->userdata('member_id');
		
		if (!isset($data['mm_id']) || $data['mm_id']	==	'')
			$this->db->insert('member_max', $data);
		else
			$this->db->update('member_max',	$data, 'mm_id = '.$data['mm_id']);
		
		return array('success'  =>  true);
	}	
	
	function get_max_rank($exercise_id = 0)
	{
		$member_id	=	$this->session->userdata('member_id');
		$sql	=	"	
						SELECT
							m.member_id
						   ,CASE WHEN e.max_type = 'T' 
						   THEN 
						   (
							   CONCAT_WS(':',FLOOR(`max_value` / 60),
							   LPAD( CAST(FLOOR((`max_value` - FLOOR(`max_value` / 60) * 60)) AS CHAR(2))   ,2,'0'))
						   )
						   ELSE `max_value`
						   END AS `max_value`
						FROM
							 member m 
								 INNER JOIN
								 (	SELECT     
										m1.member_id
									   ,m1.exercise_id
									   ,DATE_FORMAT(m1.max_date,'%c/%e/%Y')    AS max_date
									   ,m1.max_value
									FROM 
									   (SELECT mm_id, member_id, exercise_id, max_date, IFNULL(max_rep,1) AS max_rep, `max_value` FROM member_max WHERE IFNULL(max_rep,1) = 1) m1
										   LEFT JOIN (SELECT mm_id, member_id, exercise_id, max_date, IFNULL(max_rep,1) AS max_rep, `max_value` FROM member_max WHERE IFNULL(max_rep,1) = 1) m2
										   ON (m1.max_rep = m2.max_rep AND m1.member_id = m2.member_id AND m1.exercise_id = m2.exercise_id AND m1.max_value < m2.max_value)
									WHERE 
									   m2.mm_id IS NULL AND 
									   m1.max_rep = 1) reps ON
											 m.member_id = reps.member_id 
								 INNER JOIN
									 exercise e ON
										 reps.exercise_id = e.exercise_id
						WHERE
									 m.box_id = (SELECT box_id FROM member WHERE member_id = ".$member_id.") AND 
									 e.exercise_id = ".$exercise_id."
					  GROUP BY
						 `max_value`, m.member_id 
					  ORDER BY
							  CASE WHEN e.max_type = 'T' THEN `max_value` ELSE `max_value` * -1 END
							 ,max_date
					";
		
		$query = $this->db->query($sql);	

		if ($query->num_rows() == 0)
			return false;
		
		return	$query->result_array();

	}
	
	function get_member_max($mm_id	=	0)
	{
		$member_id	=	$this->session->userdata('member_id');
		
		$sql	=	"	
						SELECT
							mm_id
						   ,mm.exercise_id
						   ,DATE_FORMAT(mm.max_date,'%c/%e/%Y') AS max_date
						   ,mm.max_rep
						   ,mm.max_value
						   ,e.max_type as `max_type`
						   ,e.title AS exercise_name
					   FROM
						   member_max mm
							   INNER JOIN 
								   exercise e ON
									   mm.exercise_id	= e.exercise_id
					   WHERE
						   mm.mm_id = '.$mm_id.' AND
						   mm.member_id = '.$member_id.'
					";
		
		$query = $this->db->query($sql);	
		$row			=	$query->result_array();

		return array_shift(array_values($row));		
		
	}
	
        function get_user_max_months()
	{
		$member_id	=	$this->session->userdata('member_id');
		$sql		=	"   SELECT DISTINCT
                                                     DATE_FORMAT(max_date,'%Y-%m') AS lift_year_month
                                                    ,DATE_FORMAT(max_date,'%M %Y') AS page_title
                                            FROM 
                                                    member_max where member_id = ".$member_id."
                                            ORDER BY 
                                                    lift_year_month 
                                            DESC";
		
		$query = $this->db->query($sql);
		
		if ($query->num_rows() == 0)
			return false;
		
		return	$query->result_array();
		
	}
        
	function get_user_max_snapshot($member_max_date = '')
	{
		$member_id	=	$this->session->userdata('member_id');
		$sql		=	$this->_get_user_rep_max_snapshot_query($member_id, $member_max_date);
		
		$query = $this->db->query($sql);
		
		if ($query->num_rows() == 0)
			return false;
		
		return	$query->result_array();
		
	}
	
	//Gets the history for a user for a single exercise
	function get_member_exercise_history($exercise_id)
	{
		$member_id	=	$this->session->userdata('member_id');
		
		$sql	=	"	SELECT 
							 mm.mm_id 
							,mm.max_date
							,mm.max_rep
							,CASE	WHEN 
										e.max_type = 'T' 
									THEN 
									(
										CONCAT_WS(':',FLOOR(mm.`max_value` / 60),
										LPAD( CAST(FLOOR((mm.`max_value` - FLOOR(mm.`max_value` / 60) * 60)) AS CHAR(2))   ,2,'0'))
									)
									ELSE `max_value`
									END AS `max_value` 
						FROM 
							member_max mm 
									INNER JOIN exercise e ON mm.exercise_id = e.exercise_id
						WHERE 
							mm.member_id = ".$member_id." AND 
							mm.exercise_id = ".$exercise_id."
						ORDER BY
							max_date DESC,
							max_rep ASC
					";
		
		$query = $this->db->query($sql);	
		$result	=	$query->result_array();		
		return $result;
		
	}
	
	//Gets a single max value for runs and maxes by reps.
	//Returns a list of max values (based on reps) for weighted exercises (e.g. Bench Press 1-rep max, 3-rep max, 5-rep max, etc.)
	function get_member_exercise_max($exercise_id)
	{
		$member_id	=	$this->session->userdata('member_id');
		
		$exercise	=	$this->get_exercise($exercise_id);
		
		//Make sure there is a max recorded.
		//Get the max value for a member's exercise
		if ($exercise->max_type	===	'T')
			$this->db->select_min('max_value');
		else 
			$this->db->select_max('max_value');
	
		$this->db->where('member_id', $member_id);
		$this->db->where('exercise_id', $exercise_id);
		$query = $this->db->get('member_max');

		$result	=	$query->result_array();		
		$max_value	=	$result[0]['max_value'];

		if (is_numeric($max_value)	==	FALSE)
			return FALSE; //No member max record yet.  Go back
		
		if ($exercise->max_type	===	'W')
			$sql	=	$this->_get_weighted_max_query($exercise_id, $member_id);
		else
			$sql	=	$this->_get_time_or_rep_query($exercise_id, $member_id, $max_value);

		$query = $this->db->query($sql);	
		$result	=	$query->result_array();		
		return $result;
	}
		
	function get_exercise_list($member_id = 0)
	{		
		$sql	=	"SELECT 
						 e.exercise_id
						,e.max_type
						,e.title
						,COUNT(mm.exercise_id)	AS	recorded_max
					FROM 
						exercise e
							LEFT JOIN (SELECT * FROM member_max WHERE member_id = ".$member_id.") mm ON
								e.exercise_id = mm.exercise_id 
					WHERE 
						IFNULL(max_type,'') <> ''
					GROUP BY
						e.exercise_id, e.max_type, e.title
					ORDER BY 
						 e.max_type
						,e.title";
		
		$query = $this->db->query($sql);
				
		if ($query->num_rows() == 0)
			return FALSE;

		return $query->result_array();
	}	

	function get_exercise($exercise_id)
	{
		$query = $this->db->limit(1)->get_where('exercise', array('exercise_id' => $exercise_id));
		if ($query->num_rows() == 0)
			return false;
		
		return $query->row();

	}
	
	function get_distinct_user_lifts()
	{
		$member_id	=	$this->session->userdata('member_id');
		$sql		=	$this->_get_distinct_user_lifts_query($member_id);
		
		$query = $this->db->query($sql);

		if ($query->num_rows() == 0)
			return false;
		
		return $query->result_array();
	}
	
	function _get_distinct_user_lifts_query($member_id)
	{
		$sql	=	"	SELECT 
							e.exercise_id
							, e.max_type
							, e.title
							,COUNT(mm_id) AS lift_count
						FROM 
							member_max mm 
								INNER JOIN 
									exercise e 
										ON mm.exercise_id = e.exercise_id
						WHERE 
							mm.member_id = ".$member_id."
						GROUP BY
								e.exercise_id
							, e.max_type
							, e.title
						ORDER BY
							max_type DESC, 
							title";
		
		return $sql;
	}
	
	function _get_user_rep_max_snapshot_query($member_id, $member_max_date = '')
	{       
                $consider_all_max_dates = $member_max_date === '';
                $member_max_date = $member_max_date === '' ? '9999-12-12' : $member_max_date;
                
		$sql	=	"	SELECT super_order, max_type, exercise, max_rep, MAX(max_date) AS max_date, `max_value`, lift_year_month
						FROM
							(SELECT 
								CASE WHEN m1.max_rep = '1-rep' THEN (0) ELSE 1 END AS `super_order`
								,e.max_type
								,e.title AS `exercise`
								,CONCAT(m1.max_rep, '-rep')				AS max_rep
								,DATE_FORMAT(m1.max_date,'%c/%e/%Y')	AS max_date
								,m1.max_value
                                                                ,DATE_FORMAT(m1.max_date,'%Y-%m')	AS lift_year_month
							FROM 
								(SELECT * FROM member_max WHERE DATE_FORMAT(max_date,'%Y-%m') <= '".$member_max_date."') AS m1
									INNER JOIN 
										exercise e ON 
											m1.exercise_id = e.exercise_id 
									LEFT JOIN (SELECT * FROM member_max WHERE DATE_FORMAT(max_date,'%Y-%m') <= '".$member_max_date."') AS m2
										ON (m1.max_rep = m2.max_rep AND m1.member_id = m2.member_id AND m1.exercise_id = m2.exercise_id AND m1.max_value < m2.max_value)
							WHERE 
															m2.mm_id IS NULL AND 
															m1.member_id	= ".$member_id." AND
															e.max_type = 'W') one_rep_table";
                
                $sql	.=	$consider_all_max_dates ? '' : " WHERE lift_year_month = '".$member_max_date."'";
		$sql	.=	" GROUP BY  super_order, max_type, exercise, max_rep, `max_value` ";
		$sql	.=	" UNION ";
		$sql	.=	"	SELECT super_order, max_type, exercise, max_rep, MAX(max_date) AS max_date, `max_value`, lift_year_month
						FROM

							(SELECT 
								1 AS `super_order`
								, e.max_type
								, e.title AS exercise
								, '' AS max_rep
								,DATE_FORMAT(mm.max_date,'%c/%e/%Y') AS max_date
								, CASE WHEN e.max_type = 'T' 
									THEN 
									(
										CONCAT_WS(':',FLOOR(mm.max_value / 60),
										LPAD( CAST(FLOOR((mm.max_value - FLOOR(mm.max_value / 60) * 60)) AS CHAR(2))   ,2,'0'))
									)
									ELSE mm.max_value
									END								AS `max_value`
                                                                ,DATE_FORMAT(mm.max_date,'%Y-%m')	AS lift_year_month
								FROM 
								member_max mm 
									INNER JOIN
										(
											SELECT member_id, aa.exercise_id, 
											CASE WHEN bb.max_type = 'T' THEN (MIN(MAX_VALUE)) ELSE MAX(MAX_VALUE) END  AS MAX_VALUE 
													FROM member_max aa INNER JOIN  exercise bb ON aa.exercise_id = bb.exercise_id 
                                                                                                        WHERE DATE_FORMAT(aa.max_date,'%Y-%m') <= '".$member_max_date."'
                                                                                                        GROUP BY member_id, exercise_id                                                                                                        
										) member_max_date ON mm.exercise_id = member_max_date.exercise_id AND mm.member_id = member_max_date.member_id AND mm.max_value = member_max_date.max_value
									INNER JOIN 
										exercise e ON 
											mm.exercise_id = e.exercise_id

								WHERE
									mm.member_id = ".$member_id." AND
									e.max_type <> 'W'
								GROUP BY 
									exercise
									,max_date
									,mm.max_value) the_rest ";
                $sql	.=	$consider_all_max_dates ? '' : " WHERE lift_year_month = '".$member_max_date."'";
		$sql	.=	" GROUP BY  super_order, max_type, exercise, max_rep, `max_value`
						ORDER BY " .
							(!$consider_all_max_dates ? "" : "super_order, max_type DESC,").
							"exercise, 
							max_rep";
		return $sql;
	}
	
	//Get the max for rep or time.  If Time max, then format field for time
	//NOTE:  There will only be one value of maxes stored by time or reps 
	function _get_time_or_rep_query($exercise_id, $member_id, $max_value)
	{
		$sql	=	"	SELECT	 
								 'Personal Best'						AS max_rep
								,DATE_FORMAT(m1.max_date,'%c/%e/%Y')	AS previous_max_date
								,CASE WHEN e.max_type = 'T' 
									THEN 
									(
										CONCAT_WS(':',FLOOR(m1.max_value / 60),
										LPAD( CAST(FLOOR((m1.max_value - FLOOR(m1.max_value / 60) * 60)) AS CHAR(2))   ,2,'0'))
									)
									ELSE m1.max_value
									END									AS `max_value`
								FROM 
								member_max m1
									INNER JOIN
										exercise e ON
											m1.exercise_id	=	e.exercise_id
							WHERE 
								m1.max_value	=	".$max_value." AND
								m1.member_id	=	".$member_id." AND
								m1.exercise_id	=	".$exercise_id."
									";	
		return $sql;
	}
	
	//Get a list of maxes for any-rep maxes (e.g. 1-rep max, 3-rep max)
	function _get_weighted_max_query($exercise_id, $member_id)
	{
		$sql	=	"	SELECT 
							max_rep, MAX(previous_max_date) AS previous_max_date, `max_value`
						FROM
							(	SELECT 
									CONCAT(m1.max_rep, '-rep')				AS max_rep
									,DATE_FORMAT(m1.max_date,'%c/%e/%Y')	AS previous_max_date
									,m1.max_value
								FROM 
									member_max m1
										LEFT JOIN member_max m2
											ON (m1.max_rep = m2.max_rep AND m1.member_id = m2.member_id AND m1.exercise_id = m2.exercise_id AND m1.max_value < m2.max_value)
								WHERE 
									m2.mm_id IS NULL AND 
									m1.exercise_id	= ".$exercise_id." AND
									m1.member_id	= ".$member_id.") reps
						GROUP BY
							max_rep, `max_value`

						UNION

						SELECT 
							'Average, Male, 1-rep' AS max_rep
							,'--' AS previous_max_date
							,IFNULL(FLOOR(AVG(t.`max_value`)),'--') AS `max_value`
						FROM
							(
							SELECT 
								DISTINCT m1.member_id, m1.max_rep, m1.`max_value` 
							FROM 
								member m 
									INNER JOIN
										member_max m1 ON
											m.member_id	=	m1.member_id AND
											m.gender 	=	'M'
											LEFT JOIN member_max m2
												ON (m1.max_rep = m2.max_rep AND m1.member_id = m2.member_id AND m1.exercise_id = m2.exercise_id AND m1.max_value < m2.max_value)
							WHERE 
								m2.mm_id IS NULL AND 
								m1.max_rep = 1 AND
								m1.exercise_id	= ".$exercise_id."
							) AS t

						UNION 

						SELECT 
							'Average, Female, 1-rep' AS max_rep
							,'--' AS previous_max_date
							,IFNULL(FLOOR(AVG(t.`max_value`)),'--') AS `max_value`
						FROM
							(
							SELECT 
								DISTINCT m1.member_id, m1.max_rep, m1.`max_value` 
							FROM 
								member m 
									INNER JOIN
										member_max m1 ON
											m.member_id	=	m1.member_id AND
											m.gender 	=	'F'
											LEFT JOIN member_max m2
												ON (m1.max_rep = m2.max_rep AND m1.member_id = m2.member_id AND m1.exercise_id = m2.exercise_id AND m1.max_value < m2.max_value)
							WHERE 
								m2.mm_id IS NULL AND 
								m1.max_rep = 1 AND
								m1.exercise_id	= ".$exercise_id."
							) AS t

						ORDER BY 
							max_rep ";
		
		return $sql;
	}
}

/* End of file exercise_model.php */
/* Location: ./system/application/models/exercise_model.php */