<?php
/*
* Mobile Member Update:	Allows user to update all fields (except for e-mail and password)
 *						Don't have a good reason for this at the moment.
 *						TODO:  Merge create and update forms 
*/
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
				echo form_open('member/update',	$attributes);
				//User Login Div
				/*echo	$field_contain_div.
							form_label('User Login:', '_userLogin').
							form_input($user_login).
						$close_div;
				*/
				
				//Email div
				echo $field_contain_div.
							form_label('E-mail:', '_email').
							form_input($email).
						$close_div;
				
				//Last Name Div
				echo	$field_contain_div.
							form_label('Last Name:', '_lastName').
							form_input($last_name).
						$close_div;
				//First Name Div
				echo	$field_contain_div.
							form_label('First Name:', '_firstName').
							form_input($first_name).
						$close_div;
				//Gender Div
				echo	$field_contain_div.
							form_label('Gender:', '_gender',$select_label_attrib).
							$gender_dropdown.
						$close_div;
				//Birth Day Div
				echo	$field_contain_div.
							form_label('Birth Date:', '_birthDate').
							form_input($birth_date).
						$close_div;

				//Box Div
				echo	$field_contain_div.
							form_label('Box:', '_box',$select_label_attrib).
							$box_dropdown.
						$close_div;	
				
				//Is Competitor
				echo	$field_contain_div.
					form_label('Competitor', '_isCompetitor').
					form_checkbox($is_competitor).
				$close_div;

				//Buttons
				echo	$field_contain_div.
							anchor('welcome/index/TRUE', 'Cancel', array(	'data-ajax'=>'false',
																	'data-role'=>'button',
																	'data-inline'=>'true')).
							form_button($submit_profile).
						$close_div;


			echo form_close();?>
	</div>
</div>
