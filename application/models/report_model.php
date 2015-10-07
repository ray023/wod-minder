<?php
/** 
 * Report model
 * 
 * @author Ray Nowell
 *	
 */ 
class Report_model extends CI_Model {
    
	function get_goal_report_data($member_id = '')
	{
		$goal_report_data = array();	
		$member_id	=	$member_id	== '' ? $this->session->userdata('member_id') : $member_id;
		
		$query_array = $this->_get_goal_queries($member_id);
		
		$query = $this->db->query($query_array['crossfit_total']);
		if ($query->num_rows() == 0)
			$goal_report_data['crossfit_total']	=	FALSE;
		else
			$goal_report_data['crossfit_total']	=	$query->result_array();
		
		
		$query = $this->db->query($query_array['new_crossfit_total']);
		if ($query->num_rows() == 0)
			$goal_report_data['new_crossfit_total']	=	FALSE;
		else
			$goal_report_data['new_crossfit_total']	=	$query->result_array();
		
		return $goal_report_data;
	}
	
	function get_max_report_data($member_id	=	'')
	{
		$max_report_data = array();	
		$member_id	=	$member_id	== '' ? $this->session->userdata('member_id') : $member_id;
		
		$query_array = $this->_get_max_queries($member_id);
		
		$query = $this->db->query($query_array['max_count_by_type']);
		if ($query->num_rows() == 0)
			$max_report_data['max_count_by_type']	=	FALSE;
		else
			$max_report_data['max_count_by_type']	=	$query->result_array();
		
		
		$query = $this->db->query($query_array['lift_percentage_change']);
		if ($query->num_rows() == 0)
			$max_report_data['lift_percentage_change']	=	FALSE;
		else
			$max_report_data['lift_percentage_change']	=	$query->result_array();
		
		$query = $this->db->query($query_array['maxes_most_saved']);
		if ($query->num_rows() == 0)
			$max_report_data['maxes_most_saved']	=	FALSE;
		else
			$max_report_data['maxes_most_saved']	=	$query->result_array();
		
		$query = $this->db->query($query_array['heaviest_lifts']);
		if ($query->num_rows() == 0)
			$max_report_data['heaviest_lifts']	=	FALSE;
		else
			$max_report_data['heaviest_lifts']	=	$query->result_array();
		
		$query = $this->db->query($query_array['old_max_dates']);
		if ($query->num_rows() == 0)
			$max_report_data['old_max_dates']	=	FALSE;
		else
			$max_report_data['old_max_dates']	=	$query->result_array();
		
		$query = $this->db->query($query_array['top_pr_months']);
		if ($query->num_rows() == 0)
			$max_report_data['top_pr_months']	=	FALSE;
		else
			$max_report_data['top_pr_months']	=	$query->result_array();
		
		$query = $this->db->query($query_array['relative_strength']);
		if ($query->num_rows() == 0)
			$max_report_data['relative_strength']	=	FALSE;
		else
			$max_report_data['relative_strength']	=	$query->result_array();
		
		return $max_report_data;
	}
	
	function get_wod_report_data($member_id	=	'')
	{
		$wod_report_data = array();
		$member_id	=	$member_id	== '' ? $this->session->userdata('member_id') : $member_id;
		
		$query_array = $this->_get_wod_queries($member_id);
		
		$query = $this->db->query($query_array['wod_count_by_box']);
		if ($query->num_rows() == 0)
			$wod_report_data['where_wods_saved_array']	=	FALSE;
		else
			$wod_report_data['where_wods_saved_array']	=	$query->result_array();

		$query = $this->db->query($query_array['when_you_wod']);
		if ($query->num_rows() == 0)
			$wod_report_data['when_you_wod']	=	FALSE;
		else
			$wod_report_data['when_you_wod']	=	$query->result_array();
		
		$query = $this->db->query($query_array['box_wod_count_month_years']);
		if ($query->num_rows() == 0)
			$wod_report_data['box_wod_count_month_years']	=	FALSE;
		else
			$wod_report_data['box_wod_count_month_years']	=	$query->result_array();
		
				//benchmark_wod_count
		$query = $this->db->query($query_array['benchmark_wod_count']);
		if ($query->num_rows() == 0)
			$wod_report_data['benchmark_wod_count']	=	FALSE;
		else
			$wod_report_data['benchmark_wod_count']	=	array_shift(reset($query->result_array()));		
		
		$query = $this->db->query($query_array['benchmark_wod_count']);
		if ($query->num_rows() == 0)
			$wod_report_data['benchmark_wod_count']	=	FALSE;
		else
			$wod_report_data['benchmark_wod_count']	=	$query->result_array();
		
		$query = $this->db->query($query_array['benchmark_wod_locations']);
		if ($query->num_rows() == 0)
			$wod_report_data['benchmark_wod_locations']	=	FALSE;
		else
			$wod_report_data['benchmark_wod_locations']	=	$query->result_array();
		
		$query = $this->db->query($query_array['wods_by_type']);
		if ($query->num_rows() == 0)
			$wod_report_data['wods_by_type']	=	FALSE;
		else
			$wod_report_data['wods_by_type']	=	$query->result_array();
		
		
		$query = $this->db->query($query_array['quickest_wods']);
		if ($query->num_rows() == 0)
			$wod_report_data['quickest_wods']	=	FALSE;
		else
			$wod_report_data['quickest_wods']	=	$query->result_array();
		
		$query = $this->db->query($query_array['longest_wods']);
		if ($query->num_rows() == 0)
			$wod_report_data['longest_wods']	=	FALSE;
		else
			$wod_report_data['longest_wods']	=	$query->result_array();
		
		
		$timed_wod_05_array = Array();
		$timed_wod_10_array = Array();
		$timed_wod_15_array = Array();
		$timed_wod_20_array = Array();
		$timed_wod_30_array = Array();
		$timed_wod_40_array = Array();
		$timed_wod_41_and_higher = Array();
		$timed_wod_total_array = Array();

		$wod_05	=	sprintf($query_array['timed_wod_query'], '< 5 minute wods', '0','300');
		$query = $this->db->query($wod_05);
		if ($query->num_rows() > 0)
			$timed_wod_05_array	=	reset($query->result_array());
		
		$wod_10	=	sprintf($query_array['timed_wod_query'], '5 - 10 minute wods', '301','600');
		$query = $this->db->query($wod_10);
		if ($query->num_rows() > 0)
			$timed_wod_10_array	=	reset($query->result_array());
		
		$wod_15 = sprintf($query_array['timed_wod_query'], '10 - 15 minute wods', '601', '900');
		$query = $this->db->query($wod_15);
		if ($query->num_rows() > 0)
			$timed_wod_15_array = reset($query->result_array());

		$wod_20 = sprintf($query_array['timed_wod_query'], '15 - 20 minute wods', '901', '1200');
		$query = $this->db->query($wod_20);
		if ($query->num_rows() > 0)
			$timed_wod_20_array = reset($query->result_array());
		
		$wod_30 = sprintf($query_array['timed_wod_query'], '20 - 30 minute wods', '1201', '1800');
		$query = $this->db->query($wod_30);
		if ($query->num_rows() > 0) 
			$timed_wod_30_array = reset($query->result_array());
		
		$wod_40 = sprintf($query_array['timed_wod_query'], '30 - 40 minute wods', '1801', '2400');
		$query = $this->db->query($wod_40);
		if ($query->num_rows() > 0) 
			$timed_wod_40_array = reset($query->result_array());
		
		$wod_41_and_higher = sprintf($query_array['timed_wod_query'], '> 40 Minutes', '2401', '99999');
		$query = $this->db->query($wod_41_and_higher);
		if ($query->num_rows() > 0) 
			$timed_wod_41_and_higher = reset($query->result_array());
		
		$wod_total = sprintf($query_array['timed_wod_query'], 'Total', '0', '99999');
		$query = $this->db->query($wod_total);
		if ($query->num_rows() > 0) 
			$timed_wod_total_array = reset($query->result_array());
		
		$wod_report_data['timed_wod_array'] = array_merge(array($timed_wod_05_array), 
													array($timed_wod_10_array), 
													array($timed_wod_15_array), 
													array($timed_wod_20_array), 
													array($timed_wod_30_array),
													array($timed_wod_40_array), 
													array($timed_wod_41_and_higher), 
													array($timed_wod_total_array)
				   );
		
		return $wod_report_data;
		
	}
	
	function _get_wod_queries($member_id)
	{
		$query_array = Array();
		
		$query_array['wod_count_by_box'] = "SELECT 
												b.box_abbreviation AS place
												,COUNT(mw.mw_id) AS wod_count
											FROM 
												member_wod mw
													INNER JOIN 
														box_wod bw ON
															mw.bw_id = bw.bw_id
																INNER JOIN 
																	box b ON
																			bw.box_id = b.box_id
											WHERE 
												mw.member_id = ".$member_id."
											GROUP BY b.box_id
											UNION
											SELECT 
												'Custom WOD' AS place, 
												COUNT(mw.mw_id) AS wod_count
											FROM 
												member_wod mw
											WHERE 
												mw.member_id = ".$member_id." AND (IFNULL(mw.bw_id,0) = 0) AND (IFNULL(mw.wod_id,0) = 0)
											GROUP BY mw.member_id
											UNION
											SELECT 
												'Solo Benchmark WODs' AS place, 
												COUNT(mw.mw_id) AS wod_count
											FROM 
												member_wod mw
											WHERE 
												mw.member_id = ".$member_id." AND (IFNULL(mw.wod_id,0) > 0)
											GROUP BY mw.member_id
											;";
		
		
		$query_array['box_wod_count_month_years'] = "SELECT 
																DATE_FORMAT(CASE WHEN bw.wod_date IS NULL THEN mw.wod_date ELSE bw.wod_date END,'%M %Y')	AS lift_year_month 
															   ,COUNT( DATE_FORMAT(CASE WHEN bw.wod_date IS NULL THEN mw.wod_date ELSE bw.wod_date END,'%Y-%m')) AS count_of_monthly_wods
														   FROM 
															   member_wod mw	
																LEFT JOIN 
																	box_wod bw ON mw.bw_id = bw.bw_id
														   WHERE
															   mw.member_id = ".$member_id." 
														   GROUP BY 
															   lift_year_month 
														   ORDER BY 
															   bw.wod_date DESC
														   ";
		
		$query_array['benchmark_wod_count'] = "
												SELECT 
													COUNT(mw.mw_id) AS benchmark_wod_count_total
												FROM 
													member_wod mw
														LEFT JOIN 
															box_wod bw ON
																mw.bw_id = bw.bw_id
												WHERE 
													mw.member_id = ".$member_id." AND 
													(IFNULL(mw.wod_id,0) > 0 OR IFNULL(bw.wod_id,0) > 0);
															";
		
		$query_array['when_you_wod'] = "
											SELECT 
													IFNULL(class_time_description,'Not Saved') AS class_time
												,	COUNT(IFNULL(class_time_description,'Not Saved')) AS ct_count 
												,	CONCAT(TRUNCATE((COUNT(IFNULL(class_time_description,'Not Saved')) / (SELECT COUNT(member_id) FROM member_wod a WHERE a.member_id = ".$member_id.")) * 100, 2),'%') AS ct_percent
											FROM 
												member_wod mw 
													LEFT JOIN 
														box_class_time bct ON 
															mw.bct_id = bct.bct_id 
											WHERE 
												member_id = ".$member_id." AND 
												IFNULL(bw_id,0) <> 0
											GROUP BY 
													member_id
												,	IFNULL(class_time_description,'Not Saved')
											ORDER BY 
												ct_count DESC
											";
		
		$query_array['benchmark_wod_count']	=	"
													SELECT 
														CASE WHEN IFNULL(mw.wod_id,0) > 0 THEN w1.wod_id
																ELSE w2.wod_id END AS ww_id,
															 CASE WHEN IFNULL(mw.wod_id,0) > 0 THEN w1.title
																ELSE w2.title END AS wod_name
															,COUNT(mw.mw_id) AS benchmark_wod_count_total
															,IFNULL(TRUNCATE(AVG(NULLIF(mw.member_rating,0)),2),'N/A') AS avg_rating
													FROM 
														member_wod mw
															LEFT JOIN wod w1 ON
																mw.wod_id = w1.wod_id
															LEFT JOIN 
																box_wod bw ON
																	mw.bw_id = bw.bw_id
																LEFT JOIN wod w2 ON
																	bw.wod_id = w2.wod_id

													WHERE 
														mw.member_id = ".$member_id." AND 
														(IFNULL(mw.wod_id,0) > 0 OR IFNULL(bw.wod_id,0) > 0)
													GROUP BY 
														ww_id
													ORDER BY benchmark_wod_count_total DESC
												";
		
		$query_array['benchmark_wod_locations']	=	"
													SELECT 
														b.box_abbreviation as box_name, 
														COUNT(mw.mw_id) AS benchmark_wod_count
													FROM 
													member_wod mw
														INNER JOIN 
															box_wod bw ON
																mw.bw_id = bw.bw_id
																	INNER JOIN 
																		box b ON
																			bw.box_id = b.box_id
													WHERE 
														mw.member_id = ".$member_id." AND (IFNULL(bw.wod_id,0) > 0)
													GROUP BY 
														b.box_id
													UNION
													SELECT 'On My Own' AS box_name, COUNT(mw.mw_id) AS benchmark_wod_count_total
													FROM 
													member_wod mw
														LEFT JOIN 
															box_wod bw ON
																mw.bw_id = bw.bw_id
													WHERE mw.member_id = ".$member_id." AND (IFNULL(mw.wod_id,0) > 0);
													";
		
			$query_array['wods_by_type']	=	"
													SELECT CASE WHEN b.score_type = 'I' THEN 'Rep/Round Count' 
																WHEN b.score_type = 'O' THEN 'Other' 
																WHEN b.score_type = 'T' THEN 'Time' 
																WHEN b.score_type = 'W' THEN 'Weight' 
																END AS score_type_text
																, COUNT(b.score_type) AS count_score_type 
																,IFNULL(TRUNCATE(AVG(NULLIF(m.member_rating,0)),2),'N/A') AS avg_rating
													FROM 
														member_wod m INNER JOIN 
															box_wod b ON m.bw_id = b.bw_id
													WHERE 
													m.member_id = ".$member_id."
													GROUP BY 
													b.score_type
													ORDER BY
													count_score_type DESC;
													";
			
				$wod_sql	=	"
									SELECT 
										b.box_abbreviation AS box_name
									  , bw.simple_title AS wod_name
									  , mw.mw_id
									  , bw.wod_date 
									  , CONCAT_WS(':',FLOOR(mw.score / 60),
										  LPAD( CAST((mw.score - FLOOR(mw.score / 60) * 60) AS CHAR(2))   ,2,'0'))
										  AS score_time
									  , CAST(score AS UNSIGNED) AS score_sort
									  , CASE WHEN IFNULL(mw.member_rating,0) = 5 THEN '5 - Awesome' 
															   WHEN IFNULL(mw.member_rating,0) = 4 THEN '4 - Fun' 
															   WHEN IFNULL(mw.member_rating,0) = 3 THEN '3 - Ok' 
															   WHEN IFNULL(mw.member_rating,0) = 2 THEN '2 - Meh' 
															   WHEN IFNULL(mw.member_rating,0) = 1 THEN '1 - No way'
															   WHEN IFNULL(mw.member_rating,0) = 0 THEN '0 - Unrated'
															   ELSE IFNULL(mw.member_rating,0) END AS rating
								  FROM 
									  member_wod mw 
										  INNER JOIN 
											  box_wod bw ON mw.bw_id = bw.bw_id
												  INNER JOIN 
													  box b ON bw.box_id = b.box_id
								  WHERE 
									  mw.member_id = ".$member_id." AND 
									  mw.score > 0 AND 
									  bw.score_type = 'T'
								  ORDER BY 
									  score_sort 
									";
				
				
				$query_array['quickest_wods'] = $wod_sql.' LIMIT 5;';
				$query_array['longest_wods'] = $wod_sql.' DESC LIMIT 5;';
				
				//START AUDIT
				$this->load->model('Audit_model');
				$audit_data['controller']	=	'report_model';
				$audit_data['ip_address']	=	$_SERVER['REMOTE_ADDR'];
				$audit_data['member_id']	=	$this->session->userdata('member_id');
				$audit_data['member_name']	=	$this->session->userdata('display_name');
				$audit_data['short_description']	=	'Longest WODS Query';
				$audit_data['full_info']	= str_replace('\'', "|", $query_array['longest_wods']) ;
				$audit_data['full_info']	= str_replace("\t", " ", $audit_data['full_info']) ;
				$audit_data['full_info']	= str_replace("\r\n", " NEW_LINE ", $audit_data['full_info']) ;
				$audit_data['full_info']	= str_replace('NEW_LINE', ' \r\n ', $audit_data['full_info']) ;
				$this->Audit_model->save_audit_log($audit_data);
				//END AUDIT
				
				
				$query_array['timed_wod_query'] = "	SELECT 
															mw.mw_id
														  , '%s' AS time_title
														  , COUNT(mw.score) AS wod_count
														  , SEC_TO_TIME(SUM(mw.score)) AS time_spent_wodding
														  , IFNULL(TRUNCATE(AVG(NULLIF(mw.member_rating,0)),2),'N/A') AS avg_rating
													  FROM 
														  member_wod mw 
															  INNER JOIN 
																  box_wod bw ON mw.bw_id = bw.bw_id
																	  INNER JOIN 
																		box b ON bw.box_id = b.box_id
													  WHERE 
														  mw.member_id = ".$member_id." AND 
														  mw.score > %s AND mw.score <= %s AND
														  bw.score_type = 'T'
													  GROUP BY
														  mw.member_id";
				
		return $query_array;

		
	}

	function _get_max_queries($member_id)
	{
		$query_array = Array();

		$query_array['max_count_by_type'] = "
												SELECT
													CASE WHEN e.max_type = 'I' THEN 'Rep/Round Count' 
															WHEN e.max_type = 'R' THEN 'Other' 
															WHEN e.max_type = 'T' THEN 'Time' 
															WHEN e.max_type = 'W' THEN 'Weight' 
															END AS max_type_text
												, COUNT(e.max_type) AS count_max_type FROM 
												member_max mm 
													INNER JOIN 
														exercise e ON
															mm.exercise_id = e.exercise_id
												WHERE
													mm.member_id = ".$member_id."
												GROUP BY e.max_type
												ORDER BY 
												count_max_type DESC
												; ";
		
		
		//First Lift/ Latest Lift, percentage change
		$query_array['lift_percentage_change']	=	"
			SELECT DISTINCT 
				CONCAT(flq.title, ', ', flq.max_rep,'-rep') AS exercise,
				flq.first_lift,
				flq.max_value AS flq_value,
				llq.last_lift,
				llq.max_value AS llq_value,
				FLOOR(((llq.max_value - flq.max_value) / flq.max_value) * 100) AS percentage_change
			 FROM 
				(
				SELECT 
				mm2.title,
				mm2.exercise_id,
				mm2.max_rep,
				mm2.first_lift,
				mm1.max_value
				FROM
					member_max mm1 JOIN
						(
						SELECT
							e.title
							,e.exercise_id
							,max_rep
							,MIN(max_date) AS first_lift
							,MAX(max_date) AS last_lift
						FROM
							member_max mm
								INNER JOIN
									exercise e ON	
										mm.exercise_id = e.exercise_id
						WHERE
							mm.member_id = ".$member_id." AND
							e.max_type = 'W'
						GROUP BY
							mm.exercise_id,
							mm.max_rep,
							e.title
						HAVING 
							COUNT(mm.exercise_id) > 1
						) mm2 ON
							mm1.exercise_id = mm2.exercise_id AND
							mm1.max_rep = mm2.max_rep AND
							mm1.max_date = first_lift
				WHERE
					member_id = ".$member_id.") flq 
						INNER JOIN
						(
						SELECT 
							mm2.title,
							mm2.exercise_id,
							mm2.max_rep,
							mm2.last_lift,
							mm1.max_value
						FROM
							member_max mm1 JOIN
								(
								SELECT
									e.title
									,e.exercise_id
									,max_rep
									,MIN(max_date) AS first_lift
									,MAX(max_date) AS last_lift
								FROM
									member_max mm
										INNER JOIN
											exercise e ON	
												mm.exercise_id = e.exercise_id
								WHERE
									mm.member_id = ".$member_id." AND
									e.max_type = 'W'
								GROUP BY
									mm.exercise_id,
									mm.max_rep,
									e.title
								HAVING 
									COUNT(mm.exercise_id) > 1
								) mm2 ON
									mm1.exercise_id = mm2.exercise_id AND
									mm1.max_rep = mm2.max_rep AND
									mm1.max_date = last_lift
						WHERE
							member_id = ".$member_id."
						) llq ON 
				flq.exercise_id = llq.exercise_id AND
				flq.max_rep = llq.max_rep
			ORDER BY
			 percentage_change DESC;	
			";
		
		$query_array['maxes_most_saved']	 = "
													SELECT 
														e.title, 
														COUNT(mm.exercise_id) AS save_count
													FROM 
														member_max mm
															INNER JOIN
																exercise e ON	
																	mm.exercise_id = e.exercise_id
													WHERE
														mm.member_id = ".$member_id." 
													GROUP BY 
														e.exercise_id,
														e.title
													ORDER BY 
														save_count DESC
													LIMIT 5
													";
		
		$query_array['heaviest_lifts']	=	"
												SELECT 
													  e.title
													, MAX(mm.max_value) AS best_max_value
												FROM 
													member_max mm
														INNER JOIN
															exercise e ON	
																mm.exercise_id = e.exercise_id
												WHERE
													e.max_type = 'W' AND
													mm.max_rep = 1 AND
													mm.member_id = ".$member_id." 
												GROUP BY 
													e.exercise_id,
													e.title
												ORDER BY 
													best_max_value DESC
												LIMIT 5
												";
		
		
		$query_array['old_max_dates']	= "
											SELECT 
												CONCAT(e.title, ', ', mm.max_rep,'-rep') AS exercise,
												MAX(mm.max_date) AS max_lift_date,
												DATEDIFF(CURDATE(), MAX(mm.max_date)) AS days_since_lift
											FROM 
												member_max mm
													INNER JOIN
														exercise e ON	
															mm.exercise_id = e.exercise_id
											WHERE
												e.max_type = 'W' AND 
												mm.member_id = ".$member_id." 
											GROUP BY 
												e.exercise_id,
												e.title,
												mm.max_rep
											HAVING
												days_since_lift > 90
											ORDER BY 
												max_lift_date 
											";
		
		$query_array['top_pr_months']	=	"
											SELECT 
												lift_year_month,
												COUNT(lift_year_month) AS count_lift_year_month
											FROM
												(
												SELECT super_order, max_type, exercise, max_rep, MAX(max_date) AS max_date, `max_value`, lift_year_month
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
																		member_max m1
																			INNER JOIN 
																				exercise e ON 
																					m1.exercise_id = e.exercise_id AND
																					DATE_FORMAT(m1.max_date,'%Y-%m') <= '9999-12-12'
																			LEFT JOIN member_max m2
																				ON (m1.max_rep = m2.max_rep AND m1.member_id = m2.member_id AND m1.exercise_id = m2.exercise_id AND m1.max_value < m2.max_value  AND DATE_FORMAT(m2.max_date,'%Y-%m') <= '9999-12-12')
																	WHERE 
																									m2.mm_id IS NULL AND 
																									m1.member_id	= ".$member_id."  AND
																									e.max_type = 'W') one_rep_table

												GROUP BY  super_order, max_type, exercise, max_rep, `max_value` 
												 UNION 
												SELECT super_order, max_type, exercise, max_rep, MAX(max_date) AS max_date, `max_value`, lift_year_month
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
																							WHERE DATE_FORMAT(aa.max_date,'%Y-%m') <= '9999-12-12'
																							GROUP BY member_id, exercise_id                                                                                                        
																				) member_max_date ON mm.exercise_id = member_max_date.exercise_id AND mm.member_id = member_max_date.member_id AND mm.max_value = member_max_date.max_value
																			INNER JOIN 
																				exercise e ON 
																					mm.exercise_id = e.exercise_id

																		WHERE
																			mm.member_id = ".$member_id."  AND
																			e.max_type <> 'W'
																		GROUP BY 
																			exercise
																			,max_date
																			,mm.max_value) the_rest 
												GROUP BY  super_order, max_type, exercise, max_rep, `max_value`
																ORDER BY 
																	exercise, 
																	max_rep
												) e
											GROUP BY 
												e.lift_year_month
											ORDER BY
												count_lift_year_month DESC
											LIMIT 5
											";
		
		$query_array['relative_strength']	=	"
													SELECT DISTINCT 
														 e.title AS `exercise`
														,m1.max_value
														,mwl.weight
														,ROUND(m1.max_value / mwl.weight,2) AS rs_ratio
													FROM 
														member_max m1
															INNER JOIN 
																exercise e ON 
																	m1.exercise_id = e.exercise_id AND
																	DATE_FORMAT(m1.max_date,'%Y-%m') <= '9999-12-01' AND
																	m1.max_rep = '1-rep'
															INNER JOIN
																(SELECT member_id, weight FROM member_weight_log mwl WHERE mwl.member_id = ".$member_id." ORDER BY weight_date DESC LIMIT 1) AS mwl ON
																	m1.member_id = mwl.member_id

															LEFT JOIN member_max m2
																ON (m1.max_rep = m2.max_rep AND m1.member_id = m2.member_id AND m1.exercise_id = m2.exercise_id AND m1.max_value < m2.max_value  AND DATE_FORMAT(m2.max_date,'%Y-%m') <= '9999-12-01')
													WHERE 
																					m2.mm_id IS NULL AND 
																					m1.member_id	= ".$member_id." AND
																					e.max_type = 'W'
													ORDER BY 
														  m1.max_value / mwl.weight DESC
														, exercise
											";
	
		
		return $query_array;
	}
	
	function _get_goal_queries($member_id)
	{
		$query_array = Array();

		$query_array['crossfit_total'] = "
											SELECT DISTINCT 
												e.title
											  , m1.max_value 
											FROM
											  (SELECT * FROM member_max WHERE DATE_FORMAT(max_date,'%Y-%m') <= '9999-12-12' AND max_rep = 1) AS m1 INNER JOIN
												  exercise e ON m1.exercise_id = e.exercise_id LEFT JOIN
													  (SELECT * FROM member_max WHERE DATE_FORMAT(max_date,'%Y-%m') <= '9999-12-12'  AND max_rep = 1) AS m2 ON 
															  (m1.max_rep = m2.max_rep AND m1.member_id = m2.member_id AND m1.exercise_id = m2.exercise_id AND m1.max_value < m2.max_value) 
											WHERE
											  m2.mm_id IS NULL AND 
											  m1.member_id = ".$member_id." AND 
											  e.max_type = 'W' AND 
											  e.title IN ('Deadlift', 'Shoulder Press', 'Back Squat')
											ORDER BY
												 m1.max_value
												,e.title 
			";
		
		$query_array['new_crossfit_total'] = "
												SELECT DISTINCT
													e.title
													,m1.max_value 
												FROM
													(SELECT * FROM member_max WHERE DATE_FORMAT(max_date,'%Y-%m') <= '9999-12-12' AND max_rep = 1) AS m1 INNER JOIN
														exercise e ON m1.exercise_id = e.exercise_id LEFT JOIN
														(SELECT * FROM member_max WHERE DATE_FORMAT(max_date,'%Y-%m') <= '9999-12-12'  AND max_rep = 1) AS m2 ON 
														(m1.max_rep = m2.max_rep AND m1.member_id = m2.member_id AND m1.exercise_id = m2.exercise_id AND m1.max_value < m2.max_value) 
												WHERE
												m2.mm_id IS NULL AND m1.member_id = ".$member_id." AND e.max_type = 'W' AND 
												e.title IN ('Bench Press', 'Overhead Squat', 'Power Clean', 'Squat Clean')
												ORDER BY 
												`max_value`, e.title
												";
		
		return $query_array;
	}
}

/* End of file report_model.php */
/* Location: ./system/application/models/report_model.php */