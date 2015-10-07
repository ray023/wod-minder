<script type="text/javascript">
	$( '#PickFacilityPage' ).live( 'pageinit',function(){
		$('.facility-link').click( function(){
			var boxId	=	$(this).attr('id').replace('box_id_','');
			$('#_box').val(boxId);
		});
	});	
	$( '#PickDayPage' ).live( 'pageinit',function(){
		$('.day-link').click( function(){
			var selectedDate	=	$(this).text().substr($(this).text().lastIndexOf('-') + 1).trim();
			if(isValidDate(selectedDate))
				$('#_wodDate').val(selectedDate);
			else
				$('#_wodDate').val('');
		});
	});
	$( '#IsBenchmarkWodPage' ).live( 'pageinit',function(){
		$('.is-benchmark-link').click( function(){
			var isBenchmarkLink	=	$(this).text();
			if(isBenchmarkLink == 'No')
				$('#_benchmarkWod').val('');
		});
	});
	$( '#PickBenchmarkWodPage' ).live( 'pageinit',function(){
		$('.benchmark-wod-link').click( function(){
			var benchmarkWodId	=	$(this).attr('id').replace('wod_id_','');
			var wod				=	document.getElementById('wod_data_' + benchmarkWodId);
			var wodTitle		=	wod.getAttribute('data-wod-title');
			var wodDescription	=	wod.getAttribute('data-wod-description');
			var scoreType		=	wod.getAttribute('data-score-type');

			$('#_benchmarkWod').val(benchmarkWodId);
			$('#_scoreType').val(scoreType);
			$('#_simpleTitle').val(wodTitle);
			$('#_simpleDescription').val(wodDescription);
			
		});
	});
	$( '#PickScoretypePage' ).live( 'pageinit',function(){
		$('.score-type-link').click( function(){
			var scoreTypeId	=	$(this).attr('id').replace('score_type_','');
			$('#_scoreType').val(scoreTypeId);
		});
	});
	$( '#PickScalePage' ).live( 'pageinit',function(){
		$('.scale-link').click( function(){
			var scaleId	=	$(this).attr('id').replace('scale_id_','');
			$('#_scale').val(scaleId);
		});
	});
        $( '#BuyInPage' ).live( 'pageinit',function(){
		$('#_buyInButton').click( function(){
			var textValue = $('#_wizardBuyIn').val();
			$('#_buyIn').val(textValue);
		});
	});
	$( '#WodPage' ).live( 'pageinit',function(){
		$('#_wodButton').click( function(){
			var textValue = $('#_wizardWodName').val();
			$('#_simpleTitle').val(textValue);
			textValue = $('#_wizardWodDescription').val();
			$('#_simpleDescription').val(textValue);
		});
	});	
	$( '#CashoutPage' ).live( 'pageinit',function(){
		$('#_cashoutButton').click( function(){
			var textValue = $('#_wizardCashout').val();
			$('#_cashOut').val(textValue);
		});
	});

	
</script>
<?php
$this->load->helper('form'); ?>
	<?php if ($use_wizard): 
		/*If using wizard, WOD will go like this:
		 * pick facility (admin only)
		 *	ALL STAFF BUT NOT ADMIN ON SOCIAL MEDIA POST:
		 *		if user not logged in to facebook and twitter, prompt them to do that here
		 *		if box's fb page id and twitter id are not saved, warn them here
		 * Pick day (tomorrow - Tuesday, January 7th, 2013, today - Monday, January 6th, 2013
		 * Answer question:  Benchmark WOD?
		 *	If so, pick benchmark wod (and store that data in a var to populate later..
		 *	If not, get name of wod
		 * Pick Score Type from predefined list (will already be set if user selected benchmark wod)
		 * Enter Buy-in
		 * Enter WOD (will be predefined if user selected benchmark wod
		 *	(will need to implement some basic markdown here...brs at the very least)
		 *	add a box that will "tweetify" the wod.  use this to tweet the wod
		 * Enter Cash Out
		 * 
		 */
	?>
	<?php echo $hidden_wod_info; ?>
	<div data-role="page" id="PickFacilityPage">'.
		<div data-role="header">
			<a href="<?php echo base_url(); ?>index.php/welcome/index/TRUE" data-ajax ="false" data-icon="home" data-iconpos="notext" data-direction="reverse">Home</a>
			<h1>Pick Facility</h1>
		</div>
		<div data-role="fieldcontain">
			<ul data-role="listview" data-filter="true" data-filter-placeholder="Pick Facility..." data-filter-theme="d" data-theme="d" data-divider-theme="d">
				<?php echo $facility_list; ?>
			</ul>
		</div>
	</div>
	<div data-role="page" id="PickDayPage">
		<div data-role="header">
			<a href="#PickFacilityPage" data-icon="back" data-iconpos="notext" data-direction="reverse">Back</a>
			<h1>Pick Day</h1>
		</div>
		<div data-role="fieldcontain">
			<ul data-role="listview" data-filter="false" data-divider-theme="d">
				<?php echo $pick_day_list; ?>
			</ul>
		</div>
	</div>
	<div data-role="page" id="IsBenchmarkWodPage">
		<div data-role="header">
			<a href="#PickDayPage" data-icon="back" data-iconpos="notext" data-direction="reverse">Back</a>
			<h1>Benchmark WOD?</h1>
		</div>
		<div data-role="fieldcontain">
			<ul data-role="listview" data-filter="false" data-theme="d" data-divider-theme="d">
				<li><a class="is-benchmark-link" href="#PickScoretypePage">No</a></li>
				<li><a class="is-benchmark-link" href="#PickBenchmarkWodPage">Yes</a></li>
			</ul>
		</div>
	</div>
	<div data-role="page" id="PickBenchmarkWodPage">
		<div data-role="header">
			<a href="#IsBenchmarkWodPage" data-icon="back" data-iconpos="notext" data-direction="reverse">Back</a>
			<h1>Pick Benchmark WOD</h1>
		</div>
		<div data-role="fieldcontain">
			<ul data-role="listview" data-filter="true" data-filter-placeholder="Pick Day..." data-filter-theme="d" data-theme="d" data-divider-theme="d">
				<?php echo $benchmark_wod_list; ?>
			</ul>
		</div>
	</div>
	<div data-role="page" id="PickScoretypePage">
		<div data-role="header">
			<a href="#IsBenchmarkWodPage" data-icon="back" data-iconpos="notext" data-direction="reverse">Back</a>
			<h1>Pick Score Type</h1>
		</div>
		<div data-role="fieldcontain">
			<ul data-role="listview" data-filter="false" data-theme="d" data-divider-theme="d">
				<?php echo $score_type_list; ?>
			</ul>
		</div>
	</div>
	<div data-role="page" id="PickScalePage">
		<div data-role="header">
			<a href="#PickScoretypePage" data-icon="back" data-iconpos="notext" data-direction="reverse">Back</a>
			<h1>Pick Scale</h1>
		</div>
		<div data-role="fieldcontain">
			<ul data-role="listview" data-filter="false" data-theme="d" data-divider-theme="d">
				<?php echo $scale_list; ?>
			</ul>
		</div>
	</div>
	<div data-role="page" id="WodPage">
		<div data-role="header">
			<a href="#PickScoretypePage" data-icon="back" data-iconpos="notext" data-direction="reverse">Back</a>
			<h1>WOD</h1>
		</div>
		<div data-role="fieldcontain">
			<label for="_wizardWodName">WOD Name:</label>
			<input id="_wizardWodName" type="text" value=""  />
		</div>
		<div data-role="fieldcontain">
			<label for="_wizardWodDescription">WOD Description:</label>
			<textarea id="_wizardWodDescription"></textarea>
		</div>
		<a href="#BuyInPage" id="_wodButton" data-role="button">Next</a>
	</div>
	<div data-role="page" id="BuyInPage">
		<div data-role="header">
			<a href="#WodPage" data-icon="back" data-iconpos="notext" data-direction="reverse">Back</a>
			<h1>Buy In</h1>
		</div>
		<div data-role="fieldcontain">
			<label for="_wizardBuyIn">Buy In:</label>
			<textarea id="_wizardBuyIn"></textarea>
		</div>
		<a href="#CashoutPage" id="_buyInButton" data-role="button">Next</a>
	</div>
	<div data-role="page" id="CashoutPage">
		<div data-role="header">
			<a href="#BuyInPage" data-icon="back" data-iconpos="notext" data-direction="reverse">Back</a>
			<h1>Cashout</h1>
		</div>
		<div data-role="fieldcontain">
			<label for="_wizardCashout">Cashout:</label>
			<textarea id="_wizardCashout" ></textarea>
		</div>
		<a href="#BoxWodSavePage" id="_cashoutButton" data-role="button">Review and Save</a>
	</div>
	<?php endif; //Use Wizard IF ?>
<div data-role="page" id="BoxWodSavePage">
	<div data-role="header">
		<h1><?php echo $title; ?></h1>
	</div>	
	<div data-role="fieldcontain">	
		<div class ="error-messsage">
			<p><?php echo isset($error_message) ? $error_message : '' ;?></p>
		</div>
	<?php	

		$select_label_attrib	=	array('class' => 'select');
		$field_contain_div	=	'<div data-role="fieldcontain">';
		$close_div	=	'</div>';
		$attributes		=	array(	'id'			=>	'_profileForm',
									'data-ajax'	=>	'false');
		echo form_open('administration_functions/save_box_wod/'.$bw_id,	$attributes);

		//Box Div
		echo	$field_contain_div.
					form_label('Box:', '_box',$select_label_attrib).
					$box_dropdown.
				$close_div;
		
		//WOD Date Div
		echo	$field_contain_div.
					form_label('WOD Date:', '_wodDate').
					form_input($wod_date).
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
                
		//Scale Div
		echo	$field_contain_div.
					form_label('Scale:', '_scale',$select_label_attrib).
					$scale_dropdown.
				$close_div;

		//WOD Name
		echo	$field_contain_div.
					form_label('WOD Name:', '_simpleTitle').
					form_input($simple_title).
				$close_div;

		//Buy In Div
		echo	$field_contain_div.
					form_label('Buy In:', '_buyIn').
					form_textarea($buy_in).
				$close_div;

		//WOD
		echo	$field_contain_div.
					form_label('WOD:', '_simpleDescription').
					form_textarea($simple_description).
				$close_div;
		
		//Cash Out
		echo	$field_contain_div.
					form_label('Cash Out:', '_cashOut').
					form_textarea($cash_out).
				$close_div;

		//WOD Type Div
		//This came from Andrew, but I don't think I like it nor have a use for it
		//Keeping it here for now, but hiding it b/c it's just taking up screen space
		/*
		echo	$field_contain_div.
					form_label('WOD Type:', '_wodType',$select_label_attrib).
					$wod_type_dropdown.
				$close_div;
		*/
		
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