<?php $this->load->helper('form'); ?>
<div data-role="page">
	<div data-role="header">
		<h1><?php echo $exercise_name; ?></h1>
	</div>
	<div data-role="fieldcontain">	
		<div class ="good-messsage">
			<p><?php echo isset($good_message) ? $good_message : '' ;?></p>
		</div>
		<div class ="error-messsage">
			<p><?php echo isset($error_message) ? $error_message : '' ;?></p>
		</div>
		<!--Max Stats-->
		<div data-role="collapsible" data-mini="true" data-theme="a">
			<?php if ($previous_max_grid !== ''): ?>
				<h2>Max Stats</h2>
					<div class="ui-grid-b">
						<?php if ($max_title	!=	'Weight'):	?>
							<div class="ui-block-a mobile-grid-header">Title</div>
						<?php else : ?>
							<div class="ui-block-a mobile-grid-header">Max Rep</div>
						<?php endif;	?>
							<div class="ui-block-b mobile-grid-header date-block">Date</div>
							<div class="ui-block-c mobile-grid-header number-block"><?php echo $max_title;?></div>
						<?php echo $previous_max_grid;?>
					</div><!-- /grid-b -->
			<?php else: ?>
				<p>No max saved for this exercise.</p>
			<?php endif; ?>
		</div>
		<!--Box Rank-->
		<div data-role="collapsible" data-mini="true" data-theme="a">
			<h3>Box Rank</h3>
			<p>If you've saved a max for this exercise, your rank will be <span class="self-row">highlighted in green</span>.</p>
			<?php echo $max_rank_grid; ?>
		</div>
		<?php 
			$attributes		=	array(	'id'		=>	'_loginForm',
										'data-ajax'	=>	'false');
			echo form_open('exercise/save_member_max/'.$id_type.'/'.$id_value, $attributes);
			echo form_hidden('exercise_id',$exercise_id);
			echo form_label('Lift Date', '_liftDate');
			echo form_input($max_date);
			if ($max_type	===	'T') //If Max Type is time, then give user minutes/seconds
			{
				echo '<fieldset class="ui-grid-a">';
				echo '<div class="ui-block-a">Minutes:</div>';
				echo '<div class="ui-block-b">Seconds:</div>';
				echo '<div class="ui-block-a">',form_input($max_minutes).'</div>';
				echo '<div class="ui-block-b">'.form_input($max_seconds).'</div>';
				echo '</fieldset>';
				echo '<input type="hidden" name="max_value" id="_maxValue" value="'.$max_value.'" />';
			}
			elseif ($max_type	===	'W')
			{
				echo form_label('Maxium Number of Reps:', '_maxRep');
				echo form_input($max_rep);
				echo form_label('My Max:', '_maxValue');
				echo form_input($max_value);
			}
			else //'R'
			{
				echo form_label('My Max:', '_maxValue');
				echo form_input($max_value);				
			}
			
			echo anchor('welcome/index/TRUE', 'Cancel', array(	'data-ajax'	=>	'false',
													'data-role'=>'button',
													'data-inline'=>'true'));
			echo form_submit($submit);
			echo form_close(); 
			echo br(2);
		?>		
	</div>
</div>
