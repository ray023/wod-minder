<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Blog Class
 * 
 * The purpose of this controller is to provide CrossFit gyms a web interface
 * to quckly save user WoDs
 * 
 * @package wod-minder
 * @subpackage controller
 * @category main-screen
 * @author Ray Nowell
 * 
 */
class Blog extends MY_Controller {
	

	public function index()
	{
		//$this->output->enable_profiler(TRUE);
		define('ID_IDENTIIFER',3);
                
                $blog_id = $this->uri->segment(ID_IDENTIIFER);//blog_id
		$this->load->model('Blog_model');
		//Get box name
		$blog_array	= $this->Blog_model->get_blogs($blog_id);

                $main_content   =   '';
                foreach($blog_array as $row) 
		{
                    $main_content .=    '<h1>'.$row['headline'].'</h1>';
                    $main_content .=    $row['blog_text'];
                    $main_content .=    '<p class="post-footer">';
                    $main_content .=    '<span class="date">'.$row['formatted_blog_date'].'</span>	';
                    $main_content .=    '</p>';
		}	

		$data['main_content']   =   $main_content;
		$data['title']		=	$blog_id ? $row['headline'] : 'WOD-Minder Blog';
		$data['heading']	=	$blog_id ? $row['headline'] : 'WOD-Minder Blog';
                $data['site_description']   =	$blog_id ? substr(strip_tags($row['blog_text']), 0, 55).'...'  : 'WOD-Minder is a web app enabling CrossFit members to maintain WOD scores and Maxes.';
                $data['main_content']   =   $main_content;
		$data['view']		=	'blog_view';
		
		$this->load->vars($data);
		$this->load->view('blog_view', $data);

		return;
		
	}

}

/* End of file blog.php */
/* Location: ./application/controllers/blog.php */