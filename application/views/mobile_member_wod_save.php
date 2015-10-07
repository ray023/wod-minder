<?php
$this->load->helper('form'); ?>
<div data-role="page">
	<div data-role="header">
		<a href="<?php echo base_url(); ?>index.php/welcome/index/TRUE" data-ajax ="false" data-icon="home" data-iconpos="notext" data-direction="reverse">Home</a>
		<h1><?php echo $title; ?></h1>
		<?php if (strlen($tier) > 0): ?>
			<h1><?php echo $tier; ?></h1>
		<?php endif; ?>
	</div>
	<div data-role="fieldcontain">	
		<div class ="error-messsage">
			<p><?php echo isset($error_message) ? $error_message : '' ;?></p>
		</div>
		<div data-role="collapsible" data-mini="true" data-theme="a">
			<h3 data-theme="d">WOD Details</h3>
			<?php if (strlen($buy_in) > 0): ?>
			<div data-role="header"><h3>Buy In</h3></div>
			<p>
				<?php echo $buy_in;?>
			</p>
			<?php endif; ?>

			<div data-role="header"><h3>Description</h3></div>
			<?php echo (strlen($wod_type) == 0) ? '' : '<p><strong>Type of WOD:</strong>  '.$wod_type.'</p>'; ?>
			<?php echo $simple_description;?>

			<?php if (strlen($cash_out) > 0): ?>
			<div data-role="header"><h3>Cash Out</h3></div>
			<p>
				<?php echo $cash_out;?>
			</p>
			<?php endif; ?>
		</div>
		<!--Box Rank-->
		<div data-role="collapsible" data-mini="true" data-theme="a">
			<h3>Scores</h3>
			<?php echo $box_wod_rank_grid; ?>
		</div>
				<?php	
				
					$select_label_attrib	=	array('class' => 'select');
					$field_contain_div	=	'<div data-role="fieldcontain">';
					$close_div	=	'</div>';
					$attributes		=	array(	'id'			=>	'_profileForm',
													'data-ajax'	=>	'false');
					echo form_open('wod/save_member_box_wod/'.$box_wod_id,	$attributes);
					
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
						
					//Note Div
					echo	$field_contain_div.
								form_label('Note:', '_note').
								form_textarea($note).
							$close_div;
					
					//Box Class Time
					echo	$field_contain_div.
								form_label('Class Time:', '_boxClassTime',$select_label_attrib).
								$box_class_time_dropdown.
							$close_div;
					
					//Member Rating Div
					echo	$field_contain_div.
								form_label('Member Rating:', '_memberRating',$select_label_attrib).
								$member_rating_dropdown.
							$close_div;
					
					//RX Div
                                        if (isset($rx))
                                        {
                                            echo	$field_contain_div.
                                                                    form_label('RX', '_rx').
                                                                    form_checkbox($rx).
                                                            $close_div;
                                        }
                                        else if (isset($scale_option_wod_dropdown))
                                        {
                                            echo	$field_contain_div.
								form_label('Scale:', '_scaleOptions',$select_label_attrib).
								$scale_option_wod_dropdown.
							$close_div;                                       
                                        }

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