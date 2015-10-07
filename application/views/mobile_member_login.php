<?php $this->load->helper('form'); ?>
<div data-role="page">
	<div data-role="header">
		<h1><?php echo $title; ?></h1>
	</div>
	<div data-role="fieldcontain" class="ui-hide-label">	
		<div class ="good-messsage">
			<p><?php echo isset($good_message) ? $good_message : '' ;?></p>
		</div>
		<div class ="error-messsage">
			<p><?php echo isset($error_message) ? $error_message : '' ;?></p>
		</div>
		<?php 
			$field_contain_div	=	'<div data-role="fieldcontain">';
			$close_div	=	'</div>';
			
			$attributes		=	array(	'id'			=>	'_loginForm',
										'data-ajax'	=>	'false');
			echo form_open('member/login', $attributes);
			//User Login
			echo $field_contain_div.
						form_label('User Login', '_userLogin').
						form_input($login_input).
					$close_div;			
			//Password Box (NOTE: Since login will be on a mobile device; do not hide password)
			echo $field_contain_div.
						form_label('Password', '_password').
						form_password($password_input).
					$close_div;
			
			echo form_submit($submit);
			echo form_close(); 
			echo	$field_contain_div.
						anchor('member/create_new', 'Create account', array('data-ajax'=>'false')).
					$close_div;
			echo	$field_contain_div.
					anchor('member/forgot_password', 'Retrieve Password', array('data-ajax'=>'false')).
					$close_div;
			echo	$field_contain_div.
					'Having trouble?  <a data-ajax="false" href= "'.base_url().'support.php" >Click here</a>'.
					$close_div;
			/*
			echo	$field_contain_div.
					'Own a CrossFit facility?<br>'.str_replace('/index.php','',anchor('join/', 'Click here to find out how WOD-Minder can help you.', array('data-ajax'=>'false'))).
					$close_div;
			*/
		?>
		
	</div>
</div>
