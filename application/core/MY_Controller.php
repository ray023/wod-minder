<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{
	protected $is_admin		=	FALSE;
	protected $logged_in	=	FALSE;
	protected $display_name	=	'';

	function __construct() 
	{
		parent::__construct();
        
		$this->logged_in		= $this->session->userdata('logged_in');
		$this->is_admin			= $this->session->userdata('site_admin');
		$this->display_name     = $this->session->userdata('display_name');
        
	}
	
	//Performs a conversion of US date to a date MySQL will understand
	function make_us_date_mysql_friendly($date_passed) 
	{
		$date_var = explode('/',$date_passed);
		//User may have passed a bad date.  Log error if so
		if (count($date_var) != 3)
		{
			log_message('error', 'Bad date passed ('.$date_passed.') by member_id:  '.$this->session->userdata('member_id'));
			return $date_passed;
		}
		$year	=	$date_var[2];
		$month	=	$date_var[0];
		$day	=	$date_var[1];
		if (!checkdate($month,$day,$year))
			return false;
		
		return $date_var[2].'-'.$date_var[0].'-'.$date_var[1];
	}
	
	function mysql_to_human($date_passed)
	{
		$datetime = strtotime($date_passed);
		return date("m/d/y", $datetime);	
	}
	
	/** 
	 * Takes an array_result and turns it in an array
	 * @access public 
	 * @return */
	function set_lookup($lookup_array,$field_name_key,$field_name_value,$add_empty_value = false)
	{
		$return_array = array();

		if ($add_empty_value)
			$return_array[''] = '';

		foreach ($lookup_array as $row)
			$return_array[$row[$field_name_key]] = $row[$field_name_value]; 

		return $return_array;
	}
	
	//Creates a  thumbnail and smaller image 
	function process_image($image_data = false, $destination_folder = '', $file_prefix = '', $height_max = 300, $width_max = 300)
	{
		if (!$image_data)
			return false;
		/*
		file_name => string(11) "bb_calc.jpg" 
		file_type => string(10) "image/jpeg" 
		file_path => string(59) "C:/Users/Ray/Documents/NetBeansProjects/wod-minder/uploads/" 
		full_path => string(70) "C:/Users/Ray/Documents/NetBeansProjects/wod-minder/uploads/bb_calc.jpg" 
		raw_name => string(7) "bb_calc" 
		orig_name => string(11) "bb_calc.jpg" 
		client_name => string(11) "bb_calc.jpg" 
		file_ext => string(4) ".jpg" 
		file_size => float(50.41) 
		is_image => bool(true) 
		image_width => int(321) 
		image_height => int(625) 
		image_type => string(4) "jpeg" 
		image_size_str => string(24) "width="321" height="625"" 
		 */
		
		$ret_val	=	'';
		
		$thumbnail_width_max = 100;

		// Create an Image from it so we can do the resize
		$image_target_path =  $_SERVER['DOCUMENT_ROOT'].$destination_folder;
		
		$batch_time_stamp = strval(time()).'_'.rand(); //append to the end of all new files created so that they don't overwrite existing files and can keep them toegether.
        
		$file_prefix            = $file_prefix === '' ? $this->session->userdata('member_id') : $file_prefix;
		$new_image_name         = $file_prefix.'_'.$batch_time_stamp.'.jpg';
		$new_image_thumb_name   = str_replace('.jpg', '_thumb.jpg', $new_image_name);

		
		$file_type	= str_replace('image/' , '',strtolower($image_data['file_type'])) ;
		switch ($file_type)
		{
			case 'jpeg':
			case 'jpg':
				$src	=	imagecreatefromjpeg($image_data['full_path']);
				break;
			case 'gif':
				$src	=	imagecreatefromgif($image_data['full_path']);
				break;
			case 'png':
				$src	=	imagecreatefrompng($image_data['full_path']);
				break;
			default:
				 log_message('error', 'Invalid image file uploaded:  ' + $image_data['full_path']);
				break;
		}
			

		// Capture the original size of the uploaded image
		list($width,$height)	=	getimagesize($image_data['full_path']);

		//this should not change based on the high values entered above.  
		//See if adjust height will still meet Width Max.  If not adjust image according to height instead of width
		$new_height	= ($width_max / $width) * $height;
		$no_change	= false;
		if ($width <= $width_max && $height <= $height_max)
		{
		  $no_change = true;
		  $new_width = $width;
		  $new_height = $height;
		}
		elseif ($new_height > $height_max)
		  $new_width = ($height_max / $height) * $width;
		else //Adjust image based on height
		  $new_width = $width_max;

		$tmp_is_src = false;
		//echo '<br><b>New Width:</b> $new_width<br>';
		if ($no_change == false)
		{
		  $new_height=($height/$width)*$new_width;
		  $tmp=imagecreatetruecolor($new_width,	$new_height);
		  // this line actually does the image resizing, copying from the original
		  // image into the $tmp image
		  //echo 'Copying image to smaller size...<br>';
		  imagecopyresampled($tmp,$src,0,0,0,0,$new_width,$new_height,$width,$height);
		}
		else
		{
		  //echo 'No change required for image size...<br>';
		  $tmp = $src;
		  $tmp_is_src = true;
		}

		// now write the resized image to disk. 
		//echo 'writing resized to:  $image_target_path.$new_image_name<br>';
		imagejpeg($tmp,$image_target_path.$new_image_name,100);

		//echo 'Creating image thumbnail...<br>';
		$new_width_thumb = $thumbnail_width_max;
		//echo '$new_height/$new_width)*$new_width_thumb<br>';
		$new_height_thumb =($new_height/$new_width)*$new_width_thumb;
		$tmp_thumb = imagecreatetruecolor($new_width_thumb,$new_height_thumb);
		imagecopyresampled($tmp_thumb,$tmp,0,0,0,0,$new_width_thumb, $new_height_thumb, $new_width, $new_height);
		//echo 'writing thumbnail to:  $image_target_path$new_image_thumb_name<br>';
		imagejpeg($tmp_thumb,$image_target_path.'/thumbnail/'.$new_image_thumb_name,100);

		//echo 'Removing raw photo...<br>';
		imagedestroy($src);
		if (!$tmp_is_src)
			imagedestroy($tmp); // NOTE: PHP will clean up the temp file it created when the request has completed.


		if (unlink($image_data['full_path'])==false)
		  echo 'Could not delete file:  '.$image_data['full_path'];

        $ret_val = $new_image_name;
        
		return $ret_val;
	}


}
    

/* End of file MY_Controller.php */
/* Location: ./application/core/MY_Controller.php */
