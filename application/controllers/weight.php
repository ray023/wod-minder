<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Member Class
 * 
 * @package wod-minder
 * @subpackage controller
 * @category weight
 * @author Ray Nowell
 * 
 */
class Weight extends MY_Controller {

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
        
		redirect ('weight/save_member_weight');
		
		return;
	}
	public function save_member_weight()
	{
		//$this->output->enable_profiler(TRUE);

		define('ID_VALUE',3);
		if (!$this->logged_in)
			redirect ('member/login');
	
                $error_message = '';
		$id_value = $this->uri->segment(ID_VALUE);//id_value
		$this->load->model('Weight_model');
		
		$weight_data = '';
		
		//Initialize and have something to fall back on if weight record doesn't exist
		$weight_data['weight_date']			=	date('m/d/y');
		$weight_data['weight']				=	'';
		$weight_data['bmi']					=	'';
		$weight_data['body_fat_percentage']	=	'';
		$weight_data['note']				=	'';
		$weight_data['image_name']			=	'';
		
		if ($id_value != '') 
		{			
			$temp_weight_data					=	$this->Weight_model->get_member_weight_history($id_value);
			if(is_null($temp_weight_data))
				$id_value	=	'';
			else
				$weight_data = $temp_weight_data;				
		}

		$this->form_validation->set_rules('weight_date', 'Date:'	,	'trim|required');
		$this->form_validation->set_rules('weight', 'Weight'	,	'trim');
		$this->form_validation->set_rules('bmi', 'BMI'	,	'trim');
		$this->form_validation->set_rules('body_fat_percentage', 'BMI'	,	'trim');
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
				$data['mwl_id']			=	$id_value;
			
			$data['weight_date']	=	$this->make_us_date_mysql_friendly($this->input->post('weight_date'));
			$data['member_id']		=	$this->session->userdata('member_id');
			
			if (!$this->input->post('weight') !== '')
				$data['weight']					=	$this->input->post('weight');			
			if ($this->input->post('bmi') !== '')
				$data['bmi']					=	$this->input->post('bmi');
			if ($this->input->post('body_fat_percentage') !== '')
				$data['body_fat_percentage']	=	$this->input->post('body_fat_percentage');
			
			$data['note']			=	$this->input->post('note');
			if (!$image_data)
			{
				//do nothing.  no image.
			}
			else
			{
				$destination_folder	=	'/user_images/weight/';
				$file_prefix	=	'weight';
				$new_image_name	=	$this->process_image($image_data['upload_data'], $destination_folder, $file_prefix);

				$data['image_name']			=	$new_image_name;
				
				if ($weight_data['image_name'] !== '')
				{
					//Drop the old image (and thumbnail):
					$file_name	=	$_SERVER['DOCUMENT_ROOT'].'/user_images/weight/'.$weight_data['image_name'];
					if (file_exists($file_name))
						unlink($file_name);
					
					$file_name	=	$_SERVER['DOCUMENT_ROOT'].'/user_images/weight/thumbnail/'.str_replace('.jpg' , '_thumb.jpg',$weight_data['image_name']);
					if (file_exists($file_name))
						unlink($file_name);
				}
			}	

			$ret_val = $this->Weight_model->save_member_weight($data);
			if ($ret_val['success']) 
			{				
				$this->session->set_flashdata('good_message', 'Weight saved.');
				redirect('welcome/index/TRUE');
			}
			else
				$error_message = $ret_val['message'];
		}

		$data['title']		=	'Weight';
		$data['heading']	=	'Save Weight';
		$data['view']		=	'mobile_member_weight_save';
		$data['doc_ready_call']	=	'save_member_weight_doc_ready();';
		$data['id_value']	=	$id_value;
		
		$data['error_message'] = (validation_errors()) ? validation_errors() : $error_message;
		if (!$image_ok)
			$data['error_message']	.=	$image_error;
		
		$this->load->helper('form');
		$member_weight_meal_data					=	$this->_get_weight_meal_form_data($weight_data);
		
		$data = array_merge($data, $weight_data, $member_weight_meal_data);
				
		$this->load->vars($data);
		$this->load->view('mobile_master', $data);
		
		return;
	}
	public function delete_member_weight()
	{
		$delete_id		=	$this->uri->segment(3);

		$this->load->model('Weight_model');
		$weight_data	=	$this->Weight_model->get_member_weight_history($delete_id);
		if (!$weight_data)
		{
			$this->session->set_flashdata('error_message', 'Record not found');
			redirect('welcome/index/TRUE');
		}
		
		if ($weight_data['image_name']	!=	'')
		{
			//Remove the photo and thumbnail:
			$file_name	=	$_SERVER['DOCUMENT_ROOT'].'/user_images/weight/thumbnail/'.str_replace('.jpg' , '_thumb.jpg',$weight_data['image_name']);
			if (file_exists($file_name))
				unlink($file_name);
			
			$file_name	=	$_SERVER['DOCUMENT_ROOT'].'/user_images/weight/'.$weight_data['image_name'];
			if (file_exists($file_name))
				unlink($file_name);			
		}
			
		$ret_val = $this->Weight_model->delete_weight($delete_id);
		
		$this->session->set_flashdata('error_message', 'Record deleted');
		redirect('welcome/index/TRUE');
		
	}
	public function get_user_weight_history()
	{
		
		if (!$this->logged_in)
			redirect ('member/login');
		
		//START AUDIT
		$this->load->model('Audit_model');
		$audit_data['controller']	=	'weight_controller';
		$audit_data['ip_address']	=	$_SERVER['REMOTE_ADDR'];
		$audit_data['member_id']	=	$this->session->userdata('member_id');
		$audit_data['member_name']	=	$this->session->userdata('display_name');
		$audit_data['short_description']	=	'Get Weight History';
		$this->Audit_model->save_audit_log($audit_data);
		//END AUDIT
		
		$this->load->model('Weight_model');
		$weight_history_array	=	$this->Weight_model->get_member_weight_history();
		
		$weight_history	=	'';
		$alt_row	=	0;

		foreach($weight_history_array as $row) 
		{
			$is_odd	=	$alt_row%2==1;
			$alt_row_class	=	$is_odd	? 'alternate-row' : '';
		
			$delete_link	 =	'<a href="#" data-ajax="false" data-role="button" data-icon="delete" data-iconpos="notext" >'.$row['mwl_id'].'</a>';
			$weight_history	.=	'<div class="ui-block-a '.$alt_row_class.'">'.$delete_link.'</div>';
			$edit_link		 =	'<a href="'.base_url().'index.php/weight/save_member_weight/'.$row['mwl_id'].'" data-ajax="false">'.$row['weight_date'].'</a>';
			$weight_history	.=	'<div class="ui-block-b  date-block  grid-row-with-image '.$alt_row_class.'">'.$edit_link.'</div>';
			$weight_history	.=	'<div class="ui-block-c number-block grid-row-with-image '.$alt_row_class.'">'.$row['weight'].'</div>';
			
			$alt_row++;
		}
		
		if ($weight_history	===	'')
			$data['weight_history']	=	'No Weights Saved';
		else
			$data['weight_history']	=	$weight_history;

		$data['doc_ready_call']	=	'mobile_user_weight_history_doc_ready();';
		$data['title']			=	'Weight';
		$data['heading']		=	'Weight History';
		$data['view']			=	'mobile_member_weight_history';
		
		$this->load->vars($data);
		$this->load->view('mobile_master', $data);

		return;
		
	}
	private function _get_weight_meal_form_data($weight_data	=	array())	   
	{
		$data	=	null;
		
		$data['weight_date'] = array(
									'name'			=>	'weight_date',
									'id'			=>	'_weightDate',
									'autocomplete'	=>	'off',
									'value'			=>	set_value('weight_date',$weight_data['weight_date'])
								);
				
		$data['weight'] = array(
								'name'			=>	'weight',
								'id'			=>	'_weight',
								'maxlength'		=>	'10',
								'autocomplete'	=>	'off',
								'value'			=>	set_value('weight',$weight_data['weight'])
							);

		$data['bmi'] = array(
								'name'			=>	'bmi',
								'id'			=>	'_bmi',
								'maxlength'		=>	'10',
								'autocomplete'	=>	'off',
								'value'			=>	set_value('bmi',$weight_data['bmi'])
							);

		$data['body_fat_percentage'] = array(
								'name'			=>	'body_fat_percentage',
								'id'			=>	'_bodyFatPercentage',
								'maxlength'		=>	'10',
								'autocomplete'	=>	'off',
								'value'			=>	set_value('body_fat_percentage',$weight_data['body_fat_percentage'])
							);
		
		$data['note'] = array(
										'name'			=>	'note',
										'id'			=>	'_note',
										'value'			=>	set_value('note',$weight_data['note'])
									);

		$data['image_name'] = array(
										'name'			=>	'image_name',
										'id'			=>	'_imageName',
										'autocomplete'	=>	'off',
										'value'			=>	set_value('no_image_filler.jpg',$weight_data['image_name']),
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
	
}

/* End of file weight.php */
/* Location: ./application/controllers/weight.php */