<?php
$this->load->helper('form'); ?>
<div data-role="page">
	<div data-role="header">
		<a href="<?php echo base_url(); ?>index.php/welcome/index/TRUE" data-ajax ="false" data-icon="home" data-iconpos="notext" data-direction="reverse">Home</a>
		<h1><?php echo $title; ?></h1>
	</div>
	<div data-role="fieldcontain" class="ui-hide-label">	
		<div class ="good-messsage">
			<p><?php echo isset($good_message) ? $good_message : '' ;?></p>
		</div>
		<div class ="error-messsage">
			<p><?php echo isset($error_message) ? $error_message : '' ;?></p>
		</div>
	</div>
	<div data-role="fieldcontain">		
		<?php	

			$select_label_attrib	=	array('class' => 'select');
			$field_contain_div	=	'<div data-role="fieldcontain">';
			$close_div	=	'</div>';
			$attributes		=	array(	'id'			=>	'_profileForm',
											'data-ajax'	=>	'false');
			echo  form_open_multipart('weight/save_member_weight/'.$id_value,	$attributes);

			//Weight Date Div
			echo	$field_contain_div.
						form_label('Date:', '_weightDate').
						form_input($weight_date).
					$close_div;

			//Weight
			echo	$field_contain_div.
						form_label('Weight:', '_weight').
						form_input($weight).
					$close_div;
			
			//bmi
			echo	$field_contain_div.
						form_label('BMI:', '_bmi').
						form_input($bmi).
					$close_div;
			
			//body_fat_percentage
			echo	$field_contain_div.
						form_label('Body Fat %:', '_bodyFatPercentage').
						form_input($body_fat_percentage).
					$close_div;

			//Note Div
			echo	$field_contain_div.
						form_label('Note:', '_note').
						form_textarea($note).
					$close_div;

			$full_image_name	= base_url().'user_images/weight/'.($image_name['value'] === '' ? 'no_image_filler.jpg' : $image_name['value']);
			echo '<img id="_weightImage" src="'.$full_image_name.'" />';

			echo	$field_contain_div.
					form_label('Image:', '_image').
					'<input type="file" id ="_image" name="userfile" />'.
					$close_div;

			//Buttons
			echo	$field_contain_div.
						anchor('welcome/index/TRUE', 'Cancel', array(	'id'=>'_cancel',
																		'data-ajax'=>'false',
																		'data-role'=>'button',
																		'data-inline'=>'true')).
						form_button($submit).
					$close_div;


			echo form_close();?>
	</div>
</div>
