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
			echo form_open('administration_functions/save_benchmark_wod/'.$id_value,	$attributes);

			//Title	
			echo	$field_contain_div.
						form_label('WOD:', '_wodName').
						form_input($wod_name).
					$close_div;
			
			//WOD Category
			echo	$field_contain_div.
						form_label('WOD Category:', '_wodCategory',$select_label_attrib).
						$wod_category_dropdown.
					$close_div;
			
			//Score Type Div
			echo	$field_contain_div.
						form_label('Score Type:', '_scoreType',$select_label_attrib).
						$score_type_dropdown.
					$close_div;
			
			//Description Div
			echo	$field_contain_div.
						form_label('Description:', '_description').
						form_textarea($description).
					$close_div;
			
			//Note Div
			echo	$field_contain_div.
						form_label('Note:', '_note').
						form_textarea($note).
					$close_div;
                        
			//Image Name
			echo	$field_contain_div.
						form_label('Image Name:', '_imageName').
						form_input($image_name).
					$close_div;
                        echo    '<img src="'.$image_name['value'].'"/>';

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