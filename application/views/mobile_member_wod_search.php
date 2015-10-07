<?php
$this->load->helper('form'); ?>
<div data-role="page">
	<div data-role="header">
		<a href="<?php echo base_url(); ?>index.php/welcome/index/TRUE" data-ajax ="false" data-icon="home" data-iconpos="notext" data-direction="reverse">Home</a>
		<h1><?php echo $title; ?></h1>
	</div>
	<div data-role="fieldcontain">	
		<div class ="error-messsage">
			<p><?php echo isset($error_message) ? $error_message : '' ;?></p>
		</div>
		<?php	

			$field_contain_div	=	'<div data-role="fieldcontain">';
			$close_div	=	'</div>';
			$attributes		=	array(		'id'		=>	'_searchForm',
											'data-ajax'	=>	'false');
			echo form_open('wod/search/',	$attributes);

			echo	$field_contain_div.
						form_input($search_criteria).
					$close_div;

			//Buttons
			echo	$field_contain_div.
						form_button($submit).
					$close_div;


			echo form_close();?>
	</div>
	<?php if (strlen($wod_list) > 0): ?>
	
		<div class="ui-bar ui-bar-a">
			Search Results
		</div>
		<?php echo $wod_list;?>
	<?php endif; ?>
</div>