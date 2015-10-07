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
			$attributes		=	array(	'id'			=>	'_form',
											'data-ajax'	=>	'false');
			echo form_open('staff/save_staff_training_log/'.$id_value,	$attributes);

			//Training Date 
			echo	$field_contain_div.
						form_label('Training Date:', '_trainingDate').
						form_input($training_date).
					$close_div;
			
			//Class Time
			echo	$field_contain_div.
						form_label('Class Time:', '_classTime',$select_label_attrib).
						$class_time_dropdown.
					$close_div;

			//Class Size		
			echo	$field_contain_div.
						form_label('Class Size:', '_classSize').
						form_input($class_size).
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