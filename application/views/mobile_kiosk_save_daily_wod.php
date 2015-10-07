<?php
$this->load->helper('form'); ?>
<div data-role="page" id="Main">
	<div data-role="header">
		<a href= "<?php echo base_url()?>index.php/kiosk/pick_box" data-icon="back" data-iconpos="notext" data-ajax="false">Pick Box</a>
		<h1><?php  echo $box_name ?></h1>
	</div><!-- /header -->
	<div data-role="header">
		<h1><?php echo date("D M j Y"); ?></h1>
	</div>
	<?php if (!isset($form_data_array) || count($form_data_array) == 1) :?>
		<div data-role="header">
			<h1><?php echo $simple_title;?></h1>
		</div>
	<?php endif; ?>
	<div data-role="fieldcontain" class="ui-hide-label">	
		<div class ="good-messsage">
			<p><?php echo isset($good_message) ? $good_message : '' ;?></p>
		</div>
		<div class ="error-messsage">
			<p><?php echo isset($error_message) ? $error_message : '' ;?></p>
		</div>
	</div>
	<div data-role="content">		
		<div class="content-primary">	
				<?php	
					//Only create form if a daily wod is present
					if (isset($form_data_array))
					{
						//Set some default valuess
						$field_contain_div	=	'<div data-role="fieldcontain">';
						$close_div	=	'</div>';
						$select_label_attrib	=	array('class' => 'select');
						$attributes		=	array(	'data-ajax'	=>	'false');
						
						
						$user_login_attr = array(
														'id'			=>	'_userLogin',
														'autocomplete'	=>	'off',
														'value'			=>	'',
													);

						$user_password_attr = array(
														'id'			=>	'_userPassword',
														'autocomplete'	=>	'off',
														'value'			=>	'',
													);
						
						echo	$field_contain_div.
									form_label('User Login:', '_userLogin').
									form_input($user_login_attr).
								$close_div;

						echo	$field_contain_div.
									form_label('Password:', '_userPassword').
									form_password($user_password_attr).
								$close_div;
		
						$form_count = count($form_data_array);
						if ($form_count > 1)
						{
							
							echo '<div data-role="fieldcontain">';
							echo '<fieldset data-role="controlgroup">';
							echo '<legend>Choose a WOD:</legend>';
							foreach ($form_data_array as $form_data) {
								echo '<input type="radio" name="radio-choicec-1" class="tier-button" id="radio-choice-'.$form_data['bw_id'].'" value="choice-'.$form_data['bw_id'].'" />';
								echo '<label for="radio-choice-'.$form_data['bw_id'].'">'.$form_data['tier_name'].' - '.$form_data['simple_title'].'</label>';
							}
							echo '</fieldset>';
							echo '</div>';
						}
						
						$counter = 0;
						foreach ($form_data_array as $form_data) {
							
									   
							$bw_id					=	$form_data['bw_id'];
							$score_type				=	$form_data['score_type'];
							$score					=	$form_data['score'];
							$rx						=	$form_data['rx'];
							$member_rating_dropdown	=	str_replace('_memberRating','_memberRating-'.$counter,$form_data['member_rating_dropdown']);
							
							if (isset($score['id']))
								$score['id']	= $score['id'].'-'.$counter;
							
							$rx['id']		= $rx['id'].'-'.$counter;
							
							echo '<div class="kiosk-form" id="_formId'.$bw_id.'">';
							
							echo form_open('kiosk/save_daily_wod/',	$attributes);


							echo '<input type="hidden" name="bw_id" value="'.$bw_id.'" />';
							echo '<input type="hidden" name="score_type" class="score-type" value="'.$score_type.'" />';
							echo '<input type="hidden" name="user_login" class="hidden-login" value="" />';
							echo '<input type="hidden" name="user_password" class="hidden-password" value="" />';


							//Score Div
							if ($score_type	===	'T') //If Score Type is time, then give user minutes/seconds
							{
								echo $field_contain_div;
								echo '<fieldset class="ui-grid-a">';
								echo '<div class="ui-block-a">Minutes:</div>';
								echo '<div class="ui-block-b">Seconds:</div>';
								echo '<div class="ui-block-a">',form_input($form_data['score_minutes']).'</div>';
								echo '<div class="ui-block-b">'.form_input($form_data['score_seconds']).'</div>';
								echo '</fieldset>';
								echo '<input type="hidden" class="score" name="score" id="_score'.'-'.$counter.'" value="'.$score.'" />';
								echo $close_div;
							}
							else //W, O, I
							{
								echo	$field_contain_div.
											form_label('Score:', '_score'.'-'.$counter).
											form_input($score).
										$close_div;
							}

							//RX Div
							echo	$field_contain_div.
										form_label('RX', '_rx'.'-'.$counter).
										form_checkbox($rx).
									$close_div;
							
							//Member Rating Div
							echo	$field_contain_div.
										form_label('Member Rating:', '_memberRating'.'-'.$counter,$select_label_attrib).
										$member_rating_dropdown.
									$close_div;

							//Button
							echo	$field_contain_div.
										form_button($form_data['submit']).
									$close_div;


							echo form_close();
							
							echo '</div>';
							
							$counter++;
						}
					}
					?>			
		</div>
	</div>
</div><!--Page Main-->