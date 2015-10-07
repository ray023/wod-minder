<?php echo link_tag('css/wmd-pagedown.css' . '?' . time()); ?>
<script src="<?php echo base_url() . 'js/Markdown.Converter.js'; ?>"></script>
<script src="<?php echo base_url() . 'js/Markdown.Sanitizer.js'; ?>"></script>
<script src="<?php echo base_url() . 'js/Markdown.Editor.js'; ?>"></script>

<script type="text/javascript">
    $(document).ready(function () {
        $('#_submit').click(function (e) {

            var facebook_text = $('#_wodDate').val() + '\r\n\r\n';

            if ($('#_tier').length > 0 && $('#_tier option:selected').text() != '')
                facebook_text = facebook_text + $('#_tier option:selected').text() + '\r\n\r\n';

            if ($('#_dailyMessage').val() != '')
                facebook_text = facebook_text + $('#_dailyMessage').val() + '\r\n\r\n';

            if ($('#_buyIn').val() != '')
                facebook_text = facebook_text + 'Buy In:\r\n' + $('#_buyIn').val() + '\r\n\r\n';

            if ($('#_simpleTitle').val() != '')
                facebook_text = facebook_text + 'WOD Name:  ' + $('#_simpleTitle').val() + '\r\n';
            else
                facebook_text = facebook_text + 'WOD:  ' + '\r\n';

            if ($('#_scoreType option:selected').text() != 'Other')
                facebook_text = facebook_text + 'Score Type:  ' + $('#_scoreType option:selected').text() + '\r\n\r\n';

            if ($('#_scale option:selected').text() != '')
                facebook_text = facebook_text + 'Scale:  ' + $('#_scale option:selected').text() + '\r\n\r\n';

            facebook_text = facebook_text + $('#_simpleDescription').val() + '\r\n\r\n';


            if ($('#_cashOut').val() != '')
                facebook_text = facebook_text + 'Cashout:\r\n' + $('#_cashOut').val();

            $('#_facebookTextDiv').html(facebook_text);

            $('#_facebook_text').val($('#_facebookTextDiv').text());
        });
    });

    $('#PickDayPage').live('pageinit', function () {
        $('.day-link').click(function () {
            var selectedDate = $(this).text().substr($(this).text().lastIndexOf('-') + 1).trim();
            if (isValidDate(selectedDate))
                $('#_wodDate').val(selectedDate);
            else
                $('#_wodDate').val('');
        });
    });
    $('#SocialMediaConnectPage').live('pageinit', function () {
        //Do any stuff for social media page here
    });
    $('#DailyMessagePage').live('pageinit', function () {
        $('#_dailyMessageButton').click(function () {
            var textValue = $('#wmd-preview').html();
            $('#_dailyMessage').val(textValue);
        });
    });
    $('#IsBenchmarkWodPage').live('pageinit', function () {
        $('.is-benchmark-link').click(function () {
            var isBenchmarkLink = $(this).text();
            if (isBenchmarkLink == 'No')
                $('#_benchmarkWod').val('');
        });
    });
    $('#PickBenchmarkWodPage').live('pageinit', function () {
        $('.benchmark-wod-link').click(function () {
            var benchmarkWodId = $(this).attr('id').replace('wod_id_', '');
            $('#_benchmarkWod').val(benchmarkWodId);

            var wod = document.getElementById('wod_data_' + benchmarkWodId);
            var wodTitle = wod.getAttribute('data-wod-title');
            var wodDescription = wod.getAttribute('data-wod-description');
            var scoreType = wod.getAttribute('data-score-type');

            $('#_benchmarkWod').val(benchmarkWodId);
            $('#_scoreType').val(scoreType);
            $('#_simpleTitle').val(wodTitle);
            $('#_simpleDescription').val(wodDescription);

        });
    });
    $('#PickScoretypePage').live('pageinit', function () {
        $('.score-type-link').click(function () {
            var scoreTypeId = $(this).attr('id').replace('score_type_', '');
            $('#_scoreType').val(scoreTypeId);
        });
    });
    $('#PickScalePage').live('pageinit', function () {
        $('.scale-link').click(function () {
            var scaleId = $(this).attr('id').replace('scale_id_', '');
            $('#_scale').val(scaleId);
        });
    });

    $('#TierPage').live('pageinit', function () {
        $('.tier-link').click(function () {
            var tierId = $(this).attr('id').replace('bwt_id_', '');
            $('#_tier').val(tierId);
        });
    });

    $('#BuyInPage').live('pageinit', function () {
        $('#_buyInButton').click(function () {
            var textValue = $('#wmd-preview').html();
            $('#_buyIn').val(textValue);
        });
    });
    $('#WodPage').live('pageinit', function () {
        $('#_wodButton').click(function () {
            var textValue = $('#_wizardWodName').val();
            $('#_simpleTitle').val(textValue);
            var textValue = $('#wmd-preview').html();
            $('#_simpleDescription').val(textValue);
        });
    });
    $('#CashoutPage').live('pageinit', function () {
        $('#_cashoutButton').click(function () {
            var textValue = $('#wmd-preview').html();
            $('#_cashOut').val(textValue);
        });
    });

    $('#DailyMessagePage').live('pageshow', function () {
        $('#wmd-input').val($('#_dailyMessage').val());
        $('#wmd-preview').html($('#_dailyMessage').val());
        $('#_wmdEditor').detach().prependTo($('#_dailyMessagePlaceholder'));
    });
    $('#BuyInPage').live('pageshow', function () {
        $('#wmd-input').val($('#_buyIn').val());
        $('#wmd-preview').html($('#_buyIn').val());
        $('#_wmdEditor').detach().prependTo($('#_buyInPlaceholder'));
    });
    $('#WodPage').live('pageshow', function () {
        $('#wmd-input').val($('#_simpleDescription').val());
        $('#wmd-preview').html($('#_simpleDescription').val());
        $('#_wmdEditor').detach().prependTo($('#_wodPlaceholder'));
    });
    $('#CashoutPage').live('pageshow', function () {
        $('#wmd-input').val($('#_cashOut').val());
        $('#wmd-preview').html($('#_cashOut').val());
        $('#wmd-input').keydown();
        $('#_wmdEditor').detach().prependTo($('#_cashoutPlaceholder'));
    });
</script>
<div class="hidden-data">
    <?php //This hidden facebook div is used as a staging area to put the html of buy-in, wod, woddescription and cashout.
    // When I'm done here, I'll just take the text and post it on the wall'
    ?>
    <div id="_facebookTextDiv"></div>
    <div id="_wmdEditor">
        <div class="wmd-panel">
            <div id="wmd-button-bar"></div>
            <textarea class="wmd-input" id="wmd-input"></textarea>
        </div>
        <div id="wmd-preview" class="wmd-panel wmd-preview"></div>        
        <script type="text/javascript">
            (function () {
                var converter1 = Markdown.getSanitizingConverter();
                var editor1 = new Markdown.Editor(converter1);
                editor1.run();
            })();
        </script>
    </div>
</div>
<?php $this->load->helper('form'); ?>
<?php
if ($use_wizard):
    /* If using wizard, WOD will go like this:
     * 	ALL STAFF BUT NOT ADMIN ON SOCIAL MEDIA POST:
     * 		if user not logged in to facebook and twitter, prompt them to do that here
     * 		if box's fb page id and twitter id are not saved, warn them here
     * Pick day (tomorrow - Tuesday, January 7th, 2013, today - Monday, January 6th, 2013
     * Answer question:  Benchmark WOD?
     * 	If so, pick benchmark wod (and store that data in a var to populate later..
     * 	If not, get name of wod
     * Pick Score Type from predefined list (will already be set if user selected benchmark wod)
     * Enter Buy-in
     * Enter WOD (will be predefined if user selected benchmark wod
     * 	(will need to implement some basic markdown here...brs at the very least)
     * 	add a box that will "tweetify" the wod.  use this to tweet the wod
     * Enter Cash Out
     * 
     */
    ?>
    <?php echo $hidden_wod_info; ?>
    <div data-role="page" id="PickDayPage">
        <div data-role="header">
            <a href="<?php echo base_url(); ?>index.php/welcome/index/TRUE" data-ajax ="false" data-icon="home" data-iconpos="notext" data-direction="reverse">Home</a>
            <h1>Pick Day</h1>
        </div>
        <div data-role="fieldcontain">
            <ul data-role="listview" data-filter="false" data-divider-theme="d">
    <?php echo $pick_day_list; ?>
            </ul>
        </div>
    </div>
    <div data-role="page" id="SocialMediaConnectPage">
        <div data-role="header">
            <a href="#PickDayPage" data-icon="back" data-iconpos="notext" data-direction="reverse">Back</a>
            <h1>Social Media Connect</h1>
        </div>
        <div data-role="fieldcontain">
            <?php
            if (!$sm_package) {
                echo 'Consider subscribing to the Social Media Package.<br>';
                echo 'With it, you will be able to post your WODs to WOD-Minder and Facebook simultaneously.';
            } else {
                if (!$facebook_user)
                    echo 'There was a problem logging you into facebook.  Contact ray023@gmail.com';
                else {
                    //Sometimes, like with Moody Crossfit, they can log into facebook and neither the user name nor first_name are present (permission issue?).  idk.  but adding this to stop the error from being logged
                    if (isset($facebook_user['username']))
                        echo '<img src="https://graph.facebook.com/' . $facebook_user['username'] . '/picture">';
                    if (!$facebook_user['page_admin']) {


                        echo '<p>Could not find you as a page administrator.</p>';
                        echo '<p>You will not be able to post to Facebook until you are made a page administrator.</p>';
                    } else {
                        echo '<p>' . (!isset($facebook_user['first_name']) ? '' : $facebook_user['first_name']) . '.  You are connected to facebook and ready to post your wod.</p>';
                    }
                }
            }
            ?>
        </div>
            <?php if (!!$box_tier_dropdown): ?>
            <a href="#TierPage" data-role="button">Next</a>
            <?php else: ?>
            <a href="#DailyMessagePage" data-role="button">Next</a>
            <?php endif; ?>
    </div>
        <?php if (!!$box_tier_dropdown): ?>
        <div data-role="page" id="TierPage">
            <div data-role="header">
                <a href="#SocialMediaConnectPage" data-icon="back" data-iconpos="notext" data-direction="reverse">Back</a>
                <h1>Select Tier</h1>
            </div>
            <div data-role="fieldcontain">
                <ul data-role="listview" data-filter="false" data-theme="d" data-divider-theme="d">
        <?php echo $box_tier_list; ?>
                </ul>
            </div>
        </div>
    <?php endif; ?>
    <div data-role="page" id="DailyMessagePage">
        <div data-role="header">
            <a href="#SocialMediaConnectPage" data-icon="back" data-iconpos="notext" data-direction="reverse">Back</a>
            <h1>Daily Message</h1>
        </div>
        <div data-role="fieldcontain">
            <div id="_dailyMessagePlaceholder"></div>
        </div>
        <a href="#IsBenchmarkWodPage" id="_dailyMessageButton" data-role="button">Next</a>
    </div>
    <div data-role="page" id="IsBenchmarkWodPage">
        <div data-role="header">
            <a href="#DailyMessagePage" data-icon="back" data-iconpos="notext" data-direction="reverse">Back</a>
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
            <div id="_wodPlaceholder"></div>
        </div>
        <a href="#BuyInPage" id="_wodButton" data-role="button">Next</a>
    </div>
    <div data-role="page" id="BuyInPage">
        <div data-role="header">
            <a href="#PickScoretypePage" data-icon="back" data-iconpos="notext" data-direction="reverse">Back</a>
            <h1>Buy In</h1>
        </div>
        <div data-role="fieldcontain">
            <div id="_buyInPlaceholder"></div>
        </div>
        <a href="#CashoutPage" id="_buyInButton" data-role="button">Next</a>
    </div>

    <div data-role="page" id="CashoutPage">
        <div data-role="header">
            <a href="#BuyInPage" data-icon="back" data-iconpos="notext" data-direction="reverse">Back</a>
            <h1>Cashout</h1>
        </div>
        <div data-role="fieldcontain">
            <div id="_cashoutPlaceholder"></div>
        </div>
        <a href="#BoxWodSavePage" id="_cashoutButton" data-role="button">Review and Save</a>
    </div>
<?php endif; //Use Wizard IF  ?>
<div data-role="page" id="BoxWodSavePage">
    <div data-role="header">
<?php echo!$use_wizard ? '' : '<a href="#CashoutPage" data-icon="back" data-iconpos="notext" data-direction="reverse">Back</a>'; ?>
        <h1><?php echo $title; ?></h1>
    </div>	
    <div data-role="fieldcontain" class="ui-hide-label">	
        <div class ="good-messsage">
            <p><?php echo isset($good_message) ? $good_message : ''; ?></p>
        </div>
        <div class ="error-messsage">
            <p><?php echo isset($error_message) ? $error_message : ''; ?></p>
        </div>
    </div>
    <div data-role="fieldcontain">	

<?php
$select_label_attrib = array('class' => 'select');
$field_contain_div = '<div data-role="fieldcontain">';
$close_div = '</div>';
$attributes = array('id' => '_profileForm',
    'data-ajax' => 'false');
echo form_open_multipart('staff/save_box_wod_for_staff/' . $bw_id, $attributes);

echo '<input id="_formUniqid" type="hidden" value="' . $form_uniqid . '" name="form_uniqid">';
echo '<input id="_facebook_text" type="hidden" value="" name="facebook_text">';

//Tier
if (!!$box_tier_dropdown) {
    echo $field_contain_div .
    form_label('Tier:', '_tier', $select_label_attrib) .
    $box_tier_dropdown .
    $close_div;
}

//Daily Message Div	
echo $field_contain_div .
 form_label('Daily Message:', '_dailyMessage') .
 form_textarea($daily_message) .
 $close_div;

echo $field_contain_div .
 '<fieldset data-role="controlgroup">
				<legend>Daily Image Source:</legend>
					<input type="radio" name="image-source" id="radio-choice-21" value="pc" checked="checked" />
					<label for="radio-choice-21">Upload from my device</label>
					<input type="radio" name="image-source" id="radio-choice-22" value="web"  />
					<label for="radio-choice-22">Paste link from the web</label>
				</fieldset>' .
 $close_div;
//Daily Image
$full_image_name = base_url() . '/staff_images/box_wod/' . ($image_name['value'] === '' ? 'no_image_filler.jpg' : $image_name['value']);
echo '<div id="_imageUploadDiv">';
echo $field_contain_div .
 form_label('Daily Image:', '_imageName') .
 '<input type="file" id ="_imageName" name="userfile" />' .
 $close_div;
echo $field_contain_div .
 '<img id="_dailyImage" src="' . $full_image_name . '" />' .
 $close_div;
echo '</div>';

//Daily Image Link
echo '<div id="_imageLinkDiv">';
echo $field_contain_div .
 form_label('Daily Image:', '_imageLink') .
 '<input id ="_imageLink" name="image_link" placeholder="Paste Image URL here"/>' .
 $close_div;
echo '</div>';

//Image Caption
echo $field_contain_div .
 form_label('Image Caption:', '_imageCaption') .
 form_textarea($image_caption) .
 $close_div;


//WOD Date Div
echo $field_contain_div .
 form_label('WOD Date:', '_wodDate') .
 form_input($wod_date) .
 $close_div;

//Benchmark Wod (if applicable)
echo $field_contain_div .
 form_label('Benchmark WOD (if applicable):', '_benchmarkWod', $select_label_attrib) .
 $benchmark_wod_dropdown .
 $close_div;

//Score Type Div
echo $field_contain_div .
 form_label('Score Type:', '_scoreType', $select_label_attrib) .
 $score_type_dropdown .
 $close_div;

//Scale Div
echo $field_contain_div .
 form_label('Scale:', '_scale', $select_label_attrib) .
 $scale_dropdown .
 $close_div;


//WOD Name
echo $field_contain_div .
 form_label('WOD Name:', '_simpleTitle') .
 form_input($simple_title) .
 $close_div;

//Buy In Div
echo $field_contain_div .
 form_label('Buy In:', '_buyIn') .
 form_textarea($buy_in) .
 $close_div;

//WOD
echo $field_contain_div .
 form_label('WOD:', '_simpleDescription') .
 form_textarea($simple_description) .
 $close_div;

//Cash Out
echo $field_contain_div .
 form_label('Cash Out:', '_cashOut') .
 form_textarea($cash_out) .
 $close_div;

echo '<div data-role="fieldcontain">' .
 form_label('Post To Facebook:', '_postToFacebook') .
 '<select id="_postToFacebook" name ="post_to_facebook" data-role="slider">
					<option value="no">No</option>
					<option selected="selected" value="yes">Yes</option>
				</select> 
			</div>';
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
echo $field_contain_div .
 anchor('welcome/index/TRUE', 'Cancel', array('data-ajax' => 'false',
    'data-role' => 'button',
    'data-inline' => 'true')) .
 form_button($submit) .
 $close_div;


echo form_close();
?>
    </div>
</div>