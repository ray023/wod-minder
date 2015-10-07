<?php
/** 
 * Find_a_fit_model
 * 
 * @author Ray Nowell
 *	
 */ 
class Find_a_fit_model extends CI_Model {
	
	function Find_a_fit_model()
	{
		parent::__construct();
	}
        
        public function processs_record($data)
        {            
            $data['modified_date']	=	date("Y-m-d H:i:s");
            $this->db->update('find_a_fit_stats',   $data, 'faf_stats_id = '.$data['faf_stats_id']);
        }
        
        public function get_unprocessed_faf_records()
        {
		$sql	=   "
                                SELECT 
                                        faf_stats_id
                                        ,latitude
                                        ,longitude
                                        ,ifnull(search_term,'') as search_term
                                FROM 
                                        find_a_fit_stats fafs 
                                WHERE
                                        ifnull(processed,0) = 0 
                                ORDER BY 
                                        fafs.created_date DESC";

		$query = $this->db->query($sql);
		
		if ($query->num_rows() == 0)
				return false;
		
		return $query->result_array();		            
        }
        
        public function get_faf_history()
	{
		$sql	=   "
                                SELECT 
                                    created_date
                                    ,faf_source
                                    , CASE WHEN IFNULL(processed,0) = 0 THEN 
                                            CASE WHEN IFNULL(search_term,'') = '' THEN  CONCAT_WS(',',latitude,longitude) 
                                            ELSE search_term END
                                            ELSE 
                                                CASE WHEN IFNULL(search_term,'') = '' THEN  
                                                    CASE WHEN country_political_short_code = 'US' THEN CONCAT_WS(', ',locality_political, administrative_area_level_1) 
                                                    ELSE CONCAT_WS(', ',country_political_long_code, administrative_area_level_1) END
                                                ELSE
                                                    search_term 
                                                END
                                        END AS location_data
                                FROM 
                                    find_a_fit_stats fafs 
                                ORDER BY 
                                        fafs.created_date DESC";

		$query = $this->db->query($sql);
		
		if ($query->num_rows() == 0)
				return false;
		
		return $query->result_array();		
	}

        function get_count_of_todays_feedback()
        {
            $todays_date = date( 'Y-m-d');
            $sql = "SELECT faff_id FROM find_a_fit_feedback WHERE DATE(log_date) = '".$todays_date."'";
            $query = $this->db->query($sql);
            return $query->num_rows;
        }
        
        function save_stat($data=null)
        {
            if ($data == null)
                return;
            
            $data['modified_date']	=	date("Y-m-d H:i:s");
            $data['created_date']       =       date('Y-m-d H:i:s');

       
            $this->db->insert('find_a_fit_stats', $data);             
        }
        
        function save_feedback($data = null)
        {
            if ($data == null)
                return;
            
            $this->db->insert('find_a_fit_feedback', $data); 
        }
	
	function get_closest_crossfits($latitude = 0, $longitude	=	0, $results_count = 5)
	{
		$sql	=	"
						SELECT 
							affil_name,
							url,
							latitude,
							longitude,
							TRUNCATE(SQRT(POWER((69.1 * (latitude - ".$latitude.") ), 2) + POWER((69.1 * (".$longitude." - longitude)) * COS(latitude / 57.3), 2)),1) AS distance
						FROM 
							find_a_fit_affiliates f
						ORDER BY 
							distance
						LIMIT ".$results_count." 
						";
		
		$query		= $this->db->query($sql);	

		return $query->result_array();

	}
	
	
}

/* End of file waleo_model.php */
/* Location: ./application/models/waleo_model.php */