<div data-role="page" id="AdminEventWodSavePage">
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
		echo form_open('event/save_event_wod/'.$ew_id,	$attributes);
		
		echo $field_contain_div.
			form_label('Event:', '_eventName').
		   $event_name_dropdown.
		   $close_div;
		
		//WOD Name
		echo	$field_contain_div.
					form_label('WOD Name:', '_wodName').
					form_input($wod_name).
				$close_div;
		
		//WOD Date
		echo	$field_contain_div.
					form_label('WOD Date:', '_wodDate').
					form_input($wod_date).
				$close_div;
		
		//WOD
		echo	$field_contain_div.
					form_label('WOD:', '_simpleDescription').
					form_textarea($simple_description).
				$close_div;
		
		//Benchmark Wod (if applicable)
		echo	$field_contain_div.
					form_label('Benchmark WOD (if applicable):', '_benchmarkWod',$select_label_attrib).
					$benchmark_wod_dropdown.
				$close_div;
		
		//Score Type Div
		echo	$field_contain_div.
					form_label('Score Type:', '_scoreType',$select_label_attrib).
					$score_type_dropdown.
				$close_div;
		
		//Remainder Name Div
		echo	$field_contain_div.
					form_label('Remainder Name:', '_remainderName').
					form_input($remainder_name).
				$close_div;
		
		//Result Hyperlink Div
		echo	$field_contain_div.
					form_label('Result Hyperlink:', '_resultHyperlink').
					form_input($result_hyperlink).
				$close_div;
		

		//Note Div
		echo	$field_contain_div.
					form_label('Note:', '_note').
					form_textarea($note).
				$close_div;
		
		//team_wod
		echo	$field_contain_div.
					form_label('Team Wod', '_teamWod').
					form_checkbox($team_wod).
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