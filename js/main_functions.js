var timeOutVarOnDraw	=	0;

$.fn.extend({
 trackChanges: function() {
   $(":input",this).change(function() {
      $(this.form).data("changed", true);
   });
 }
 ,
 isChanged: function() { 
   return this.data("changed"); 
 }
});

function member_event_info_doc_ready()
{
	$("form").trackChanges();
	
	$('#_wodList a').click(function()
	{
		if ($("form").isChanged()) {
			if (!confirm("'My Event Info' has changed and not been saved.\r\nClick 'cancel' to return and save.\r\nClick 'ok' to go to '" +$(this).text() + "'."))
				return false;
		}	
	});  
	
}

function isValidDate(passedDate) 
{
	var d = new Date(passedDate);
	if ( Object.prototype.toString.call(d) !== "[object Date]" )
		return false;
	return !isNaN(d.getTime());
}

function isNumber(n) 
{
  return !isNaN(parseFloat(n)) && isFinite(n);
}
function draw_bar_with_weights() 
{
		var WIDTH_FACTOR	=	.8; //80% of screen size
		var HEIGHT_FACTOR	=	.1; //10% of height size
		var WEIGHT_SPACER	=	 2;
		var ctx = $("#_barCanvas")[0].getContext("2d");
		
		ctx.canvas.width  = (window.innerWidth	*	WIDTH_FACTOR);
		ctx.canvas.height = (window.innerHeight	*	HEIGHT_FACTOR);
		
		var bar_width	=	ctx.canvas.width * .8;
		var bar_height	=	ctx.canvas.height * .1;
		var bar_x		=	(ctx.canvas.width - bar_width)
		var bar_y		=	(ctx.canvas.height * .5)	

		var plate_stop_width	=	bar_width * .01;
		var plate_stop_height	=	bar_height * 4;
		var plate_stop_y		=	bar_y - ((plate_stop_height - (bar_y / 2)));
		var rubber_plate_height	=	bar_height * 8;
		var rubber_plate_y		=	(ctx.canvas.height / 2) - (rubber_plate_height/2) + (bar_height/2);
		var small_plate_height	=	plate_stop_height;
		var small_plate_y		=	plate_stop_y;
		var left_plate_stop_x	=	bar_x + (bar_width * .3);
		var right_plate_stop_x	=	bar_x + (bar_width * .7);
		
		var forty_five_plate_width		=	bar_width * .04;
		var thirty_five_plate_width		=	bar_width * .03;
		var twenty_five_plate_width		=	bar_width * .025;
		var fifteen_five_plate_width	=	bar_width * .02;
		var ten_plate_width				=	bar_width * .015;
		var small_plate_width			=	bar_width * .01;
				
		//Draw Bar
		ctx.fillStyle = "black";
		ctx.fillRect (bar_x, bar_y, bar_width, bar_height);
		
		//Draw Plate stop left
		ctx.fillStyle = "black";
		ctx.fillRect (left_plate_stop_x, plate_stop_y, plate_stop_width, plate_stop_height);
		
		//Draw Plate stop right
		ctx.fillStyle = "black";
		ctx.fillRect (right_plate_stop_x, plate_stop_y, plate_stop_width, plate_stop_height);
		
		var current_plate_left_x	=	left_plate_stop_x;
		var current_plate_right_x	=	right_plate_stop_x + plate_stop_width;
		var current_plate_width = 0;
		var current_plate_height = 0;
		var current_plate_y	=	0;
		var current_plate_color	=	'';

		if (typeof sessionStorage['plates'] === 'undefined') 
			return;
		
		var storedNames=JSON.parse(sessionStorage['plates']);
		
		for (var plateCounter=0; plateCounter<storedNames.length; plateCounter++) {
			var $plate_value	=	parseFloat(storedNames[plateCounter]);

			switch ($plate_value)
			{
			case 45:
				current_plate_width		=	forty_five_plate_width;
				current_plate_color		=	'red';
				current_plate_height	=	rubber_plate_height;
				current_plate_y			=	rubber_plate_y;
				break;
			case 35:
				current_plate_width		=	thirty_five_plate_width;
				current_plate_color		=	'blue';
				current_plate_height	=	rubber_plate_height;
				current_plate_y			=	rubber_plate_y;
				break;
			case 25:
				current_plate_width		=	twenty_five_plate_width;
				current_plate_color		=	'chocolate';
				current_plate_height	=	rubber_plate_height;
				current_plate_y			=	rubber_plate_y;
				break;
			case 15:
				current_plate_width		=	fifteen_five_plate_width;
				current_plate_color		=	'teal';
				current_plate_height	=	rubber_plate_height;
				current_plate_y			=	rubber_plate_y;
				break;
			case 10:
				current_plate_width		=	ten_plate_width;
				current_plate_color		=	'green';
				current_plate_height	=	rubber_plate_height;
				current_plate_y			=	rubber_plate_y;
				break;
			case 5:
				current_plate_width		=	small_plate_width;
				current_plate_color		=	'orange';
				current_plate_height	=	small_plate_height;
				current_plate_y			=	small_plate_y;
				break;
			case 2.5:
				current_plate_width		=	small_plate_width;
				current_plate_color		=	'purple';
				current_plate_height	=	small_plate_height;
				current_plate_y			=	small_plate_y;
				break;
			case 0.25:
				current_plate_width		=	small_plate_width;
				current_plate_color		=	'steelblue';
				current_plate_height	=	small_plate_height;
				current_plate_y			=	small_plate_y;
				break;

			}
			current_plate_left_x	=	current_plate_left_x - current_plate_width;

			ctx.fillStyle	=	current_plate_color;
			ctx.fillRect(current_plate_left_x, current_plate_y, current_plate_width, current_plate_height);
			ctx.fillStyle	=	current_plate_color;
			ctx.fillRect(current_plate_right_x, current_plate_y, current_plate_width, current_plate_height);
			current_plate_right_x	=	current_plate_right_x + current_plate_width + WEIGHT_SPACER;
			current_plate_left_x = current_plate_left_x - WEIGHT_SPACER;
		}

}

$(window).resize(function() {
    if(this.resizeTO) clearTimeout(this.resizeTO);
    this.resizeTO = setTimeout(function() {
        $(this).trigger('resizeEnd');
    }, 500);
});

$(window).bind('resizeEnd', function() {
		
	var mobile_active_page_name	=	$.mobile.activePage.data('url');
	
	if (mobile_active_page_name != 'BarbellCalculator')
		return;

	draw_bar_with_weights();
});

function recalculate_weight()
{
	var running_tally	=	parseInt($('#_barbellBase').val());	
	if (typeof sessionStorage['plates'] != 'undefined') {
		var storedNames=JSON.parse(sessionStorage['plates']);
		for (var i=0; i<storedNames.length; i++)
			running_tally = running_tally + (storedNames[i] * 2);
		
		if ((storedNames.length - 1) < 0)
		{
			$('#undo').text('No plates on bar');
			$('#plates_on_bar').html('');
		}
		else
		{
			$('#undo').text('Drop ' + storedNames[storedNames.length - 1]);
			$('#plates_on_bar').html(storedNames.join(' | '));
		}
		
		if (timeOutVarOnDraw	!==	0)
			clearTimeout(timeOutVarOnDraw);
	
		var timeOutVarOnDraw	=	self.setTimeout(function(){draw_bar_with_weights(); timeOutVarOnDraw	=	0},1000);
	}
	else
	{
		$('#undo').text('No plates on bar');
		$('#plates_on_bar').html('');
		
		if (timeOutVarOnDraw	!==	0)
			clearTimeout(timeOutVarOnDraw);

		draw_bar_with_weights();
	}
	
	
	
	$('#_calculatedWeight').text(running_tally);
		
}

function fill_plate_count() {
	
	//See if there is a weight that needs to be preloaded into the plate counts
	if(!sessionStorage.weight_value)
		return;
	
	var weight_value = sessionStorage.weight_value;
	sessionStorage.removeItem("weight_value");
	
	if (weight_value < 33) 
	{
		recalculate_weight();
		return;
	}
	
	if (weight_value >= 45)
	{
		$('#_barbellBase').val('45');
		weight_value = weight_value - 45;
	}
	else
	{
		$('#_barbellBase').val('33');
		weight_value = weight_value - 33;
	}
	
	$('#_barbellBase').slider('refresh');
	
	if (weight_value < .5) //Smallest weight
	{
		recalculate_weight();
		return;
	}
	
	var plateArray	=	[45,35,25,10,5,2.5,.5,.25]
	var storedPlates=[];
	var storedplateCount=0;
	
	for (var tt = 0; tt < plateArray.length; tt++) {
		var plate_value	=	parseFloat(plateArray[tt]);
		if (weight_value >= (plate_value * 2))
		{
			//Set plate count:
			plate_count = Math.floor((Math.floor(weight_value / plate_value)) / 2);
			for (var mm = 1; mm <= plate_count; mm++ ) {
				storedPlates[storedplateCount]	=	plate_value;
				storedplateCount++;
			}
			weight_value = weight_value - (plate_value * plate_count * 2);
			if (weight_value == 0)
				break; //leave the for
		}
	}
	sessionStorage['plates']=JSON.stringify(storedPlates);
	
	recalculate_weight();
}

function wod_wizard_doc_ready()
{
	function filterItems() {
		//Show all checkboxes
		$(':checkbox').parent().show();
		$('#_movementCheckboxes').controlgroup('refresh'); //refresh look after showing all checkboxes
				
		if ($(":checkbox:checked").length == 0)
		{
			$(".wod-div").hide();
			return;
		}
		
		var movements = $(":checkbox:checked").map( function(){ return 'movement-id-' + this.value; }).get();
		
		//var goodClasses = movements.join(",");
		///$(".wod-div").hide().filter(goodClasses).show();
		$(".wod-div").each(function() {
			var hasAllClasses = true;
			for (var i = 0; i < movements.length; i++) 
			{
				if (!$(this).hasClass(movements[i]))
					{
					hasAllClasses = false;
					break;
					}
			}
			if (hasAllClasses)
				$(this).show();
			else
				$(this).hide();
		});

		
		//Get available movements from each wod
		var availableMovementArray = new Array();
		$(".wod-div:visible").each(function(i,e) {
			var tempArray = this.className.split(/\s+/);
			for (var i = 0; i < tempArray.length; i++) 
			{
				var tempValue = tempArray[i].replace('movement-id-','');
				if (tempValue != 'wod-div' && $.inArray(tempValue, availableMovementArray) == -1) 
					availableMovementArray.push(tempValue);//availableMovementArray.push(tempArray[i].replace('movement-id-',''));
			}
		});
		
		$(":checkbox:not(:checked)").each(function() {
			console.log($(this).next().id + ' in array:' + $.inArray($(this).val(), availableMovementArray));
			if ($.inArray($(this).val(), availableMovementArray) == -1) 
					$(this).parent().hide();
		});
	
		$('#_movementCheckboxes').controlgroup('refresh'); //refresh look after hiding checkboxes
		
	}

	filterItems();

	$(":checkbox").change(filterItems);
}

function mobile_user_weight_history_doc_ready()
{
	$('.ui-block-a a').click(function()
	{
		var answer = confirm('Delete record?');
		if (answer)
				window.location.href = base_url() + 'index.php/weight/delete_member_weight/' + $(this).attr('title');
		else
			return false;
		
	});  
	
}

function mobile_staff_training_log_history_doc_ready()
{
	$('.ui-block-a a').click(function()
	{
		var answer = confirm('Delete record?');
		if (answer)
				window.location.href = base_url() + 'index.php/staff/delete_staff_training_log/' + $(this).attr('title');
		else
			return false;
		
	});  
	
}

function mobile_max_history_doc_ready() 
{
	$('.ui-block-a a').click(function()
	{
		var answer = confirm('Delete record?');
		if (answer)
				window.location.href = base_url() + 'index.php/member/delete_member_history/' + $(this).attr('title');
		else
			return false;
		
	});  
}

function mobile_user_paleo_history_doc_ready() 
{
	$('.ui-block-a a').click(function()
	{
		var answer = confirm('Delete record?');
		if (answer)
				window.location.href = base_url() + 'index.php/paleo/ajax_dmpm/' + $(this).attr('title');
		else
			return false;
		
	});  
}

function fill_member_box()
{
	$('#_memberId').val(sessionStorage.member_id_selected);
	$('#_memberName').val(sessionStorage.member_name_selected);
}
function admin_page_init()
{
	$(document).delegate('#SaveMemberStaff'	, 'pageshow', fill_member_box);
	
			$('#Main').live('pageinit',function()
			{
				$('.member-link').click(function(e){
				e.preventDefault();
				sessionStorage.member_id_selected	=	$(this).next().val();
				sessionStorage.member_name_selected	=	$(this).html();

				$.mobile.changePage("#SaveMemberStaff");
		});

		});	
}

function welcome_page_init()
{
		$(window).resize(function() {
		  //resize just happened, pixels changed
		});
		
		$(document).delegate('#BarbellCalculator'	, 'pageshow', fill_plate_count);
		
		$('#Main').live('pagehide', function(){
				$('#goodMessage').html('');
				$('#badMessage').html('');
			});
		
		$('#RecordNewMax').live('pageinit',function(){
			
			$.ajax({
				url: base_url() + 'index.php/welcome/ajax_audit/welcome/record_new_max',
				cache: false
			});
			
			//User may have set max from the Barbell calculator
			//If so, preload value in a session storage variable
			//to pre-load in the record new max screen
			if (!sessionStorage.potential_max)
				return;

			$('#RecordNewMax .content-primary a').click(function(e){
				//e.preventDefault();
				sessionStorage.barbell_max_value	=	sessionStorage.potential_max;
			});
		});

		$('#BarbellCalculator').live('pageinit',function(){
			
			$.ajax({
				url: base_url() + 'index.php/welcome/ajax_audit/welcome/using_barbell_calc',
				cache: false
			});
			
			try {
				sessionStorage['bc_test'] = 1;
				localStorage.removeItem('bc_test');
			} 
			catch(e) {
				//If user has an iPhone in Private Browsing mode, that could be the problem.  Just report an error so they know it's not me.
				alert('ERROR!\r\nSession Storage disabled.\r\nAre you using Safari in "Private" mode?\r\nIf so, that\'s your problem.\r\n' );//+ e);
				
				$.ajax({
					url: base_url() + 'index.php/welcome/ajax_audit/welcome/no_session_storage',
					cache: false
				});
			}
			
			$('#_resetWeight').click(function(){
				sessionStorage.removeItem('plates');
				recalculate_weight();
			});
			
			$('#_setNewMax').click(function(){
				//Calling it potential_max b/c not sure if user is ready to 
				//save till they select exercise
				sessionStorage.potential_max	=	$('#_calculatedWeight').html();
				$.mobile.changePage("#RecordNewMax");
			});
			
			$('#_barbellBase').change(function() {
				recalculate_weight();
			});
			$('#undo').click(function() {

				if (typeof sessionStorage['plates'] === 'undefined')
					return;
				var storedPlates=[];
				storedPlates=JSON.parse(sessionStorage['plates']);
				storedPlates.pop();
				sessionStorage['plates']=JSON.stringify(storedPlates);
				recalculate_weight();
			});

			$('.plate-button').click(function() {
				var storedPlates=[];
				if (typeof sessionStorage['plates'] != 'undefined') {
					storedPlates=JSON.parse(sessionStorage['plates']);
				}
				
				var oPlateToAdd = $(this).text().replace(' #','');
				var newIndex	=	storedPlates.length;
				storedPlates[newIndex]=oPlateToAdd;
				sessionStorage['plates']=JSON.stringify(storedPlates);
				recalculate_weight();
			});
						
			recalculate_weight();
		});

	
	$('#MaxSnapshot').live('pageinit',function(event){
		
		$.ajax({
			url: base_url() + 'index.php/welcome/ajax_audit/welcome/MaxSnapshot',
			cache: false
		});

		
		$('.calculated-value a').click(function(){
				sessionStorage.weight_value=$(this).html();
				$.mobile.changePage("#BarbellCalculator");
			});
		
		$('#_singleRepMaxSlider').change(function() { 
			var percentageValue	=	$('#_singleRepMaxSlider').val() / 100;
			$('.original-value').each(function () {
				var newValue = Math.floor(percentageValue * $(this).html());
				var checkDigit = newValue % 10;
				var newFactor = 0;
				//TODO:  Probably a bit overkill to round to nearest 5, but works
				switch(checkDigit)
				{
					case 0:
					case 1:
					case 2:
					case 3:
						newFactor = 0;
						break;
					case 4:
					case 5:
					case 6:
						newFactor = 5;
						break;
					case 7:
					case 8:
					case 9:
						newFactor = 10;
						break;
				}
				newValue	=	(Math.floor(newValue/10) * 10) + newFactor;
				//$(this).next().html('<a href="#" class="ui-link">' + newValue + '</a>');
				$(this).next().children('a').text(newValue);
		});
		});
		});
		
		
		$('#MyBoxHistory').live('pageinit',function(){
			
		$.ajax({
			url: base_url() + 'index.php/welcome/ajax_audit/welcome/MyBoxHistory',
			cache: false
		});
			
			$('.ui-block-a a').click(function()
				{
					var answer = confirm('Delete record?');
					if (answer)
							window.location.href = base_url() + 'index.php/wod/delete_member_wod/' + $(this).attr('title');
					else
						return false;

				});  
		});
		
		$('#CustomWODHistory').live('pageinit',function(){
			
			$.ajax({
				url: base_url() + 'index.php/welcome/ajax_audit/welcome/CustomWodHistory',
				cache: false
			});
			
			$('.ui-block-a a').click(function()
				{
					var answer = confirm('Delete record?');
					if (answer)
							window.location.href = base_url() + 'index.php/wod/delete_member_wod/' + $(this).attr('title');
					else
						return false;

				});  
		});
		
}

function find_a_fit_doc_ready()
{
	try
	{
		$('#_addressField').closest('div').hide();
		$('#_searchFits').hide();

		//If applicable, get user's preferred number of boxes to return
		if (supports_html5_storage() && localStorage.getItem("result_count") !== null)
			$('#_resultCount').val(localStorage.getItem("result_count"));

		if (navigator.geolocation) 
		{ 
			navigator.geolocation.getCurrentPosition(onSuccess, onError,{timeout:10000, enableHighAccuracy:false});
		}
		else 
		{
			alert('This browser does not support geolocation.');
			$.ajax({
					url: base_url() + 'index.php/welcome/ajax_audit/find_a_fit/unsupported_browser',
					cache: false
			});
		}
		set_control_functions();
	}
	catch (e)
	{
		alert('Exception on load:' + e.message);
	}		
}

function member_max_doc_ready()
{	
	if (sessionStorage.barbell_max_value)
	{
		var max_value	=	sessionStorage.barbell_max_value;
		sessionStorage.removeItem("weight_value");
		$('#_maxValue').val(max_value);
	}

	$('#_submit').click(function() 
		{
			var liftDate = $('#_liftDate').val();
			if (!isValidDate(liftDate))
			{
				alert('Lift date is not valid.  Use format mm/dd/yy');
				return false;
			}
			//If max minutes does not exist, then this is not a timed max.  So leave
			if ($('#_maxMinutes').length == 0)
					return;

			if (!isInt($('#_maxMinutes').val()))
			{
				window.alert('Minutes must be an integer')
				return false;
			}
			if (!isInt($('#_maxSeconds').val()))
			{
				if ($('#_maxSeconds').val() == '')
					$('#_maxSeconds').val('0');
				else
				{
					window.alert('Seconds must be an integer')
					return false;
				}
			}
			//before submitting timed max, calculate (in seconds) the total duration for the max.
			//Then put the calculation in the hidden field max value
			var max_value = (parseInt($('#_maxMinutes').val()) * 60) + parseInt($('#_maxSeconds').val());
			$('#_maxValue').val(max_value);
			return true;
		}
	);
}
function save_member_custom_wod_doc_ready()
{
	$('#_submit').click(function() 
		{	
			var wodDate = $('#_wodDate').val();
			if (!isValidDate(wodDate))
			{
				alert('WOD date is not valid.  Use format mm/dd/yy');
				return false;
			}
			
			if ($('#_score').val() == '')
			{
				alert('Score is a required field.');
				return false;
			}
		}
	);
}

function save_kiosk_wod_doc_ready()
{
	if ($('.kiosk-form').length > 1)
		$('.kiosk-form').hide();
	
	$('.tier-button').click(function(){
		$('.kiosk-form').hide();
		var formId = $(this).attr('id').replace('radio-choice-','');
		$('#_formId' + formId).show();
	});
	

	$('.submit-button').click(function(){
		
		var userLogin = $('#_userLogin').val();
		var userPassword = $('#_userPassword').val();
		$('.hidden-login').val(userLogin);
		$('.hidden-password').val(userPassword);
		
		var score_type = $(this).closest('.kiosk-form').find('.score-type').val();

		if (score_type	== 'T')
		{
			if (!isInt($(this).closest('.kiosk-form').find('.score-minutes').val()))
			{
				window.alert('Minutes must be an integer')
				return false;
			}
			if (!isInt($(this).closest('.kiosk-form').find('.score-seconds').val()))
			{
				if ($(this).closest('.kiosk-form').find('.score-seconds').val() == '')
					$(this).closest('.kiosk-form').find('score-seconds').val('0');
				else
				{
					window.alert('Seconds must be an integer')
					return false;
				}
			}
			//before submitting timed score, calculate (in seconds) the total duration for the score.
			//Then put the calculation in the hidden field score value
			var score_value = (parseInt($(this).closest('.kiosk-form').find('.score-minutes').val()) * 60) + parseInt($(this).closest('.kiosk-form').find('.score-seconds').val());
			$(this).closest('.kiosk-form').find('.score').val(score_value);
			return true;
		}
		else if ($(this).closest('.kiosk-form').find('.score').val() == '')
		{
			//since not dealing with time, score should be entered by user
			alert('Score is a required field.');
			return false;
		}

		return true;
	});
		
		
}

function save_member_event_wod_doc_ready()
{	
	$('#_submit').click(function() 
		{	

			var score_type = $('#_scoreType').val();
			var score = $('#_score').val().trim();
			
			if (score_type	== 'T')
			{
				if (!isInt($('#_scoreMinutes').val()))
				{
					window.alert('Minutes must be an integer')
					return false;
				}
				if (!isInt($('#_scoreSeconds').val()))
				{
					if ($('#_scoreSeconds').val() == '')
						$('#_scoreSeconds').val('0');
					else
					{
						window.alert('Seconds must be an integer')
						return false;
					}
				}
				//before submitting timed score, calculate (in seconds) the total duration for the score.
				//Then put the calculation in the hidden field score value
				var score_value = (parseInt($('#_scoreMinutes').val()) * 60) + parseInt($('#_scoreSeconds').val());
				$('#_score').val(score_value);
				return true;
			}
			else if ($('#_score').val() == '')
			{
				//since not dealing with time, score should be entered by user
				alert('Score is a required field.');
				return false;
			}
		}
	);
}

function save_member_wod_doc_ready()
{	
	$('#_submit').click(function() 
		{	
			//only applicable to benchmark wods (not box wods); so don't check if it doesn't exist
			if($('#_wodDate').length > 0)
			{
				var wodDate = $('#_wodDate').val();
				if (!isValidDate(wodDate))
				{
					alert('WOD date is not valid.  Use format mm/dd/yy');
					return false;
				}
			}

			var score_type = $('#_scoreType').val();
			var score = $('#_score').val().trim();
			
			if (score_type	== 'T')
			{
				if (!isInt($('#_scoreMinutes').val()))
				{
					window.alert('Minutes must be an integer')
					return false;
				}
				if (!isInt($('#_scoreSeconds').val()))
				{
					if ($('#_scoreSeconds').val() == '')
						$('#_scoreSeconds').val('0');
					else
					{
						window.alert('Seconds must be an integer')
						return false;
					}
				}
				//before submitting timed score, calculate (in seconds) the total duration for the score.
				//Then put the calculation in the hidden field score value
				var score_value = (parseInt($('#_scoreMinutes').val()) * 60) + parseInt($('#_scoreSeconds').val());
				$('#_score').val(score_value);
				return true;
			}
			else if ($('#_score').val() == '')
			{
				//since not dealing with time, score should be entered by user
				alert('Score is a required field.');
				return false;
			}
			else if ($('#_rx').is(':checked'))
			{
                            /*  08/19/2013 : Commenting out the stricter rules for RX.  
                             *               It's confusing the users and gym owners.
                             *               I think what I need is something like "Strict RX" where
                             *               these rules are enforced.  
				var intRegex = /^\d+$/;
				//Stricter Rules when user RX's WOD.
				if (score_type	== 'I')
				{
					if(!intRegex.test(score)) 
					{
						alert('If RX, only integer allowed as score.');
						return false;
					}
				}
				else if (score_type	== 'W' && !isNumber(score))
				{
					alert('If RX, only number allowed as score.');
					return false;					
				}
                             */
			}
		}
	);
}
function save_member_weight_doc_ready()
{
	$('#_submit').click(function(){
		var wd	=	$('#_weightDate').val();
		var w	=	$('#_weight').val().trim();
		var b	=	$('#_bmi').val().trim();
		var bfp	=	$('#_bodyFatPercentage').val().trim();
		
		if (wd.length == 0 )
		{
			alert('Date is required');
			return false;
		}
		if(!isValidDate(wd))
		{
			alert('Date is not valid');
			return false;
		}
		if(w.length > 0 && !isNumber(w))
		{
			alert('Weight must be numeric');
			return false;
		}
		if(b.length > 0 && !isNumber(b))
		{
			alert('BMI must be numeric');
			return false;
		}
		if(bfp.length > 0 && !isNumber(bfp))
		{
			alert('Body Fat % must be numeric');
			return false;
		}
		
	})
}
function update_member_doc_ready()
{
	$('#_submit').click(function() 
		{			
			if ($('#_firstName').val() == '')
			{
				alert('First Name is a required field.');
				return false;
			}
			
			var birthDate = $('#_birthDate').val();
			if (!isValidDate(birthDate))
			{
				alert('Birthdate is not valid.  Use format mm/dd/yy');
				return false;
			}			
		}
	);
	
}

function create_member_doc_ready()
{
	
	$('#_box').change(function(){
		var boxText = $('#_box option:selected').text();
		if (boxText === 'Other')
			{
			$('#_otherBox').show();
			$('#obLabel').removeClass('ui-hidden-accessible');
			}
		else
			{
			$('#_otherBox').hide();
			$('#obLabel').addClass('ui-hidden-accessible');
			}
	});

		var boxText = $('#_box option:selected').text();
		if (boxText === 'Other')
			{
			$('#_otherBox').show();
			$('#obLabel').removeClass('ui-hidden-accessible');
			}
		else
			{
			$('#_otherBox').hide();
			$('#obLabel').addClass('ui-hidden-accessible');
			}

	$('#_submit').click(function() 
		{
			if ($('#_userLogin').val() == '')
			{
				alert('User Login is a required field.');
				return false;
			}
			
			if ($('#_firstName').val() == '')
			{
				alert('First Name is a required field.');
				return false;
			}
			
			if ($('#_password').val() == '')
			{
				alert('Password is a required field.');
				return false;
			}
			
			if ($('#_email').val() == '')
			{
				alert('E-mail is a required field.');
				return false;
			}
			
			var birthDate = $('#_birthDate').val();
			if (!isValidDate(birthDate))
			{
				alert('Birthdate is not valid.  Use format mm/dd/yy');
				return false;
			}			
		}
	);

}

function admin_event_save_page_init()
{
	$('#AdminEventSavePage').live('pageinit',function()
	{
		$('#_hostingEntity').hide();
		
		$("[name=entity-source]").change(function() {
			if ($(this).val() == 'box')
			{
				$('#_hostBoxDiv').show();
				$('#_hostingEntity').hide();			
			}
			else
			{
				$('#_hostBoxDiv').hide();
				$('#_hostingEntity').show();						
			}
		});
	}
	);
}

function box_wod_save_page_init()
{
	$('#BoxWodSavePage').live('pageinit',function()
	{
		$('#_imageLinkDiv').hide();
		$("[name=image-source]").change(function() {
			if ($(this).val() == 'pc')
			{
				$('#_imageUploadDiv').show();
				$('#_imageLinkDiv').hide();
			}
			else
			{
				$('#_imageUploadDiv').hide();
				$('#_imageLinkDiv').show();
			}
		});
	}
	);
}


function mobile_benchmark_wod_history_doc_ready()
{
	$('.ui-block-a a').click(function()
		{
			var answer = confirm('Delete record?');
			if (answer)
					window.location.href = base_url() + 'index.php/wod/delete_member_wod/' + $(this).attr('title');
			else
				return false;

		});  	
}

//http://stackoverflow.com/questions/3885817/how-to-check-if-a-number-is-float-or-integer
function isInt(n) 
{
	if (isNaN(parseInt(n)))
		return false;
	
	return n % 1 === 0;
}
