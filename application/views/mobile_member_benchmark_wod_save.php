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
		<div data-role="collapsible" data-mini="true" data-theme="a">
			<h3 data-theme="d">WOD Details</h3>
			<div data-role="header"><h1>Description</h1></div>
			<?php echo (strlen($wod_type) == 0) ? '' : '<p><strong>Type of WOD:</strong>  '.$wod_type.'</p>'; ?>
			<?php echo $description;?>
		</div>
				<?php	
				
					$select_label_attrib	=	array('class' => 'select');
					$field_contain_div	=	'<div data-role="fieldcontain">';
					$close_div	=	'</div>';
					$attributes		=	array(	'id'			=>	'_profileForm',
													'data-ajax'	=>	'false');
					echo form_open('wod/save_member_benchmark_wod/'.$id_type.'/'.$id_value,	$attributes);
					
					//WOD Date Div
					echo	$field_contain_div.
								form_label('WOD Date:', '_wodDate').
								form_input($wod_date).
							$close_div;
					
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
					
					//RX Div
					echo	$field_contain_div.
								form_label('RX', '_rx').
								form_checkbox($rx).
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