<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Event Class
 * 
 * The purpose of this controller is to provide an interface to manage events
 * 
 * @package wod-minder
 * @subpackage controller
 * @author Ray Nowell
 * 
 */
class Event extends MY_Controller {
	
	function __construct() 
	{
		parent::__construct();
		$this->load->library('form_validation');
	}
	
	/**
	 * Index Page for this controller.
	 *
	 * 
	 * 
	 * @access public
	 */
	public function index()
	{
		redirect ('welcome/index/TRUE');
	}
	
	public function save_member_event_wod()
	{
		//$this->output->enable_profiler(TRUE);
		if (!$this->logged_in)
			redirect ('member/login');

		$ew_id			=	$this->uri->segment(3);
		if ($ew_id === '')
			redirect ('welcome/index/TRUE');

		$error_message = '';
		$this->load->model('Event_model');

		$member_id			=	$this->session->userdata('member_id');
		$event_wod			=	$this->Event_model->get_event_wod($ew_id);

		$event_wod_note		= $event_wod['note'];
		unset($event_wod['note']); //so we don't mix with mew note
		$member_event_wod	=	$this->Event_model->get_member_event_wod($ew_id, $member_id);
		
		if(!$member_event_wod)
		{
			$member_event_wod['score']			=	'';
			$member_event_wod['remainder']		=	'';
			$member_event_wod['rank']			=	'';
			$member_event_wod['member_rating']	=	'';
			$member_event_wod['note']			=	'';
		}
		
		

		if ($event_wod['score_type']	===	'T')
		{
			$this->form_validation->set_rules('score_minutes'	, 'Minutes'	,	'trim');
			$this->form_validation->set_rules('score_seconds'	, 'Seconds'	,	'trim');
			$this->form_validation->set_rules('score'			, 'Time'	,	'trim');
		}
		else
			$this->form_validation->set_rules('score'	, 'Score'	,	'trim');


		$this->form_validation->set_rules('remainder'		, 'Remainder'		,	'trim'); 
		$this->form_validation->set_rules('rank'			, 'Rank'			,	'trim'); 
		$this->form_validation->set_rules('member_rating'	, 'Member Rating'	,	'trim'); 
		$this->form_validation->set_rules('note'			, 'Note'			,	'trim');


		if ($this->form_validation->run() == TRUE) 
		{
			$data['ew_id']			=	$event_wod['ew_id'];
			$data['member_id']		=	$this->session->userdata('member_id');
			$data['remainder']		=	$this->input->post('remainder');
			if ($this->input->post('rank') !== '')
				$data['rank']			=	$this->input->post('rank');
			$data['score']			=	$this->input->post('score');
			$data['note']			=	$this->input->post('note');
			$data['member_rating']	=	$this->input->post('member_rating');	

			$ret_val = $this->Event_model->save_member_event_wod($data);
			if ($ret_val['success']) 
			{
				$this->session->set_flashdata('good_message', 'Event Wod saved.');
				redirect('member/save_event/'.$event_wod['event_id']);
			}
			else
				$error_message = $ret_val['message'];
		}

		$data['title']		=	$event_wod['simple_title'];
		$data['heading']	=	'Save Event WOD';
		$data['view']		=	'mobile_member_event_wod_save';

		$data['error_message'] = (validation_errors()) ? validation_errors() : $error_message;
		$data['doc_ready_call']	=	'save_member_event_wod_doc_ready();';

		$this->load->helper('form');
		$event_wod_data							=	$this->_get_member_event_wod_form_data($event_wod, $member_event_wod);
		$event_wod_data['ew_id']				=	$event_wod['ew_id'];
		$event_wod_data['wod_date']				=	$event_wod['wod_date'];
		$event_wod_data['simple_description']	=	$event_wod['simple_description'];
		$event_wod_data['score_type']			=	$event_wod['score_type'];
		
		$event_wod_data['ew_id']			=	$ew_id;

		$data['event_wod_note']		=	$event_wod_note;
		$data['event_id']			=	$event_wod['event_id'];
		$data['result_hyperlink']	=	$event_wod['result_hyperlink'];
		$data = array_merge($data, $event_wod_data);

		$this->load->vars($data);
		$this->load->view('mobile_master', $data);

		
	}
	
	public function save_event()
	{
				
		if (!$this->logged_in)
			redirect ('member/login');
		
		if (!$this->is_admin)
			redirect ('welcome/index/TRUE');
		
        $error_message = '';
		$this->load->model('Event_model');
		$event_id			=	$this->uri->segment(3);
		$event = null;
		if ($event_id != '')
			$event		=	$this->Event_model->get_event($event_id);
		else
		{
			$event['event_name'] = '';
			$event['is_team_event'] = FALSE;
			$event['start_date'] = '';
			$event['duration'] = 1;
			$event['es_id'] = '';
			$event['event_main_hyperlink'] = '';
			$event['result_hyperlink'] = '';
			$event['facebook_page'] = '';
			$event['twitter_account'] = '';
			$event['hosting_box_id'] = '';
			$event['event_note'] = '';
		}
		
		$event_note = $event['event_note'];

		$this->form_validation->set_rules('event_name'				, 'Event Name'	,	'trim|required');
		$this->form_validation->set_rules('start_date'				, 'Start Date'	,	'trim|required');
		$this->form_validation->set_rules('duration'				, 'Duration'	,	'trim|required');
		$this->form_validation->set_rules('hosting_box_id'			, 'Hosting Box' , 'trim');
		$this->form_validation->set_rules('host_name'				, 'Host Name' , 'trim');
		$this->form_validation->set_rules('is_team_event'			, 'Team Event' , 'trim');
		$this->form_validation->set_rules('event_name'				, 'Event Name' , 'trim');
		$this->form_validation->set_rules('result_hyperlink'		, 'Result Hyperink' , 'trim');
		$this->form_validation->set_rules('event_main_hyperlink'	, 'Event Main Hyperlink' , 'trim');
		$this->form_validation->set_rules('facebook_page'			, 'Facebook Page' , 'trim');
		$this->form_validation->set_rules('twitter_account'			, 'Twitter Account' , 'trim');
		$this->form_validation->set_rules('es_id'					, 'Event Scale'		,	'trim|required');

		        
		
		if ($this->form_validation->run() == TRUE) 
		{
			if (!!$event_id)
				$data['event_id']	=	$event_id;

			if ($this->input->post('entity-source') === 'box')
			{
				$data['hosting_box_id']			= $this->input->post('hosting_box_id');
				$data['host_name']				= null;				
			}
			else
			{
				$data['hosting_box_id']			= null;
				$data['host_name']				= $this->input->post('host_name');				
			}
			$data['event_name']				= $this->input->post('event_name');
			$data['es_id']					= $this->input->post('es_id');
			$data['is_team_event']			= $this->input->post('is_team_event');
			$data['start_date']				= $this->make_us_date_mysql_friendly($this->input->post('start_date'));
			$data['duration']				= $this->input->post('duration');
			$data['result_hyperlink']		= $this->input->post('result_hyperlink');
			$data['event_main_hyperlink']	= $this->input->post('event_main_hyperlink');
			$data['facebook_page']			= $this->input->post('facebook_page');
			$data['twitter_account']		= $this->input->post('twitter_account');
			$data['note']					= $this->input->post('note');

			$ret_val = $this->Event_model->save_event($data);
			if ($ret_val['success']) 
			{
				$this->session->set_flashdata('good_message', 'Event saved.');
				redirect('welcome/index/TRUE');
			}
			else
				$error_message = $ret_val['message'];
		}

		$data['title']		=	'Save Event';
		$data['heading']	=	'Save Event';
		$data['view']		=	'mobile_admin_event_save';
		$data['other_function_call']	=	'admin_event_save_page_init();';
		
		
		$data['error_message'] = (validation_errors()) ? validation_errors() : $error_message;
		
		$this->load->helper('form');
		$event['note']	=	$event_note;
		$event_data							=	$this->_get_event_form_data($event);
		$event_data['event_id']				=	$event_id;
		
		$data = array_merge($data, $event_data);
				
		$this->load->vars($data);
		$this->load->view('mobile_master', $data);


	}

	public function save_event_wod()
	{
	
		if (!$this->logged_in)
			redirect ('member/login');
		
		if (!$this->is_admin)
			redirect ('welcome/index/TRUE');
		
        $error_message = '';
		$this->load->model('Event_model');
		$ew_id			=	$this->uri->segment(3);
		$event_wod = null;
		if ($ew_id != '')
			$event_wod		=	$this->Event_model->get_event_wod($ew_id);
		

		$this->form_validation->set_rules('event_id'				, 'Event Name'		,	'trim|required');
		$this->form_validation->set_rules('simple_title'			, 'WOD Name'		,	'trim|required');
		$this->form_validation->set_rules('wod_date'				, 'WOD Date'		,	'trim|required');
		$this->form_validation->set_rules('score_type'				, 'Score Type'		,	'trim|required');
		$this->form_validation->set_rules('wod_id'					, 'Benchmark WOD' 	, 'trim');
		$this->form_validation->set_rules('remainder'				, 'Remainder Name' 	, 'trim');
		$this->form_validation->set_rules('simple_description'		, 'Description' 	, 'trim');
		$this->form_validation->set_rules('note'					, 'Note' 			, 'trim');
		$this->form_validation->set_rules('team_wod'				, 'Team WOD' 		, 'trim');
		$this->form_validation->set_rules('result_hyperlink'		, 'Result Hyperink' , 'trim');		        
		
		if ($this->form_validation->run() == TRUE) 
		{
			if (!!$ew_id)
				$data['ew_id']	=	$ew_id;

			$data['event_id']				= $this->input->post('event_id');
			$data['wod_id']					= $this->input->post('wod_id');
			$data['score_type']				= $this->input->post('score_type');
			$data['wod_date']				= $this->make_us_date_mysql_friendly($this->input->post('wod_date'));
			$data['simple_title']			= $this->input->post('simple_title');
			$data['simple_description']		= $this->input->post('simple_description');
			$data['note']					= $this->input->post('note');
			$data['team_wod']				= $this->input->post('team_wod');
			$data['result_hyperlink']		= $this->input->post('result_hyperlink');
			$data['remainder_name']			= $this->input->post('remainder_name');

			$ret_val = $this->Event_model->save_event_wod($data);
			if ($ret_val['success']) 
			{
				$this->session->set_flashdata('good_message', 'Event WOD saved.');
				redirect('welcome/index/TRUE');
			}
			else
				$error_message = $ret_val['message'];
		}

		$data['title']		=	'Save Event WOD';
		$data['heading']	=	'Save Event WOD';
		$data['view']		=	'mobile_admin_event_wod_save';
		
		
		$data['error_message'] = (validation_errors()) ? validation_errors() : $error_message;
		
		$this->load->helper('form');
		$event_wod_data							=	$this->_get_event_wod_form_data($event_wod);
		$event_wod_data['ew_id']				=	$ew_id;
		
		$data = array_merge($data, $event_wod_data);
				
		$this->load->vars($data);
		$this->load->view('mobile_master', $data);


	}
	
	
	public function select_event()
	{
		//$this->output->enable_profiler(TRUE);
		
		if (!$this->logged_in)
			redirect ('member/login');
			
		$direction_uri			=	$this->uri->segment(3); //where to go
		$member_id = 0;
		
		if (!$direction_uri)
			$direction_uri = 'some_uri_i_dont_know_yet'; //where user will go to save an event
		else
		{
			if ($direction_uri === 'member')
				$member_id = $this->session->userdata('member_id');
		}

		$event_list	=	'';
		$this->load->model('Event_model');

		$event_array	=	$this->Event_model->get_event('',$member_id);
		foreach($event_array as $row) 
		{
			if ($row['duration'] == 1)
				$event_date_range = $row['start_date'];
		    else
			{
				$Date1 = $row['start_date_mysql_format'];
				$date = new DateTime($Date1);
				$date->add(new DateInterval('P'.$row['duration'].'D')); // P1D means a period of 1 day
				$event_date_range = $row['start_date'].' - '.$date->format('m/d/Y');
			}
			
			$data_theme		 =	'data-theme="'.($row['recorded_event']	?	'e'	:	'c').'"';
			$event_name = $row['box_abbreviation'] === null ? $row['event_name'] : $row['box_abbreviation'].'\'s '.$row['event_name'];
			$event_list	.=	'<li '.$data_theme.'><a data-ajax="false" href="'.base_url().'index.php/'.$direction_uri.'/save_event/'.$row['event_id'].'">'.$event_name.'</a><span class="ui-li-count">'.$event_date_range.'</span></li>';
		}
		
		$data['event_list']	=	$event_list;
		$data['title']				=	'Event';
		$data['heading']			=	'Edit Event';
		$data['view']				=	'mobile_event_select';
		$this->load->vars($data);
		$this->load->view('mobile_master', $data);
	}
	
	public function publish()
	{
		if (!$this->logged_in)
			redirect ('member/login');
		
		if (!$this->is_admin)
			redirect ('welcome/index/TRUE');
		
		echo 'publish event';
	}
	
	public function select_event_wod()
	{
		if (!$this->logged_in)
			redirect ('member/login');
		
		if (!$this->is_admin)
			redirect ('welcome/index/TRUE');
		
		$this->load->model('Event_model');
		$event_list_array		= $this->Event_model->get_events_with_wods();
		
		$event_list = '';
		foreach($event_list_array as $row) 
		{
			$full_event_display = $row['hosting_entity'].' - '.$row['event_name'];
			if ($row['wod_count'] == 1)
				$event_list .= '<li><a href="'.base_url().'index.php/event/save_event_wod/'.$row['ew_id_single'].'" data-ajax="false">' .$full_event_display. '</a><span class="ui-li-count">'.$row['wod_count'].'</span></li>';
			else
				$event_list	.= '<li><a href="#PAGE_ID_'.$row['event_id'].'">'.$full_event_display.'</a><span class="ui-li-count">'.$row['wod_count'].'</span></li>';;
		}
		
		$event_wod_list_array	= $this->Event_model->get_event_wods_for_events_with_multiple_wods();
		$current_event		=	0;
		$page_opener		=	'<div data-role="content" id="PAGE_ID_"><div data-role="header"><a href="#Main" data-icon="back" data-iconpos="notext" data-direction="reverse">Back</a><h1>Pick Event WOD</h1></div><div data-role="content"><div class="content-primary"><ul data-role="listview" data-filter="true" data-filter-placeholder="Search boxes..." data-filter-theme="d" data-theme="d" data-divider-theme="d">';
		$page_closer		=	'</ul></div></div></div><!--Page Closer-->';
		$running_content	=	'';
		$first_run		= TRUE;

		foreach($event_wod_list_array as $row) 
		{
			if ($current_event != $row['event_id'])
			{
				if ($first_run)
				{
					$first_run	=	FALSE;	
					$running_content	.=	str_replace('PAGE_ID_','PAGE_ID_'.$row['event_id'],$page_opener);
				}
				else
					$running_content	.=	$page_closer.str_replace('PAGE_ID_','PAGE_ID_'.$row['event_id'],$page_opener);
				
				$current_event = $row['event_id'];
			}
			$running_content  .= '<li><a data-ajax="false" href="'.base_url().'index.php/event/save_event_wod/'.$row['ew_id'].'">'.$row['simple_title'].'</a></li>';
		}
		
		$running_content	.=	$page_closer;
		
		$data['event_list']			=	$event_list;
		$data['event_wod_pages']	=	$running_content;
		
		$data['title']		=	'Event WOD';
		$data['heading']	=	'Event WOD';
		$data['view']		=	'mobile_event_wod_select';


		$this->load->vars($data);
		$this->load->view('mobile_master', $data);

	}
	
	private function _get_event_form_data($event)
	{
		$data	=	null;
		
		$data['event_name'] = array(
									'name'			=>	'event_name',
									'id'			=>	'_eventName',
									'autocomplete'	=>	'off',
									'value'			=>	set_value('event_name',$event['event_name'])
								);
		$data['is_team_event'] = array(
									'name'			=>	'is_team_event',
									'id'			=>	'_isTeamEvent',
									'data-mini'		=>	'true',
									'value'			=>	'1',
									'checked'		=> set_value('is_team_event',$event['is_team_event'] === '1' ? 'checked' : ''),
								);
		                
		$data['start_date'] = array(
									'name'			=>	'start_date',
									'id'			=>	'_startDate',
									'autocomplete'	=>	'off',
									'value'			=>	set_value('start_date',$event['start_date'])
								);
		
		$data['duration'] = array(
									'name'			=>	'duration',
									'id'			=>	'_duration',
									'autocomplete'	=>	'off',
									'value'			=>	set_value('duration',$event['duration'])
								);
		
		$event_scale_options			=	$this->_get_event_scale_lookup(TRUE); //true is blank row
		$event_scale_attrib				=	'id = "_eventScale"';
		$data['event_scale_dropdown']	=	form_dropdown('es_id', $event_scale_options, set_value('es_id',$event['es_id']), $event_scale_attrib);
		
		$data['event_main_hyperlink'] = array(
							'name'			=>	'event_main_hyperlink',
							'id'			=>	'_eventMainHyperlink',
							'autocomplete'	=>	'off',
							'value'			=>	set_value('event_main_hyperlink',$event['event_main_hyperlink'])
						);
		
		$data['result_hyperlink'] = array(
							'name'			=>	'result_hyperlink',
							'id'			=>	'_resultHyperlink',
							'autocomplete'	=>	'off',
							'value'			=>	set_value('result_hyperlink',$event['result_hyperlink'])
						);
		
		$data['facebook_page'] = array(
							'name'			=>	'facebook_page',
							'id'			=>	'_facebookPage',
							'autocomplete'	=>	'off',
							'value'			=>	set_value('facebook_page',$event['facebook_page'])
						);
		
		$data['twitter_account'] = array(
					'name'			=>	'twitter_account',
					'id'			=>	'_twitterAccount',
					'autocomplete'	=>	'off',
					'value'			=>	set_value('twitter_account',$event['twitter_account'])
				);
				
		$hosting_box_options	=	$this->_get_box_lookup(TRUE); //true is blank row
		$hosting_box_attrib		=	'id = "_hostBox"';
		$data['hosting_box_dropdown']	=	form_dropdown('hosting_box_id', $hosting_box_options, set_value('hosting_box_id',$event['hosting_box_id']), $hosting_box_attrib);
		
		
		$data['note'] = array(
										'name'			=>	'note',
										'id'			=>	'_note',
										'value'			=>	set_value('note',$event['note'])
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
	
	private function _get_box_lookup($blank_row = false)
	{
		
		$this->load->model('Box_model');
		$box_list_lookup = $this->Box_model->get_box_list(TRUE, FALSE);
		return $this->set_lookup($box_list_lookup,'box_id','box_name',$blank_row);
		
	}
	
	private function _get_event_scale_lookup($blank_row = false)
	{

		$this->load->model('Event_model');
		
		$event_scale_list_lookup = $this->Event_model->get_event_scale_list();
		return $this->set_lookup($event_scale_list_lookup,'es_id','scale_name',$blank_row ? BLANK_ROW : false);
		
	}
		
	private function _get_member_event_wod_form_data($event_wod, $member_event_wod)
	{
		$data	=	null;
		
		switch ($event_wod['score_type']) 
		{
			case 'T':
				$score			=	set_value('score', $member_event_wod['score']);
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
										'value'			=>	set_value('score',$member_event_wod['score'])
									);
				break;
					
		}
		
		if ($event_wod['remainder_name'] !== '')
		{
			$data['remainder_name'] = $event_wod['remainder_name'];
			$data['remainder'] = array(
											'name'			=>	'remainder',
											'id'			=>	'_remainder',
											'value'			=>	set_value('remainder',$member_event_wod['remainder'])
										);
		}
		
		$data['rank'] = array(
										'name'			=>	'rank',
										'id'			=>	'_rank',
										'value'			=>	set_value('rank',$member_event_wod['rank'])
									);
		
		$data['note'] = array(
										'name'			=>	'note',
										'id'			=>	'_note',
										'value'			=>	set_value('note',$member_event_wod['note'])
									);
		
		$member_rating_options		=	array(
												'-1'	=>	'',
												'5'		=>	'5 - Awesome!!',
												'4'		=>	'4 - Fun',
												'3'		=>	'3 - Ok',
												'2'		=>	'2 - Meh',
												'1'		=>	'1 - No way',
												);
		
		$member_rating_attrib	=	'id = "_memberRating" data-native-menu="true"';

		$data['member_rating_dropdown'] =  form_dropdown('member_rating', $member_rating_options, set_value('member_rating',$member_event_wod['member_rating']), $member_rating_attrib);
               
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

	private function _get_event_wod_form_data($event)
	{
		$data	=	null;
		
		$event_name_options				=	$this->_get_event_lookup(TRUE); //true is blank row
		$event_name_attrib				=	'id = "_eventName"';
		$data['event_name_dropdown']	=	form_dropdown('event_id', $event_name_options, set_value('event_id',$event['event_id']), $event_name_attrib);
		
		$data['remainder_name'] = array(
									'name'			=>	'remainder_name',
									'id'			=>	'_remainderName',
									'autocomplete'	=>	'off',
									'value'			=>	set_value('remainder_name',$event['remainder_name'])
								);
		
		$data['wod_name'] = array(
									'name'			=>	'simple_title',
									'id'			=>	'_wodName',
									'autocomplete'	=>	'off',
									'value'			=>	set_value('simple_title',$event['simple_title'])
								);
                
		$data['wod_date'] = array(
									'name'			=>	'wod_date',
									'id'			=>	'_wodDate',
									'autocomplete'	=>	'off',
									'value'			=>	set_value('wod_date',$event['wod_date'])
								);
		
		$score_type_options = array(
								''	=>	'',
								'T'	=>	'For Time',			//Integer, stored in seconds, T tells UI to display minutes second
								'I'	=>	'Reps/Round Count', //Integer values stored
								'W'	=>	'Maximum Weight', //Decimal value stored (e.g. 105.5 lbs)
								'O'	=>	'Other', //Unknown way to score...becomes free text field
								);
		$score_type_attrib	=	'id = "_scoreType" ';
		$data['score_type_dropdown'] =  form_dropdown('score_type', $score_type_options, set_value('score_type',$event['score_type']), $score_type_attrib);
		
		$benchmark_wod_options			=	$this->_get_benchmark_wod_lookup(TRUE); //true is blank row
		$benchmark_wod_attrib			=	'id = "_benchmarkWod"';
		$data['benchmark_wod_dropdown']	=	form_dropdown('wod_id', $benchmark_wod_options, set_value('wod_id',$event['wod_id']), $benchmark_wod_attrib);
		
		$data['simple_description'] = array(
										'name'			=>	'simple_description',
										'id'			=>	'_simpleDescription',
										'autocomplete'	=>	'off',
										'value'			=>	set_value('simple_description',$event['simple_description'])
									);
		
		$data['team_wod'] = array(
				'name'			=>	'team_wod',
				'id'			=>	'_teamWod',
				'data-mini'		=>	'true',
				'value'			=>	'1',
				'checked'		=> set_value('team_wod',$event['team_wod'] === '1' ? 'checked' : ''),
				);
		
		$data['result_hyperlink'] = array(
							'name'			=>	'result_hyperlink',
							'id'			=>	'_resultHyperlink',
							'autocomplete'	=>	'off',
							'value'			=>	set_value('result_hyperlink',$event['result_hyperlink'])
						);
						
		$data['note'] = array(
										'name'			=>	'note',
										'id'			=>	'_note',
										'value'			=>	set_value('note',$event['note'])
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
	
	private function _get_event_lookup($blank_row = false)
	{

		$this->load->model('Event_model');
		
		$wod_type_list_lookup = $this->Event_model->get_event();
		return $this->set_lookup($wod_type_list_lookup,'event_id','event_name',$blank_row ? BLANK_ROW : false);

	}

	private function _get_benchmark_wod_lookup($blank_row = false)
	{

		$this->load->model('Wod_model');
		
		$wod_type_list_lookup = $this->Wod_model->get_benchmark_wod_list();
		return $this->set_lookup($wod_type_list_lookup,'wod_id','title',$blank_row ? BLANK_ROW : false);

	}


}

/* End of file event.php */
/* Location: ./application/controllers/event.php */