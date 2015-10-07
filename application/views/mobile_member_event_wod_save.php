<?php
$this->load->helper('form'); ?>
<div data-role="page">
	<div data-role="header">
		<a href="<?php echo base_url().'index.php/member/save_event/'.$event_id; ?>" data-ajax ="false" data-icon="back" data-iconpos="notext" data-direction="reverse">Back</a>
		<h1><?php echo $title; ?></h1>
	</div>
	<div data-role="fieldcontain">	
		<div class ="error-messsage">
			<p><?php echo isset($error_message) ? $error_message : '' ;?></p>
		</div>
		<?php if (!(strlen($result_hyperlink) == 0	&&	strlen($event_wod_note) == 0  &&	strlen($simple_description) == 0 ))  : ?>
			<div data-role="collapsible" data-collapsed="true" data-theme="a">
				<h3 data-theme="d">WOD Details</h3>
				<?php echo !isset($simple_description)		||	($simple_description	=== '')	?	''	:	$simple_description	.	'<br>'; ?>
				<?php echo !isset($result_hyperlink)		||	($result_hyperlink		=== '')	?	''	:	'<a href="'													.	$result_hyperlink	.	'" target="_blank">Results</a><br>'; ?>
				<?php echo !isset($event_wod_note)			||	($event_wod_note		=== '')	?	''	:	$event_wod_note		.	'<br>'; ?>
			</div>
		<?php endif; ?>
		<?php	
				
			$select_label_attrib	=	array('class' => 'select');
			$field_contain_div	=	'<div data-role="fieldcontain">';
			$close_div	=	'</div>';
			$attributes		=	array(	'id'			=>	'_profileForm',
											'data-ajax'	=>	'false');
			echo form_open('event/save_member_event_wod/'.$ew_id,	$attributes);
					
			//ScoreType used in javascript for saving validation
			echo '<input type="hidden" name="score_type" id="_scoreType" value="'.$score_type.'" />';
			//Score Div
			if ($score_type	===	'T') //If Score Type is time, then give user minutes/seconds
			{
				echo $field_contain_div;
				echo '<fieldset class="ui-grid-a">';
				echo '<div class="ui-block-a">Minutes:</div>';
				echo '<div class="ui-block-b">Seconds:</div>';
				echo '<div class="ui-block-a">',form_input($score_minutes).'</div>';
				echo '<div class="ui-block-b">'.form_input($score_seconds).'</div>';
				echo '</fieldset>';
				echo '<input type="hidden" name="score" id="_score" value="'.$score.'" />';
				echo $close_div;
			}
			else //W, O, I
			{
				echo	$field_contain_div.
							form_label('Score:', '_score').
							form_input($score).
						$close_div;
			}
			
			//Remainder
			if	(isset($remainder_name) && $remainder_name != null)
			{
				echo	$field_contain_div.
					form_label($remainder_name, '_remainder').
					form_input($remainder).
				$close_div;
			}
			
			//Rank
			echo	$field_contain_div.
				form_label('Rank', '_rank').
				form_input($rank).
			$close_div;
						
			//Note Div
			echo	$field_contain_div.
						form_label('Note:', '_note').
						form_textarea($note).
					$close_div;

			//Member Rating Div
			echo	$field_contain_div.
						form_label('Member Rating:', '_memberRating',$select_label_attrib).
						$member_rating_dropdown.
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