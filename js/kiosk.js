var check_date_interval = 5000;
var rotate_interval = 1000;

function update_button_html()
{
	boxId = $('#_boxId').val();
	$.ajax({
		url: base_url() + 'index.php/kiosk/get_wod_buttons/' + boxId,
		cache: false
	})
		.done(function( html ) {
			$('#_resultsMenu').html(html);
			

			$('#_resultsMenu button').unbind('click');
			$('#_resultsMenu button').unbind('mouseenter');
			$('#_resultsMenu button').unbind('mouseleave');
			
			
			$('#_resultsMenu button').on('click', function () {
				var oClass = ($(this).attr('class'));
						$('#_resultsContainer').fadeOut('fast',function(){
							$('#_resultsContainer').html($('#_hideUs .' + oClass).html());
							$('#_resultsContainer').fadeIn();
						});

						$('body').data('current',oClass);


			});
			
			$('#_resultsMenu button').on('mouseenter', function () {
					
					$(this).click();
					clearInterval(rotateIntervalId);
				}
			);
			
			$('#_resultsMenu button').on('mouseleave', function () {
					if($('#_resultsMenu button').length > 1)
						rotateIntervalId = window.setInterval(cycle_results, rotate_interval);
				}
			);
				
			
			$('#_resultsMenu button').first().click();
			if($('#_resultsMenu button').length > 1)
			{
				$('#_resultsMenu').show();
					rotateIntervalId = window.setInterval(cycle_results, rotate_interval);
			}
			else if ($('#_resultsMenu button').length == 1)
			{
				clearInterval(rotateIntervalId);
				$('#_resultsMenu').hide();
			}
					
		});
}

function update_wod_html()
{
	boxId = $('#_boxId').val();
	$.ajax({
		url: base_url() + 'index.php/kiosk/get_wod_results/' + boxId,
		cache: false
	})
		.done(function( html ) {
			$('#_hideUs').html(html);
			if($('#_resultsMenu button').length == 1)
				$('#_resultsMenu button').first().click()
			
		});

}

function check_dates()
{
	var update_wod_buttons = false;
	var update_wod_results = false;
	boxId = $('#_boxId').val();
	$.ajax({
		url: base_url() + 'index.php/kiosk/check_dates/' + boxId,
		cache: false
	})
		.done(function( date_values ) {
			if (date_values === '')
			{
				$('#_resultsMenu').html('<h1>No wods saved for today</h1>');
				return;
			}
			
			date_array = date_values.split('|');
			b = date_array[0].split(',');
			box_wod_date = new Date(b[0],b[1],b[2],b[3],b[4],b[5]).getTime();
			b = date_array[1].split(',');
			member_wod_date = new Date(b[0],b[1],b[2],b[3],b[4],b[5]).getTime();
			member_wod_date = isNaN(member_wod_date) ? '' : member_wod_date;
		
			
			//Check Box Wod Date
			if ($('#_lastButtonUpdate').val() === '')
			{
				update_wod_buttons	 =	true;
				$('#_lastButtonUpdate').val(box_wod_date);
			}
			else
			{				
				var htmlBwDate = $('#_lastButtonUpdate').val();
				
				if (box_wod_date > htmlBwDate)
				{
					update_wod_buttons	 =	true;
					$('#_lastButtonUpdate').val(box_wod_date);
				}
			}
			
			//Check Member Wod Date
			if ($('#_lastWodUpdate').val() === '' && member_wod_date !== '')
			{
				update_wod_results	 =	true;
				$('#_lastWodUpdate').val(member_wod_date);
			}
			else
			{
				var html2BwDate = $('#_lastWodUpdate').val();
				
				if (member_wod_date > html2BwDate)
				{
					update_wod_results	 =	true;
					$('#_lastWodUpdate').val(member_wod_date);
				}
			}
			
			if(update_wod_buttons)
				update_button_html();
			
			if(update_wod_results)
				update_wod_html();
			
		});	
}

function box_results_doc_ready()
{
	$('#_hideUs').hide();
	
	//Assume this is a page refresh
	$('#_lastButtonUpdate').val('');
	$('#_lastWodUpdate').val('');
	
	$('#_resultsContainer').html('');
	check_dates();
}


var checkDateIntervalId = window.setInterval(check_dates, check_date_interval);

var rotateIntervalId = 0;//window.setInterval(cycle_results, rotate_interval);
function cycle_results() 
{ 
	var curButtonClass = $('body').data('current');
	var currentButton = $('#_resultsMenu .' + curButtonClass); 
	var nextButton = currentButton.next().length?currentButton.next():currentButton.closest('div').find('button:first');
	if (curButtonClass === 'results-3')
		console.log(curButtonClass);
	$('body').data('current',nextButton.attr('class')); 
	nextButton.click();
}