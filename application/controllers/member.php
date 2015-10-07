<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Member Class
 * 
 * @package wod-minder
 * @subpackage controller
 * @category member
 * @author Ray Nowell
 * 
 */
class Member extends MY_Controller {

	function __construct() 
	{
		parent::__construct();
		$this->load->library('form_validation');
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
        
		redirect ('member/update');
		
		return;
	}
	
	//NOTE:  I don't like this here but the way I coded selecting an event it has to go here for now
	public function save_event()
	{
		//$this->output->enable_profiler(TRUE);
		if (!$this->logged_in)
			redirect ('member/login');
		
		$event_id	=	$this->uri->segment(3);
		if ($event_id == '')
			redirect ('welcome/index/TRUE');
		
		$error_message = '';
		$this->load->model('Event_model');
		$this->load->model('Member_model');
		$event_id	=	$this->uri->segment(3);
		$event	=	null;
		$member_event = null;
		if ($event_id == '')
			redirect ('welcome/index/TRUE');
		
		$event			=	$this->Event_model->get_event($event_id);
		$member_id		=	$this->session->userdata('member_id');
		$member_event	=	$this->Event_model->get_member_event_info($event_id, $member_id);
		
		$event_wod_list	=	'';
		$event_wod_array	=	$this->Event_model->get_event_wod_for_member($event_id, $member_id);
		if (!$event_wod_array)
			$event_wod_list = 'No WODs are saved for the box you\'ve selected.';
		else
		{
			foreach($event_wod_array as $row) 
			{
				$wod_name	=	$row['simple_title'];
				$data_theme	=	'data-theme="'.($row['recorded_wod']	?	'e'	:	'c').'"';
				$event_wod_list	.=	'<li '.$data_theme.'><a data-ajax="false" href="'.base_url().'index.php/event/save_member_event_wod/'.$row['ew_id'].'">'.$wod_name.'</a></li>';
			}
		}
		
		$this->form_validation->set_rules('eso_id'				, 'Event Scale' , 'trim');
		if ($event['is_team_event'])
		{
			$this->form_validation->set_rules('teammates'			, 'Teammates' , 'trim');
			$this->form_validation->set_rules('team_name'			, 'Team Name' , 'trim');
		}
		$this->form_validation->set_rules('rank'					, 'Rank' , 'trim');
		$this->form_validation->set_rules('number_of_competitors'	, 'Number of Competitors' , 'trim|numeric');
		$this->form_validation->set_rules('note'					, 'Note' , 'trim');
		
		if ($this->form_validation->run() == TRUE) 
		{
			$data['event_id']			=	$event_id;
			$data['member_id']			=	$this->session->userdata('member_id');
			if ($event['is_team_event'])
			{
				$data['team_name']		=	$this->input->post('team_name');
				$data['teammates']		=	$this->input->post('teammates');
			}
			else
			{
				$data['team_name']		=	null;
				$data['teammates']		=	null;				
			}
			$data['eso_id']			=	$this->input->post('eso_id');
			if ($this->input->post('rank') !== '')
				$data['rank']			=	$this->input->post('rank');
			else
				$data['rank']			=	null;
			
			$data['note']			=	$this->input->post('note');
			
			if ($this->input->post('number_of_competitors') !== '')
				$data['number_of_competitors']			=	$this->input->post('number_of_competitors');
			else
				$data['number_of_competitors']			=	null;
			
			$ret_val = $this->Event_model->save_member_event_info($data);
			if ($ret_val['success']) 
			{
				$member_event['eso_id']	=	$data['eso_id'];
				$member_event['rank']	=	$data['rank'];
				$member_event['note']	=	$data['note'];
				$member_event['number_of_competitors']	=	$data['number_of_competitors'];
				$member_event['teammates']	=	$data['teammates'];
				$member_event['team_name']	=	$data['team_name'];
				
				$this->session->set_flashdata('good_message', 'Event Info Saved.');
			}
			else
				$error_message = $ret_val['message'];
			
			
			$data['good_message']	=	'Event Info Saved';

		}
	
		
		$event_scale_option_options		=	$this->_get_event_scale_options_lookup($event['es_id'],TRUE); //true is blank row
		
		$event_scale_option_attrib		=	'id = "_eventScaleOptions"';
		$data['event_scale_option_dropdown']  =	form_dropdown('eso_id', $event_scale_option_options, set_value('eso_id',$member_event['eso_id']), $event_scale_option_attrib);
		
		$data['rank'] = array(
								'name'			=>	'rank',
								'id'			=>	'_rank',
								'autocomplete'	=>	'off',
								'value'			=>	set_value('rank',$member_event['rank'])
							);
		if ($event['is_team_event'])
		{
			$data['teammates'] = array(
						'name'			=>	'teammates',
						'id'			=>	'_teammates',
						'autocomplete'	=>	'off',
						'value'			=>	set_value('teammates',$member_event['teammates'])
					);
			
			$data['team_name'] = array(
						'name'			=>	'team_name',
						'id'			=>	'_teamName',
						'autocomplete'	=>	'off',
						'value'			=>	set_value('team_name',$member_event['team_name'])
					);

		}
		
		$data['number_of_competitors'] = array(
								'name'			=>	'number_of_competitors',
								'id'			=>	'_numberOfCompetitors',
								'autocomplete'	=>	'off',
								'value'			=>	set_value('number_of_competitors',$member_event['number_of_competitors'])
							);

		$data['note'] = array(
								'name'			=>	'note',
								'id'			=>	'_note',
								'autocomplete'	=>	'off',
								'value'			=>	set_value('note',$member_event['note'])
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

		if (isset($data['good_message']) && strlen($data['good_message']) == 0)
			$data['good_message']			= $this->session->flashdata('good_message');
		
		$data['doc_ready_call']	=	'member_event_info_doc_ready();';
		$data['event_wod_list']	=	$event_wod_list;
		$data['title']			=	'Save member event';
		$data['heading']		=	'Save member event';
		$data['view']			=	'mobile_member_event_save';
		
		$data['error_message'] = $this->session->flashdata('error_message');

		$data = array_merge($data, $event);
		$this->load->vars($data);
		$this->load->view('mobile_master', $data);

	}
		
	public function save_event_wod()
	{
		if (!$this->logged_in)
			redirect ('member/login');
		
		if (!$this->is_admin)
			redirect ('welcome/index/TRUE');
		
		echo 'save member event wod';
	}
	
	public function email_paleo_data()
	{
		$this->load->model('Member_model');
		$max_array = $this->Member_model->get_email_paleo_data();
		
		$excel_file_name	=	$this->session->userdata('display_name').'_paleo_data_'.date('Y_m_d').'.xls';
		
		/** Error reporting */
		error_reporting(E_ALL);
		ini_set('display_errors', TRUE);
		ini_set('display_startup_errors', TRUE);
		date_default_timezone_set('America/Chicago');

		define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

		/** Include PHPExcel */
		require_once './phpExcel/Classes/PHPExcel.php';


		// Create new PHPExcel object
		//log_message('debug', date('H:i:s') , " Create new PHPExcel object" );
		$objPHPExcel = new PHPExcel();

		// Set document properties
		//log_message('debug', date('H:i:s') , " Set document properties" );
		$objPHPExcel->getProperties()->setCreator("WOD-Minder.com")
									->setLastModifiedBy("Wod-Minder")
									->setTitle("User WOD Data")
									->setSubject("User WOD Data")
									->setDescription("Contains all Paleo Meals for a user from wod-minder.com")
									->setKeywords("crossfit WOD")
									->setCategory("Crossfit");


		// Add headers
		//log_message('debug', date('H:i:s') , " Add some data");
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A1', 'Meal Date')
					->setCellValue('B1', 'Meal Time')
					->setCellValue('C1', 'Meal Type')
					->setCellValue('D1', 'Protein')
					->setCellValue('E1', 'Veggie/Fruit')
					->setCellValue('F1', 'Fat')
					->setCellValue('G1', 'Note');
		
		$rowCounter = 2;
		//Add Max Data
		foreach($max_array as $row) 
		{
			$objPHPExcel->getActiveSheet()->setCellValue('A' .	$rowCounter, $row['meal_date'])
											->setCellValue('B' . $rowCounter, $row['meal_time'])
											->setCellValue('C' . $rowCounter, $row['meal_type'])
											->setCellValue('D' . $rowCounter, $row['protein'])
											->setCellValue('E' . $rowCounter, $row['veggie_or_fruit'])
											->setCellValue('F' . $rowCounter, $row['fat'])
											->setCellValue('G' . $rowCounter, $row['note']);
			$rowCounter++;
		}
		
		// Rename worksheet
		//log_message('debug', date('H:i:s') , " Rename worksheet");
		$objPHPExcel->getActiveSheet()->setTitle('Paleo Data');


		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);

		// Save Excel5 file
		//log_message('debug', date('H:i:s') , " Write to Excel5 format");
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('./phpExcel/temp/'.$excel_file_name, __FILE__);
		//log_message('debug', date('H:i:s') , " File written");


		//log_message('debug', date('H:i:s') , " Peak memory usage: " , (memory_get_peak_usage(true) / 1024 / 1024) , " MB");

		// Echo done
		//log_message('debug', date('H:i:s') , " Done writing files");
		//log_message('debug', 'Files have been created in ' , getcwd());

		//File has been created now e-mail it:
		$this->load->helper('email'); //just used for validation
		$this->load->library('email');
		$this->load->model('Login_model');
		
		$user_email	=	$this->Login_model->get_user_email();

		$email_html_message			=	'<html><body>'.
										'Hi '.$this->session->userdata('display_name').'<br>'.
										'<p>
											Your Paleo data as of today is attached in an excel file.
										</p>'.
										'</body></html>';
										
		$config	=	$this->_get_email_config_settings();
		$this->email->initialize($config);
		
		$this->email->from('ray023@gmail.com', 'WOD-Minder Admin');
		$this->email->to($user_email);
		$this->email->subject('WOD-Minder Paleo Meal data as of '.date('F j, Y'));
		$this->email->message($email_html_message);
		$this->email->attach('./phpExcel/temp/'.$excel_file_name);
		$this->email->send();
		
		$this->session->set_flashdata('good_message', 'Paleo meals sent to '.$user_email.'.<br>If you don\'t receive  in a few minutes, check your spam folder.');
		unlink('./phpExcel/temp/'.$excel_file_name);
		redirect('welcome/index/TRUE');
	
	}
	public function email_wod_data()
	{
		
		//START AUDIT
		$this->load->model('Audit_model');
		$audit_data['controller']	=	'member_controller';
		$audit_data['ip_address']	=	$_SERVER['REMOTE_ADDR'];
		$audit_data['member_id']	=	$this->session->userdata('member_id');
		$audit_data['member_name']	=	$this->session->userdata('display_name');
		$audit_data['short_description']	=	'Email WOD Data';
		$this->Audit_model->save_audit_log($audit_data);
		//END AUDIT
			
		$this->load->model('Member_model');
		$max_array = $this->Member_model->get_email_wod_data();
		
		$excel_file_name	=	$this->session->userdata('display_name').'_wod_data_'.date('Y_m_d').'.xls';
		
		/** Error reporting */
		error_reporting(E_ALL);
		ini_set('display_errors', TRUE);
		ini_set('display_startup_errors', TRUE);
		date_default_timezone_set('America/Chicago');

		define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

		/** Include PHPExcel */
		require_once './phpExcel/Classes/PHPExcel.php';


		// Create new PHPExcel object
		//log_message('debug', date('H:i:s') , " Create new PHPExcel object" );
		$objPHPExcel = new PHPExcel();

		// Set document properties
		//log_message('debug', date('H:i:s') , " Set document properties" );
		$objPHPExcel->getProperties()->setCreator("WOD-Minder.com")
									->setLastModifiedBy("Wod-Minder")
									->setTitle("User WOD Data")
									->setSubject("User WOD Data")
									->setDescription("Contains all WODS for a user from wod-minder.com")
									->setKeywords("crossfit WOD")
									->setCategory("Crossfit");


		// Add headers
		//log_message('debug', date('H:i:s') , " Add some data");
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A1', 'Name')
					->setCellValue('B1', 'WOD Date')
					->setCellValue('C1', 'Score')
					->setCellValue('D1', 'RX')
					->setCellValue('E1', 'Rating')
					->setCellValue('F1', 'WOD Name')
					->setCellValue('G1', 'Score Type')
					->setCellValue('H1', 'Category')
					->setCellValue('I1', 'Benchmark WOD?')
					->setCellValue('J1', 'Box WOD?')
					->setCellValue('K1', 'Note');
		
		$rowCounter = 2;
		//Add Max Data
		foreach($max_array as $row) 
		{
			$objPHPExcel->getActiveSheet()->setCellValue('A' .	$rowCounter, $row['first_name'])
											->setCellValue('B' . $rowCounter, $row['wod_date'])
											->setCellValue('C' . $rowCounter, $row['score'])
											->setCellValue('D' . $rowCounter, $row['rx'])
											->setCellValue('E' . $rowCounter, $row['member_rating'])
											->setCellValue('F' . $rowCounter, $row['title'])
											->setCellValue('G' . $rowCounter, $row['score_type'])
											->setCellValue('H' . $rowCounter, $row['category'])
											->setCellValue('I' . $rowCounter, $row['benchmark_wod'])
											->setCellValue('J' . $rowCounter, $row['box_wod'])
											->setCellValue('K' . $rowCounter, $row['note']);
			$rowCounter++;
		}
		
		// Rename worksheet
		//log_message('debug', date('H:i:s') , " Rename worksheet");
		$objPHPExcel->getActiveSheet()->setTitle('WOD Data');


		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);

		// Save Excel5 file
		//log_message('debug', date('H:i:s') , " Write to Excel5 format");
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('./phpExcel/temp/'.$excel_file_name, __FILE__);
		//log_message('debug', date('H:i:s') , " File written");


		//log_message('debug', date('H:i:s') , " Peak memory usage: " , (memory_get_peak_usage(true) / 1024 / 1024) , " MB");

		// Echo done
		//log_message('debug', date('H:i:s') , " Done writing files");
		//log_message('debug', 'Files have been created in ' , getcwd());

		//File has been created now e-mail it:
		$this->load->helper('email'); //just used for validation
		$this->load->library('email');
		$this->load->model('Login_model');
		
		$user_email	=	$this->Login_model->get_user_email();

		$email_html_message			=	'<html><body>'.
										'Hi '.$this->session->userdata('display_name').'<br>'.
										'<p>
											Your WOD data as of today is attached in an excel file.
										</p>'.
										'</body></html>';
										
		$config	=	$this->_get_email_config_settings();
		$this->email->initialize($config);
		$this->email->from('ray023@gmail.com', 'WOD-Minder Admin');
		$this->email->to($user_email);
		$this->email->subject('WOD-Minder WOD data as of '.date('F j, Y'));
		$this->email->message($email_html_message);
		$this->email->attach('./phpExcel/temp/'.$excel_file_name);
		$this->email->send();
		
		$this->session->set_flashdata('good_message', 'User WOD Data sent to '.$user_email.'.<br>If you don\'t receive  in a few minutes, check your spam folder.');
		unlink('./phpExcel/temp/'.$excel_file_name);
		redirect('welcome/index/TRUE');
		
		
		
	}
	public function email_max_data()
	{
		$this->load->model('Member_model');
		$max_array = $this->Member_model->get_email_max_data();
		
		//START AUDIT
		$this->load->model('Audit_model');
		$audit_data['controller']	=	'member_controller';
		$audit_data['ip_address']	=	$_SERVER['REMOTE_ADDR'];
		$audit_data['member_id']	=	$this->session->userdata('member_id');
		$audit_data['member_name']	=	$this->session->userdata('display_name');
		$audit_data['short_description']	=	'Email Max Data';
		$this->Audit_model->save_audit_log($audit_data);
		//END AUDIT
		
		$excel_file_name	=	$this->session->userdata('display_name').'_max_data_'.date('Y_m_d').'.xls';
		
		/** Error reporting */
		error_reporting(E_ALL);
		ini_set('display_errors', TRUE);
		ini_set('display_startup_errors', TRUE);
		date_default_timezone_set('America/Chicago');

		define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

		/** Include PHPExcel */
		require_once './phpExcel/Classes/PHPExcel.php';


		// Create new PHPExcel object
		//log_message('debug', date('H:i:s') , " Create new PHPExcel object" );
		$objPHPExcel = new PHPExcel();

		// Set document properties
		//log_message('debug', date('H:i:s') , " Set document properties" );
		$objPHPExcel->getProperties()->setCreator("WOD-Minder.com")
									->setLastModifiedBy("Wod-Minder")
									->setTitle("User Max Data")
									->setSubject("User Max Data")
									->setDescription("Contains all max lifts for a user from wod-minder.com")
									->setKeywords("crossfit max")
									->setCategory("Crossfit");


		// Add headers
		//log_message('debug', date('H:i:s') , " Add some data");
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A1', 'Name')
					->setCellValue('B1', 'Exercise')
					->setCellValue('C1', 'Max Date')
					->setCellValue('D1', 'Max Value')
					->setCellValue('E1', 'Reps')
					->setCellValue('F1', 'Max Type');
		
		$rowCounter = 2;
		//Add Max Data
		foreach($max_array as $row) 
		{
			$objPHPExcel->getActiveSheet()->setCellValue('A' . $rowCounter, $row['first_name'])
											->setCellValue('B' . $rowCounter, $row['exercise'])
											->setCellValue('C' . $rowCounter, $row['max_date'])
											->setCellValue('D' . $rowCounter, $row['max_value'])
											->setCellValue('E' . $rowCounter, $row['max_rep'])
											->setCellValue('F' . $rowCounter, $row['max_type']);
			$rowCounter++;
		}
		
		// Rename worksheet
		//log_message('debug', date('H:i:s') , " Rename worksheet");
		$objPHPExcel->getActiveSheet()->setTitle('Max Data');


		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);

		// Save Excel5 file
		//log_message('debug', date('H:i:s') , " Write to Excel5 format");
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('./phpExcel/temp/'.$excel_file_name, __FILE__);
		//log_message('debug', date('H:i:s') , " File written");


		//log_message('debug', date('H:i:s') , " Peak memory usage: " , (memory_get_peak_usage(true) / 1024 / 1024) , " MB");

		// Echo done
		//log_message('debug', date('H:i:s') , " Done writing files");
		//log_message('debug', 'Files have been created in ' , getcwd());

		//File has been created now e-mail it:
		$this->load->helper('email'); //just used for validation
		$this->load->library('email');
		$this->load->model('Login_model');
		
		$user_email	=	$this->Login_model->get_user_email();

		$email_html_message			=	'<html><body>'.
										'Hi '.$this->session->userdata('display_name').'<br>'.
										'<p>
											Your max data as of today is attached in an excel file.
										</p>'.
										'</body></html>';
										
		$config	=	$this->_get_email_config_settings();
		$this->email->initialize($config);		
		$this->email->from('ray023@gmail.com', 'WOD-Minder Admin');
		$this->email->to($user_email);
		$this->email->subject('WOD-Minder Max data as of '.date('F j, Y'));
		$this->email->message($email_html_message);
		$this->email->attach('./phpExcel/temp/'.$excel_file_name);
		$this->email->send();
		
		$this->session->set_flashdata('good_message', 'User Max Data sent to '.$user_email.'.<br>If you don\'t receive  in a few minutes, check your spam folder.');
		unlink('./phpExcel/temp/'.$excel_file_name);
		redirect('welcome/index/TRUE');
		
	}
	public function delete_member_history()
	{
		$delete_id		=	$this->uri->segment(3);
		
		$this->load->model('Member_model');
		$ret_val = $this->Member_model->delete_member_history($delete_id);
			
		
		$this->session->set_flashdata('error_message', 'Record deleted');
		redirect('welcome/index/TRUE');		
	}
	
	/**
	 * Updates member information.
	 *
	 * @access public
	 */
	public function update()
	{		
		if (!$this->logged_in)
			redirect ('member/login');
		
		//START AUDIT
		$this->load->model('Audit_model');
		$audit_data['controller']	=	'member_controller';
		$audit_data['ip_address']	=	$_SERVER['REMOTE_ADDR'];
		$audit_data['member_id']	=	$this->session->userdata('member_id');
		$audit_data['member_name']	=	$this->session->userdata('display_name');
		$audit_data['short_description']	=	'Update Profile';
		$this->Audit_model->save_audit_log($audit_data);
		//END AUDIT
                
                $error_message = '';
		
		$this->form_validation->set_rules('last_name'		, 'Last Name'		, 'trim|max_length[100]');
		$this->form_validation->set_rules('first_name'		, 'First Name'		, 'trim|max_length[100]|required');		
		$this->form_validation->set_rules('gender'			, 'Gender'			, 'required');
		$this->form_validation->set_rules('birth_date'		, 'Birth Date'		, 'trim|required');
		$this->form_validation->set_rules('box_id'			, 'Box'				, 'required');
		$this->form_validation->set_rules('is_competitor'	, 'Is Competitor'	, 'trim');
		
		if ($this->form_validation->run() == true) 
		{
			$data['last_name']		=	$this->input->post('last_name');
			$data['first_name']		=	$this->input->post('first_name');
			$data['gender']			=	$this->input->post('gender');
			$data['box_id']			=	$this->input->post('box_id');
			$data['is_competitor']	=	$this->input->post('is_competitor');
			$data['birth_date']		=	$this->make_us_date_mysql_friendly($this->input->post('birth_date'));
			
			$data['modified_date']	=	date("Y-m-d H:i:s");
			$data['modified_by']	=	$this->session->userdata('display_name');
			
			$this->load->model('Member_model');
			$ret_val = $this->Member_model->update_member($data);
			if ($ret_val['success']) 
			{
				$this->_set_user_session_data($ret_val);
				$this->session->set_flashdata('good_message', 'User account updated.');
				redirect('welcome/index/TRUE');
			}
			else
				$error_message = $ret_val['message'];

		}
		
		$data['site_admin']		=	$this->is_admin;
        $data['display_name']	=	$this->display_name;

		$data['doc_ready_call']	=	'update_member_doc_ready();';
		$data['title']			=	'Member Update';
		$data['heading']		=	'Member Update';
		$data['view']			=	'mobile_member_update';
		
		$data['error_message'] = (validation_errors()) ? validation_errors() : $error_message;

		$this->load->helper('form');
		$member_profile_data	=	$this->_get_member_profile_form_data();
		$data = array_merge($data, $member_profile_data);
				
		$this->load->vars($data);
		$this->load->view('mobile_master', $data);

		return;
	}
	
	public function logout()
	{
		//START AUDIT
		$this->load->model('Audit_model');
		$audit_data['controller']	=	'member_controller';
		$audit_data['ip_address']	=	$_SERVER['REMOTE_ADDR'];
		$audit_data['member_id']	=	$this->session->userdata('member_id');
		$audit_data['member_name']	=	$this->session->userdata('display_name');
		$audit_data['short_description']	=	'Manual Logout';
		$this->Audit_model->save_audit_log($audit_data);
		//END AUDIT
		
		$this->session->sess_destroy();
		redirect('welcome'); 
    }

	public function benchmark_wod_history()
	{
		if (!$this->logged_in)
			redirect ('member/login');
		$dagger_html = '&#8224;';
		$wod_id		=	$this->uri->segment(3);
		
		$this->load->model('Wod_model');
		$benchmark_wod					=	$this->Wod_model->get_benchmark_wod($wod_id);
		$benchmark_wod_history_array	=	$this->Wod_model->get_member_benchmark_wod_history($wod_id);
		
		$benchmark_wod_history	=	'';
		$alt_row	=	0;
		foreach($benchmark_wod_history_array as $row) 
		{
			$is_odd	=	$alt_row%2==1;
			$alt_row_class	=	$is_odd	? 'alternate-row' : '';
		
			$delete_link		 =	'<a href="#" data-ajax="false" data-role="button" data-icon="delete" data-iconpos="notext">'.$row['mw_id'].'</a>';
			$benchmark_wod_history	.=	'<div class="ui-block-a '.$alt_row_class.'">'.$delete_link.'</div>';
			$edit_route			=	$row['wod_id'] == null ? 'wod/save_member_box_wod/'.$row['bw_id'] : 'wod/save_member_benchmark_wod/mw_id/'.$row['mw_id'] ;
			$edit_link			=	'<a href="'.base_url().'index.php/'.$edit_route.'" data-ajax="false">'.$this->mysql_to_human($row['wod_date']).'</a>';			
			$benchmark_wod_history	.=	'<div class="ui-block-b  date-block  grid-row-with-image '.$alt_row_class.'">'.$edit_link.'</div>';
			$score = $row['score'];
			if ($row['score_type']	===	'T')
			{
				$minutes	=	floor($score / 60);
				$seconds	=	$score - ($minutes * 60);
				$pad_length	=	2 - strlen($seconds);
				$seconds	=	strval(str_pad($seconds, 2 , '0', STR_PAD_LEFT));
				$score =	$minutes.':'.$seconds;
			}
			$score = $score.($row['rx']	== 1 ? '' : '*');
			$score = $score.($row['wod_at_box'] ? $dagger_html : '');
			$benchmark_wod_history	.=	'<div class="ui-block-c number-block grid-row-with-image '.$alt_row_class.'">'.$score.'</div>';
			
			$alt_row++;
		}
		
		$data['benchmark_wod_history']	=	$benchmark_wod_history;
		
		$data['doc_ready_call']	=	'mobile_benchmark_wod_history_doc_ready();';
		
		$data['site_admin']		=	$this->is_admin;
        $data['display_name']	=	$this->display_name;

		$data['title']		=	$benchmark_wod['wod_name'];
		$data['heading']	=	'benchmark_wod History';
		$data['view']		=	'mobile_member_benchmark_wod_history';
		
		$data['error_message'] = $this->session->flashdata('error_message');

		$this->load->vars($data);
		$this->load->view('mobile_master', $data);

		return;
	}
	
	public function exercise_history()
	{
		if (!$this->logged_in)
			redirect ('member/login');
		
		$exercise_id		=	$this->uri->segment(3);
		
		$this->load->model('Exercise_model');
		$exercise				=	$this->Exercise_model->get_exercise($exercise_id);
		$exercise_history_array	=	$this->Exercise_model->get_member_exercise_history($exercise_id);
		
		$exercise_history	=	'';
		$alt_row	=	0;
		foreach($exercise_history_array as $row) 
		{
			$is_odd	=	$alt_row%2==1;
			$alt_row_class	=	$is_odd	? 'alternate-row' : '';
		
			$delete_link		 =	'<a href="#" data-ajax="false" data-role="button" data-icon="delete" data-iconpos="notext">'.$row['mm_id'].'</a>';
			$exercise_history	.=	'<div class="ui-block-a '.$alt_row_class.'">'.$delete_link.'</div>';
			//Not ready to implement edit link yet
			$edit_link			 =	'<a href="'.base_url().'index.php/exercise/save_member_max/mm_id/'.$row['mm_id'].'" data-ajax="false">'.$this->mysql_to_human($row['max_date']).'</a>';
			$exercise_history	.=	'<div class="ui-block-b date-block  grid-row-with-image '.$alt_row_class.'">'.$edit_link.'</div>';
			$exercise_history	.=	'<div class="ui-block-c number-block grid-row-with-image '.$alt_row_class.'">'.$row['max_rep'].'</div>';
			$exercise_history	.=	'<div class="ui-block-d number-block grid-row-with-image '.$alt_row_class.'">'.$row['max_value'].'</div>';
			
			$alt_row++;
		}
		
		$data['exercise_history']	=	$exercise_history;
		
		$data['doc_ready_call']	=	'mobile_max_history_doc_ready();';
		
		$data['site_admin']		=	$this->is_admin;
        $data['display_name']	=	$this->display_name;

		$data['title']		=	$exercise->title.' History';
		$data['heading']	=	'Exercise History';
		$data['view']		=	'mobile_member_max_history';
		
		$data['error_message'] = $this->session->flashdata('error_message');

				
		$this->load->vars($data);
		$this->load->view('mobile_master', $data);

		return;
	}
	
	public function login() 
	{
		//$this->output->enable_profiler(TRUE);
		$error_message = '';
		$this->form_validation->set_rules('user_login'	, 'User Login'	, 'trim|min_length[3]|max_length[200]|required');
		$this->form_validation->set_rules('password'	, 'Password'	, 'trim|min_length[3]|max_length[500]|required');
		
		if ($this->form_validation->run() == true) 
		{
			$user_login	=	$this->input->post('user_login');
			$password	=	$this->input->post('password');
			
			$this->load->model('Login_model');
			$ret_val = $this->Login_model->login_user($user_login, $password);
			if ($ret_val['success']) 
			{

				$this->_set_user_session_data($ret_val);
				
				//START AUDIT
				$this->load->model('Audit_model');
				$audit_data['controller']	=	'member_controller';
				$audit_data['ip_address']	=	$_SERVER['REMOTE_ADDR'];
				$audit_data['member_id']	=	$this->session->userdata('member_id');
				$audit_data['member_name']	=	$this->session->userdata('display_name');
				$audit_data['short_description']	=	'Successful Login';
				$this->Audit_model->save_audit_log($audit_data);
				//END AUDIT
				
				redirect($this->config->item('base_url'));  //login is successful
			} 
			else
				$error_message = $ret_val['message'];
		}
		
		$data['good_message'] = $this->session->flashdata('good_message');
		$data['error_message'] = (validation_errors()) ? validation_errors() : $error_message;
		
		$data['login_input'] = array(
				'name'			=>	'user_login',
				'id'			=>	'_userLogin',
				'maxlength'		=>	'200',
				'placeholder'	=>	'User Login',
				'value'			=>	set_value('user_login'),
			);

		$data['password_input'] = array(
				'name'			=>	'password',
				'id'			=>	'_password',
				'maxlength'		=>	'500',
				'placeholder'	=>	'Password',
				'value'			=> '',
			);
		
		$data['submit'] = array(
				'name'			=>	'submit',
				'id'			=>	'_submit',
				'value'			=>	'Login',
			);


		$data['title']		=	'WOD Minder';
		$data['heading']	=	'WOD Minder Login';
		$data['view'] = 'mobile_member_login';
		$this->load->vars($data);
		$this->load->view('mobile_master', $data);
		
		return;
	}

	public function create_new() 
	{
		$error_message = '';
		//$this->form_validation->set_rules('user_login'		, 'User Login'		, 'trim|min_length[3]|max_length[20]|required');
		$this->form_validation->set_rules('last_name'		, 'Last Name'		, 'trim|max_length[100]');
		$this->form_validation->set_rules('first_name'		, 'First Name'		, 'trim|max_length[100]|required');
		$this->form_validation->set_rules('password'		, 'Password'		, 'trim|min_length[3]|max_length[500]|required');
		$this->form_validation->set_rules('gender'			, 'Gender'			, 'required');
		$this->form_validation->set_rules('box_id'			, 'Box'				, 'required');
		$this->form_validation->set_rules('other_box'		, 'Other Box'		, 'trim|max_length[100]');
		$this->form_validation->set_rules('email'			, 'E-mail'			, 'required|valid_email');
		$this->form_validation->set_rules('birth_date'		, 'Birth Date'		, 'trim|required');
		$this->form_validation->set_rules('is_competitor'	, 'Is Competitor'	, 'trim');
		
		if ($this->form_validation->run() == true) 
		{
			$data['user_login']		=	$this->input->post('email'); //backwards compatibility
			$data['password']		=	$this->input->post('password');
			$data['last_name']		=	$this->input->post('last_name');
			$data['first_name']		=	$this->input->post('first_name');
			$data['gender']			=	$this->input->post('gender');
			$data['email']			=	$this->input->post('email');
			$data['box_id']			=	$this->input->post('box_id');
			$data['is_competitor']	=	$this->input->post('is_competitor');
			$data['other_box']		=	$this->input->post('other_box');
			$data['birth_date']		=	$this->make_us_date_mysql_friendly($this->input->post('birth_date'));
			
			$data['created_date']	=	date('Y-m-d H:i:s');
			$data['created_by']		=	$this->session->userdata('display_name');
			$data['modified_date']	=	date("Y-m-d H:i:s");
			$data['modified_by']	=	$this->session->userdata('display_name');
			
			$this->load->model('Login_model');
			$ret_val = $this->Login_model->create_user($data);
			if ($ret_val['success']) 
			{
				//Log them in so they're not 
				$this->_set_user_session_data($ret_val);
				$this->_send_user_welcome_email($ret_val);
				$this->session->set_flashdata('good_message', 'User account created.  You are logged in.');
				$this->session->set_flashdata('show_add2home_popup', TRUE);
				redirect('welcome/index/TRUE');
			} 
			else
				$error_message = $ret_val['message'];
			
		}
		
		if (validation_errors())
		{
			//START AUDIT
			$creation_data_string = '';
			//$creation_data_string		.=	'login:  '.$this->input->post('user_login').'\r\n';
			$creation_data_string		.=	'pw:  '.$this->input->post('password').'\r\n';
			$creation_data_string		.=	'last name:  '.$this->input->post('last_name').'\r\n';
			$creation_data_string		.=	'first name:  '.$this->input->post('first_name').'\r\n';
			$creation_data_string		.=	'gender:  '.$this->input->post('gender').'\r\n';
			$creation_data_string		.=	'email:  '.$this->input->post('email').'\r\n';
			$creation_data_string		.=	'box id:  '.$this->input->post('box_id').'\r\n';
			$creation_data_string		.=	'competitor:  '.$this->input->post('is_competitor').'\r\n';
			$creation_data_string		.=	'other box:  '.$this->input->post('other_box').'\r\n';
			$creation_data_string		.=	'birth date:  '.$this->make_us_date_mysql_friendly($this->input->post('birth_date')).'\r\n';

			$this->load->model('Audit_model');
			$audit_data['controller']	=	'member_controller';
			$audit_data['ip_address']	=	$_SERVER['REMOTE_ADDR'];
			$audit_data['short_description']	=	'Bad Creation Attempt';
			$audit_data['full_info']	=	'Form Data:  '.$creation_data_string;
			$this->Audit_model->save_audit_log($audit_data);
			//END AUDIT
		}
		
		$data['error_message'] = (validation_errors()) ? validation_errors() : $error_message;
		
		 /*
		$data['login_input'] = array(
				'name'			=>	'user_login',
				'id'			=>	'_userLogin',
				'maxlength'		=>	'20',
				'placeholder'	=>	'User Login',
				'autocomplete'	=>	'off',
				'value'			=> set_value('user_login'),
			);
		*/

		$data['password_input'] = array(
				'name'			=>	'password',
				'id'			=>	'_password',
				'maxlength'		=>	'20',
				'placeholder'	=>	'Password',
				'autocomplete'	=>	'off',
				'value'			=> set_value('password'),
			);
		
		$data['last_name'] = array(
										'name'			=>	'last_name',
										'id'			=>	'_lastName',
										'maxlength'		=>	'100',
										'placeholder'	=>	'Last Name',
										'autocomplete'	=>	'off',
										'value'			=>	set_value('last_name')
									);
		
		$data['first_name'] = array(
										'name'			=>	'first_name',
										'id'			=>	'_firstName',
										'maxlength'		=>	'100',
										'placeholder'	=>	'First Name',
										'autocomplete'	=>	'off',
										'value'			=>	set_value('first_name')
									);
		
		$data['birthdate_input'] = array(
				'name'			=>	'birth_date',
				'id'			=>	'_birthDate',
				'maxlength'		=>	'20',
				'placeholder'	=>	'Birthday',
				'autocomplete'	=>	'off',
				'value'			=> set_value('birth_date'),
			);
		
		$data['email'] = array(
				'name'			=>	'email',
				'id'			=>	'_email',
				'maxlength'		=>	'200',
				'placeholder'	=>	'Email',
				'autocomplete'	=>	'off',
				'value'			=> set_value('email'),
			);
		
		$gender_options = array(
								''	=>	'',
								'm'	=>	'Male',
								'f'	=>	'Female',
								);
		$gender_attrib	=	'id = "_gender" data-native-menu="true"';
		$data['gender_dropdown'] =  form_dropdown('gender', $gender_options, set_value('gender'), $gender_attrib);
		
		$data['is_competitor'] = array(
									'name'			=>	'is_competitor',
									'id'			=>	'_isCompetitor',
									'data-mini'		=>	'true',
									'value'			=>	'1',
									'checked'		=> 'checked',
								);

		
		$box_options	=	$this->_get_box_lookup(TRUE); //true is blank row
		$box_attrib	=	'id = "_box" data-native-menu="true"';
		$data['box_dropdown'] =  form_dropdown('box_id', $box_options, set_value('box_id'), $box_attrib);
		
		$data['other_box'] = array(
										'name'			=>	'other_box',
										'id'			=>	'_otherBox',
										'maxlength'		=>	'100',
										'placeholder'	=>	'Other Box',
										'autocomplete'	=>	'off',
										'value'			=>	set_value('other_box')
									);

		
		
		$data['submit'] = array(
				'name'			=>	'submit',
				'id'			=>	'_submit',
				'value'			=>	'Create',
			);

		$data['doc_ready_call']	=	'create_member_doc_ready();';
		$data['title']		=	'Create Account';
		$data['heading']	=	'Create Account';
		$data['view'] = 'mobile_member_create';
		$this->load->vars($data);
		$this->load->view('mobile_master', $data);

		return;
	}
	
	public function forgot_password() 
	{
		$this->form_validation->set_rules('user_name_or_email', 'Login'	, 'trim|min_length[3]|required');
		
		$data['good_message'] = $this->session->flashdata('good_message');
		$data['error_message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error_message');
		
		$data['user_name_or_email'] = array(
				'name'			=>	'user_name_or_email',
				'id'			=>	'_userNameOrEmail',
				'maxlength'		=>	'200',
				'placeholder'	=>	'User Login or E-mail',
				'autocomplete'	=>	'off',
				'value'			=>	set_value('user_login'),
			);
		
		$data['submit'] = array(
				'name'			=>	'submit',
				'id'			=>	'_submit',
				'value'			=>	'Retrieve Password',
			);
		
		//This is for when user completely forgets their login
		$data['user_identifier'] = array(
				'name'			=>	'user_identifier',
				'id'			=>	'_userIdentifier',
				'maxlength'		=>	'200',
				'placeholder'	=>	'User Identifier',
				'autocomplete'	=>	'off',
			);
		$data['submit_identifier'] = array(
				'name'			=>	'submit_identifier',
				'id'			=>	'_submitIdentifier',
				'value'			=>	'Recover Login',
			);

		$data['title']		=	'Retrieve Password';
		$data['heading']	=	'Retrieve Password';
		$data['view'] = 'mobile_member_retrieve_password';
		$this->load->vars($data);
		$this->load->view('mobile_master', $data);

		return;
	}
	
	public function completely_forgotten_login()
	{
		
		$this->load->library('email');
		$form_value	=	$this->input->post('user_identifier');
		
		$email_html_message			=	'<html><body>'.
										'Person who forgot their login:  '.$form_value.'<br>'.
										'</body></html>';
		
		
		//START AUDIT
		$this->load->model('Audit_model');
		$audit_data['controller']	=	'member_controller';
		$audit_data['ip_address']	=	$_SERVER['REMOTE_ADDR'];
		$audit_data['short_description']	=	'Completely Forgot Login';
		$audit_data['full_info']	=	'Form Value:  '.$form_value;
		$this->Audit_model->save_audit_log($audit_data);
		//END AUDIT
		

		$config	=	$this->_get_email_config_settings();
		$this->email->initialize($config);
		$this->email->from('ray023@gmail.com', 'WOD-Minder Admin');
		$this->email->to('ray023@gmail.com');
		$this->email->subject('User completely forgot their WOD-Minder login');
		$this->email->message($email_html_message);
		$this->email->send();
		
		$this->session->set_flashdata('good_message', 'Administrator notified.  You will receive your login info shortly.');
		redirect('member/login');
		
		return;
		
	}
	public function retrieve_password() 
	{
		//$this->output->enable_profiler(TRUE);
		
		$this->load->helper('email'); //just used for validation
		$this->load->library('email');
		$this->load->model('Login_model');
		$user_email	=	'';
		
		$form_value	=	$this->input->post('user_name_or_email');
		
		if (valid_email($form_value))
			$user_email = $form_value;
		else
		{	
			$user_email	=	$this->Login_model->get_user_login_email($form_value);
			
			if (!$user_email)
			{
				$this->session->set_flashdata('error_message', 'User Login Invalid');
				redirect('member/forgot_password');
			}
		}
		
		$login_data	=	$this->Login_model->get_user_login_data_by_email($user_email);

		if (!$login_data)
		{
			$this->session->set_flashdata('error_message', 'User E-mail Invalid');
			redirect('member/forgot_password');
		}			
		
		$email_html_message			=	'<html><body>'.
										'Your user login is '.$login_data['user_login'].'<br>'.
										'Your password is '.$login_data['password'].'<br>'.
										'<a href="'.  base_url().'">Login to WOD-Minder</a>'.
										'</body></html>';
		

		$config	=	$this->_get_email_config_settings();
		$this->email->initialize($config);
		$this->email->from('ray023@gmail.com', 'WOD-Minder Admin');
		$this->email->to($user_email);
		$this->email->subject('Your WOD-Minder Login');
		$this->email->message($email_html_message);
		$this->email->send();
		
		$this->session->set_flashdata('good_message', 'User password sent.');
		redirect('member/login');
		
		return;
	}
	
	//Set session data for user; auto-log them in after they create account
	//or when they log in successfully.
	private function _set_user_session_data($user_data)
	{
		$session_data = array(
								'site_admin'	=>	$user_data['site_admin'],
								'member_id'		=>	$user_data['member_id'],
								'display_name'	=>	$user_data['display_name'],
								'member_box_id'	=>	$user_data['member_box_id'],
								'logged_in'		=>	true
							);

		$this->session->set_userdata($session_data);
		
		return;
	}
	
	
	private function _get_event_scale_options_lookup($scale_id = 0, $blank_row = false)
	{

		$this->load->model('Event_model');
		
		$event_scale_option_list_lookup = $this->Event_model->get_event_scale_option_list($scale_id);
		return $this->set_lookup($event_scale_option_list_lookup,'eso_id','scale_option',$blank_row ? BLANK_ROW : false);
		
	}

	
	private function _get_member_profile_form_data() 
	{
		$data	=	null;

		$this->load->model('Member_model');
		$member_info = $this->Member_model->get_member();
		
		$data['user_login'] = array(
										'name'			=>	'user_login',
										'id'			=>	'_userLogin',
										'maxlength'		=>	'20',
										'placeholder'	=>	'User Login',
										'autocomplete'	=>	'off',
										'value'			=>	set_value('user_login',$member_info->user_login),
										'disabled'		=>	'disabled'
									);
		
		$data['last_name'] = array(
										'name'			=>	'last_name',
										'id'			=>	'_lastName',
										'maxlength'		=>	'100',
										'placeholder'	=>	'Last Name',
										'autocomplete'	=>	'off',
										'value'			=>	set_value('last_name',$member_info->last_name)
									);
		
		$data['first_name'] = array(
										'name'			=>	'first_name',
										'id'			=>	'_firstName',
										'maxlength'		=>	'100',
										'placeholder'	=>	'First Name',
										'autocomplete'	=>	'off',
										'value'			=>	set_value('first_name',$member_info->first_name)
									);
		
		$gender_options = array(
								'm'	=>	'Male',
								'f'	=>	'Female',
								);
		
		$gender_attrib	=	'id = "_gender" data-native-menu="false"';

		$data['gender_dropdown'] =  form_dropdown('gender', $gender_options, $member_info->gender, $gender_attrib);

				$data['is_competitor'] = array(
									'name'			=>	'is_competitor',
									'id'			=>	'_isCompetitor',
									'data-mini'		=>	'true',
									'value'			=>	'1',
									'checked'		=> set_value('is_competitor',$member_info->is_competitor === '1' ? 'checked' : ''),
								);
		
		$box_options	=	$this->_get_box_lookup();
		$box_attrib	=	'id = "_box" data-native-menu="true"';
		$data['box_dropdown'] =  form_dropdown('box_id', $box_options, $member_info->box_id, $box_attrib);
		
		$birth_date = $this->mysql_to_human($member_info->birth_date);
		$data['birth_date'] = array(
										'name'			=>	'birth_date',
										'id'			=>	'_birthDate',
										'maxlength'		=>	'100',
										'placeholder'	=>	'Birth Date',
										'autocomplete'	=>	'off',
										'value'			=>	set_value('birth_date', $birth_date)
									);		

		$data['password_input'] = array(
				'name'			=>	'password',
				'id'			=>	'_password',
				'maxlength'		=>	'20',
				'placeholder'	=>	'Password',
				'autocomplete'	=>	'off',
				'value'			=> set_value('birth_date',$member_info->birth_date),
			);
		
		$data['email'] = array(
				'name'			=>	'email',
				'id'			=>	'_email',
				'maxlength'		=>	'20',
				'placeholder'	=>	'Email',
				'autocomplete'	=>	'off',
				'value'			=> set_value('email',$member_info->email),
				'disabled'		=>	'disabled',
			);

		
		$data['submit_profile'] = array(
										'class'			=>	'ui-btn-hidden',
										'value'			=>	'Update',
										'aria-disabled'	=>	'false',
										'data-inline'	=>	'true',
										'data-theme'	=>	'b',
										'type'			=>	'Submit',
										'id'			=>	'_submit',
										);

		
		return $data;
		
		}

	private function _get_box_lookup($blank_row = false)
	{

		$this->load->model('Box_model');
		
		$box_list_lookup = $this->Box_model->get_box_list();
		return $this->set_lookup($box_list_lookup,'box_id','box_name',$blank_row ? BLANK_ROW : false);
		
	}
	
	private function _send_user_welcome_email($user_data)
	{
		$this->load->library('email');
		$email_html_message			=	'<html><body>'.
										'Hi '.$user_data['display_name'].'<br>'.
										'<p>
											Thank-you for signing up to WOD-Minder.
										</p>'.
										'<p>
											Your login is <i>'.$user_data['user_login'].'</i>.
										</p>
										<p>
											Consider adding WOD-Minder to your smart-phone\'s home screen for quick access:
											<ul>
												<li><a href="http://www.youtube.com/watch?v=kcXhkbJ4GWs">Click here for iPhone demonstration</a>.</li>
												<li><a href="http://www.youtube.com/watch?v=U9kGAAJ1FUA">Click here for Android demonstration</a>.</li>
											</ul>
										</p>
										<p>
											For latest news you can follow WOD-Minder on Twitter <a href="http://twitter.com/wod_minder">@wod_minder</a> and <a href="https://www.facebook.com/WodMinder">Facebook</a>.
										</p>
										--Ray
										'.
										'</body></html>';
										
		$config	=	$this->_get_email_config_settings();
		$this->email->initialize($config);
		$this->email->from('ray023@gmail.com', 'WOD-Minder Admin');
		$this->email->to($user_data['email']);
		$this->email->subject('Welcome to WOD-Minder');
		$this->email->message($email_html_message);
		$this->email->send();
	}
	
	private function _get_email_config_settings()
	{
		$this->load->library('encrypt');
		
		$config['protocol']		=	'mail';
		$config['charset']		=	'iso-8859-1';
		$config['mailtype']		=	'html';
		
		return $config;
	}
}

/* End of file member.php */
/* Location: ./application/controllers/member.php */