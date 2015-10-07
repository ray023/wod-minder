<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Kiosk Class
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
class Kiosk extends MY_Controller {
	
	/**
	 * Index Page for this controller.
	 *
	 * 
	 * 
	 * @access public
	 */
	public function index()
	{
		//$this->output->enable_profiler(TRUE);
		
		$this->load->library('form_validation');
		
		define('BOX_SEGMENT',3);
		$box_id	=	$this->uri->segment(BOX_SEGMENT);
		
		$data = null;
		$form_data = null;
		$form_data_array = array();
		$error_message = '';
		
		//If box id is empty,check for a cookie that has the box id
		if ($box_id == '')
		{
			$box_id	=	$this->input->cookie('kiosk_box', TRUE);
			//If box id still empty, then have user pick the box
			if ($box_id == '')
				redirect('kiosk/pick_box/');
		}
		
		//Set box id as cookie b/c user could have just selected a new box:
		$cookie = array(
			'name'   => 'kiosk_box',
			'value'  => $box_id,
			'expire' => 259200, //roughly three days (which is the longest kiosk should go w/o prompting
			);
		$this->input->set_cookie($cookie); 
		
		$data['title']			=	'Kiosk Menu';
		$data['heading']		=	'Kiosk Menu';
		$data['view']			=	'mobile_kiosk_menu';
		
		$this->load->vars($data);
		$this->load->view('mobile_master', $data);
		
	}
	
	public function view_daily_wod()
	{
		$box_id	=	$this->input->cookie('kiosk_box', TRUE);
		if ($box_id == '')
			redirect('kiosk/pick_box/');
		
		
		$this->load->model('Box_model');
		$box_wod_array	= $this->Box_model->get_box_wod_for_kiosk($box_id);
		
		$page_html = '';
		if (!$box_wod_array)
			$page_html = 'No WOD saved for today';
		else
		{
			foreach ($box_wod_array as $box_wod) 
			{
				$header	 =	'<div data-role="collapsible" data-collapsed="false" >';
				$tier_value = $box_wod['tier_name']	==	''	?	''	:	$box_wod['tier_name'].':  '	;
				$header .= '<h3>'.$tier_value.$box_wod['simple_title'].'</h3>';
				if ($box_wod['buy_in'] != '')
					$header .= '<h4>Buy In</h4>'.$box_wod['buy_in'];
				$header .= '<h4>WOD</h4>'.$box_wod['simple_description'];
				if ($box_wod['cash_out'] != '')
					$header .= '<h4>Cash Out</h4>'.$box_wod['cash_out'];
				$page_html .= $header . '</div>';

			}
		}
		
		$data['page_html'] = $page_html;
		
		$data['title']			=	'Daily WODs';
		$data['heading']		=	'Daily WODs';
		$data['view']			=	'mobile_kiosk_view_daily_wod';
		
		$this->load->vars($data);
		$this->load->view('mobile_master', $data);


	}
	
	public function save_daily_wod()
	{
		
		$this->load->library('form_validation');
		
		$data = null;
		$form_data = null;
		$form_data_array = array();
		$error_message = '';
		
		$box_id	=	$this->input->cookie('kiosk_box', TRUE);
		if ($box_id == '')
			redirect('kiosk/pick_box/');

		$this->load->model('Box_model');
		//Get box name
		$box_array	= $this->Box_model->get_box_info($box_id);
		$box_name	=	$box_array->box_name;
		
		//get the box wod info for current date
		$box_wod_array	= $this->Box_model->get_box_wod_for_kiosk($box_id);
		
		$simple_title = 'No WOD saved for today';
		$score_type = '';
		$bw_id = 0;
		
		if (!!$box_wod_array)
		{			
			//Check if there's any data that needs to be saved and try to save
			$this->form_validation->set_rules('user_login'	, 'User Login'	,	'trim|required');
			$this->form_validation->set_rules('user_password'	, 'Password'	,	'trim|required');
			if ($score_type	===	'T')
			{
				$this->form_validation->set_rules('score_minutes'	, 'Minutes'	,	'trim|required|numeric');
				$this->form_validation->set_rules('score_seconds'	, 'Seconds'	,	'trim|required|numeric');
				//just in case js doesn't work
				$this->form_validation->set_rules('score'			, 'Time'	,	'trim|required');
			}
			else
				$this->form_validation->set_rules('score'	, 'Score'	,	'trim|required');
			
			//
			if ($this->form_validation->run() == TRUE) 
			{
				//Get post data
				$user_login		=	$this->input->post('user_login');
				$user_password	=	$this->input->post('user_password');
				$this->load->model('Login_model');
				
				$authenticate_data	=	$this->Login_model->login_user($user_login,$user_password);
								
				if (!$authenticate_data['success'])
					$error_message	=	$authenticate_data['message'];
				else
				{
					$save_data['bw_id']			=	$this->input->post('bw_id');
					$save_data['member_id']		=	$authenticate_data['member_id'];
					$save_data['score']			=	$this->input->post('score');
					$save_data['rx']			=	$this->input->post('rx');
					$save_data['member_rating']	=	$this->input->post('member_rating');
									
					$this->load->model('Box_model');
					
					$save_result	=	$this->Box_model->save_kiosk_wod($save_data, $authenticate_data['display_name']);
					
					if (!$save_result['success'])
						$error_message	=	$save_result['message'];
					else
						$data['good_message']	=	'WOD saved for '.$user_login;					
				}
				
			}

			if ($error_message === '')
			{
				//START AUDIT
				$this->load->model('Audit_model');
				$audit_data['controller']	=	'kiosk_controller';
				$audit_data['ip_address']	=	$_SERVER['REMOTE_ADDR'];
				$audit_data['member_id']	=	$this->session->userdata('member_id');
				$audit_data['member_name']	=	$this->session->userdata('display_name');
				$audit_data['short_description']	=	'Failed Save';
				$audit_data['full_info']	=	$error_message;
				$this->Audit_model->save_audit_log($audit_data);
				//END AUDIT	
			}
			
                        
			$data['error_message'] = (validation_errors()) ? validation_errors() : $error_message;
                        
			foreach ($box_wod_array as $box_wod) {
				$form_data =	$this->_get_kiosk_wod_form_data($box_wod['score_type']);
				$temp_data = array_merge($box_wod, $form_data);
				array_push($form_data_array, $temp_data);
			}
			
			$data['form_data_array']	=	$form_data_array;
			if (count($form_data_array) == 1)
			{
				$first_form = $form_data_array[0];
				$simple_title	=	$first_form['simple_title'];
			}
		}
		
		$data['simple_title'] = $simple_title;
		$data['box_name'] = $box_name;

		$data['doc_ready_call']	=	'save_kiosk_wod_doc_ready();';
		$data['title']			=	'Kiosk Save';
		$data['heading']		=	'Kiosk Save';
		$data['view']			=	'mobile_kiosk_save_daily_wod';
		
		$this->load->vars($data);
		$this->load->view('mobile_master', $data);

		return;
	}
	
	public function results()
	{

		$this->load->model('Box_model');

		$box_id	=	$this->input->cookie('kiosk_box', TRUE);
		if ($box_id == '')
			redirect('kiosk/pick_box/');
		
		$box_array	= $this->Box_model->get_box_info($box_id);
		$box_name	=	$box_array->box_name;
		
		$data['box_name']	= $box_name;
		$data['box_id']		=	$box_id;
		
		$data['doc_ready_call']	=	'box_results_doc_ready();';
		$data['title']			=	'Kiosk Results for '.$box_name;
		$data['heading']		=	'Kiosk Results for '.$box_name;

		$data['logo'] = null;
		$data['css_header_background_url'] = null;
		switch ($box_name)
		{
			case 'CrossFit Irondale':
				$data['background_image_url']			=	base_url().'images/CID/cid_irondale_background.jpg';
				//$data['css_header_background_url']	=	base_url().'images/CID/cid_irondale_background.jpg';
				$data['logo']						=	base_url().'images/CID/cid.png';
				break;
			case 'AMP Strength & Conditioning':
				//$data['background_image_url']			=	base_url().'images/amp/background.jpg';
				//$data['css_header_background_url']	=	base_url().'images/CID/cid_irondale_background.jpg';
				//$data['logo']						=	base_url().'images/amp/logo.jpg';
				break;
			default:
				$data['logo'] = null;
				$data['css_header_background_url'] = null;
				
		}

				   
		$this->load->vars($data);
		$this->load->view('kiosk_results', $data);

		return;
		
	}
	
	//Load list of boxes for user to select
	//These should be actual boxes, not my demo or other non-boxes
	//Kiosk could be used at any crossfit for an event 
	//so all available crossfits should be listed
	public function pick_box()
	{
		$this->load->model('Box_model');
		
		$box_list	=	'';
		$box_array	= $this->Box_model->get_box_list(TRUE); //TRUE means exclude N/A and 'Other'
		
		foreach ($box_array as $row)
			$box_list .= '<li><a href="'.base_url().'index.php/kiosk/index/'.$row['box_id'].'" data-ajax="false">' .$row['box_name'] . '</a></li>';
		
		
		$data['box_list']			=	$box_list;
		$data['title']				=	'Pick Box';
		$data['heading']			=	'Pick Box';
		$data['view']				=	'mobile_kiosk_pick_box';
		$this->load->vars($data);
		$this->load->view('mobile_master', $data);
		
		return;
		
	}
	
	//Gets controls for both Box WODs and BenchMark WODs
	private function _get_kiosk_wod_form_data($score_type = 'O')
	{
		$data	=	null;

		switch ($score_type) 
		{
			
			case 'T':
				$score			=	'';
				$data['score']	=	$score;
				
				$data['score_minutes'] = array(
						'name'			=>	'score_minutes',
						'class'			=>	'score-minutes',
						'type'			=>	'number',
						'autocomplete'	=>	'off',
						'value'			=>	'',
						'data-inline'	=>	'true',
						);

				$data['score_seconds'] = array(
						'name'			=>	'score_seconds',
						'class'			=>	'score-seconds',
						'type'			=>	'number',
						'autocomplete'	=>	'off',
						'value'			=>	'',
						'data-inline'	=>	'true',
						);
						break;
			case 'I':
			case 'W':
			case 'O':
			default:
				$data['score'] = array(
										'id'			=>  '_score',
										'name'			=>	'score',
										'class'			=>	'score',
										'maxlength'		=>	'200',
										'autocomplete'	=>	'off',
										'value'			=>	''
									);
				break;
					
		}
		
		$data['rx'] = array(
				'id'			=>  '_rx',
				'name'			=>	'rx',
				'class'			=>	'rx',
				'data-mini'		=>	'true',
				'value'			=>	'1',
				'checked'		=>	'',
			);
		
		$member_rating_options		=	array(
												'-1'	=>	'',
												'5'		=>	'5 - Awesome!!',
												'4'		=>	'4 - Fun',
												'3'		=>	'3 - Ok',
												'2'		=>	'2 - Meh',
												'1'		=>	'1 - No way',
												);
		
		$member_rating_attrib	=	'id="_memberRating" class = "member-rating" data-native-menu="true"';

		$data['member_rating_dropdown'] =  form_dropdown('member_rating', $member_rating_options, '-1', $member_rating_attrib);

		$data['submit'] = array(
										'class'			=>	'ui-btn-hidden submit-button',
										'value'			=>	'Save',
										'aria-disabled'	=>	'false',
										'data-inline'	=>	'true',
										'data-theme'	=>	'b',
										'type'			=>	'Submit',
										);
		
		return $data;
	}

	//**********
	//AJAX CALLS
	//**********
	
	public function check_dates($box_id = 0)
	{
		$ret_val = '';
		$this->load->model('Kiosk_model');
		$date_values	= $this->Kiosk_model->check_dates($box_id);
		
		echo $date_values;
	}
	
	public function get_wod_buttons($box_id = 0)
	{
		$this->load->model('Box_model');
		$box_wod_array	= $this->Box_model->get_box_wod_for_kiosk($box_id);	
		
		if (!$box_wod_array)
		{
			echo '<h1>No wods saved for today</h1>';
			return;
		}
		$return_html = '';
		foreach ($box_wod_array as $box_wod) 
		{
			$tier_value = $box_wod['tier_name']	==	''	?	''	:	$box_wod['tier_name'].':  '	;
			$button_html	 =	'<button class="results-'.$box_wod['bw_id'].'">'.$tier_value.$box_wod['simple_title'].'</button>';
			$return_html .= $button_html;
		}
		
		echo $return_html;
		
	}
	
	public function get_wod_results($box_id = 0)
	{
		$this->load->model('Box_model');
		
		$box_wod_array	= $this->Box_model->get_box_wod_for_kiosk($box_id);	
		if (!$box_wod_array)
		{
			echo 'No wods saved for today';
			return;
		}
		
		$this->load->model('Kiosk_model');
		$return_html = '';
		foreach ($box_wod_array as $box_wod) {
			$tier_value = $box_wod['tier_name']	==	''	?	''	:	$box_wod['tier_name'].':  '	;
			$div_html	 =	'<div class="results-'.$box_wod['bw_id'].'">
								<table class="results-master-table">
								<col style="width:35%">
								<col style="width:35%">
								<col style="width:30%">
									<caption>'.$tier_value.$box_wod['simple_title'].'</caption>';
			
			$div_html	.=	'<tr><th>Men\'s</th><th>Women\'s</th><th>Ratings</th></tr>';
			$div_html	.=	'<tr>';
			
			$male_wod_results_array	=	$this->Kiosk_model->get_wod_results_for_kiosk($box_wod['bw_id'], 'm');
			if (!$male_wod_results_array)
				$div_html	.=	'<td>No results saved</td>';
			else
				$div_html	.=	'<td>'.$this->_build_gender_results_table($male_wod_results_array).'</td>';
			
			$female_wod_results_array	=	$this->Kiosk_model->get_wod_results_for_kiosk($box_wod['bw_id'], 'f');
			if (!$female_wod_results_array)
				$div_html	.=	'<td>No results saved</td>';
			else
				$div_html	.=	'<td>'.$this->_build_gender_results_table($female_wod_results_array).'</td>';
			
			$div_html	.=	'<td>'.$this->_get_box_wod_rating_html($box_wod['bw_id']).'</td>';
			
			
			$div_html	.=	'</tr>';
			$div_html	.=	'<tfoot><tr><td colspan=3>An asterisk(*) indicates the WOD was scaled.  Otherwise, the WOD is RX</td></tr></tfoot>';
			$div_html	.=	'</table></div>';

			$return_html .= $div_html;
		}
		
		echo $return_html;

		
	}
	private function _build_gender_results_table($result_array)
	{
		$table_html = '<table class="gender-results">';
		foreach ($result_array as $wod_result) {
			$score = $wod_result['score'].($wod_result['rx']	== 1 ? '' : '*');
			$table_html .= '<tr><td>'.$wod_result['member_name'].'</td><td class="numeric-td">'.$score.'</td></tr>';
		}
		$table_html .= '</table>';
		
		return $table_html;
	}
	
	private function _get_box_wod_rating_html($bw_id = 0)
	{
		$rating_html = '';
		$box_wod_rating_array			=	$this->Box_model->get_box_wod_rating($bw_id);
		$box_wod_rating_grid			=	'';
		if (!$box_wod_rating_array)
			return $rating_html;
		
		$vote_sum = 0;
		foreach($box_wod_rating_array as $row) 
		{
			$box_wod_rating_grid  .=	'<tr>';
			$box_wod_rating_grid  .=	'<td>'.$row['rating'].'</td>';
			$box_wod_rating_grid  .=	'<td class="numeric-td">'.$row['votes'].'</td>';
			$box_wod_rating_grid  .=	'<td class="numeric-td">'.$row['percentage'].'</td>';
			$box_wod_rating_grid  .=	'</tr>';
			
			$vote_sum += $row['votes'];
		}
		
		$tfoot = '<tfoot><tr><td>Total:</td><td class="numeric-td">'.$vote_sum.'</td><td class="numeric-td">100%</td></tr></tfoot>';
		//$div_html	.=	'<tfoot><td colspan=3>An asterisk(*) indicates the WOD was scaled.  Otherwise, the WOD is RX</td></tfoot>';
		
		
		$rating_html	=	'<table class="gender-results ratings-grid">'.
							'<tr>'.
							'<th>Rating</th>'.
							'<th>Votes</th>'.
							'<th>Percentage</th>'.
							'</tr>'.$tfoot.$box_wod_rating_grid;
		
		$rating_html .= '</table>';
		
		
		return $rating_html;
	}
}

/* End of file kiosk.php */
/* Location: ./application/controllers/kiosk.php */