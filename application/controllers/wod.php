<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * WOD Class
 * 
 * @package wod-minder
 * @subpackage controller
 * @category wod
 * @author Ray Nowell
 * 
 */
class Wod extends MY_Controller {

	function __construct() 
	{
		parent::__construct();
		$this->load->library('form_validation');
	}
	
	public function todays_wod()
	{
		if (!$this->logged_in)
			redirect ('member/login');
		
		$box_id	=	$this->session->userdata('member_box_id');
		$this->load->model('Box_model');
		
		$wod_list = $this->Box_model->get_list_of_todays_wods($box_id);
		
		if (!$wod_list)
		{
			$box_array	= $this->Box_model->get_box_info($box_id);
			$box_name	=	$box_array->box_name;
			$this->session->set_flashdata('error_message', $box_name.' has not saved a WOD for today ('.date( 'm/d/Y').').');
			redirect('welcome/index/TRUE');
		}
		else if (count($wod_list) == 1)
		{
			$first_row = $wod_list[0];
			$wod_id = $first_row['bw_id'];
			redirect('wod/save_member_box_wod/'.$wod_id);
		}
		$wod_select_html = '';
		foreach($wod_list as $row) 
		{
			$data_theme = $row['user_saved'] ? 'data-theme="e"' : '';
			
			//Set main page grid
			$wod_select_html .= '<li '.$data_theme.'><a href="'.base_url().'index.php/wod/save_member_box_wod/'.$row['bw_id'].'" data-ajax="false">' . $row['tier_and_wod'] . '</a></li>';
		}
		
		$data['wod_select_html']	=	$wod_select_html;
		$data['title']				=	'Daily WODs';
		$data['heading']			=	'Daily WODs';
		$data['view']				=	'mobile_member_wod_daily_select';
						
		$this->load->vars($data);
		$this->load->view('mobile_master', $data);

		
		
		return;
	}
	
	/*
	 * Provides a decision engine that allows user to pinpoint and select the wod they want
	 */
	public function wod_wizard()
	{
		if (!$this->logged_in)
			redirect ('member/login');
		
		//START AUDIT
		$this->load->model('Audit_model');
		$audit_data['controller']	=	'wod_controller';
		$audit_data['ip_address']	=	$_SERVER['REMOTE_ADDR'];
		$audit_data['member_id']	=	$this->session->userdata('member_id');
		$audit_data['member_name']	=	$this->session->userdata('display_name');
		$audit_data['short_description']	=	'Using WOD Wizard';
		$this->Audit_model->save_audit_log($audit_data);
		//END AUDIT
		
		$this->load->model('Wod_model');
		
		$benchmark_wod_movements_html	=	'';
		$benchmark_wod_movements_array	=	$this->Wod_model->get_benchmark_wod_movements();

		foreach($benchmark_wod_movements_array as $row) 
		{
			$checkbox_markup	=	'<input id="_movement_'.$row['movement_id'].'" type="checkbox" value="'.$row['movement_id'].'">';
			$checkbox_markup	.=	'<label for="_movement_'.$row['movement_id'].'">'.$row['movement'].'</label>';
			$benchmark_wod_movements_html .= $checkbox_markup;
		}		
		$data['benchmark_wod_movements_html']	=	$benchmark_wod_movements_html;
		
		
		
		$wod_id					=	 0;
		$wod_category			=	'';
		$wod					=	'';
		$description			=	'';
		$hero_image				=	'';
		$note					=	'';
		$wod_wizard_html		=	'';
		$record_button			=	'';
		$data_theme = '';
		$movement_class_array	=	null;
		
		$wod_wizard_array		=	$this->Wod_model->get_wod_wizard_wods_and_movements();
		foreach($wod_wizard_array as $row) 
		{
			if ($wod_id != $row['wod_id'])
			{
				if ($wod_id != 0)
				{
					
					//Set HTML
					$movement_classes	=	implode(" ",$movement_class_array);
					$wod_div_html	=	"<div data-role=\"collapsible\" data-theme=\"".$data_theme."\" class=\"wod-div ".$movement_classes."\" data-wod_id=\"".$wod_id."\">".
										$wod.
										$hero_image.
										$wod_category.
										$description.
										$note.
										$record_button.
										"</div>";
					
					$wod_wizard_html	.=	$wod_div_html;
				}
				$wod_id			=	$row['wod_id'];
				$wod			=	'<h1>'.$row['wod'].'</h1>';
				$wod_category	=	'<h2>'.$row['wod_category'].'</h2><br>';
				
				switch ($row['wod_category']) {
					case 'Benchmark Girls':
						$data_theme = 'b';
						break;
					case 'New Girls':
						$data_theme = 'c';
						break;
					case 'Hero':
						$data_theme = 'a';
						break;
					case 'Other':
						$data_theme = 'e';
						break;
					default:
						$data_theme = '';
						break;
				}
				
				$description	=	'<h3>Description:</h3><br>'.$row['description'].'<br>';
				$note			=	$row['note'] === '' ? '' : '<h3>Note:</h3>'.$row['note'].'<br>';
				$record_button	=	anchor(base_url().'index.php/wod/save_member_benchmark_wod/wod_id/'.$row['wod_id'], 'Record '.$row['wod'], array(	'data-ajax'=>'false',
																																		'data-role'=>'button'));


				if (strtolower($row['wod_category'])	=== 'hero')
					$hero_image	= '<div class="hero-image"><img src="'.$row['image_name'].'"/></div>';
			    else
					$hero_image	= '';
				$movement_class_array	=	array();
			}
			array_push($movement_class_array,'movement-id-'.$row['movement_id']);
			
		}
		
		$record_button	=	anchor(base_url().'index.php/wod/save_member_benchmark_wod/wod_id/'.$row['wod_id'], 'Record '.$row['wod'], array(	'data-ajax'=>'false',
																																		'data-role'=>'button'));
		$wod_div_html	=	"<div data-role=\"collapsible\" data-theme=\"".$data_theme."\" class=\"wod-div ".$movement_classes."\" data-wod_id=\"".$wod_id."\">".
										$wod.
										$hero_image.
										$wod_category.
										$description.
										$note.
										$record_button.
										"</div>";
					
		$wod_wizard_html	.=	$wod_div_html;
		
		$data['doc_ready_call']	=	'wod_wizard_doc_ready();';
		$data['wod_wizard_html']	=	$wod_wizard_html;
		$data['title']				=	'WoD Wizard';
		$data['heading']			=	'WoD Wizard';
		$data['view']				=	'mobile_member_wod_wizard';
						
		$this->load->vars($data);
		$this->load->view('mobile_master', $data);
	
		
		return;


		
	}
	
	public function search()
	{
		if (!$this->logged_in)
			redirect ('member/login');
		
		$this->load->model('Wod_model');
		
		//Initialize
		$search_results		= FALSE; 
		$search_criteria	=	'';
		$wod_list	=	'';
		
		$this->form_validation->set_rules('search_criteria'	, 'Search Term'	,	'trim|required');
		if ($this->form_validation->run() == TRUE) 
		{
			$search_criteria	=	$this->input->post('search_criteria');
			$search_results		=	$this->Wod_model->search($search_criteria);
			
			//START AUDIT
			$this->load->model('Audit_model');
			$audit_data['controller']	=	'wod_controller';
			$audit_data['ip_address']	=	$_SERVER['REMOTE_ADDR'];
			$audit_data['member_id']	=	$this->session->userdata('member_id');
			$audit_data['member_name']	=	$this->session->userdata('display_name');
			$audit_data['short_description']	=	'Using Search';
			$audit_data['full_info']	=	$search_criteria;
			$this->Audit_model->save_audit_log($audit_data);
			//END AUDIT
		}
				
		$this->load->helper('form');
		$data['search_criteria'] = array(
										'name'			=>	'search_criteria',
										'id'			=>	'_searchCriteria',
										'value'			=>	set_value('search_criteria',$search_criteria)
									);
		
		$data['submit'] = array(
										'id'			=>	'_submit',
										'class'			=>	'ui-btn-hidden',
										'value'			=>	'Search',
										'aria-disabled'	=>	'false',
										'data-inline'	=>	'true',
										'data-theme'	=>	'c',
										'type'			=>	'Submit',
										);

		
		
		if (!$search_criteria)
			$data['$search_results']	=	'';
		else
		{
			
			if (!$search_results)
			{
				$wod_list	=	'<p>No WoDs found that match criteria "'.$search_criteria.'"</p>';
				$wod_list	.=	'<p>Try less words.  For example, if you searched <i>"Arm and Hammer"</i>, try <i>"Hammer"</i>.</p>';
				$wod_list	.=	'<p>Or, it could be an issue with puncuation.  If <i>"DT"</i> didn\'t work, try <i>"D.T."</i></p>';
				$wod_list	.=	'<p>Or just email me:  <a href="mailto:ray023@gmail.com?Subject=Search issue on '.$search_criteria.'">ray023@gmail.com</a></p>';
			}
			else
			{
				$wod_list = '<ul data-role="listview" data-theme="d" data-divider-theme="d">';
				foreach($search_results as $row) 
				{
					if ($row['bw_id'] != 0)
					{
					    $data_theme = 'e';
						$link_extra = 'save_member_box_wod/'.$row['bw_id'];
					}
					else if ($row['wod_id'] != 0)
					{
						$data_theme = 'a';
						$link_extra = 'save_member_benchmark_wod/mw_id/'.$row['mw_id'];
					}
					else
					{
						$data_theme = 'b';
						$link_extra = 'save_member_custom_wod/'.$row['mw_id'];
					}
					//
					//Set main page grid
					$wod_list .= '<li data-theme="'.$data_theme.'"><a href="'.base_url().'index.php/wod/'.$link_extra.'"  data-ajax="false">' . $row['simple_title'] . '</a><span class="ui-li-count">' . $this->mysql_to_human($row['wod_date']).'</span></li>';
				}
				$wod_list .= '</ul>';
				
			}

		}
		
		$data['wod_list']	=	$wod_list;
		$data['title']		=	'Search WODs';
		$data['heading']	=	'Search WODs';
		$data['view']		=	'mobile_member_wod_search';
		
		$data['error_message'] = validation_errors();
				
		$this->load->vars($data);
		$this->load->view('mobile_master', $data);
	
		
		return;
	}
	
	public function get_public_wods_with_menu_navigation()
	{

		$box_id			=	$this->uri->segment(3);
		$limit			=	10000;
		$tier			=	'';
		$custom_order_by  = '	year_value DESC
								,DATE_FORMAT(wod_date,\'%m\')
								,DATE_FORMAT(wod_date,\'%d\') DESC ';

		$this->load->model('Box_model');
		$public_box_wod_array		=	$this->Box_model->get_public_box_wods($box_id, $limit, '', $custom_order_by);
		if (!$public_box_wod_array)
		{
			echo $_GET['callback'] . '(' . "{'wodJsonHtml' : 'No wods saved for box'}" . ')';
			return true;
		}

		$year_value = $public_box_wod_array[0]['year_value'];
		$month_year_value = $public_box_wod_array[0]['month_year_value'];
		$div_button_years	=	'<div id="_buttonYears"><button id="year-'.$year_value.'" class="year-button">'.$year_value.'</button>';
		$div_button_months	=	'<div class="button-months"><div class="month-button year-'.$year_value.'"><button id="monthYear'.$year_value.'-'.$month_year_value.'">'.$month_year_value.'</button>';
		$div_wod_list		=	'<div class="wod-list monthYear'.$year_value.'-'.$month_year_value.'">';
		$return_html	=	'';
		foreach ($public_box_wod_array as $row) 
		{
			if ($row['year_value'] != $year_value || $row['month_year_value'] != $month_year_value )
			{
				if ($row['year_value'] != $year_value)
				{
					$div_button_years	.=	'<button id="year-'.$row['year_value'].'" class="year-button">'.$row['year_value'].'</button>';
					$div_button_months	.=	'</div><div class="month-button year-'.$row['year_value'].'"><button id="monthYear'.$row['year_value'].'-'.$row['month_year_value'].'">'.$row['month_year_value'].'</button>';
					$div_wod_list		.=	'</div><div class="wod-list monthYear'.$row['year_value'].'-'.$row['month_year_value'].'">';
				}
				else if ($row['month_year_value'] != $month_year_value)
				{
					$div_button_months	.=	'<button id="monthYear'.$row['year_value'].'-'.$row['month_year_value'].'">'.$row['month_year_value'].'</button>';
					$div_wod_list		.=	'</div><div class="wod-list monthYear'.$row['year_value'].'-'.$row['month_year_value'].'">';
				}
				
				$year_value = $row['year_value'];
				$month_year_value = $row['month_year_value'];
			}
			
			$div_wod_list	.= '<table>';
			$div_wod_list	.= '<col width="30%">';
			$div_wod_list	.= '<col width="40%">';
			$div_wod_list	.= '<col width="30%">';
			$div_wod_list	.= '<caption>'.$row['wod_date_long_format'].'</caption>';
			
			if ($row['image_name'] !== '' || $row['image_link'] !== '')
			{

				$image_url = $row['image_name'] === '' ? $row['image_link'] : (base_url().'staff_images/box_wod/'.$row['image_name']);

				$image_col_span = $row['daily_message'] === '' ? 3 : 1;

				$div_wod_list	.=	'<tr>';
				$div_wod_list	.=	'	<td colspan="'.$image_col_span.'"><img src="'.$image_url.'"/><br><span class="image-caption">'.$row['image_caption'].'</span></td>';

				if ($row['daily_message'] !== '')
					$div_wod_list	.=	'	<td colspan="2"><div class="daily-message">'.$row['daily_message'].'&nbsp;</div></td>';

				$div_wod_list	.=	'</tr>';
			}
			else if ($row['daily_message'] !== '')
			{
				$div_wod_list	.=	'<tr>';
				$div_wod_list	.=	'	<td colspan="3"><div class="daily-message">'.$row['daily_message'].'&nbsp;</div></td>';
				$div_wod_list	.=	'</tr>';
			}

			$td_wod_colspan = 1 + ($row['buy_in'] == '' ? 1 : 0) + ($row['cash_out'] == '' ? 1 : 0);
			$div_wod_list	.=	'<tr>'.
									($row['buy_in'] == '' ? '' : '<td>'.$row['buy_in'].'</td>').
									'<td colspan="'.$td_wod_colspan.'">'.$row['wod'].'&nbsp;</td>'.
									($row['cash_out'] == '' ? '' : '<td>'.$row['cash_out'].'</td>').
								'</tr>';
				
			$div_wod_list	.= '</table><br>';
			
		}
		
		$div_button_years	.=	'</div>';
		$div_button_months	.=	'</div></div>';
		$div_wod_list		.=	'</div>';
		
		$return_html	=	json_encode($div_button_years.$div_button_months.$div_wod_list);
		echo $_GET['callback'] . '(' . "{'wodJsonHtml' : '".str_replace('\'', '&#39;', $return_html)."'}" . ')';

		
	}
	public function get_public_wods_for_box_website_with_tier()
	{
		$box_id			=	$this->uri->segment(3);
		$tier_id			=	$this->uri->segment(4);
		$limit			=	$this->uri->segment(5)	==	'' ? 5 : $this->uri->segment(5) ;
		$return_format	=	$this->uri->segment(6)	==	'' ? 'div' : $this->uri->segment(6) ;
		
		$return_html	=	$this->_get_public_wod_html($box_id, $limit, $return_format, $tier_id);
		echo $_GET['callback'] . '(' . "{'wodJsonHtml' : '".str_replace('\'', '&#39;', $return_html)."'}" . ')';
	}
	
	public function get_public_wods_for_box_website()
	{
		$box_id			=	$this->uri->segment(3);
		$limit			=	$this->uri->segment(4)	==	'' ? 5 : $this->uri->segment(4) ;
		$return_format	=	$this->uri->segment(5)	==	'' ? 'div' : $this->uri->segment(5) ;
		
		$return_html	=	$this->_get_public_wod_html($box_id, $limit, $return_format);
		echo $_GET['callback'] . '(' . "{'wodJsonHtml' : '".str_replace('\'', '&#39;', $return_html)."'}" . ')';
	}
	
	public function get_public_wods_json_format($box_id = 0, $limit, $tier = '')
	{
		$box_id			=	$this->uri->segment(3);
		$limit			=	$this->uri->segment(4)	==	'' ? 5 : $this->uri->segment(4) ;
		
		//$return_html	=	$this->_get_public_wod_html($box_id, $limit, $return_format);
		$this->load->model('Box_model');
		$public_box_wod_array		=	$this->Box_model->get_public_box_wods($box_id, $limit, $tier);
		echo json_encode($public_box_wod_array);
	}
        
	public function get_public_wods_json_format_with_callback($box_id = 0, $limit, $tier = '')
	{
		$box_id			=	$this->uri->segment(3);
		$limit			=	$this->uri->segment(4)	==	'' ? 5 : $this->uri->segment(4) ;
		
		//$return_html	=	$this->_get_public_wod_html($box_id, $limit, $return_format);
		$this->load->model('Box_model');
		$public_box_wod_array		=	$this->Box_model->get_public_box_wods($box_id, $limit, $tier);
		$return_html = json_encode($public_box_wod_array);
		echo $_GET['callback'] . '(' . "{'wodJsonHtml' : '".str_replace('\'', '&#39;', $return_html)."'}" . ')';
	}
	
	function _get_public_wod_html($box_id = 0, $limit, $return_format, $tier = '')
	{
		
		$this->load->model('Box_model');
		$public_box_wod_array		=	$this->Box_model->get_public_box_wods($box_id, $limit, $tier);
		
		$return_html	=	'';
		if (!$public_box_wod_array)
		{
			echo $_GET['callback'] . '(' . "{'wodJsonHtml' : 'No wods saved for box'}" . ')';
			return true;
		}
		foreach ($public_box_wod_array as $row)
		{
			$box_array	= $this->Box_model->get_box_info($box_id);
			$box_name	=	$box_array->box_name;
			
			if ($return_format	=== 'div')
			{
				$return_html	.=	'<div class="wod-date">'.$row['wod_date_long_format'].'</div><div class="clear-both"></div>';

				if ($row['image_name'] !== '')
					$return_html	.=	'<div class="image-name"><img src="'.base_url().'staff_images/box_wod/'.$row['image_name'].'"/></div>';
				if ($row['image_link'] !== '')
					$return_html	.=	'<div class="image-link"><img src="'.$row['image_link'].'"/></div>';
				if ($row['image_caption'] !== '')
					$return_html	.=	'<div class="image-caption">'.$row['image_caption'].'</div>';
				if ($row['daily_message'] !== '')
					$return_html	.=	'<div class="daily-message'.( ($row['image_name'] !== '' || $row['image_link'] !== '') ? '-with-image' : '').'">'.$row['daily_message'].'</div><div class="clear-both"></div>';

				$return_html	.=	'<div class="buy-in">'.$row['buy_in'].'</div>'.
									'<div class="wod">'.$row['wod'].'</div>'.
									'<div class="cash-out">'.$row['cash_out'].'</div>'.
									'<div class="clear-both"></div>'
									;
			
			}
			else
			{
				$return_html	.= '<table>';
				$return_html	.= '<col width="30%">';
				$return_html	.= '<col width="40%">';
				$return_html	.= '<col width="30%">';
				$return_html	.= '<caption>'.$row['wod_date_long_format'].'</caption>';
				

				if ($row['image_name'] !== '' || $row['image_link'] !== '')
				{
					
					$image_url = $row['image_name'] === '' ? $row['image_link'] : (base_url().'staff_images/box_wod/'.$row['image_name']);
					
					if ($box_name == 'CrossFit North Atlanta')
						$image_col_span = 3;
					else
						$image_col_span = $row['daily_message'] === '' ? 3 : 1;
					
					$return_html	.=	'<tr>';
					$return_html	.=	'	<td colspan="'.$image_col_span.'"><img src="'.$image_url.'"/><br><span class="image-caption">'.$row['image_caption'].'</span></td>';

					if ($box_name == 'CrossFit North Atlanta')
						$return_html	.=	'</tr>';
					
					if ($row['daily_message'] !== '')
					{
						if ($box_name == 'CrossFit North Atlanta')
							$return_html	.=	'	<tr><td colspan="3"><div class="daily-message">'.'<h1>Daily Message</h1>'.$row['daily_message'].'&nbsp;</div></td></tr>';
						else
							$return_html	.=	'	<td colspan="2"><div class="daily-message">'.$row['daily_message'].'&nbsp;</div></td>';
					}
					$return_html	.=	'</tr>';
				}
				else if ($row['daily_message'] !== '')
				{
					$return_html	.=	'<tr>';
					$return_html	.=	'	<td colspan="3"><div class="daily-message">'.$row['daily_message'].'&nbsp;</div></td>';
					$return_html	.=	'</tr>';
				}

				$td_wod_colspan = 1 + ($row['buy_in'] == '' ? 1 : 0) + ($row['cash_out'] == '' ? 1 : 0);
				$return_html	.=	'<tr>'.
										($row['buy_in'] == '' ? '' : '<td>'.$row['buy_in'].'</td>').
										'<td colspan="'.$td_wod_colspan.'">'.$row['wod'].'&nbsp;</td>'.
										($row['cash_out'] == '' ? '' : '<td>'.$row['cash_out'].'</td>').
									'</tr>';
				
				$return_html	.= '</table><br>';
			}
		}
		
		//echo '<!DocType HTML><html><body>'.$return_html.'</body></html>';exit();
		
		return	json_encode($return_html);
		
	}
	
	/**
	 * Index Page for this controller.
	 *
	 * @access public
	 */
	public function index()
	{		
		if (!$this->logged_in)
			redirect ('member/login');
        
		redirect ('wod/save_member_box_wod');
		
		return;
	}
	/*
	 * Validation Rules:  js is used in conjunction with this function to validate the data the user saves.  
	 *                    When RX'ing, user must save an Integer for Rep/Round Count WODs and must save numeric for Max Weight WODs.
	 *                    Timed WODS is always restricted to time values; RX or not.
	 *                    "Other WODS", or (non-RX WODS which are also non-timed WODs) have no restriction on score value.
	 *                    the only "behind the scenes" validation is Time; js does the rest of the validation.
	 */
	public function save_member_box_wod()
	{
		//$this->output->enable_profiler(TRUE);
		if (!$this->logged_in)
			redirect ('member/login');
		
                $error_message = '';
		$this->load->model('Box_model');
		$box_wod_id			=	$this->uri->segment(3);
		$member_id			=	$this->session->userdata('member_id');
		$member_box_wod		=	$this->Box_model->get_member_box_wod($member_id, $box_wod_id);
                
		if ($member_box_wod['score_type']	===	'T')
		{
			$this->form_validation->set_rules('score_minutes'	, 'Minutes'	,	'trim|required|numeric');
			$this->form_validation->set_rules('score_seconds'	, 'Seconds'	,	'trim|required|numeric');
			//On 12/12/2012, had a weird issue where nobody's timed scores saved.
			//Could not reproduce so adding this line a precaution to make sure scores save
			$this->form_validation->set_rules('score'			, 'Time'	,	'trim|required');
		}
		else
			$this->form_validation->set_rules('score'	, 'Score'	,	'trim|required');
		
		
		$this->form_validation->set_rules('note'	, 'Note'	,	'trim');
                $this->form_validation->set_rules('so_id'	, 'Scale Option'	,	'trim');
                $this->form_validation->set_rules('member_rating'		, 'Member Rating'	,	'trim'); 
                $this->form_validation->set_rules('rx'	, 'RX'	,	'trim');
                $this->form_validation->set_rules('bct_id'	, 'Class Time'	,	'trim');
                
		
		if ($this->form_validation->run() == TRUE) 
		{
			$data['bw_id']			=	$member_box_wod['bw_id'];
			$data['member_id']		=	$this->session->userdata('member_id');
			$data['score']			=	$this->input->post('score');
			$data['note']			=	$this->input->post('note');
			$data['rx']			=	$this->input->post('rx');

			if ($member_box_wod['scale_id'] !== '' && $this->input->post('so_id') != false)
			{
				$data['so_id']  =	$this->input->post('so_id');
				$is_rx = $this->Box_model->is_scale_option_rx($this->input->post('so_id'));
				$data['rx'] = $is_rx;
			}
                        
			$data['member_rating']	=	$this->input->post('member_rating');	
			$data['bct_id']	=	$this->input->post('bct_id');
			
			$ret_val = $this->Box_model->save_member_box_wod($data);
			if ($ret_val['success']) 
			{
				$this->session->set_flashdata('good_message', 'Wod saved.');
				redirect('welcome/index/TRUE');
			}
			else
			{
				//reset rx
				unset($data['rx']);
				$error_message = $ret_val['message'];
			}
		}

		$data['title']		=	$member_box_wod['simple_title'];
		$data['tier']		=	$member_box_wod['tier'];
		$data['heading']	=	'Save WOD';
		$data['view']		=	'mobile_member_wod_save';
		
		$data['error_message'] = (validation_errors()) ? validation_errors() : $error_message;
		$data['doc_ready_call']	=	'save_member_wod_doc_ready();';
		
		$this->load->helper('form');
		$member_box_wod_data						=	$this->_get_wod_form_data($member_box_wod);
		$member_box_wod_data['bw_id']				=	$member_box_wod['bw_id'];
		$member_box_wod_data['wod_date']			=	$member_box_wod['wod_date'];
		$member_box_wod_data['buy_in']				=	$this->_mark_down_text($member_box_wod['buy_in']);
		$member_box_wod_data['simple_description']              =	$this->_mark_down_text($member_box_wod['simple_description']);
		$member_box_wod_data['cash_out']			=	$this->_mark_down_text($member_box_wod['cash_out']);
		$member_box_wod_data['wod_type']			=	$member_box_wod['wod_type'];
		$member_box_wod_data['score_type']			=	$member_box_wod['score_type'];
		
		$member_box_wod_data['box_wod_id']			=	$box_wod_id;
		
		$box_wod_rank_array	=	$this->Box_model->get_box_wod_rank($box_wod_id);
		if (!$box_wod_rank_array)
			$data['box_wod_rank_grid']	=	'No users have saved scores for this WOD.';
		else
		{
			$alt_row	=	0;
			$rank	=	1;
			$box_wod_rank_grid	=	'';
                        $current_scale_option = '';
                        $grid_header = '<div class="ui-grid-a"><div class="ui-block-a mobile-grid-header ">Rank</div><div class="ui-block-b mobile-grid-header number-block">Score</div>';
                        $grid_html = '<p>If you\'ve saved your score for this WOD, it will be <span class="self-row">highlighted in green</span>.</p>';
			foreach($box_wod_rank_array as $row) 
                        {
                            if ($current_scale_option != $row['scale_option'])    
                            {
                                if ($current_scale_option != '')
                                {
                                    $grid_html .= '<h2>'.$current_scale_option.'</h2>'.$grid_header.$box_wod_rank_grid.'</div>';
                                    $box_wod_rank_grid = '';
                                    $alt_row = 0;
                                }
                                $current_scale_option = $row['scale_option'];                                
                            }
                            $is_odd	=	$alt_row%2==1;
                            $alt_row_class	=	$is_odd	? 'alternate-row' : '';
                            $alt_row_class	=	$row['member_id']	== $this->session->userdata('member_id')	?	'self-row'	:	$alt_row_class;
                            $box_wod_rank_grid .= '<div class="ui-block-a  '.$alt_row_class.'">'.$rank.' - '.$row['full_name'].'</div>';
                            $box_wod_rank_grid .= '<div class="ui-block-b number-block '.$alt_row_class.'">'.$row['score'].'</div>';
                            $alt_row++;
                            $rank++;
                        }
                        
                        $grid_html .= '<h2>'.$current_scale_option.'</h2>'.$grid_header.$box_wod_rank_grid.'</div>';

			$data['box_wod_rank_grid']	=	$grid_html;
		}

		
		$data = array_merge($data, $member_box_wod_data);
				
		$this->load->vars($data);
		$this->load->view('mobile_master', $data);

	}
	public function save_member_benchmark_wod()
	{
		define('ID_IDENTIIFER',3);
		define('ID_VALUE',4);
		//$this->output->enable_profiler(TRUE);
		if (!$this->logged_in)
			redirect ('member/login');
		
                $error_message = '';
		$id_type = $this->uri->segment(ID_IDENTIIFER);//wod_id
		$id_value = $this->uri->segment(ID_VALUE);//id_value
		
		$this->load->model('Wod_model');
		
		$wod_data = '';
		if ($id_type === 'wod_id') //This will be a new wod to save
		{
			$wod_data					=	$this->Wod_model->get_benchmark_wod($id_value);
			$wod_data['wod_date']		=	date('m/d/y');
			$wod_data['score']			=	'';
			$wod_data['note']			=	'';
			$wod_data['member_rating']	=	'';
			$wod_data['rx']				=	'';
		}
		else //Saving an existing member's wod
			$wod_data					=	$this->Wod_model->get_member_benchmark_wod($id_value);
				
		if ($wod_data['score_type']	===	'I')
			$this->form_validation->set_rules('score'	, 'Score'	,	'trim|required|integer');
		elseif ($wod_data['score_type']	===	'W')
			$this->form_validation->set_rules('score'	, 'Score'	,	'trim|required|numeric');
		elseif ($wod_data['score_type']	===	'T')
		{
			$this->form_validation->set_rules('score_minutes'	, 'Minutes'	,	'trim|required|numeric');
			$this->form_validation->set_rules('score_seconds'	, 'Seconds'	,	'trim|required|numeric');
                        $this->form_validation->set_rules('score'		, 'Score'	,	'trim');
		}
		else
			$this->form_validation->set_rules('score'	, 'Score'	,	'trim|required');

		$this->form_validation->set_rules('note'	, 'Note'	,	'trim');
                $this->form_validation->set_rules('member_rating'		, 'Member Rating'	,	'trim'); 
                $this->form_validation->set_rules('rx'	, 'RX'	,	'trim');
		
		if ($this->form_validation->run() == TRUE) 
		{
			//Determines insert or update in data layer
			if ($id_type	!=	'wod_id')
				$data['mw_id']			=	$id_value;
			
			$data['wod_id']			=	$wod_data['wod_id']	;
			$data['wod_date']		=	$this->make_us_date_mysql_friendly($this->input->post('wod_date'));
			$data['member_id']		=	$this->session->userdata('member_id');
			$data['score']			=	$this->input->post('score');
			$data['note']			=	$this->input->post('note');
			$data['rx']				=	$this->input->post('rx');
			$data['member_rating']	=	$this->input->post('member_rating');	
			
			$ret_val = $this->Wod_model->save_member_benchmark_wod($data);
			if ($ret_val['success']) 
			{
				$this->session->set_flashdata('good_message', 'Benchmark WOD saved.');
				redirect('welcome/index/TRUE');
			}
			else
				$error_message = $ret_val['message'];

		}

		$data['title']		=	$wod_data['wod_name'];
		$data['heading']	=	'Save Benchmark WOD';
		$data['view']		=	'mobile_member_benchmark_wod_save';
		$data['id_type']	=	$id_type;
		$data['id_value']	=	$id_value;
		
		$data['error_message'] = (validation_errors()) ? validation_errors() : $error_message;
		$data['doc_ready_call']	=	'save_member_wod_doc_ready();';
		
		$this->load->helper('form');
		$member_benchmark_wod_data					=	$this->_get_wod_form_data($wod_data, TRUE); //true for saving benchmark wod
		
		$data = array_merge($data, $wod_data, $member_benchmark_wod_data);
				
		$this->load->vars($data);
		$this->load->view('mobile_master', $data);
		
	}
	//This saves a WOD a member does on their own that is not related to a benchmark WOD
	//Score Type is assumed "Other"
	//RX, Score, 
	//Note is the description of the wod and whatever notes they want to enter
	public function save_member_custom_wod()
	{
		define('ID_VALUE',3);
		//$this->output->enable_profiler(TRUE);
		if (!$this->logged_in)
			redirect ('member/login');
                
                $error_message = '';
		$id_value = $this->uri->segment(ID_VALUE);//id_value
		$this->load->model('Wod_model');
		
		$wod_data = '';
		if ($id_value == '') //This will be a new wod to save
		{
			$wod_data['wod_date']		=	date('m/d/y');
			$wod_data['custom_title']	=	'';
			$wod_data['score']			=	'';
			$wod_data['note']			=	'';
			$wod_data['rx']				=	'';
		}
		else //Saving an existing member's wod
			$wod_data					=	$this->Wod_model->get_member_custom_wod($id_value);
				
		$this->form_validation->set_rules('score'		, 'Score'	,	'trim|required');
		$this->form_validation->set_rules('custom_title', 'WOD Name',	'trim');
		$this->form_validation->set_rules('note'		, 'Note'	,	'trim');
		
		if ($this->form_validation->run() == TRUE) 
		{
			//Determines insert or update in data layer
			if ($id_value	!=	'')
				$data['mw_id']			=	$id_value;
			
			$data['wod_date']		=	$this->make_us_date_mysql_friendly($this->input->post('wod_date'));
			$data['member_id']		=	$this->session->userdata('member_id');
			$data['custom_title']	=	$this->input->post('custom_title');
			$data['score']			=	$this->input->post('score');
			$data['note']			=	$this->input->post('note');
			
			$ret_val = $this->Wod_model->save_member_custom_wod($data);
			if ($ret_val['success']) 
			{
				$this->session->set_flashdata('good_message', 'Custom WOD saved.');
				redirect('welcome/index/TRUE');
			}
			else
				$error_message = $ret_val['message'];
		}

		$data['title']		=	'Custom WOD';
		$data['heading']	=	'Save Custom WOD';
		$data['view']		=	'mobile_member_custom_wod_save';
		$data['id_value']	=	$id_value;
		$data['doc_ready_call']	=	'save_member_custom_wod_doc_ready();';
		
		$data['error_message'] = (validation_errors()) ? validation_errors() : $error_message;
		
		$this->load->helper('form');
		$member_custom_wod_data					=	$this->_get_custom_wod_form_data($wod_data);
		
		$data = array_merge($data, $wod_data, $member_custom_wod_data);
				
		$this->load->vars($data);
		$this->load->view('mobile_master', $data);
	}
	public function delete_member_wod()
	{
		$delete_id		=	$this->uri->segment(3);
		
		$this->load->model('Wod_model');
		$ret_val = $this->Wod_model->delete_member_wod($delete_id);
		
		$this->session->set_flashdata('error_message', 'Record deleted');
		redirect('welcome/index/TRUE');
		
	}
	
	//Gets controls for both Box WODs and BenchMark WODs
	private function _get_wod_form_data($wod, $is_benchmark_wod	=	false)
	{
		$data	=	null;
                
		
		switch ($wod['score_type']) 
		{
			
			case 'T':
				$score			=	set_value('score', $wod['score']);
				$score_minutes	=	'';
				$score_seconds	=	'';
				$data['score']	=	$score;

				if ($score > 0)
				{
					$score_minutes	=	floor($score / 60);
					$score_seconds	=	$score - ($score_minutes * 60);
				}
				
				$data['score_minutes'] = array(
						'name'			=>	'score_minutes',
						'id'			=>	'_scoreMinutes',
						'type'			=>	'number',
						'autocomplete'	=>	'off',
						'value'			=>	$score_minutes,
						'data-inline'	=>	'true',
						);

				$data['score_seconds'] = array(
						'name'			=>	'score_seconds',
						'id'			=>	'_scoreSeconds',
						'type'			=>	'number',
						'autocomplete'	=>	'off',
						'value'			=>	$score_seconds,
						'data-inline'	=>	'true',
						);
						break;
			case 'I':
			case 'W':
			case 'O':
			default:
				$data['score'] = array(
										'name'			=>	'score',
										'id'			=>	'_score',
										'maxlength'		=>	'200',
										'autocomplete'	=>	'off',
										'value'			=>	set_value('score',$wod['score'])
									);
				break;
					
		}
		
		$data['note'] = array(
										'name'			=>	'note',
										'id'			=>	'_note',
										'value'			=>	set_value('note',$wod['note'])
									);
		if ($is_benchmark_wod)
		{
			$data['wod_date'] = array(
										'name'			=>	'wod_date',
										'id'			=>	'_wodDate',
										'autocomplete'	=>	'off',
										'value'			=>	set_value('wod_date',$wod['wod_date'])
									);
		}
		else
		{
			$box_class_time_options			=	$this->_get_box_class_time_lookup($wod['bw_id'],TRUE); //true is blank row
			$box_class_time_attrib	=	' id = "_boxClassTime" data-native-menu="true"';
			$data['box_class_time_dropdown'] =  form_dropdown('bct_id', $box_class_time_options, set_value('bct_id',$wod['bct_id']), $box_class_time_attrib);
		}

		$member_rating_options		=	array(
												'-1'	=>	'',
												'5'		=>	'5 - Awesome!!',
												'4'		=>	'4 - Fun',
												'3'		=>	'3 - Ok',
												'2'		=>	'2 - Meh',
												'1'		=>	'1 - No way',
												);
		
		$member_rating_attrib	=	'id = "_memberRating" data-native-menu="true"';

		$data['member_rating_dropdown'] =  form_dropdown('member_rating', $member_rating_options, set_value('member_rating',$wod['member_rating']), $member_rating_attrib);
               
                if (isset($wod['scale_id']) && $wod['scale_name'] === 'No Scale')
                {
                    //Do nothing.  No Scale.
                }
                else if (!isset($wod['scale_id']) || $wod['scale_id'] === '0' || $wod['scale_id'] === '' || $wod['scale_name'] === 'RX/Scaled')
                {
		$data['rx'] = array(
				'name'			=>	'rx',
				'id'			=>	'_rx',
				'data-mini'		=>	'true',
				'value'			=>	'1',
				'checked'		=> set_value('rx',$wod['rx'] === '1' ? 'checked' : ''),
			);
                }
                else
                {
                    $scale_option_wod_options			=	$this->_get_scale_options_lookup($wod['scale_id'],TRUE); //true is blank row
                    $scale_option_wod_attrib			=	'id = "_scaleOptions"';
                    $data['scale_option_wod_dropdown']         =	form_dropdown('so_id', $scale_option_wod_options, set_value('so_id',$wod['so_id']), $scale_option_wod_attrib);
                }

		$data['submit'] = array(
										'id'			=>	'_submit',
										'class'			=>	'ui-btn-hidden',
										'value'			=>	'Save',
										'aria-disabled'	=>	'false',
										'data-inline'	=>	'true',
										'data-theme'	=>	'b',
										'type'			=>	'Submit',
										);
		
		return $data;
	}
	
	private function _get_custom_wod_form_data($wod)
	{
		$data	=	null;
		
		
		$data['custom_title'] = array(
								'name'			=>	'custom_title',
								'id'			=>	'_customTitle',
								'maxlength'		=>	'100',
								'autocomplete'	=>	'off',
								'value'			=>	set_value('custom_title',$wod['custom_title'])
							);
                
		$data['score'] = array(
								'name'			=>	'score',
								'id'			=>	'_score',
								'maxlength'		=>	'200',
								'autocomplete'	=>	'off',
								'value'			=>	set_value('score',$wod['score'])
							);
		
		$data['note'] = array(
										'name'			=>	'note',
										'id'			=>	'_note',
										'autocomplete'	=>	'off',
										'value'			=>	set_value('note',$wod['note'])
									);
									
		$data['wod_date'] = array(
									'name'			=>	'wod_date',
									'id'			=>	'_wodDate',
									'autocomplete'	=>	'off',
									'value'			=>	set_value('wod_date',$wod['wod_date'])
								);
		
		$data['submit'] = array(
										'id'			=>	'_submit',
										'class'			=>	'ui-btn-hidden',
										'value'			=>	'Save',
										'aria-disabled'	=>	'false',
										'data-inline'	=>	'true',
										'data-theme'	=>	'b',
										'type'			=>	'Submit',
										);
		
		return $data;
	}
	
	//provide simple markdown to format the text
	private function _mark_down_text($text	=	'')
	{
		if ($text	===	'')
			return;
		$order   = array("\r\n", "\n", "\r");
		$text	=	str_replace($order, "<br />", $text);
		return $text;
	}
        
	private function _get_scale_options_lookup($scale_id = 0, $blank_row = false)
	{

		$this->load->model('Box_model');
		
		$wod_type_list_lookup = $this->Box_model->get_scale_option_list($scale_id);
		return $this->set_lookup($wod_type_list_lookup,'so_id','option',$blank_row ? BLANK_ROW : FALSE);
		
	}
	
	private function _get_box_class_time_lookup($bw_id = 0, $blank_row = FALSE)
	{

		$this->load->model('Box_model');
		
		$box_class_time_list_lookup = $this->Box_model->get_box_class_time_list($bw_id);
		return $this->set_lookup($box_class_time_list_lookup,'bct_id','class_time_description',$blank_row ? BLANK_ROW : FALSE);
		
	}
}

/* End of file wod.php */
/* Location: ./application/controllers/wod.php */