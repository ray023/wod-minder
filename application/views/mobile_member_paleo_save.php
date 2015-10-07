<script>
	function resetForm()
	{
		//Reset form		
		$('#_mealTime').val('');
		$('#_mealTime').selectmenu("refresh");
		$('#_mealType').val('');
		$('#_mealType').selectmenu("refresh");
		$('#_protein').val('');
		$('#_veggieOrFruit').val('');
		$('#_fat').val('');
		$('#_note').val('');
		$("#_mealImage").attr('src', base_url() + 'user_images/paleo/no_image_filler.jpg');
		$("#_submit").val('Save');
		$('#_submit').button('refresh');
		$('#_image').replaceWith( $('#_image').val('').clone('#_image' ));
	}
	function progressHandlingFunction(e)
	{
		if(e.lengthComputable){
			$('progress').attr({value:e.loaded,max:e.total});
		}
	}

	function beforeSendHandler(data, status)
	{
		//Not using yet.  Nothing to do here.
	}
	function onSuccess(data, status)
	{
		var errorMessageReturned	=	false;
		data = $.trim(data);
		var divs = $(data).filter(function(){ return $(this).is('div') });
		divs.each(function() 
			{
				 returnId	=	$(this).attr('id');
				 switch (returnId)
				 {
					case 'errorMessage':
						$('#_errorMessage').html($(this).html());
						//If an error message other than "record deleted" was returned, then keep form in error and don't reset
						if ($(this).html().toLowerCase().indexOf('deleted')	<	0)
							errorMessageReturned	=	true;
						break;
					case 'goodMessage':
						$('#_goodMessage').html($(this).html());
						$('#_goodMessage').show();
						break;
			        case 'gridData':
						$("#_gridData").html($(this).html());
						$('#_gridData').trigger('create');
						break;
					case 'previousMealDate':
						$("#_previousMealButton").text('Previous Day, ' + $(this).text());
						$("#_previousMealButton").button("refresh");
						break;
					case 'nextMealDate':
						$('#_nextMealButton').text('Next Day, ' + $(this).text());
						$('#_nextMealButton').button('refresh');
						break;						
				 }
			});
		//Since form goes blank on save/update, just clear the image
		$("#_mealImage").attr('src', base_url() + 'user_images/paleo/no_image_filler.jpg');
		
		$('#_goodMessage').delay(3200).fadeOut(300);
		
		$('#_progress').val(0);
		$('#_progress').hide();
		
		//Form in error, don't reset it yet.
		if (errorMessageReturned)
			return;
		
		//User has updated record with no errors, collapse meal form
		if ($('#_submit').val()	==	'Update')
			$('#_addMealFrame').trigger('collapse');

		resetForm();

	}

	function onError(data, status)
	{
		$('#_errorMessage').html('error:  ' + data.status);
	}

	$(document).ready(function() {
		
		//If FormData is not supported, then save through regular HTTP request
		if (typeof FormData	===	'undefined') 
			$('#_mealButtons').hide();
		$('#_progress').hide();
		$('#_image').replaceWith( $('#_image').val('').clone('#_image' ));
		
		$('.mp-delete-link').live('click',(function(e){
			
			e.preventDefault();
			
			var r	=	confirm("You are about to delete a paleo meal.  Click 'ok' to confirm.");
			if (r!=true)
				return false;
				
			
			var id	=	$(this).attr('id').replace('delete_id_','');
			
			$.ajax({
				type: "POST",
				url: base_url() + "index.php/paleo/ajax_dmpm/" + id,
				cache: false,
				success: onSuccess,
				error: onError
			});
			
			return false;
		}));
		$('.mp-edit-link').live('click',(function(e){

			//Can't do the fun ajax stuff; just load through HTTP request
			if (typeof FormData	===	'undefined') 
				return;
			else
				e.preventDefault();
			
			var paleo_id	=	$(this).attr('id').replace('edit_id_','');
			sessionStorage.paleo_id	=	paleo_id;
			
			//Now get the form data from the hidden div and populate the paleo form:
			var $jsonData	=	$('#_paleo_form_data_' + paleo_id).text();
			var objPaleo = jQuery.parseJSON($jsonData);
			
			$('#_mealDate').val(objPaleo.meal_date);
			
			var mealTime	= parseInt(objPaleo.meal_time_for_form);
			$('#_mealTime').val(mealTime);
			$('#_mealTime').selectmenu("refresh");
			
			$('#_mealType').val(objPaleo.meal_type_id);
			$('#_mealType').selectmenu("refresh");
			
			$('#_protein').val(objPaleo.protein);
			$('#_veggieOrFruit').val(objPaleo.veggie_or_fruit);
			$('#_fat').val(objPaleo.fat);
			$('#_note').val(objPaleo.note);
			
			var imageName	=	objPaleo.image_name == '' ? 'no_image_filler.jpg' : objPaleo.image_name;
			var imageSrc = base_url() + 'user_images/paleo/' + imageName;
			$("#_mealImage").attr("src", imageSrc);


			$("#_submit").val('Update');
			$("#_submit").button("refresh");
			
			$('#_addMealFrame').trigger('expand');
			return false;
		}));
		
		$('#_cancel').click(function(e)
		{
			if (typeof FormData	===	'undefined') 
				return;
			
			e.preventDefault();
			
			if ($('#_submit').val()	!=	'Update')
				$('#_addMealFrame').trigger('collapse');
			
			resetForm();
			
			
			
		});

		$('.meal-day-button').click(function()
		{
			var mealDate = $(this).text().substring();
			mealDate	=	$.trim(mealDate.substring(mealDate.indexOf(',') + 1));
			
			if(!isValidDate(mealDate))
				return false; //do nothing
			
			//Collapse add frame
			$('#_addMealFrame').trigger('collapse');
			
			//Put date back in date control
			$('#_mealDate').val(mealDate);
			
			//Make it php readable
			var mealDateArray	=	mealDate.split('/');
			mealDate	=	mealDateArray[2] + '-' + mealDateArray[0] + '-' + mealDateArray[1]; 

			$.ajax({
				type: 'POST',
				url: base_url() + 'index.php/paleo/ajax_move_paleo_page/' + mealDate,
				cache: false,
				contentType: false,
				processData: false,
				success: onSuccess,
				error: onError
			});
			
			//Clear error message in case user moves to different screen
			$('#_errorMessage').html('');
			return false;	
		}
		);
		
		$('#_submit').click(function()
		{
			//If FormData is not supported, then save through regular HTTP request
			if (typeof FormData	===	'undefined') 
				return true;
			
			$('#_errorMessage').html('');
			$('#_goodMessage').html('');
			$('#_progress').show();
			
			
			var updateUrl	=	'';
			
			if ($('#_submit').val()	==	'Update')
				updateUrl	=	'/' + sessionStorage.paleo_id;

			var formData = new FormData($('#_profileForm')[0]);

			$.ajax({
				type: 'POST',
				url: base_url() + 'index.php/paleo/ajax_spm' + updateUrl,
				xhr: function() {  // custom xhr
						   myXhr = $.ajaxSettings.xhr();
						   if(myXhr.upload){ // check if upload property exists
							   myXhr.upload.addEventListener('progress',progressHandlingFunction, false); // for handling the progress of the upload
						   }
						   return myXhr;
					   },
				beforeSend: beforeSendHandler,
				cache: false,
				contentType: false,
				processData: false,
				data: formData,
				success: onSuccess,
				error: onError
			});
			
			return false;
		});
		
		<?php //If user pulls up Paleo history, then there will be a date in the url
			 //check for that and collapse ?>
		var lastElementOfUrl = document.URL.substring(document.URL.lastIndexOf('/') + 1).trim();
		var dateElement	=	lastElementOfUrl.split('-');
		if (dateElement.length == 3 && (isValidDate(dateElement[1] + '/' + dateElement[2] + '/' + dateElement[0])) )
			$('#_addMealFrame').trigger('collapse');
	});
</script>
<?php
$this->load->helper('form'); ?>
<div data-role="page">
	<div data-role="header">
		<a href="<?php echo base_url(); ?>index.php/welcome/index/TRUE" data-ajax ="false" data-icon="home" data-iconpos="notext" data-direction="reverse">Home</a>
		<h1><?php echo $title; ?></h1>
	</div>
	<div data-role="fieldcontain">
		
		<div id="_goodMessage" class ="good-messsage">
		</div>
		<div id="_errorMessage" class ="error-messsage" >
		</div>
		<div data-role="fieldcontain" id="_mealButtons">
			<button id="_previousMealButton" class="meal-day-button" data-ajax="false" data-role="button" data-inline="true" data-mini="true" data-icon="arrow-l">Previous Day, <?php echo $previous_meal_date;?></button> 
			<button id="_nextMealButton" class="meal-day-button" data-ajax="false" data-role="button" data-inline="true" data-mini="true" data-icon="arrow-r" data-iconpos="right">Next Day, <?php echo $next_meal_date;?></button> 
		</div>
		<div id="_addMealFrame" data-role="collapsible" data-mini="true" data-theme="a" data-collapsed="false">
			<h3 data-theme="d" id="_mealFrameText">Meal Details</h3>
				<?php	
				
					$select_label_attrib	=	array('class' => 'select');
					$field_contain_div	=	'<div data-role="fieldcontain">';
					$close_div	=	'</div>';
					$attributes		=	array(	'id'			=>	'_profileForm',
													'data-ajax'	=>	'false');
					echo  form_open_multipart('paleo/save_member_paleo_meal/'.$id_value,	$attributes);
					
					//Paleo Meal Date Div
					echo	$field_contain_div.
								form_label('Date:', '_mealDate').
								form_input($meal_date).
							$close_div;
					
					//Meal Time Div
					echo	$field_contain_div.
								form_label('Meal Time:', '_mealTime',$select_label_attrib).
								$meal_time_dropdown.
							$close_div;
					
					//Meal Type
					echo	$field_contain_div.
								form_label('Meal Type:', '_mealType',$select_label_attrib).
								$meal_type_dropdown.
							$close_div;
					
					//Protein
					echo	$field_contain_div.
								form_label('Protein:', '_protein').
								form_input($protein).
							$close_div;

					//Veggie or fruit
					echo	$field_contain_div.
								form_label('Veggie/Fruit:', '_veggieOrFruit').
								form_input($veggie_or_fruit).
							$close_div;
					
					//Fat
					echo	$field_contain_div.
								form_label('Fat:', '_fat').
								form_input($fat).
							$close_div;

					//Note Div
					echo	$field_contain_div.
								form_label('Note:', '_note').
								form_textarea($note).
							$close_div;
					
					$full_image_name	= base_url().'user_images/paleo/'.($image_name['value'] === '' ? 'no_image_filler.jpg' : $image_name['value']);
					echo '<img id="_mealImage" src="'.$full_image_name.'" />';
					
					echo	$field_contain_div.
							form_label('Image:', '_image').
							'<input type="file" id ="_image" name="userfile" />'.
							'<progress id="_progress"></progress>'.
							$close_div;
					
					//Buttons
					echo	$field_contain_div.
								anchor('welcome/index/TRUE', 'Cancel', array(	'id'=>'_cancel',
																				'data-ajax'=>'false',
																				'data-role'=>'button',
																				'data-inline'=>'true')).
								form_button($submit).
							$close_div;
						
						
					echo form_close();?>
		</div>
		<div id="_gridData">
			<?php echo $paleo_history; ?>
		</div>
	</div>
</div>
