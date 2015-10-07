<?php 
$this->load->helper('form'); ?>
<div data-role="page" id="Main">
	<div data-role="header">
		<h1><?php echo $title; ?></h1>
	</div>
		<div class="content-primary">	
			<ul data-role="listview" data-filter="true" data-filter-placeholder="Search members..." data-filter-theme="d"data-theme="d" data-divider-theme="d">
				<?php echo $member_list;?>
			</ul>
		</div><!--/content-primary -->	
</div>


<div data-role="page" id="SaveMemberStaff">

	<div data-role="header">
		<h1>Save Member Staff</h1>
	</div><!-- /header -->

	<div data-role="content">	
	<div data-role="fieldcontain">	
		<div class ="error-messsage">
			<p><?php echo isset($error_message) ? $error_message : '' ;?></p>
		</div>
		<?php 
			$select_label_attrib	=	array('class' => 'select');
			$field_contain_div	=	'<div data-role="fieldcontain">';
			$close_div	=	'</div>';
			
			$attributes		=	array(	'id'		=>	'_setMemberStaffForm',
										'data-ajax'	=>	'false');
			echo form_open('administration_functions/set_member_staff', $attributes);
			
			echo '<input type="hidden" id="_memberId" value="set on initialize" name="member_id">';
			
			//Member Name
			echo	$field_contain_div.
						form_label('Member:', '_memberName').
						form_input($member_name_input).
					$close_div;			
			
			//Box Div
			echo	$field_contain_div.
						form_label('Box:', '_box',$select_label_attrib).
						$box_dropdown.
					$close_div;			
			
			//Buttons
			echo	$field_contain_div.
						anchor('welcome/index/TRUE', 'Cancel', array(	'data-ajax'=>'false',
																'data-role'=>'button',
																'data-inline'=>'true')).
						form_submit($submit).
					$close_div;
			
			echo form_close(); 
		?>		
	</div>		
	</div><!-- content -->
</div><!-- /page -->
