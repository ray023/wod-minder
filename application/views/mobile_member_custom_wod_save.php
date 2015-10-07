<?php
$this->load->helper('form'); ?>
<div data-role="page">
	<div data-role="header">
		<h1><?php echo $title; ?></h1>
	</div>
	<div data-role="fieldcontain">	
		<div class ="error-messsage">
			<p><?php echo isset($error_message) ? $error_message : '' ;?></p>
		</div>
		<?php	

			$select_label_attrib	=	array('class' => 'select');
			$field_contain_div	=	'<div data-role="fieldcontain">';
			$close_div	=	'</div>';
			$attributes		=	array(	'id'			=>	'_profileForm',
											'data-ajax'	=>	'false');
			echo form_open('wod/save_member_custom_wod/'.$id_value,	$attributes);

			//WOD Date Div
			echo	$field_contain_div.
						form_label('WOD Date:', '_wodDate').
						form_input($wod_date).
					$close_div;

			//Custom Title		
			echo	$field_contain_div.
						form_label('Title:', '_customTitle').
						form_input($custom_title).
					$close_div;

			//Score Div					
			echo	$field_contain_div.
						form_label('Score:', '_score').
						form_input($score).
					$close_div;

			//Note Div
			echo	$field_contain_div.
						form_label('Note:', '_note').
						form_textarea($note).
					$close_div;

					//Buttons
			echo	$field_contain_div.
						anchor('welcome/index/TRUE', 'Cancel', array(	'data-ajax'=>'false',
																'data-role'=>'button',
																'data-inline'=>'true')).
						form_button($submit).
					$close_div;

			echo form_close();?>
	</div>
</div>