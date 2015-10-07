<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class MY_Form_validation extends CI_Form_validation 
{

	function __construct()	
	{
		parent::__construct();
	}

	function valid_date($date_passed)
	{
		$CI =&get_instance();
		$date_var	=	explode('/',$date_passed);

		if (count($date_var) == 3)
		{
			$year		=	$date_var[2];
			$month		=	$date_var[0];
			$day		=	$date_var[1];
		
			if (checkdate($month,$day,$year))
			return true;
		}

		// match but logically invalid
		$CI->form_validation->set_message('valid_date', "The date or date format is invalid.  Use 'mm/dd/yyyy'");
		return false;
	}

}


/* End of file MY_Form_validation.php */
/* Location: ./application/libraries/MY_Form_validation.php */