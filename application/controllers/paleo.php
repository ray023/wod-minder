<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Member Class
 * 
 * @package wod-minder
 * @subpackage controller
 * @category paleo
 * @author Ray Nowell
 * 
 */
class Paleo extends MY_Controller {

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
        
		redirect ('paleo/save_member_paleo_meal');
		
		return;
	}
	public function save_member_paleo_meal()
	{
		//$this->output->enable_profiler(TRUE);

		define('ID_VALUE',3);
		if (!$this->logged_in)
			redirect ('member/login');
	
		$id_value = $this->uri->segment(ID_VALUE);//id_value
		$this->load->model('Paleo_model');
		
		$paleo_meal_data = '';
		
		//Initialize and have something to fall back on if paleo meal doesn't exist
		$paleo_meal_data['meal_date']		=	date('m/d/y');
		$paleo_meal_data['meal_time']		=	date('G');
		$paleo_meal_data['meal_type_id']	=	'';
		$paleo_meal_data['protein']			=	'';
		$paleo_meal_data['veggie_or_fruit']	=	'';
		$paleo_meal_data['fat']				=	'';
		$paleo_meal_data['note']			=	'';
		$paleo_meal_data['image_name']		=	'';

		if ($id_value != '') //This will be a new paleo to save //Saving an existing member's paleo
		{
			if(!strtotime($id_value))
			{				
				$temp_paleo_meal_data					=	$this->Paleo_model->get_member_paleo_meal($id_value);
				if(is_null($temp_paleo_meal_data))
					$id_value	=	'';
				else
				{
					$paleo_meal_data = $temp_paleo_meal_data;				
				}
			}
			else
			{
				$paleo_meal_data['meal_date']		=	date('m/d/y', strtotime($id_value));
				$paleo_meal_data['meal_time']		=	'';
				$paleo_meal_data['meal_type_id']	=	'';
				$paleo_meal_data['protein']			=	'';
				$paleo_meal_data['veggie_or_fruit']	=	'';
				$paleo_meal_data['fat']				=	'';
				$paleo_meal_data['note']			=	'';
				$paleo_meal_data['image_name']		=	'';
			}
		}
		
		$paleo_meal_data['paleo_history']	=	$this->_get_paleo_history_by_meal_date($this->make_us_date_mysql_friendly($paleo_meal_data['meal_date']));
		
		$this->form_validation->set_rules('meal_date', 'Date:'	,	'trim|required');
		$this->form_validation->set_rules('meal_time', 'Meal Time',	'trim|required');
		$this->form_validation->set_rules('meal_type_id', 'Meal'	,	'trim}required');
		$this->form_validation->set_rules('protein', 'Protein'	,	'trim');
		$this->form_validation->set_rules('veggie_or_fruit', 'Veggie/Fruit'	,	'trim');
		$this->form_validation->set_rules('Fat', 'Fat'	,	'trim');
		$this->form_validation->set_rules('note', 'Note'	,	'trim');
		
		//Did user select an image to upload the file?
		$image_data	=	FALSE;
		$image_ok	=	TRUE;
		if (isset($_FILES['userfile']) && $_FILES['userfile']['name'] != '') 
		{
			$config['upload_path']		=	'./uploads/';
			$config['allowed_types']	=	'gif|jpg|png|jpeg';
			$config['max_size']			=	'7160'; //7MB
			$this->load->library('upload', $config);

			if (!$this->upload->do_upload()) 
			{
				$image_error = $this->upload->display_errors();
				$image_ok = false;
			} 
			else 
				$image_data = array('upload_data' => $this->upload->data());
		}
		
		if ($image_ok && $this->form_validation->run() == TRUE) 
		{
			//Determines insert or update in data layer
			if ($id_value	!=	'')
				$data['mp_id']			=	$id_value;
			
			$data['meal_date_time']	=	$this->make_us_date_mysql_friendly($this->input->post('meal_date'));
			$data['meal_date_time']	=	$data['meal_date_time'].' '.$this->input->post('meal_time').':00';
			$data['member_id']		=	$this->session->userdata('member_id');
			if ($this->input->post('meal_type_id') != 0)
				$data['meal_type_id']	=	$this->input->post('meal_type_id');
			$data['protein']		=	$this->input->post('protein');
			$data['veggie_or_fruit']=	$this->input->post('veggie_or_fruit');
			$data['fat']			=	$this->input->post('fat');
			$data['note']			=	$this->input->post('note');
			if (!$image_data)
			{
				//do nothing.  no image.
			}
			else
			{
				$destination_folder	=	'/user_images/paleo/';
				$file_prefix	=	'paleo';
				$new_image_name	=	$this->process_image($image_data['upload_data'], $destination_folder, $file_prefix);

				$data['image_name']			=	$new_image_name;
				
				if ($paleo_meal_data['image_name'] !== '')
				{
					//Drop the old image (and thumbnail):
					$file_name	=	$_SERVER['DOCUMENT_ROOT'].'/user_images/paleo/'.$paleo_meal_data['image_name'];
					if (file_exists($file_name))
						unlink($file_name);
					
					$file_name	=	$_SERVER['DOCUMENT_ROOT'].'/user_images/paleo/thumbnail/'.str_replace('.jpg' , '_thumb.jpg',$paleo_meal_data['image_name']);
					if (file_exists($file_name))
						unlink($file_name);
				}
			}	
				
			$ret_val = $this->Paleo_model->save_member_paleo_meal($data);
			if ($ret_val['success']) 
			{				
				$this->session->set_flashdata('good_message', 'Paleo Meal saved.');
				redirect('welcome/index/TRUE');
			}
			else
			{
				$this->session->set_flashdata('error_message', $ret_val['message']);
				redirect('paleo/'.$id_value);
			}
		}

		$data['title']		=	'Paleo Meal';
		$data['heading']	=	'Save Paleo Meal';
		$data['view']		=	'mobile_member_paleo_save';
		$data['id_value']	=	$id_value;
		
		$data['error_message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error_message');
		if (!$image_ok)
			$data['error_message']	.=	$image_error;
		
		$this->load->helper('form');
		$member_paleo_meal_data					=	$this->_get_paleo_meal_form_data($paleo_meal_data);
		
		$data = array_merge($data, $paleo_meal_data, $member_paleo_meal_data);
				
		$this->load->vars($data);
		$this->load->view('mobile_master', $data);
		
		return;
	}
	
	public function ajax_move_paleo_page() 
	{
		define('ID_VALUE',3);
		$meal_date = $this->uri->segment(ID_VALUE);

		$this->load->model('Paleo_model');

		$paleo_history = $this->_get_paleo_history_by_meal_date($meal_date);
		echo '<div id="gridData">'.$paleo_history.'</div>';
		
		$p	=	$this->Paleo_model->get_previous_meal($meal_date);
		echo '<div id="previousMealDate">'.$p['meal_date'].'</div>';
		
		$n	=	$this->Paleo_model->get_next_meal($meal_date);
		echo '<div id="nextMealDate">'.$n['meal_date'].'</div>';
		
		return;
		
	}
	//Originally called this function ajax_save_paleo_meal, but it caused problems
	//on some phones.  The javascript and CI would get confused and call 
	// save_paleo_meal; not ajax_save_paleo_meal oddly enough.
	//Don't care to inspect the problem at this point; rename should suffice
	public function ajax_spm() 
	{
		define('ID_VALUE',3);
		if (!$this->logged_in)
		{
			echo '<div id="errorMessage">You are not logged in.</div>';
			return;
		}
		
		$this->form_validation->set_rules('meal_date', 'Date'	,	'trim|required');
		$this->form_validation->set_rules('meal_time', 'Meal Time',	'trim|required');
		$this->form_validation->set_rules('meal_type_id', 'Meal Type'	,	'trim|required');
		$this->form_validation->set_rules('protein', 'Protein'	,	'trim');
		$this->form_validation->set_rules('veggie_or_fruit', 'Veggie/Fruit'	,	'trim');
		$this->form_validation->set_rules('Fat', 'Fat'	,	'trim');
		$this->form_validation->set_rules('note', 'Note'	,	'trim');
		
		$form_is_valid	=	$this->form_validation->run();
		
		if (!$form_is_valid)
		{
			echo '<div id="errorMessage">'.validation_errors().'</div>';
			return;
		}
				
		$id_value = $this->uri->segment(ID_VALUE);//id_value
		$this->load->model('Paleo_model');

		//Initialize and have something to fall back on if paleo meal doesn't exist
		$paleo_meal_data['meal_date']		=	date('m/d/y');
		$paleo_meal_data['meal_time']		=	date('G');
		$paleo_meal_data['meal_type_id']	=	'';
		$paleo_meal_data['protein']			=	'';
		$paleo_meal_data['veggie_or_fruit']	=	'';
		$paleo_meal_data['fat']				=	'';
		$paleo_meal_data['note']			=	'';
		$paleo_meal_data['image_name']		=	'';

		if ($id_value != '') //Saving an existing member's paleo
		{
			if(!strtotime($id_value))
			{
				$temp_paleo_meal_data					=	$this->Paleo_model->get_member_paleo_meal($id_value);
				if(is_null($temp_paleo_meal_data))
					$id_value	=	'';
				else
				{
					$paleo_meal_data	=	$temp_paleo_meal_data;
				}
			}
			else
			{
				$paleo_meal_data['meal_date']		=	date('m/d/y', strtotime($id_value));
				$paleo_meal_data['meal_time']		=	'';
				$paleo_meal_data['meal_type_id']	=	'';
				$paleo_meal_data['protein']			=	'';
				$paleo_meal_data['veggie_or_fruit']	=	'';
				$paleo_meal_data['fat']				=	'';
				$paleo_meal_data['note']			=	'';
				$paleo_meal_data['image_name']		=	'';
			}
		}
		
		//Did user select an image to upload the file?
		$image_data	=	FALSE;
		$image_ok	=	TRUE;
		if (isset($_FILES['userfile']) && $_FILES['userfile']['name'] != '') 
		{
			$config['upload_path']		=	'./uploads/';
			$config['allowed_types']	=	'gif|jpg|png|jpeg';
			$config['max_size']			=	'7160'; //7MB
			$this->load->library('upload', $config);

			if (!$this->upload->do_upload()) 
			{
				echo $this->upload->display_errors();
				return;
			} 
			else 
				$image_data = array('upload_data' => $this->upload->data());
		}
		
		if ($image_ok && $form_is_valid) 
		{
			//Determines insert or update in data layer
			if ($id_value	!=	'')
				$data['mp_id']			=	$id_value;
			
			$data['meal_date_time']	=	$this->make_us_date_mysql_friendly($this->input->post('meal_date'));
			$data['meal_date_time']	=	$data['meal_date_time'].' '.$this->input->post('meal_time').':00';
			$data['member_id']		=	$this->session->userdata('member_id');
			if ($this->input->post('meal_type_id') != 0)
				$data['meal_type_id']	=	$this->input->post('meal_type_id');
			$data['protein']		=	$this->input->post('protein');
			$data['veggie_or_fruit']=	$this->input->post('veggie_or_fruit');
			$data['fat']			=	$this->input->post('fat');
			$data['note']			=	$this->input->post('note');
			if (!$image_data)
			{
				//do nothing.  no image.
			}
			else
			{
				$destination_folder	=	'/user_images/paleo/';
				$file_prefix	=	'paleo';
				$new_image_name	=	$this->process_image($image_data['upload_data'], $destination_folder, $file_prefix);

				$data['image_name']			=	$new_image_name;
				
				if ($paleo_meal_data['image_name'] !== '')
				{
					//Drop the old image (and thumbnail):
					$file_name	=	$_SERVER['DOCUMENT_ROOT'].'/user_images/paleo/'.$paleo_meal_data['image_name'];
					if (file_exists($file_name))
						unlink($file_name);
					
					$file_name	=	$_SERVER['DOCUMENT_ROOT'].'/user_images/paleo/thumbnail/'.str_replace('.jpg' , '_thumb.jpg',$paleo_meal_data['image_name']);
					if (file_exists($file_name))
						unlink($file_name);
				}
			}	
				
			$ret_val = $this->Paleo_model->save_member_paleo_meal($data);
			if ($ret_val['success']) 
			{
				$this->load->model('Paleo_model');
				
				$the_date = $this->make_us_date_mysql_friendly($this->input->post('meal_date'));
				$paleo_history = $this->_get_paleo_history_by_meal_date($the_date);
				echo '<div id="goodMessage">Meal Saved</div>';
				echo '<div id="gridData">'.$paleo_history.'</div>';
				$p	=	$this->Paleo_model->get_previous_meal($the_date);
				echo '<div id="previousMealDate">'.$p['meal_date'].'</div>';
				$n	=	$this->Paleo_model->get_next_meal($the_date);
				echo '<div id="nextMealDate">'.$n['meal_date'].'</div>';
				/*  DON'T THINK I'M GOING TO USE THIS BECAUSE 
				 *  FORM GOES BLANK WHEN USER SAVES/UPDATES
				if ($new_image_name != FALSE)
					echo '<div id="newImageName">'.$new_image_name.'</div>';
				return;
				*/
			}
			else
			{
				echo '<div id="errorMessage">'.$ret_val['message'].'</div>';
				return;
			}
		}
		
    }
	//Change the name of this function so we don't run into the same issue
	//as with saving the paleo meal
	public function ajax_dmpm()
	{
		$delete_id		=	$this->uri->segment(3);

		$this->load->model('Paleo_model');
		$paleo_meal_data	=	$this->Paleo_model->get_member_paleo_meal($delete_id);
		if ($paleo_meal_data['image_name']	!=	'')
		{
			//Remove the photo and thumbnail:
			$file_name	=	$_SERVER['DOCUMENT_ROOT'].'/user_images/paleo/thumbnail/'.str_replace('.jpg' , '_thumb.jpg',$paleo_meal_data['image_name']);
			if (file_exists($file_name))
				unlink($file_name);
			
			$file_name	=	$_SERVER['DOCUMENT_ROOT'].'/user_images/paleo/'.$paleo_meal_data['image_name'];
			if (file_exists($file_name))
				unlink($file_name);			
		}
			
		$ret_val = $this->Paleo_model->delete_paleo_meal($delete_id);

		$the_date = $this->make_us_date_mysql_friendly($paleo_meal_data['meal_date']);
		$paleo_history = $this->_get_paleo_history_by_meal_date($the_date);
		echo '<div id="errorMessage">Meal Deleted</div>';
		echo '<div id="gridData">'.$paleo_history.'</div>';
		
	}
	public function get_user_paleo_history()
	{
		
		if (!$this->logged_in)
			redirect ('member/login');
		
		$this->load->model('Paleo_model');
		$paleo_history_array	=	$this->Paleo_model->get_member_paleo_select_list();
		
		$paleo_history	=	'';
		$alt_row	=	0;
		
		foreach($paleo_history_array as $row) 
			$paleo_history  .= '<li><a data-ajax="false" href="'.base_url().'index.php/paleo/save_member_paleo_meal/'.$row['meal_day'].'">'.$row['meal_date'].'</a><span class="ui-li-count">'.$row['meal_count'].'</span></li>';

		/*
		foreach($paleo_history_array as $row) 
		{
			$is_odd	=	$alt_row%2==1;
			$alt_row_class	=	$is_odd	? 'alternate-row' : '';
		
			$delete_link	 =	'<a href="#" data-ajax="false" data-role="button" data-icon="delete" data-iconpos="notext" >'.$row['mp_id'].'</a>';
			$paleo_history	.=	'<div class="ui-block-a '.$alt_row_class.'">'.$delete_link.'</div>';
			$edit_link		 =	'<a href="'.base_url().'index.php/paleo/save_member_paleo_meal/'.$row['mp_id'].'" data-ajax="false">'.$row['meal_date'].'</a>';
			$paleo_history	.=	'<div class="ui-block-b  date-block  grid-row-with-image '.$alt_row_class.'">'.$edit_link.'</div>';
			$paleo_history	.=	'<div class="ui-block-c number-block grid-row-with-image '.$alt_row_class.'">'.$row['meal_type'].'</div>';
			
			$alt_row++;
		}
		*/
		
		if ($paleo_history	===	'')
			$data['paleo_history']	=	'No Paleo Meals Saved';
		else
			$data['paleo_history']	=	$paleo_history;

		$data['doc_ready_call']	=	'mobile_user_paleo_history_doc_ready();';
		$data['title']			=	'Paleo Meal';
		$data['heading']		=	'Paleo History';
		$data['view']			=	'mobile_member_paleo_history';
		
		$this->load->vars($data);
		$this->load->view('mobile_master', $data);

		return;
		
	}
	private function _get_paleo_history_by_meal_date($meal_date		=	'')
	{
		$this->load->model('Paleo_model');
		$paleo_history_array	=	$this->Paleo_model->get_member_paleo_meal_history($meal_date);
		$paleo_history	=	'';
		$alt_row	=	0;
		foreach($paleo_history_array as $row) 
		{
			$is_odd	=	$alt_row%2==1;
			$alt_row_class	=	$is_odd	? 'alternate-row' : '';

			$delete_link	 =	'<a href="" data-role="button" data-icon="delete" data-iconpos="notext" class="mp-delete-link" id="delete_id_'.$row['mp_id'].'" >Delete</a>';
			$paleo_history	.=	'<div class="ui-block-a '.$alt_row_class.'">'.$delete_link.'</div>';
			$edit_link		 =	'<a href="'.base_url().'index.php/paleo/save_member_paleo_meal/'.$row['mp_id'].'" data-ajax="false" class="mp-edit-link" id="edit_id_'.$row['mp_id'].'">'.$row['meal_time'].'</a>';
			$paleo_history	.=	'<div class="ui-block-b  date-block  '.$alt_row_class.'">'.$edit_link.'</div>';
			if ($row['image_name']	!= '')
			{
				$thumb_nail_link	=	base_url().'user_images/paleo/thumbnail/'.str_replace('.jpg' , '_thumb.jpg',$row['image_name']) ;
				$meal	= '<img src="'.$thumb_nail_link.'"/>';
			}
			else
				$meal	=	$row['meal_type'];
				
			$paleo_history	.=	'<div class="ui-block-c  date-block '.$alt_row_class.'">'.$meal.'</div>';
			$paleo_history	.=	'<div class="hidden-data" id="_paleo_form_data_'.$row['mp_id'].'">'.json_encode($row).'</div>';//NOTE:  Fatcow currently at 5.3.  Can't do pretty print yet., JSON_PRETTY_PRINT).'</div>';

			$alt_row++;
		}

		if ($paleo_history	===	'')
			$paleo_history	=	'No paleo meals saved for this date';
		else 
		{
			$paleo_history	=	'<div class="ui-grid-b">
				<div class="ui-block-a mobile-grid-header">&nbsp;</div>
				<div class="ui-block-b mobile-grid-header date-block">Time</div>
				<div class="ui-block-c mobile-grid-header number-block">Meal Type</div>
				'.$paleo_history.'
			</div><!-- /grid-c -->';
		
		}
		
		return $paleo_history;
	}
	private function _get_paleo_meal_form_data($paleo_meal_data	=	array())	   
	{
		$data	=	null;
		
		$data['meal_date'] = array(
									'name'			=>	'meal_date',
									'id'			=>	'_mealDate',
									'autocomplete'	=>	'off',
									'value'			=>	set_value('meal_date',$paleo_meal_data['meal_date'])
								);
		
		$meal_time_options = array(
								''	=>	'',
								 '0'	=>	'12 am','1'		=>	'1 am', '2'	=>	'2 am','3'	=>	'3 am','4'	=>	 '4 am', '5'	=>	 '5 am',
								 '6'	=>	 '6 am','7'		=>	'7 am', '8'	=>	'8 am','9'	=>	'9 am','10'	=>	'10 am','11'	=>	'11 am',	
								'12'	=>	'12 pm','13'	=>	'1 pm','14'	=>	'2 pm','15'	=>	'3 pm','16'	=>	 '4 pm','17'	=>	 '5 pm',	 
								'18'	=>	 '6 pm','19'	=>	'7 pm','20'	=>	'8 pm','21'	=>	'9 pm','22'	=>	'10 pm','23'	=>	'11pm'
								);
		$meal_time_attrib	=	'id = "_mealTime" ';
		$data['meal_time_dropdown'] =  form_dropdown('meal_time', $meal_time_options, set_value('meal_type_id',$paleo_meal_data['meal_time']), $meal_time_attrib);
		
		
		$meal_type_options	=	$this->_get_meal_type_lookup(TRUE); //true is blank row
		$meal_type_attrib	=	'id = "_mealType" '; 
		$data['meal_type_dropdown'] =  form_dropdown('meal_type_id', $meal_type_options, set_value('meal_type_id',$paleo_meal_data['meal_type_id']),$meal_type_attrib);
		
		$data['protein'] = array(
								'name'			=>	'protein',
								'id'			=>	'_protein',
								'maxlength'		=>	'250',
								'autocomplete'	=>	'off',
								'value'			=>	set_value('protein',$paleo_meal_data['protein'])
							);

		$data['veggie_or_fruit'] = array(
								'name'			=>	'veggie_or_fruit',
								'id'			=>	'_veggieOrFruit',
								'maxlength'		=>	'250',
								'autocomplete'	=>	'off',
								'value'			=>	set_value('veggie_or_fruit',$paleo_meal_data['veggie_or_fruit'])
							);

		$data['fat'] = array(
								'name'			=>	'fat',
								'id'			=>	'_fat',
								'maxlength'		=>	'250',
								'autocomplete'	=>	'off',
								'value'			=>	set_value('fat',$paleo_meal_data['fat'])
							);
		
		$data['note'] = array(
										'name'			=>	'note',
										'id'			=>	'_note',
										'value'			=>	set_value('note',$paleo_meal_data['note'])
									);

		$data['image_name'] = array(
										'name'			=>	'image_name',
										'id'			=>	'_imageName',
										'autocomplete'	=>	'off',
										'value'			=>	set_value('no_image_filler.jpg',$paleo_meal_data['image_name']),
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
		
		$md	=	$this->make_us_date_mysql_friendly($paleo_meal_data['meal_date']);
		$p	=	$this->Paleo_model->get_previous_meal($md);
		$data['previous_meal_date']	=	$p['meal_date'];
		
		$n	=	$this->Paleo_model->get_next_meal($md);
		$data['next_meal_date']	=	$n['meal_date'];
		
		
		return $data;

	}
	private function _get_meal_type_lookup($blank_row = false)
	{

		$this->load->model('Paleo_model');
		
		$meal_type_list_lookup = $this->Paleo_model->get_meal_type_list();
		return $this->set_lookup($meal_type_list_lookup,'meal_type_id','title',$blank_row ? BLANK_ROW : false);
		
	}
}

/* End of file paleo.php */
/* Location: ./application/controllers/paleo.php */