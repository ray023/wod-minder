<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Administration Functions Class
 * 
 * @package wod-minder
 * @subpackage controller
 * @category Administration
 * @author Ray Nowell
 * 
 * 
 * Currently have no intention of giving this functionality to end users
 * 
 * Right now, it contains only the ability of setting/changing the box 
 * in which the member is an owner or part of the staff
 */
class Administration_functions extends MY_Controller {

	function __construct() 
	{
		parent::__construct();
		$this->load->library('form_validation');
	}
	
	public function delete_log_file()
	{
		if (!$this->logged_in)
			redirect ('member/login');
			
		if (!$this->is_admin)
			redirect ('welcome/index/TRUE');
		
		$directory = $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'application'.DIRECTORY_SEPARATOR.'logs'.DIRECTORY_SEPARATOR;
		
		$log_file_name	=	$this->uri->segment(3);
		
		$result = unlink($directory.$log_file_name);
		
		if ($result)
		{
			$this->session->set_flashdata('error_message', 'Log File ('.$log_file_name.') Deleted');
			redirect ('welcome/index/TRUE');
		}
		
		echo 'Could not delete file:'.$directory.$log_file_name;
		
	}
	public function view_audit_trail()
	{
		if (!$this->logged_in)
			redirect ('member/login');
			
		if (!$this->is_admin)
			redirect ('welcome/index/TRUE');
		
		$audit_log_list	=	'';
		$this->load->model('Audit_model');
		$audit_log_array	=	$this->Audit_model->get_audit_log();
		
		$alt_row = 1;
		$audit_log_list = '';
		foreach($audit_log_array as $row) 
		{
			$is_odd         =	$alt_row%2==1;
			$alt_row_class	=	$is_odd	? 'alternate-row' : '';
			$audit_log_list	.=	'<div class="ui-block-a '.$alt_row_class.'">'.$row['log_date'].'</div>';

			if ($row['member_id'] == null)
				$audit_log_list	.=	'<div class="ui-block-b '.$alt_row_class.'">&nbsp;</div>';
			else
				$audit_log_list	.=	'<div class="ui-block-b '.$alt_row_class.'">'.$row['member_name'].' ('.$row['box_abbreviation'].')'.'</div>';
			
			if ($row['full_info'] == null)
				$audit_log_list	.=	'<div class="ui-block-c '.$alt_row_class.'">'.$row['short_description'].'</div>';
			else
				$audit_log_list	.=	'<div class="ui-block-b '.$alt_row_class.'"><a href="#" onClick="alert(\''.strip_tags($row['full_info']).' \');">'.$row['short_description'].'</a></div>';
			
			$alt_row++;
		}
		
		$data['audit_log_list']	=	$audit_log_list;
		$data['title']				=	'Audit Trail';
		$data['heading']			=	'Audit Trail';
		$data['view']				=	'mobile_admin_get_audit_trail';
		$this->load->vars($data);
		$this->load->view('mobile_master', $data);

		
	}
			   
	
	public function select_benchmark_wod()
	{
		if (!$this->logged_in)
			redirect ('member/login');
			
		if (!$this->is_admin)
			redirect ('welcome/index/TRUE');

		$benchmark_wod_list	=	'';
		$this->load->model('Wod_model');
		$benchmark_wod_array	=	$this->Wod_model->get_benchmark_wod();
		foreach($benchmark_wod_array as $row) 
			$benchmark_wod_list	.=	'<li><a data-ajax="false" href="'.base_url().'index.php/administration_functions/save_benchmark_wod/'.$row['wod_id'].'">'.$row['wod_name'].'</a></li>';
		
		$data['benchmark_wod_list']	=	$benchmark_wod_list;
		$data['title']				=	'Benchmark WOD';
		$data['heading']			=	'Save Benchmark WOD';
		$data['view']				=	'mobile_admin_benchmark_wod_select';
		$this->load->vars($data);
		$this->load->view('mobile_master', $data);
	}
	/*
	 * Saves/Edits a hero, girl or other benchmarkwod
	 */
	public function save_benchmark_wod()
	{
		define('ID_VALUE',3);
		//$this->output->enable_profiler(TRUE);
		if (!$this->logged_in)
			redirect ('member/login');
			
		if (!$this->is_admin)
			redirect ('welcome/index/TRUE');
	
		$id_value = $this->uri->segment(ID_VALUE);//id_value
		$this->load->model('Wod_model');
		
		$wod_data = '';
		if ($id_value == '') //This will be a new wod to save
		{
			$wod_data['wod_category_id']	=	'';
			$wod_data['wod_name']				=	'';
			$wod_data['description']		=	'';
			$wod_data['note']				=	'';
			$wod_data['score_type']			=	'';
                        $wod_data['image_name']			=	'';
		}
		else //Saving an existing benchmark wod
			$wod_data					=	$this->Wod_model->get_benchmark_wod($id_value);

		$this->form_validation->set_rules('wod_category_id'	, 'Category'	,	'trim|required');
		$this->form_validation->set_rules('wod_name'			, 'WOD Name'	,	'trim|required');
		$this->form_validation->set_rules('description'		, 'WOD Name'	,	'trim|required');
		$this->form_validation->set_rules('note'			, 'Note'		,	'trim');
		$this->form_validation->set_rules('score_type'		, 'Score Type'	,	'trim|required');
                $this->form_validation->set_rules('image_name'			, 'Image Name'	,	'trim');
		
		
		if ($this->form_validation->run() == TRUE) 
		{
			//Determines insert or update in data layer
			if ($id_value	!=	'')
				$data['wod_id']			=	$id_value;
			
			
			$data['wod_category_id']	=	$this->input->post('wod_category_id');
			$data['title']				=	$this->input->post('wod_name');
			$data['description']		=	$this->input->post('description');
			$data['note']				=	$this->input->post('note');
			$data['score_type']			=	$this->input->post('score_type');
                        $data['image_name']			=	$this->input->post('image_name');
			
			$ret_val = $this->Wod_model->save_benchmark_wod($data);
			if ($ret_val['success']) 
			{
				$this->session->set_flashdata('good_message', 'Benchmark WOD saved.');
				redirect('welcome/index/TRUE');
			}
			else
			{
				$this->session->set_flashdata('error_message', $ret_val['message']);
				redirect('wod/'.$id_value);
			}
		}

		$data['title']		=	'Benchmark WOD';
		$data['heading']	=	'Save Benchmark WOD';
		$data['view']		=	'mobile_admin_benchmark_wod_save';
		$data['id_value']	=	$id_value;
		
		$data['error_message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error_message');
		
		$this->load->helper('form');
		$benchmark_wod_data					=	$this->_get_benchmark_wod_form_data($wod_data);
		
		$data = array_merge($data, $wod_data, $benchmark_wod_data);
				
		$this->load->vars($data);
		$this->load->view('mobile_master', $data);
	}

	/**
	 *Backs up the entire WOD-Minder database and prompts user for download 
	 */
	public function database_backup()
	{
		if (!$this->logged_in)
			redirect ('member/login');
		
		if (!$this->is_admin)
			redirect ('welcome/index/TRUE');
		
		$user_name		=	$this->db->username;
		$password		=	$this->db->password;
		$database_name	=	$this->db->database;
		$host			=	$this->db->hostname;
		$date_suffix	=	date("Y-m-d");
		$file_name		=	$database_name.'_'.$date_suffix;
		
		exec('mysqldump --user='.$user_name.' --password='.$password.' --host='.$host.' '.$database_name.' > '.$file_name.'.sql');

		// Checking files are selected
		$zip = new ZipArchive(); // Load zip library
		$zip_name = $file_name.'.zip'; // Zip name

		if($zip->open($zip_name, ZIPARCHIVE::CREATE)!==TRUE)
		{
			echo "* Sorry ZIP creation failed at this time";
			return;
		}

		$zip->addFile($file_name.'.sql'); // Adding files into zip	
		$zip->close();
		if(file_exists($zip_name))
		{
			// push to download the zip
			header('Content-type: application/zip');
			header('Content-Disposition: attachment; filename="'.$zip_name.'"');
			readfile($zip_name);
			// remove zip file is exists in temp path
			unlink($zip_name);
			unlink($file_name.'.sql');
		}
		
		return;
	}
	public function destroy_user()
	{
		if (!$this->logged_in)
			redirect ('member/login');
		
		if (!$this->is_admin)
			redirect ('welcome/index/TRUE');

		$member_id			=	$this->uri->segment(3);
		$this->load->model('Member_model');
		$this->Member_model->erase_members_existance($member_id);
		
		$this->session->set_flashdata('error_message', 'Member completely removed from WOD Minder');
		redirect ('welcome/index/TRUE');
		
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
		
		if (!$this->is_admin)
			redirect ('welcome/index/TRUE');
		        
		$this->load->model('Box_model');
		$box_list_lookup	=	$this->Box_model->get_box_list(TRUE);
		$box_options	=	$this->set_lookup($box_list_lookup,'box_id','box_name',BLANK_ROW);
		$box_attrib	=	'id = "_box" data-native-menu="false"';
		$data['box_dropdown'] =  form_dropdown('box_id', $box_options, '', $box_attrib);
				
		$this->load->model('Member_model');
		$member_list		=	'';
		$member_list_array	=	$this->Member_model->get_all_members();
		foreach ($member_list_array as $row)
			$member_list .= '<li><a class="member-link" href="#">' .trim($row['first_name'].' '.$row['last_name']) . '</a><input type="hidden" value="'.$row['member_id'].'" /></li>';
		
		$data['member_list']	=	$member_list;
		
		$data['member_name_input'] = array(
				'id'			=>	'_memberName',
				'disabled'		=>	'disabled',
			);

		
		$data['submit'] = array(
				'name'			=>	'submit',
				'id'			=>	'_submit',
				'data-inline'	=>	'true',
				'value'			=>	'Set Member as Staff',
			);

		$data['other_function_call']	=	'admin_page_init();';
		
		$data['site_admin']		=	$this->is_admin;
        $data['display_name']	=	$this->display_name;

		$data['title']		=	'Set Member Staff';
		$data['heading']	=	'Set Member Staff';
		$data['view']		=	'mobile_admin_set_member_staff';
		
		$this->load->vars($data);
		$this->load->view('mobile_master', $data);

	}
	public function save_box_wod()
	{
		//$this->output->enable_profiler(TRUE);
		if (!$this->logged_in)
			redirect ('member/login');
		
		if (!$this->is_admin)
			redirect ('welcome/index/TRUE');
		
		$bw_id			=	$this->uri->segment(3);
		$this->load->model('Box_model');
		
		$this->form_validation->set_rules('wod_date'			, 'Date'			,	'trim|required');
		$this->form_validation->set_rules('box_id'				, 'Box'				,	'trim|required');
		$this->form_validation->set_rules('simple_title'		, 'WOD Name'		,	'trim');
		$this->form_validation->set_rules('simple_description'	, 'WOD Description'	,	'trim');
		$this->form_validation->set_rules('buy_in'				, 'Buy-In'			,	'trim');
		$this->form_validation->set_rules('cash_out'			, 'Cash Out'		,	'trim');
		$this->form_validation->set_rules('score_type'			, 'Score Type'		,	'trim|required');
		
		if ($this->form_validation->run() == TRUE) 
		{
			if ($bw_id != '')
				$data['bw_id']				=	$bw_id;
			
			$data['wod_date']			=	$this->make_us_date_mysql_friendly($this->input->post('wod_date'));
			$data['box_id']				=	$this->input->post('box_id');
			$data['simple_title']		=	$this->input->post('simple_title');
			$data['simple_description']	=	$this->input->post('simple_description');
			$data['buy_in']				=	$this->input->post('buy_in');
			$data['cash_out']			=	$this->input->post('cash_out');
			$data['score_type']			=	$this->input->post('score_type');
			
			$wod_type_id				=	$this->input->post('wod_type_id');
			if ($wod_type_id > 0)
				$data['wod_type_id']	=	$wod_type_id;
                        
			$scale_id				=	$this->input->post('scale_id');
			$data['wod_id']				=	$this->input->post('wod_id');
			if ($scale_id > 0)
				$data['scale_id']		=	$scale_id;
                        else
                            	$data['scale_id']		=	'';


			//The '$data['box_id']' below looks a little strange.  It's needed for security when saving as staff
			$ret_val = $this->Box_model->save_box_wod($data, $data['box_id']);
			if ($ret_val['success']) 
			{
				$this->session->set_flashdata('good_message', 'Box WOD saved.');
				redirect('welcome/index/TRUE');
			}
			else
			{
				$this->session->set_flashdata('error_message', $ret_val['message']);
				redirect('administration_functions/save_box_wod/'.$bw_id);
			}
		}
		
		if ($bw_id != '')
		{
			$bw			=	$this->Box_model->get_box_wod($bw_id);
			$box_wod['bw_id']				=	$bw->bw_id;
			$box_wod['wod_date']			=	$this->mysql_to_human($bw->wod_date);
			$box_wod['buy_in']				=	$bw->buy_in;
			$box_wod['cash_out']			=	$bw->cash_out;
			$box_wod['simple_title']		=	$bw->simple_title;
			$box_wod['simple_description']	=	$bw->simple_description;
			$box_wod['wod_id']				=	$bw->wod_id;
			$box_wod['score_type']			=	$bw->score_type;
			$box_wod['box_id']				=	$bw->box_id;
                        $box_wod['scale_id']				=	$bw->scale_id;
			$box_wod['wod_type_id']			=	$bw->wod_type_id;
			
			$data['use_wizard']	=	FALSE; //Don't use wizard when user is editing an existing box wod
		}
		else
		{
			$box_wod['bw_id']				=	'';
			$box_wod['wod_date']			=	date('m/d/y');
			$box_wod['buy_in']				=	'';
			$box_wod['cash_out']				=	'';
			$box_wod['simple_title']		=	'';
			$box_wod['simple_description']	=	'';
			$box_wod['wod_id']				=	'';
			$box_wod['score_type']			=	'';
			$box_wod['box_id']				=	'';
                        $box_wod['scale_id']				=	'';
			$box_wod['wod_type_id']			=	'';
			
			$data['use_wizard']	=	TRUE; //Creating new user; use wizard
		}

		$data['title']		=	'Save WOD';
		$data['heading']	=	'Save WOD';
		$data['view']		=	'mobile_admin_box_wod_save';
		
		$data['error_message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error_message');
		
		if ($data['use_wizard'] && strlen($data['error_message'])	>	0)
			$data['use_wizard']	=	FALSE;
		else //var use_wizard has passed all checks, load all lists required to show the wizard
			$wizard_data	=	$this->_get_box_wod_wizard_data();
		
		$this->load->helper('form');
		$box_wod_data						=	$this->_get_box_wod_form_data($box_wod);
		$box_wod_data['bw_id']				=	$box_wod['bw_id'];
		
		if ($data['use_wizard'])
			$data = array_merge($data, $box_wod_data, $wizard_data);
		else
			$data = array_merge($data, $box_wod_data);
				
		$this->load->vars($data);
		$this->load->view('mobile_master', $data);

	}
	public function edit_box_wod()
	{	
		if (!$this->logged_in || !$this->is_admin)
			redirect ('member/login');

		$data['title']		=	'Admin Box WOD';
		$data['heading']	=	'Admin Box WOD';
		$data['view']		=	'mobile_admin_edit_box_wod';

		$box_wod_list	=	'';
		$this->load->model('Box_model');
		$box_wod_list_for_admin_array = $this->Box_model->get_box_wods_for_admin($this->session->userdata('member_id'));
		
		$current_box		=	0;
		$box_list			=	'';
		$page_opener		=	'<div data-role="content" id="PAGE_ID_"><div data-role="header"><a href="#Main" data-icon="back" data-iconpos="notext" data-direction="reverse">Back</a><h1>Pick Box WOD</h1></div><div data-role="content"><div class="content-primary"><ul data-role="listview" data-filter="true" data-filter-placeholder="Search boxes..." data-filter-theme="d" data-theme="d" data-divider-theme="d">';
		$page_closer		=	'</ul></div></div></div><!--Page Closer-->';
		$running_content	=	'';
		$first_run		= TRUE;

		foreach($box_wod_list_for_admin_array as $row) 
		{
			if ($current_box != $row['box_id'])
			{
				if ($first_run)
				{
					$first_run	=	FALSE;	
					$running_content	.=	str_replace('PAGE_ID_','PAGE_ID_'.$row['box_id'],$page_opener);
				}
				else
					$running_content	.=	$page_closer.str_replace('PAGE_ID_','PAGE_ID_'.$row['box_id'],$page_opener);
				
				$box_list	.= '<li><a href="#PAGE_ID_'.$row['box_id'].'">'.$row['box'].'</a></li>';;
				$current_box = $row['box_id'];
			}
			$running_content  .= '<li><a data-ajax="false" href="'.base_url().'index.php/administration_functions/save_box_wod/'.$row['bw_id'].'">'.$row['simple_title'].'</a><span class="ui-li-count">'.$this->mysql_to_human($row['wod_date']).'</span></li>';
		}
		
		$running_content	.=	$page_closer;
		$data['box_list']		=	$box_list;
		$data['box_wod_pages']	=	$running_content;

		$this->load->vars($data);
		$this->load->view('mobile_master', $data);

		return;
	}
        //Currently have no intention of giving this functionality to end users
	public function set_member_staff()
	{
		
		//$this->output->enable_profiler(TRUE);
		if (!$this->logged_in)
			redirect ('member/login');
		
		if (!$this->is_admin)
			redirect ('welcome/index/TRUE');
		
		$member_id	=	$this->input->post('member_id');
		$box_id		=	 $this->input->post('box_id');
		
		$this->load->model('Box_model');
		
		$ret_val	=	$this->Box_model->set_box_staff($member_id, $box_id);
		
		if ($ret_val)
		{
			$good_message = $box_id	=== '-1' ? 'Member removed from staff' : 'Member saved as staff';
			$this->session->set_flashdata('good_message', $good_message);
		}
		else
			$this->session->set_flashdata('bad_message', 'Problem setting member as staff');
		
		
		redirect('welcome/index/TRUE');
		
		
	}
	
	public function member_summary()
	{
				//$this->output->enable_profiler(TRUE);
		if (!$this->logged_in)
			redirect ('member/login');
		
		if (!$this->is_admin)
			redirect ('welcome/index/TRUE');
		
		$member_id			=	$this->uri->segment(3);
		$this->load->model('Member_model');
					
		if ($member_id	==	'')
		{
			$member_list		=	'';
			$member_list_array	=	$this->Member_model->get_all_members();
			foreach ($member_list_array as $row)
				$member_list .= '<li><a data-ajax="false" class="member-link" href="'.base_url().'index.php/administration_functions/member_summary/'.$row['member_id'].'">' .trim($row['first_name'].' '.$row['last_name']) . '</a><input type="hidden" value="'.$row['member_id'].'" /></li>';

			$data['member_list']	=	$member_list;
		}
		else
		{
			$this->load->library('encrypt');
			//$member_id
			$data['member_list']	=	false;
			$member_info_array		=	$this->Member_model->get_member($member_id);
			$member_info			=	'<table>';
			foreach ($member_info_array as $key => $value)
				if ($key	!=	'password')
					$member_info	.=	'<tr><td>'.$key.'</td><td>'.$value.'</td></tr>';
				else
				{
					$value	= $this->encrypt->decode($value);
					$member_info	.=	'<tr><td>'.$key.'</td><td>'.$value.'</td></tr>';
				}
			
			
			$member_summary_array	=	$this->Member_model->get_member_summary_info($member_id);
			$member_summary_count	=	'';
			foreach ($member_summary_array as $row)
				$member_summary_count	.=	'<tr><td>'.$row['MyCategory'].'</td><td>'.$row['TheCount'].'</td></tr>';
			
			$member_summary_count	.=	'</table>';
			$data['member_id']		=	$member_id;
			$data['member_html']	=	$member_info.$member_summary_count;
		}
			
		$data['title']		=	'Member Summary';
		$data['heading']	=	'Member Summary';
		$data['view']		=	'mobile_admin_member_summary';
				
		$this->load->vars($data);
		$this->load->view('mobile_master', $data);
		
		return true;
	}
	
	private function _get_benchmark_wod_form_data($benchmark_wod)
	{
		$data	=	null;

		$data['wod_name'] = array(
										'name'			=>	'wod_name',
										'id'			=>	'_wodName',
										'autocomplete'	=>	'off',
										'value'			=>	set_value('wod_name',$benchmark_wod['wod_name'])
									);
                
		$data['image_name'] = array(
										'name'			=>	'image_name',
										'id'			=>	'_imageName',
										'autocomplete'	=>	'off',
										'value'			=>	set_value('image_name',$benchmark_wod['image_name'])
									);
		
		$wod_category_options			=	$this->_get_wod_category_lookup(TRUE); //true is blank row
		$wod_category_attrib			=	'id = "_wodCategory"';
		$data['wod_category_dropdown']	=  form_dropdown('wod_category_id', $wod_category_options, set_value('wod_category_id',$benchmark_wod['wod_category_id']), $wod_category_attrib);
				
		$data['description'] = array(
										'name'			=>	'description',
										'id'			=>	'_description',
										'autocomplete'	=>	'off',
										'value'			=>	set_value('description',$benchmark_wod['description'])
									);

		$data['note'] = array(
										'name'			=>	'note',
										'id'			=>	'_note',
										'autocomplete'	=>	'off',
										'value'			=>	set_value('note',$benchmark_wod['note'])
									);
				
		$score_type_options = array(
								''	=>	'',
								'T'	=>	'For Time',			//Integer, stored in seconds, T tells UI to display minutes second
								'I'	=>	'Reps/Round Count', //Integer values stored
								'W'	=>	'Maximum Weight', //Decimal value stored (e.g. 105.5 lbs)
								'O'	=>	'Other', //Unknown way to score...becomes free text field
								);
		$score_type_attrib	=	'id = "_scoreType" ';
		$data['score_type_dropdown'] =  form_dropdown('score_type', $score_type_options, set_value('score_type',$benchmark_wod['score_type']), $score_type_attrib);


		$data['submit'] = array(
										'class'			=>	'ui-btn-hidden',
										'value'			=>	'Save',
										'aria-disabled'	=>	'false',
										'data-inline'	=>	'true',
										'data-theme'	=>	'b',
										'type'			=>	'Submit',
										);
		
		return $data;
	}
	
	//User will be presnted with a wizard for saving box wods
	private function _get_box_wod_wizard_data()
	{
		$this->load->model('Box_model');
		$this->load->model('Wod_model');
                $this->load->model('Scale_model');
		$wizard_data		=	null;
		$facility_list		=	'';
		$pick_day_list		=	'';
		$benchmark_wod_list	=	'';
		$hidden_wod_info	=	'';
		$score_type_list	=	'';
                $scale_list             =       '';
		
		//Get Facilities for selection
		$facility_list_array = $this->Box_model->get_box_list(TRUE);
		foreach($facility_list_array as $row) 
			$facility_list  .= '<li><a id="box_id_'.$row['box_id'].'" class="facility-link" href="#PickDayPage">'.$row['box_name'].'</a></li>';
		
		$today_text	=	"Today - " . date("l - m/d/Y", mktime(0, 0, 0, date("m"), date("d"), date("Y")));
		$tomorrow		=	mktime(0, 0, 0, date("m"), date("d") + 1, date("Y"));
		$tomorrow_text	=	"Tomorrow - " . date("l - m/d/Y", $tomorrow);
		$other_day_text		=	"Some other date";
		
		$day_array	=	array($tomorrow_text, $today_text, $other_day_text);
		foreach($day_array as $day_text) 
			$pick_day_list  .= '<li><a class="day-link" href="#IsBenchmarkWodPage">'.$day_text.'</a></li>';
		
		
		//Get list of Benchmark WODs
		$benchmark_wod_array = $this->Wod_model->get_benchmark_wod_list();
		
		foreach($benchmark_wod_array as $row) 
		{
			//score_type description title
			$hidden_wod_info		.=	'<div class="hidden-data" id="wod_data_'.$row['wod_id'].'"  data-wod-title="'.$row['title'].'" data-score-type="'.$row['score_type'].'" data-wod-description="'.str_replace('<br>',"\r\n",str_replace('"','&Prime;',$row['description'])).'" ></div>';
			$benchmark_wod_list		.=	'<li><a id="wod_id_'.$row['wod_id'].'" class="benchmark-wod-link" href="#BuyInPage">'.$row['title'].'</a></li>';
		}
					
		$score_type_array = array(
									'T'	=>	'For Time',			//Integer, stored in seconds, T tells UI to display minutes second
									'I'	=>	'Reps/Round Count', //Integer values stored
									'W'	=>	'Maximum Weight', //Decimal value stored (e.g. 105.5 lbs)
									'O'	=>	'Other', //Unknown way to score...becomes free text field
								);
		
		foreach($score_type_array as $key => $value) 
			$score_type_list  .= '<li><a id="score_type_'.$key.'" class="score-type-link" href="#PickScalePage">'.$value.'</a></li>';
		
		$scale_array = $this->Scale_model->get_scale_list('ADMIN');

                foreach($scale_array as $row) 
                        $scale_list		.=	'<li><a id="scale_id_'.$row['scale_id'].'" class="scale-link" href="#WodPage">'.$row['box_name'].' - '.$row['scale_name'].'</a></li>';

                $wizard_data['scale_list']		=	$scale_list;
		$wizard_data['facility_list']		=	$facility_list;
		$wizard_data['pick_day_list']		=	$pick_day_list;
		$wizard_data['benchmark_wod_list']	=	$benchmark_wod_list;
		$wizard_data['score_type_list']		=	$score_type_list;
		$wizard_data['hidden_wod_info']		=	$hidden_wod_info;
		
		return $wizard_data;
	}
			   
	
	private function _get_box_wod_form_data($member_box_wod)
	{
		$data	=	null;

		$data['wod_date'] = array(
										'name'			=>	'wod_date',
										'id'			=>	'_wodDate',
										'autocomplete'	=>	'off',
										'value'			=>	set_value('wod_date',$member_box_wod['wod_date'])
									);
		
		$box_options			=	$this->_get_box_lookup(TRUE); //true is blank row
		$box_attrib				=	'id = "_box"';
		$data['box_dropdown']	=  form_dropdown('box_id', $box_options, set_value('box_id',$member_box_wod['box_id']), $box_attrib);
		
		$benchmark_wod_options			=	$this->_get_benchmark_wod_lookup(TRUE); //true is blank row
		$benchmark_wod_attrib			=	'id = "_benchmarkWod"';
		$data['benchmark_wod_dropdown']	=	form_dropdown('wod_id', $benchmark_wod_options, set_value('wod_id',$member_box_wod['wod_id']), $benchmark_wod_attrib);
		
		
		$wod_type_options			=	$this->_get_wod_type_lookup(TRUE); //true is blank row
		$wod_type_attrib			=	'id = "_wodType"';
		$data['wod_type_dropdown']	=	form_dropdown('wod_type_id', $wod_type_options, set_value('wod_type_id',$member_box_wod['wod_type_id']), $wod_type_attrib);
                
                $scale_options			=	$this->_get_scale_lookup(); //true is blank row
		$scale_attrib			=	'id = "_scale"';
		$data['scale_dropdown']	=	form_dropdown('scale_id', $scale_options, set_value('scale_id',$member_box_wod['scale_id']), $scale_attrib);

		
		$data['buy_in'] = array(
										'name'			=>	'buy_in',
										'id'			=>	'_buyIn',
										'autocomplete'	=>	'off',
										'value'			=>	set_value('buy_in',$member_box_wod['buy_in'])
									);

		$data['cash_out'] = array(
										'name'			=>	'cash_out',
										'id'			=>	'_cashOut',
										'autocomplete'	=>	'off',
										'value'			=>	set_value('cash_out',$member_box_wod['cash_out'])
									);
		
		$data['simple_title'] = array(
										'name'			=>	'simple_title',
										'id'			=>	'_simpleTitle',
										'autocomplete'	=>	'off',
										'value'			=>	set_value('simple_title',$member_box_wod['simple_title'])
									);

		$data['simple_description'] = array(
										'name'			=>	'simple_description',
										'id'			=>	'_simpleDescription',
										'autocomplete'	=>	'off',
										'value'			=>	set_value('simple_description',$member_box_wod['simple_description'])
									);
		
		$score_type_options = array(
								''	=>	'',
								'T'	=>	'For Time',			//Integer, stored in seconds, T tells UI to display minutes second
								'I'	=>	'Reps/Round Count', //Integer values stored
								'W'	=>	'Maximum Weight', //Decimal value stored (e.g. 105.5 lbs)
								'O'	=>	'Other', //Unknown way to score...becomes free text field
								);
		$score_type_attrib	=	'id = "_scoreType" data-native-menu="false"';
		$data['score_type_dropdown'] =  form_dropdown('score_type', $score_type_options, set_value('score_type',$member_box_wod['score_type']), $score_type_attrib);


		$data['submit'] = array(
										'class'			=>	'ui-btn-hidden',
										'value'			=>	'Save',
										'aria-disabled'	=>	'false',
										'data-inline'	=>	'true',
										'data-theme'	=>	'b',
										'type'			=>	'Submit',
										);
		
		return $data;
	}

	
	private function _get_wod_category_lookup($blank_row = false)
	{
		$this->load->model('Wod_model');
		$wod_category_list_lookup = $this->Wod_model->get_wod_category_list();
		return $this->set_lookup($wod_category_list_lookup,'wod_category_id','title',$blank_row ? BLANK_ROW : false);
	}
	
	private function _get_box_lookup($blank_row = false)
	{
		
		$this->load->model('Box_model');
		$box_list_lookup = $this->Box_model->get_box_list(TRUE);
		return $this->set_lookup($box_list_lookup,'box_id','box_name',$blank_row ? BLANK_ROW : false);
		
	}
	
	private function _get_wod_type_lookup($blank_row = false)
	{

		$this->load->model('Wod_model');
		
		$wod_type_list_lookup = $this->Wod_model->get_wod_type_list();
		return $this->set_lookup($wod_type_list_lookup,'wod_type_id','title',$blank_row ? BLANK_ROW : false);
		
	}
	
	//Returns a list of the benchmark CrossFit WODs that may apply to the box WOD (Heroes, Girls and Other)
	private function _get_benchmark_wod_lookup($blank_row = false)
	{

		$this->load->model('Wod_model');
		
		$wod_type_list_lookup = $this->Wod_model->get_benchmark_wod_list();
		return $this->set_lookup($wod_type_list_lookup,'wod_id','title',$blank_row ? BLANK_ROW : false);
		
	}
        
        private function _get_scale_lookup()
	{

            $scale_array = array();
            $this->load->model('Scale_model');
            $this->load->model('Box_model');

            $box_id = 'ADMIN';

            $scale_list_lookup = $this->Scale_model->get_scale_list($box_id);
            
            $scale_array[''] = '';


            foreach ($scale_list_lookup as $row)
                    $scale_array[$row['scale_id']] = $row['box_name'].' - '.$row['scale_name']; 
                
            return $scale_array;
		
	}


}

/* End of file administrator_functions.php */
/* Location: ./application/controllers/administrator_functions.php */