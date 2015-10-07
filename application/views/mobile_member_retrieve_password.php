<?php $this->load->helper('form'); ?>
<div data-role="page" id="Main">
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
		<?php 
			$attributes		=	array(	'id'		=>	'_retrievePasswordForm',
										'data-ajax'	=>	'false');
			echo form_open('member/retrieve_password', $attributes);
			echo form_input($user_name_or_email);
			echo br(2);
			echo form_submit($submit);
			echo form_close(); 
		?>
		
	</div>
	<div data-role="fieldcontain">	
		<?php echo anchor('#CompletelyForgottenLogin', 'I don\'t remember my login or email');?>	
	</div>
</div>

<div data-role="page" id="CompletelyForgottenLogin">
	<div data-role="header">
		<a href="#Main" data-icon="back" data-iconpos="notext" data-direction="reverse">Go back</a>
		<h1><?php echo 'User Recovery'; ?></h1>
	</div>
	<div data-role="fieldcontain" class="ui-hide-label">
		Enter your name:
		<?php 

			$attributes		=	array(	'id'		=>	'_completelyforgottenLoginForm',
										'data-ajax'	=>	'false');
			echo form_open('member/completely_forgotten_login', $attributes);
			echo form_input($user_identifier);
			echo br(2);
			echo form_submit($submit_identifier);
			echo form_close();  
		?>
		
	</div>
</div>