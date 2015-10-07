<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * WOD Class
 * 
 * @package wod-minder
 * @subpackage controller
 * @category drawing
 * @author Ray Nowell
 * @description Drawings are contests held for the WOD-Minder site.  This controller will display elligible users
 * 
 */
class Drawing extends MY_Controller {

	function __construct() 
	{
		parent::__construct();
	}
			
	/**
	 * Index Page for this controller.
	 *
	 * @access public
	 */
	public function index()
	{		
                $this->load->model('Drawing_model');
		$drawing_array = $this->Drawing_model->get_5000_wod_giveaway_members();
                
                $alt_row = 1;
                $drawing_grid = '';
                foreach($drawing_array as $row) 
                {
                        $is_odd         =	$alt_row%2==1;
                        $alt_row_class	=	$is_odd	? 'alternate-row' : '';
                        $alt_row_class	=	$row['member_id']   == $this->session->userdata('member_id')	?	'self-row'	:	$alt_row_class;
                        $drawing_grid .= '<div class="ui-block-a '.$alt_row_class.'">'.$row['member_name'].'</div>';
                        $drawing_grid .= '<div class="ui-block-b '.$alt_row_class.'">'.$row['box_name'].'</div>';
                        $alt_row++;
                }
                
                $data['eligible_members_grid'] =	$drawing_grid;
		$data['title']                  =	'5,000 WoD Giveaway!';
		$data['heading']                =	'5,000 WoD Giveaway!';
		$data['view']                   =	'mobile_drawing';
						
		$this->load->vars($data);
		$this->load->view('mobile_master', $data);
                
		return;
	}
	
	
}

/* End of file wod.php */
/* Location: ./application/controllers/drawing.php */