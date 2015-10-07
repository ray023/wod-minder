<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Exercise Class
 * 
 * @package wod-minder
 * @subpackage controller
 * @category main-screen
 * @author Ray Nowell
 * 
 */
class Exercise extends MY_Controller {
	
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
		redirect ('welcome');
	}
        
	public function get_user_max_pr_board()
	{
		//$this->output->enable_profiler(TRUE);
		if (!$this->logged_in)
			redirect ('member/login');
            
		$this->load->model('Exercise_model');
			
		//START AUDIT
		$this->load->model('Audit_model');
		$audit_data['controller']	=	'exercise_controller';
		$audit_data['ip_address']	=	$_SERVER['REMOTE_ADDR'];
		$audit_data['member_id']	=	$this->session->userdata('member_id');
		$audit_data['member_name']	=	$this->session->userdata('display_name');
		$audit_data['short_description']	=	'Get PR Data';
		$this->Audit_model->save_audit_log($audit_data);
		//END AUDIT
            
		$user_max_month_array    =   $this->Exercise_model->get_user_max_months();
		$pr_details = '';
            
		$pr_main_menu            =  '';
		$pr_sub_pages            =  '';
		$month_count              =	0;
		$pr_count = 0;

		if (!$user_max_month_array)
		{
			$pr_main_menu	=	'No Maxes Saved.<br>';
			$pr_main_menu	.=	'Save a max to enjoy the benefit of knowing your monthly PRs!';
			$pr_count		=	'';
		}	
		else
		{
			foreach($user_max_month_array as $maxmonth_row)
			{
				$user_pr_array =   $this->Exercise_model->get_user_max_snapshot($maxmonth_row['lift_year_month']);
				$pr_count = 0;
				$pr_row_data = '';                

				foreach($user_pr_array as $pr_row)
				{
					$alt_row_class	= $pr_count%2 == 1 ? 'alternate-row' : '';
					$max_rep		= $pr_row['max_rep'] == '' ? '' : ', '.$pr_row['max_rep'];
					$pr_row_data	.=	'<div class="ui-block-a '.$alt_row_class.' ">'.$pr_row['exercise'].$max_rep.'</div>';
					$pr_row_data	.=	'<div class="ui-block-b number-block '.$alt_row_class.' ">'.$pr_row['max_date'].'</div>';
					$pr_row_data	.=	'<div class="ui-block-c number-block '.$alt_row_class.' ">'.intval($pr_row['max_value']).'</div>';
					$pr_count++;
				}

				$pr_details		.=	'<div data-role="page" id="id_'.$pr_row['lift_year_month'].'">'.
														'<div data-role="header">
																<a href="#PrPicker" data-icon="back" data-iconpos="notext" data-direction="reverse">Go back and pick WOD</a>
																<h1>'.$maxmonth_row['page_title'].'</h1>
														</div><!-- /header -->';									
				$pr_details		.= '<div class="ui-grid-b">';
				$pr_details		.= '<div class="ui-block-a mobile-grid-header">Movement</div>';
				$pr_details		.= '<div class="ui-block-b mobile-grid-header date-block">Date</div>';
				$pr_details		.= '<div class="ui-block-c mobile-grid-header number-block">Value</div>';
				$pr_details             .= $pr_row_data;
				$pr_details              .= '</div></div></div>';

				$pr_main_menu .=    '<li data-theme="e"><a href="#id_'.$pr_row['lift_year_month'].'">'.$maxmonth_row['page_title'].'</a><span class="ui-li-count">'.$pr_count.'</span></li>';

				$month_count++;
			}
		}
            
		$data['pr_month_list']  =       $pr_main_menu;//.'<ul>'.$pr_sub_pages.'</ul>';
		$data['pr_details']     =       $pr_details;
		$data['title']		=	'Monthly PR\'s';
		$data['heading']	=	'Monthly PR\'s';
		$data['view']		=	'mobile_member_monthly_prs';
		
		$this->load->vars($data);
		$this->load->view('mobile_master', $data);
            
		return;
	}
	
	/*
	 * Saves a member max either as a new record an edits an existing one.
	 */
	public function save_member_max()
	{
		//$this->output->enable_profiler(TRUE);
		if (!$this->logged_in)
			redirect ('member/login');
		
		define('ID_IDENTIIFER',3);
		define('ID_VALUE',4);
		$id_type	=	$this->uri->segment(ID_IDENTIIFER);
		$id_value	=	$this->uri->segment(ID_VALUE);
                $error_message  =	'';

		$this->load->model('Exercise_model');
		
		$max_data	=	'';
		if ($id_type === 'exercise_id') //This will be a new wod to save
		{
			$exercise					=	$this->Exercise_model->get_exercise($id_value);
			$max_data['exercise_id']	=	$exercise->exercise_id;
			$max_data['max_type']		=	$exercise->max_type;
			$max_data['exercise_name']	=	$exercise->title;
			
			$max_data['max_date']		=	date('m/d/y');
			$max_data['mm_id']			=	'';
			$max_data['max_rep']		=	'';
			$max_data['max_value']		=	'';
		}
		else //otherwise, editing existing member max (mm_id)
			$max_data					=	$this->Exercise_model->get_member_max($id_value);

		$this->form_validation->set_rules('max_date'	, 'Lift Date'	,	'trim|required');
		$this->form_validation->set_rules('max_value'	, 'My Max'		,	'trim|greater_than[0]|required');		
		if ($max_data['max_type'] == 'W')
			$this->form_validation->set_rules('max_rep'		, 'Reps'		,	'trim|integer|greater_than[0]|less_than[50]|required');

		if ($this->form_validation->run() == TRUE) 
		{
			$max_date				=	$this->input->post('max_date');
			
			$data['max_date']		=	$this->make_us_date_mysql_friendly($max_date);
			if ($max_data['max_type'] == 'W')
				$data['max_rep']		=	$this->input->post('max_rep');

			$data['max_value']		=	$this->input->post('max_value');
			
			if ($id_type === 'exercise_id')
			{
				$data['exercise_id']	=	$id_value;
				$data['created_date']	=	date('Y-m-d H:i:s');
				$data['created_by']		=	$this->session->userdata('display_name');
			}
			else
				$data['mm_id']	=	$id_value;
				
			$data['modified_date']	=	date("Y-m-d H:i:s");
			$data['modified_by']	=	$this->session->userdata('display_name');
			
			$ret_val = $this->Exercise_model->save_member_max($data);
			if ($ret_val['success']) 
			{
				$this->session->set_flashdata('good_message', 'Max saved.');
				redirect('welcome/index/TRUE');
			}
			else
				$error_message = $ret_val['message'];
		}
		
		$this->load->helper('form'); 
		
		$data['site_admin']		=	$this->is_admin;
        $data['display_name']	=	$this->display_name;

		$data['title']		=	'Exercise';
		$data['heading']	=	'Exercise';
		$data['view']		=	'mobile_member_max_save';
		$data['id_type']	=	$id_type;
		$data['id_value']	=	$id_value;
		
		$data['exercise_id']	=	$max_data['exercise_id'];
		$data['max_title']		=	'Not set';
		switch ($max_data['max_type']) 
		{
			case 'T':
				$data['max_title']	=	'Time';
				break;
			case 'W':
				$data['max_title']	=	'Weight/#';
				break;
			case 'R':
				$data['max_title']	=	'Reps';
				break;
		}
		
		$data['doc_ready_call']	=	'member_max_doc_ready();';
		
		$data['error_message'] = (validation_errors()) ? validation_errors() : $error_message;
		$data['good_message']	= $this->session->flashdata('good_message');
		
		$form_controls	= $this->_get_form_controls($max_data);
				
		$previous_max	=	$this->Exercise_model->get_member_exercise_max($max_data['exercise_id']);
		$previous_max_grid['previous_max_grid'] = '';
		if ($previous_max != FALSE)
		{
			$alt_row	=	0;
			foreach($previous_max as $row) 
				{
					//$previous_max_grid['previous_max_grid'] .= '<div class="ui-block-a '.($alt_row%2==1) ? '' : 'alternate-row'.'">'.$row['max_rep'].'</div>';
					$is_odd	=	$alt_row%2==1;
					$alt_row_class	=	$is_odd	? 'alternate-row' : '';
					$previous_max_grid['previous_max_grid'] .= '<div class="ui-block-a '.$alt_row_class.'">'.$row['max_rep'].'</div>';
					$previous_max_grid['previous_max_grid'] .= '<div class="ui-block-b date-block '.$alt_row_class.'">'.$row['previous_max_date'].'</div>';
					$previous_max_grid['previous_max_grid'] .= '<div class="ui-block-c number-block '.$alt_row_class.'">'.$row['max_value'].'</div>';
					$alt_row++;
				}
		}
		
		$max_rank_array	=	$this->Exercise_model->get_max_rank($max_data['exercise_id']);
		if (!$max_rank_array)
			$data['max_rank_grid']	=	'No users have saved maxes for this exercise yet.';
		else
		{
			$alt_row	=	0;
			$rank	=	1;
			$max_rank_grid	=	'';
			foreach($max_rank_array as $row) 
				{
					//$previous_max_grid['previous_max_grid'] .= '<div class="ui-block-a '.($alt_row%2==1) ? '' : 'alternate-row'.'">'.$row['max_rep'].'</div>';
					$is_odd	=	$alt_row%2==1;
					$alt_row_class	=	$is_odd	? 'alternate-row' : '';
					$alt_row_class	=	$row['member_id']	== $this->session->userdata('member_id')	?	'self-row'	:	$alt_row_class;
					$max_rank_grid .= '<div class="ui-block-a number-block '.$alt_row_class.'">'.$rank.'</div>';
					$max_rank_grid .= '<div class="ui-block-b number-block '.$alt_row_class.'">'.$row['max_value'].'</div>';
					$alt_row++;
					$rank++;
				}
				

			$max_rank_grid	=	'<div class="ui-grid-a">
									<div class="ui-block-a mobile-grid-header number-block">Rank</div>
									<div class="ui-block-b mobile-grid-header number-block">Value</div>
									'.$max_rank_grid.'
								 </div>';
			$data['max_rank_grid']	=	$max_rank_grid;
		}
		
		$data = array_merge($data,$max_data, $form_controls,$previous_max_grid);
		
		$this->load->vars($data);
		$this->load->view('mobile_master', $data);

		return;
	}
	
	
	/*
	 * Gets the form controls required for saving max data
	 */
	private function _get_form_controls($max_data)
	{
		$data		=	'';
		$max_date	=	$max_data['max_date'] === '' ? date("m/d/y") : $max_data['max_date'];
		
		$data['max_date'] = array(
				'name'			=>	'max_date',
				'id'			=>	'_liftDate',
				'maxlength'		=>	'20',
				'placeholder'	=>	'Lift Date',
				'autocomplete'	=>	'off',
				'value'			=> set_value('max_date', $max_date),
			);
		
		$data['max_rep'] = array(
				'name'			=>	'max_rep',
				'id'			=>	'_maxRep',
				'type'			=>	'number',
				'placeholder'	=>	'Maximum Reps',
				'autocomplete'	=>	'off',
				'value'			=>	set_value('max_rep', $max_data['max_rep']),
			);
				
		if ($max_data['max_type']	!=	'T')
		{
			$data['max_value'] = array(
				'name'			=>	'max_value',
				'id'			=>	'_maxValue',
				'type'			=>	'number',
				'placeholder'	=>	'My Max',
				'autocomplete'	=>	'off',
				'value'			=> set_value('max_value', $max_data['max_value']),
			);
		}
		else
		{
			$max_value		=	set_value('max_value', $max_data['max_value']);
			$data['max_value']	=	$max_value;
			
			$max_minutes	=	'';
			$max_seconds	=	'';
			
			if ($max_value > 0)
			{
				$max_minutes	=	floor($max_value / 60);
				$max_seconds	=	$max_value - ($max_minutes * 60);
			}
			$data['max_minutes'] = array(
					'name'			=>	'max_minutes',
					'id'			=>	'_maxMinutes',
					'type'			=>	'number',
					'autocomplete'	=>	'off',
					'value'			=>	$max_minutes,
					'data-inline'	=>	'true',
					);
			
			$data['max_seconds'] = array(
					'name'			=>	'max_seconds',
					'id'			=>	'_maxSeconds',
					'type'			=>	'number',
					'autocomplete'	=>	'off',
					'value'			=>	$max_seconds,
					'data-inline'	=>	'true',
					);
		}
		
		$data['submit'] = array(
				'name'			=>	'submit',
				'id'			=>	'_submit',
				'class'			=>	'ui-btn-hidden',
				'value'			=>	'Save Max',
				'aria-disabled'	=>	'false',
				'data-inline'	=>	'true',
				'data-theme'	=>	'b',
				'type'			=>	'Submit',
			);

		return $data;
	}
}

/* End of file exercise.php */
/* Location: ./application/controllers/exercise.php */