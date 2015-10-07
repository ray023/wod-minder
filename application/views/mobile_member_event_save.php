<div data-role="page" id="MemberEventSavePage">
	<div data-role="header">
		<a href="<?php echo base_url(); ?>index.php/welcome/index/TRUE" data-ajax ="false" data-icon="home" data-iconpos="notext" data-direction="reverse">Home</a>
		<h1><?php echo $event_name; ?></h1>
	</div>	
	<div data-role="fieldcontain" class="ui-hide-label">	
		<div class ="good-messsage">
			<p><?php echo isset($good_message) ? $good_message : ''; ?></p>
		</div>
		<div class ="error-messsage">
			<p><?php echo isset($error_message) ? $error_message : ''; ?></p>
		</div>
	</div>
	<div data-role="fieldcontain" class="ui-hide-label">	
		<div class="ui-grid-a">
			<div class="ui-block-a"><strong>Host:</strong></div>
			<div class="ui-block-b"><?php echo $hosting_entity; ?></div>
			<div class="ui-block-a"><strong>Event Date:</strong></div>
			<div class="ui-block-b"><?php echo $start_date; ?></div>			
		</div><!-- /grid-a -->
	</div>
	<?php if (!(strlen($event_main_hyperlink) == 0 && strlen($result_hyperlink) == 0 && strlen($result_hyperlink) == 0 &&   strlen($facebook_page) == 0  &&   strlen($twitter_account) == 0	&&	strlen($event_note) == 0 ))  : ?>
		<div data-role="collapsible" data-collapsed="true" data-theme="a">
			<h2>Event Info</h2>
			<?php echo !isset($event_main_hyperlink)	||	($event_main_hyperlink === '')	?	''	:	'<a  href="'		.	$event_main_hyperlink	.	'" target="_blank"  data-ajax="false" >Event Home Page</a><br>'; ?>
			<?php echo !isset($result_hyperlink)		||	($result_hyperlink=== '')		?	''	:	'<a href="'			.	$result_hyperlink		.	'" target="_blank">Results</a><br>'; ?>
			<?php echo !isset($facebook_page)			||	($facebook_page === '')			?	''	:	'<a href="'			.	$facebook_page			.	'" target="_blank">Facebook Page</a><br>'; ?>
			<?php echo !isset($twitter_account)			||	($twitter_account === '')		?	''	:	'<a href="'			.	$twitter_account		.	'" target="_blank">Twitter</a><br>'; ?>
			<?php echo !isset($event_note)				||	($event_note === '')			?	''	:	'<h3>About</h3><p>'	.	$event_note				.	'</p>'; ?>
		</div>
	<?php endif; ?>
	<div data-role="collapsible" data-collapsed="false"  data-theme="a">
		<h3>My Event Info</h3>
		<div data-role="fieldcontain">	
			<?php
			$select_label_attrib = array('class' => 'select');
			$field_contain_div = '<div data-role="fieldcontain">';
			$close_div = '</div>';
			$attributes = array('id' => '_profileForm',
				'data-ajax' => 'false');

			echo form_open('member/save_event/'.$event_id,	$attributes);

			//Event Scale Option Div
			echo	$field_contain_div.
				form_label('Scale:', '_eventScaleOptions',$select_label_attrib).
				$event_scale_option_dropdown.
				$close_div;		

			//Rank Div
			echo	$field_contain_div.
						form_label('Overall Rank:', '_rank').
						form_input($rank).
					$close_div;

			//Number of competitors
			echo	$field_contain_div.
						form_label('Number of Competitors:', '_numberOfCompetitors').
						form_input($number_of_competitors).
					$close_div;

			if (isset($teammates))
			{
				echo	$field_contain_div.
						form_label('Team Name:', '_teamName').
						form_input($team_name).
					$close_div;

				echo	$field_contain_div.
						form_label('Teammates:', '_teammates').
						form_input($teammates).
					$close_div;
			}

			//Note Div
			echo	$field_contain_div.
						form_label('Note:', '_note').
						form_textarea($note).
					$close_div;


			//Buttons
			echo	$field_contain_div.
						form_button($submit).
					$close_div;


			echo form_close();
			?>
		</div>
	</div>
	<div data-role="header">
		<h1>Event WODs</h1>
	</div>
	<div id="_wodList" data-role="fieldcontain">	
		<ul data-role="listview" data-filter="false" data-theme="d" data-divider-theme="d">
		<?php echo $event_wod_list;?>
		</ul>
	</div>
</div>