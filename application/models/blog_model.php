<?php
/** 
 * Blog_model
 * 
 * @author Ray Nowell
 *	
 */ 
class Blog_model extends CI_Model {

	function Blog_model()
	{
		parent::__construct();
	}
	
        public function get_blogs($blog_id = false)
	{

		$sql = "
				SELECT  
					DATE_FORMAT(blog_date,'%W %M %e, %Y') AS formatted_blog_date,
					headline,
					blog_text,
                                        publish
				FROM 
					blog b
                                ".
                        ($blog_id ? 'WHERE blog_id = '.$blog_id : '' )
                        ."
				ORDER BY 
					b.blog_date DESC
                    ";
		
		$query = $this->db->query($sql);

		if ($query->num_rows() == 0)
			return FALSE;
		
		return	$query->result_array();
	}
}

/* End of file blog_model.php */
/* Location: ./system/application/models/blog_model.php */