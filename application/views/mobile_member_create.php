<?php 
/*
* Mobile Member Create:	Called from the Login Screen if user does not have account
 *						Only contains required fields to get started.
 *						User can use member update form to update the rest of the fields
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
			
			$attributes		=	array(	'id'		=>	'_createMemberForm',
										'data-ajax'	=>	'false');
			echo form_open('member/create_new', $attributes);
			/*
			//User Login Div
			echo	$field_contain_div.
						form_label('User Login*:', '_userLogin').
						form_input($login_input).
					$close_div;
			*/
			
			//Email div
			echo $field_contain_div.
						form_label('E-mail*:', '_email').
						form_input($email).
					$close_div;
			
			//Last Name Div
			echo	$field_contain_div.
						form_label('Last Name:', '_lastName').
						form_input($last_name).
					$close_div;
			//First Name Div
			echo	$field_contain_div.
						form_label('First Name*:', '_firstName').
						form_input($first_name).
					$close_div;
			
			//Password Box (NOTE: Since login will be on a mobile device; do not hide password)
			echo $field_contain_div.
						form_label('Password*:', '_password').
						form_input($password_input).
					$close_div;
			
			//Birth Day Div
			echo	$field_contain_div.
						form_label('Birthday* (mm/dd/yy):', '_birthDate').
						form_input($birthdate_input).
					$close_div;

			//Gender Div
			echo	$field_contain_div.
						form_label('Gender*:', '_gender',$select_label_attrib).
						$gender_dropdown.
					$close_div;
			
			//Box Div
			echo	$field_contain_div.
						form_label('Box*:<a href="#boxHelp" data-rel="popup" >?</a>', '_box',$select_label_attrib).
						$box_dropdown.
					$close_div;
			
			//Other Box Div
			echo	$field_contain_div.
					   '<label id="obLabel" for="_otherBox" class="ui-hidden-accessible">Other Box:</label>'.
						form_input($other_box).
					$close_div;

			//Is Competitor
			echo	$field_contain_div.
				form_label('I compete in events', '_isCompetitor').
				form_checkbox($is_competitor).
			$close_div;

			echo form_submit($submit);
			echo form_close(); 
			echo '(*) <i>Required field</i>';
		?>		
<div id="boxHelp" data-role="popup" data-overlay-theme="a" data-theme="e" class="ui-content">
	<p>Select the CrossFit facility where you are a member.</p>
	<p>If your facility is not listed, select <b>Other</b> and then enter the Box where you are a member.</p>
	<p>If you are not a member of a facility, select <b>N/A</b>.  You will still be able to save your maxes and WODs.</p>
</div>		
	</div>
</div>

