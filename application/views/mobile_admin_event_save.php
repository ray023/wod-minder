<div data-role="page" id="AdminEventSavePage">
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
	</div>
	<div data-role="fieldcontain">	
	<?php	

		$select_label_attrib	=	array('class' => 'select');
		$field_contain_div	=	'<div data-role="fieldcontain">';
		$close_div	=	'</div>';
		$attributes		=	array(	'id'			=>	'_profileForm',
									'data-ajax'	=>	'false');
		echo form_open('event/save_event/'.$event_id,	$attributes);
		
		//Event Name
		echo	$field_contain_div.
					form_label('Event Name:', '_eventName').
					form_input($event_name).
				$close_div;
		
		
		echo	$field_contain_div.
				'<fieldset data-role="controlgroup">
				<legend>Hosting Entity:</legend>
					<input type="radio" name="entity-source" id="radio-choice-21" value="box" checked="checked" />
					<label for="radio-choice-21">Pick a box</label>
					<input type="radio" name="entity-source" id="radio-choice-22" value="non-box"  />
					<label for="radio-choice-22">Non-box entity</label>
				</fieldset>'.
				$close_div;
		
		
		echo	'<div id="_hostBoxDiv">';
		echo $field_contain_div.
			form_label('Hosting Box:', '_hostBox').
		   $hosting_box_dropdown.
		   $close_div;
		echo	'</div>';
		
		echo	'<div id="_hostingEntity">';
		echo $field_contain_div.
			form_label('Hosting Entity:', '_hostingEntity').
		   '<input id ="_hostingEntity" name="host_name" />'.
		   $close_div;
		echo	'</div>';
        
		//Start Date
		echo	$field_contain_div.
					form_label('Start Date:', '_startDate').
					form_input($start_date).
				$close_div;
		
		//Duration 
		echo	$field_contain_div.
					form_label('Duration (in days):', '_duration').
					form_input($duration).
				$close_div;
		
		//Event Hyperlink
		echo	$field_contain_div.
					form_label('Event Hyperlink:', '_eventMainHyperlink').
					form_input($event_main_hyperlink).
				$close_div;
		
		//Result Hyperlink Div
		echo	$field_contain_div.
					form_label('Result Hyperlink:', '_resultHyperlink').
					form_input($result_hyperlink).
				$close_div;
		
		//Facebook Page
		echo	$field_contain_div.
					form_label('Facebook Page:', '_facebookPage').
					form_input($facebook_page).
				$close_div;
		
		//twitter_account
		echo	$field_contain_div.
					form_label('Twitter:', '_twitterAccount').
					form_input($twitter_account).
				$close_div;
		
		//Scale Div
		echo	$field_contain_div.
					form_label('Scale:', '_eventScale',$select_label_attrib).
					$event_scale_dropdown.
				$close_div;
		
		//Note Div
		echo	$field_contain_div.
					form_label('Note:', '_note').
					form_textarea($note).
				$close_div;
		
		//Is Team Event Div
		echo	$field_contain_div.
					form_label('Team Event', '_isTeamEvent').
					form_checkbox($is_team_event).
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