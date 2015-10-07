<?php
/** 
 * Login
 * This class handles model work necessary for logging in user
 * 
 * @author Ray Nowell
 *	
 */ 
class Login_model extends CI_Model {

	function Login_model()
	{
		parent::__construct();
			$this->load->library('encrypt');
	}
	
	//Returns user email based on member_id; used for emailing Max Data
	function get_user_email($member_id	=	'')
	{
		$member_id	=	$member_id == '' ? $this->session->userdata('member_id') : $member_id;
		$query = $this->db->limit(1)->get_where('member', array('member_id' => $member_id));
		if ($query->num_rows() == 0)
			return false;
		
		$email = $query->row()->email;
		
		return $email; 
	}
	function get_user_login_email($form_value)
	{
		$query = $this->db->limit(1)->get_where('member', array('user_login' => $form_value));
		if ($query->num_rows() == 0)
			return false;
		
		$email = $query->row()->email;
		
		return $email; 
	}
	
	function get_user_login_data_by_email($user_email)
	{
		//START AUDIT
		$this->load->model('Audit_model');
		$audit_data['controller']	=	'login_model';
		$audit_data['ip_address']	=	$_SERVER['REMOTE_ADDR'];
		$audit_data['short_description']	=	'Email retrieve attempt';
		$audit_data['full_info']	=	'Email:  '.$user_email;
		$this->Audit_model->save_audit_log($audit_data);
		//END AUDIT
			
		$query = $this->db->limit(1)->get_where('member', array('email' => $user_email));
		if ($query->num_rows() == 0)
			return false;
		
		$user_login	=	$query->row()->user_login;
		$password = $query->row()->password;
		$decoded_pw	= $this->encrypt->decode($password);
		
		return array(	'password' => $decoded_pw,
						'user_login'	=>$user_login);
	}
	
	function create_user($data)
	{
		$data['password'] = $this->encrypt->encode($data['password']);
		$data['user_login'] = strtolower($data['user_login']);
		
		$query = $this->db->limit(1)->get_where('member', array('user_login' => $data['user_login']));
		if ($query->num_rows() > 0)
		{
			return array(	'success'  =>  false,
							'message'  =>  'This user login already exists');
		}
		
		$query = $this->db->limit(1)->get_where('member', array('email' => $data['email']));
		if ($query->num_rows() > 0)
		{
			return array(	'success'  =>  false,
							'message'  =>  'This email already exists');
		}
		
		$this->db->insert('member', $data);
		//Return new member values to log them in and send welcome email
		$new_member_id	=	$this->db->insert_id();
		$query	=	$this->db->limit(1)->get_where('member', array('member_id' => $new_member_id));
		$row			=	$query->result_array();
		$member_row	=	array_shift(array_values($row));
		return array(	'success'		=>  true,
						'site_admin'	=>	false,
						'member_id'		=>	$member_row['member_id'],
						'display_name'	=>	$member_row['first_name'],
						'member_box_id'	=>	$member_row['box_id'],
						'email'			=>	$member_row['email'],
						'user_login'	=>	$member_row['user_login'],
					);
	}
	
	//This is used for both the main login and kiosk save
	function login_user($user_login, $password)
	{
        $user_login = strtolower($user_login);
		
		$this->db->from('member');
		$this->db->where('user_login', $user_login);
		$this->db->or_where('email', $user_login);
		$this->db->limit(1);
		$query = $this->db->limit(1)->get();
		
		if ($query->num_rows() === 0)
		{
			//START AUDIT
			$this->load->model('Audit_model');
			$audit_data['controller']	=	'login_model';
			$audit_data['ip_address']	=	$_SERVER['REMOTE_ADDR'];
			$audit_data['short_description']	=	'Bad User ID or Email';
			$audit_data['full_info']	=	'User ID:  '.$user_login;
			$this->Audit_model->save_audit_log($audit_data);
			//END AUDIT
			
			return array(	'success'  =>  false,
							'message'  =>  'User login not found');
		}
		
		$row = $query->row();
		$decoded_pw = $this->encrypt->decode($row->password);
		
		//Remove any preceding/following spaces
		//make password case insensitive:  do this because pw input is not hidden and phone browser's autocorrect the box
		$password	=	strtolower(trim($password));
		$decoded_pw	=	strtolower(trim($decoded_pw));

		if ($password != $decoded_pw)
		{
			
			//START AUDIT
			$this->load->model('Audit_model');
			$audit_data['controller']	=	'login_model';
			$audit_data['ip_address']	=	$_SERVER['REMOTE_ADDR'];
			$audit_data['short_description']	=	'Bad Password';
			$audit_data['full_info']	=	'User ID (or email):  '.$user_login.'\r\n'.'PW:  '.$password;
			$this->Audit_model->save_audit_log($audit_data);
			//END AUDIT
			
			return array(	'success'  =>  false,
							'message'  =>  'Password incorrect');
		}


		$ret_val = array(	'success'		=>	true,
							'member_id'		=>	$row->member_id,
							'display_name'	=>	$row->first_name,
							'site_admin'	=>	$row->site_admin,
							'member_box_id'	=>	$row->box_id,
							'message'		=>  '');        

		return $ret_val;
	}
}

/* End of file login_model.php */
/* Location: ./system/application/models/login_model.php */