function onSuccess(position) {
        
		$('#geolocation').html('Location Found; retrieving nearest facilities...');

        var result_count = $('#_resultCount').val();

        $.ajax({
            url: base_url() + 'index.php/find_a_fit/ajax_get_nearest_boxes_with_options/browser/current_position/' + 
                        result_count + '/' + 
                        position.coords.latitude + '/' + 
                        position.coords.longitude,
            cache: false,
            contentType: 'application/json; charset=utf-8',
            }).done(function ( data ) {
                $('#geolocation').html(data);
            }).fail(function() {
                var errorMsg = '<span class ="error-messsage">Unable to reach box server.  Please make sure you have network access.</span>';
                $('#geolocation').html(errorMsg);
            });

    }

// onError Callback receives a PositionError object
function onError(error) {

    var myHtml = 'Error getting current position.';
    switch(error.code) {
        case error.PERMISSION_DENIED:
            myHtml = "User denied the request for Geolocation."
            break;
        case error.POSITION_UNAVAILABLE:
            myHtml = "Location information is unavailable."
            break;
        case error.TIMEOUT:
            myHtml = "The request to get user location timed out.<br>Please turn on (or restart) Location services on your device."
            break;
        case error.UNKNOWN_ERROR:
            myHtml = "An unknown error occurred."
            break;
    }

        var errorMsg = '<span class ="error-messsage">' + myHtml + '</span>';
		$('#geolocation').html(errorMsg);
}

//Should be supported by all PhoneGap Applications; including just in case
function supports_html5_storage() {
    try 
    {
        return 'localStorage' in window && window['localStorage'] !== null;
    } 
    catch (e) 
    {
        return false;
    }
}
 
function refresh_appropriate_source() {
    if ($('#_refreshOnLocationButton').is(':visible'))
    {
        $('#_refreshOnLocationButton').click();
    }
    else if ($.trim($('#_addressField').val()) != '')
    {
        $('#_searchFits').click();
    }
    else
        $('#geolocation').html('');
}

//Set new default number of affiliates to return
function result_count_button_click(result_count) {
    $('#_resultMenu').collapsible("collapse");
    $('#_popupNested').popup( "close" );

    $('#_resultCount').val(result_count);

    if (!supports_html5_storage())
        alert('Local Storage not supported.  Settings cannot be saved.');
    else
    {
        localStorage.setItem("result_count",result_count);
    }

    refresh_appropriate_source();
}

//"fits" are affiliated gyms
function search_fits() {
    if ($.trim($('#_addressField').val()) == '')
    {
        alert('Please enter Address Value');
        return false;
    }
    var encodedAddress = encodeURIComponent($('#_addressField').val());     
    var element = document.getElementById('geolocation');
    var result_count = $('#_resultCount').val();

    element.innerHTML = 'Searching nearest facilities based on address given...';

    $.ajax({
        url: base_url() + 'index.php/find_a_fit/ajax_get_nearest_boxes_with_options/browser/address_field/' + 
                    result_count + '/' + 
                    encodedAddress,
        cache: false,
        crossDomain: true,
        contentType: 'application/json; charset=utf-8',
        }).done(function ( data ) {
            element.innerHTML = data;
        }).fail(function() {
            var errorMsg = '<span class ="error-messsage">Unable to reach box server.  Please make sure you have network access.</span>';
            var element = document.getElementById('geolocation');
            element.innerHTML = errorMsg;
        });
}

//Change page layout based on searching on current position or inputed address
function source_button_click(source_type) {
    $('#_sourceMenu').collapsible("collapse");
    $('#_popupNested').popup( "close" );

    if(source_type == 'Address')
    {
        $('#_sourceType').val('Address');
        $('#_addressField').closest('div').show();
        $('#_searchFits').show();
        $('#_refreshOnLocationIcon').hide();
        $('#_refreshOnLocationButton').hide();
    }
    else
    {
        $('#_sourceType').val('Location');
        $('#_addressField').closest('div').hide();
        $('#_searchFits').hide();
        $('#_refreshOnLocationIcon').show();
        $('#_refreshOnLocationButton').show();          
    }

    refresh_appropriate_source();
}

//Send feedback and device information to server
function submit_feedback() {  
    var user_feedback = $('#_userFeedback').val();

    if (user_feedback == null || $.trim(user_feedback) === '')
        return;

    $.ajax({
        url: base_url() + 'index.php/find_a_fit/ajax_submit_feedback/' + 
                    encodeURIComponent(user_feedback) + '/' + 
                    'Web Browser' + '/' + 
                    '' + '/' + 
                    '' + '/' + 
                    '' + '/' + 
                    '',
        cache: false,
        crossDomain: true,
        contentType: 'application/json; charset=utf-8',
        }).done(function ( data ) {
            alert(data);
        }).fail(function() {
            var errorMsg = '<span class ="error-messsage">Unable to reach box server for feedback.  Please make sure you have network access.</span>';
            var element = document.getElementById('geolocation');
            element.innerHTML = errorMsg;
        });
}

//Initialization adding events to controls on page
function set_control_functions() {
    $('.refresh-on-location-button').click(function() {
            navigator.geolocation.getCurrentPosition(onSuccess, onError,{timeout:10000, enableHighAccuracy:false});
        });

    $('.result-count-button').click(function() {    
            result_count_button_click($(this).text());
        });

    $('#_showFeedbackFields').on( "click", function() {
            $('#_feedbackMenu').collapsible("collapse");
            $('#_popupNested').popup( "close" );

            $('#_feedbackDiv').show();
            $('#_resultsDiv').hide();
            $('#_refreshOnLocationIcon').hide();
            $('#_optionButton').hide();
        });

    $('#_cancelFeedback').on( "click", function() {
            $('#_resultsDiv').show();
            $('#_optionButton').show();
            $('#_feedbackDiv').hide(); 
            
            source_button_click($('#_sourceType').val());
            refresh_appropriate_source();
        });

    $('#_submitFeedback').on( "click", function() {
            $('#_resultsDiv').show();
			$('#_optionButton').show();
            $('#_feedbackDiv').hide();
			
            submit_feedback();
            source_button_click($('#_sourceType').val());
            refresh_appropriate_source();


        });

    $('#_gotoFacebook').click(function(){
            $('#_feedbackMenu').collapsible("collapse");
            $('#_popupNested').popup( "close" );
            window.open('https://www.facebook.com/379546762215330', '_system', '');
        });

    $('.source-button').click(function(){         
            source_button_click($(this).text());
        });

    $('#_searchFits').click(function(){
            search_fits();
        });
}