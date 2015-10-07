<?php $this->load->helper('form'); ?>
<!-- Start Main -->
<div data-role="page" id="Main">
    <div data-role="header">
        <?php
        echo anchor('member/update', 'My Profile', array('data-ajax' => 'false',
            'class' => 'ui-btn-left',
            'data-icon' => 'gear',
            'data-iconpos' => 'notext'
        ));
        ?>
        <h1>WOD Minder</h1>
        <?php
        echo anchor('member/logout', 'Logout', array('data-ajax' => 'false',
            'class' => 'ui-btn-right',
        ));
        ?>
    </div><!-- /header -->
    <div data-role="content">
        <div id="goodMessage" class ="good-messsage">
            <p><?php echo isset($good_message) ? $good_message : ''; ?></p>
        </div>
        <div id="badMessage" class ="error-messsage">
            <p><?php echo isset($error_message) ? $error_message : ''; ?></p>
        </div>
        <?php if ($site_admin && $error_logs !== ''): ?>
            <p>
                <?php echo anchor('#ErrorLog', 'ERRORS', array('data-role' => 'button')); ?>	
            </p>			
            <?php endif; ?>
        <?php if ($display_todays_wod): ?>
            <p>
                <?php echo anchor('wod/todays_wod', 'Today\'s WOD', array('data-ajax' => 'false',
                    'data-role' => 'button'));
                ?>	
            </p>
            <?php endif; ?>
        <p>
        <?php echo anchor('#WOD', 'WOD', array('data-role' => 'button')); ?>	
        </p>
        <p>
            <?php echo anchor('#Max', 'Max', array('data-role' => 'button')); ?>	
        </p>
        <?php if ($is_competitor): ?>
            <p>
                <?php echo anchor('event/select_event/member', 'Event', array('data-ajax' => 'false',
                    'data-role' => 'button'));
                ?>	
            </p>
            <?php endif; ?>
        <p>
            <?php echo anchor('#WeightJournal', 'Weight Journal', array('data-role' => 'button')); ?>	
        </p>
        <p>
            <?php //Hiding paleo for now because feature sucks and nobody uses it
            //echo anchor('#Paleo', 'Paleo', array('data-role'=>'button'));
            ?>	
        </p>
        <p>
        <?php echo anchor('#BarbellCalculator', 'Barbell Calculator', array('data-role' => 'button')); ?>	
        </p>
        <p>
        <?php echo anchor('report', 'My CrossFit Summary', array('data-ajax' => 'false', 'data-theme' => 'e',
            'data-role' => 'button'));
        ?>	
        </p>
        <!-- Future Home of Find-A-Fit
        <p>-->
        <?php //echo anchor('member/update', 'My Profile', array(	'data-ajax'=>'false',
        //'data-role'=>'button'));
        ?>	
        <!--</p>-->
        <?php if ($member_is_staff): ?>
            <p>
    <?php echo anchor('#StaffFunctions', 'Staff Functions', array('data-role' => 'button')); ?>	
            </p>					
<?php endif; ?>
<?php if ($site_admin): ?>
            <p>
    <?php echo anchor('#AdminFunctions', 'Administrator Functions', array('data-role' => 'button')); ?>	
            </p>			
<?php endif; ?>
    </div><!-- /content Main -->
</div><!-- /page Main-->


<!-- Paleo -->
<div data-role="page" id="Paleo">

    <div data-role="header">
        <a href="#Main" data-icon="home" data-iconpos="notext" data-direction="reverse">Home</a>
        <h1>Paleo</h1>
    </div><!-- /header -->

    <div data-role="content">	
        <p>
            <?php echo anchor('paleo/save_member_paleo_meal', 'Record Paleo Meal', array('data-ajax' => 'false',
                'data-role' => 'button'));
            ?>	
        </p>
        <p>
<?php echo anchor('paleo/get_user_paleo_history', 'Paleo History', array('data-ajax' => 'false',
    'data-role' => 'button'));
?>	
        </p>
        <p>
<?php echo anchor('member/email_paleo_data', 'Email Data', array('data-ajax' => 'false',
    'data-role' => 'button'));
?>	
        </p>
        <p>
<?php echo anchor('#Main', 'Return', array('data-role' => 'button',
    'data-inline' => 'true'));
?>	
        </p>
    </div><!-- content, Paleo -->
</div><!-- /page Paleo-->


<!-- WOD -->
<div data-role="page" id="WOD">

    <div data-role="header">
        <a href="#Main" data-icon="home" data-iconpos="notext" data-direction="reverse">Home</a>
        <h1>WOD</h1>
    </div><!-- /header -->

    <div data-role="content">	
        <p>
<?php echo anchor('#RecordWodScoreMenu', 'Record WOD Score', array('data-role' => 'button')); ?>	
        </p>
        <p>
<?php echo anchor('#WodHistoryMenu', 'WOD History', array('data-role' => 'button')); ?>	
        </p>
        <p>
<?php echo anchor('member/email_wod_data', 'Email Data', array('data-ajax' => 'false',
    'data-role' => 'button'));
?>	
        </p>
    </div><!-- content, WOD -->
</div><!-- /page WOD-->

<!-- Max -->
<div data-role="page" id="Max">

    <div data-role="header">
        <a href="#Main" data-icon="home" data-iconpos="notext" data-direction="reverse">Home</a>
        <h1>Max</h1>
    </div><!-- /header -->

    <div data-role="content">	
        <p>
<?php echo anchor('#RecordNewMax', 'Record New Max', array('data-role' => 'button')); ?>	
        </p>
        <p>
<?php echo anchor('#MaxSnapshot', 'Max Snapshot', array('data-role' => 'button')); ?>	
        </p>
        <p>
<?php echo anchor('#MaxHistory', 'Max History', array('data-role' => 'button')); ?>	
        </p>
        <p>
<?php echo anchor('exercise/get_user_max_pr_board', 'Monthly PR\'s', array('data-ajax' => 'false', 'data-role' => 'button')); ?>	
        </p>
        <p>
<?php echo anchor('member/email_max_data', 'Email Data', array('data-ajax' => 'false',
    'data-role' => 'button'));
?>	
        </p>                
    </div><!-- content, Max -->
</div><!-- /page Max-->


<!-- RecordNewMax -->
<div data-role="page" id="RecordNewMax">

    <div data-role="header" data-theme="f">
        <h1>Search filter bar</h1>
        <a href="#Main" data-icon="home" data-iconpos="notext" data-direction="reverse">Home</a>
    </div><!-- /header -->
    <div data-role="content">		
        <div class="content-primary">	
            <ul data-role="listview" data-filter="true" data-filter-placeholder="Search exercises..." data-filter-theme="d" data-theme="d" data-divider-theme="d">
<?php echo $exercise_list; ?>
            </ul>
        </div><!--/content-primary -->		
    </div><!-- content, RecordNewMax -->
</div><!-- /page, RecordNewMax -->

<!-- MaxSnapshot -->
<div data-role="page" id="MaxSnapshot">
    <div data-role="header">
        <h1>Max Snapshot</h1>
        <a href="#Main" data-icon="home" data-iconpos="notext" data-direction="reverse">Home</a>
    </div><!-- /header -->
    <p>
        This is a snapshot showing the best weight, rep or time for all recorded exercises.
    </p>
    <div data-role="fieldcontain">	
        <label for="_singleRepMaxSlider">Change Single-Rep Max Percentage:</label>
        <input type="range" id="_singleRepMaxSlider" value="100" min="0" max="100" step="5" />
    </div>	
    <div data-role="fieldcontain">	
<?php echo isset($single_rep_max_snapshot) ? $single_rep_max_snapshot : 'No single rep max snapshot'; ?>
    </div>
    <div data-role="fieldcontain">	
            <?php echo isset($user_max_snapshot) ? $user_max_snapshot : ''; ?>
    </div>
                <?php
                echo anchor('#Main', 'Return', array('data-direction' => 'reverse',
                    'data-role' => 'button',
                    'data-inline' => 'true'));
                ?>
</div><!-- /page, MaxSnapshot -->

<!-- MaxHistory -->
<div data-role="page" id="MaxHistory">
    <div data-role="header">
        <h1>Max History</h1>
        <a href="#Main" data-icon="home" data-iconpos="notext" data-direction="reverse">Home</a>
    </div><!-- /header -->
    <div data-role="content">		
        <div class="content-primary">
<?php if (isset($user_max_history)): ?>
                <ul data-role="listview" data-filter="true" data-filter-placeholder="Search exercises..." data-filter-theme="d" data-theme="d" data-divider-theme="d">
    <?php echo $user_max_history; ?>
                </ul>
<?php else: ?>
                <p>
                    No lift history
                </p>
            <?php endif; ?>
        </div><!--/content-primary -->		
    </div><!-- content,  -->	
<?php
echo anchor('#Main', 'Return', array('data-direction' => 'reverse',
    'data-role' => 'button',
    'data-inline' => 'true'));
?>
</div><!-- /page, MaxHistory -->

<!-- Weight Journal -->
<div data-role="page" id="WeightJournal">

    <div data-role="header">
        <a href="#Main" data-icon="home" data-iconpos="notext" data-direction="reverse">Home</a>
        <h1>Weight Journal</h1>
    </div><!-- /header -->

    <div data-role="content">	
        <p>
<?php echo anchor(base_url() . 'index.php/weight/save_member_weight/', 'Record New Weight', array('data-ajax' => 'false',
    'data-role' => 'button'));
?>		
        </p>
        <p>
<?php echo anchor(base_url() . 'index.php/weight/get_user_weight_history/', 'Weight History', array('data-ajax' => 'false',
    'data-role' => 'button'));
?>	
        </p>
        <p>
<?php echo anchor('#Main', 'Return', array('data-role' => 'button',
    'data-inline' => 'true'));
?>	
        </p>
    </div><!-- content, Weight Journal -->
</div><!-- /page Weight Journal-->

<!-- BarbellCalculator -->
<div data-role="page" id="BarbellCalculator">
    <div data-role="header">
        <h1>Barbell Calculator</h1>
        <a href="#Main" data-icon="home" data-iconpos="notext" data-direction="reverse">Home</a>
    </div><!-- /header -->
    <div class="center-wrapper barbell-weight-display"><h1 id="_calculatedWeight">Calculated on Load</h1></div>
    <div class="bar-canvas">
        <canvas id="_barCanvas"></canvas>
    </div>
    <div class="ui-grid-b">
        <div class="ui-block-a">
            <div data-role="fieldcontain">
                <select id="_barbellBase" name ="barbell-base" data-role="slider">
                    <option value="33">33 lb.</option>
                    <option selected="selected" value="45">45 lb.</option>
                </select> 
            </div>
        </div>
        <div class="ui-block-b"><a href="#" id="_resetWeight" data-role="button">Reset</a></div>
        <div class="ui-block-c"><a href="#" id="_setNewMax" data-role="button">Max</a></div>
    </div><!-- /grid-b -->

    <div data-role="fieldcontain">	
                <?php echo $barbell_grid; ?>
    </div>
                <?php
                echo anchor('#Main', 'Return', array('data-direction' => 'reverse',
                    'data-role' => 'button',
                    'data-inline' => 'true'));
                ?>
</div><!-- /page, BarbellCalculator -->

<!-- RecordWodScoreMenu -->
<div data-role="page" id="RecordWodScoreMenu">

    <div data-role="header" data-theme="f">
        <div data-role="header">
            <h1>Record WOD Menu</h1>
            <a href="#Main" data-icon="home" data-iconpos="notext" data-direction="reverse">Home</a>
        </div><!-- /header -->
    </div><!-- /header -->
    <div data-role="content">		
        <div class="content-primary">
            <p>
            <?php echo anchor('#SaveBoxWod', 'From My Box', array('data-role' => 'button')); ?>	
            </p>
            <p>
<?php echo anchor('#BenchmarkWodMenu', 'Benchmark WODs', array('data-role' => 'button')); ?>
            </p>
            <p>
<?php echo anchor(base_url() . 'index.php/wod/save_member_custom_wod/', 'Custom WOD', array('data-ajax' => 'false',
    'data-role' => 'button'));
?>	
            </p>
        </div><!--/content-primary -->		
    </div><!-- content, RecordWodScoreMenu -->
</div><!-- /page, RecordWodScoreMenu -->

<!-- BenchmarkWodMenu -->
<div data-role="page" id="BenchmarkWodMenu">
    <div data-role="header" data-theme="f">
        <div data-role="header">
            <h1>Benchmark WOD</h1>
            <a href="#Main" data-icon="home" data-iconpos="notext" data-direction="reverse">Home</a>
        </div><!-- /header -->
    </div><!-- /header -->
    <div data-role="content">		
        <div class="content-primary">
<?php echo anchor('#AllBenchmarkWoDs', 'Pick by WoD Name', array('data-role' => 'button')); ?>
<?php echo anchor(base_url() . 'index.php/wod/wod_wizard/', 'Pick by Movement', array('data-ajax' => 'false',
    'data-role' => 'button'));
?>	
        </div><!--/content-primary -->		
    </div><!-- content, BenchmarkWodMenu -->
</div><!-- /page, BenchmarkWodMenu -->

<!-- AllBenchmarkWoDs -->
<div data-role="page" id="AllBenchmarkWoDs">

    <div data-role="header" data-theme="f">
        <h1>Search filter bar</h1>
        <a href="#Main" data-icon="home" data-iconpos="notext" data-direction="reverse">Home</a>
    </div><!-- /header -->
    <div data-role="content">		
        <div class="content-primary">	
            <ul data-role="listview" data-filter="true" data-filter-placeholder="Pick a WOD..." data-filter-theme="d" data-theme="d" data-divider-theme="d">
<?php echo $benchmark_wod_list; ?>
            </ul>
        </div><!--/content-primary -->		
    </div><!-- content, AllBenchmarkWoDs -->
</div><!-- /page, AllBenchmarkWoDs -->


<!-- SaveBoxWod -->
<div data-role="page" id="SaveBoxWod">

    <div data-role="header" data-theme="f">
        <h1>Search filter bar</h1>
        <a href="#Main" data-icon="home" data-iconpos="notext" data-direction="reverse">Home</a>
    </div><!-- /header -->
    <div data-role="content">		
        <div class="content-primary">	
            <ul data-role="listview" data-filter="true" data-filter-placeholder="Pick a WOD..." data-filter-theme="d" data-theme="d" data-divider-theme="d">
                <?php echo $box_wod_list; ?>
            </ul>
        </div><!--/content-primary -->		
    </div><!-- content, SaveBoxWod -->
</div><!-- /page, SaveBoxWod -->


<div data-role="page" id="WodHistoryMenu">
    <div data-role="header" data-theme="f">
        <div data-role="header">
            <h1>WOD History Menu</h1>
            <a href="#Main" data-icon="home" data-iconpos="notext" data-direction="reverse">Home</a>
        </div><!-- /header -->
    </div><!-- /header -->
    <div data-role="content">		
        <div class="content-primary">
            <p>
<?php echo anchor('#MyBoxHistory', 'My Box History', array('data-role' => 'button')); ?>	
            </p>
            <p>
<?php echo anchor('#CustomWODHistory', 'Custom WODs', array('data-role' => 'button')); ?>	
            </p>
            <p>
                <?php echo anchor('#BenchmarkWodHistory', 'Benchmark WODs', array('data-role' => 'button')); ?>	
            </p>
            <p>
<?php echo anchor('wod/search', 'Search', array('data-ajax' => 'false',
    'data-role' => 'button'));
?>	
            </p>
            <p>
<?php echo anchor('#Main', 'Return', array('data-role' => 'button',
    'data-inline' => 'true'));
?>	
            </p>
        </div><!--/content-primary -->		
    </div><!-- content, RecordWodScoreMenu -->
</div>
<!-- CustomWodHistory -->
<div data-role="page" id="CustomWODHistory">

    <div data-role="header">
        <a href="#Main" data-icon="home" data-iconpos="notext" data-direction="reverse">Home</a>
        <h1>Custom WOD History</h1>
    </div>
    <div data-role="content">		
        <div class="content-primary">	
            <div class="ui-grid-c">
                <div class="ui-block-a mobile-grid-header">&nbsp;</div>
                <div class="ui-block-b mobile-grid-header date-block">Date</div>
                <div class="ui-block-c mobile-grid-header number-block">WOD</div>
                <div class="ui-block-d mobile-grid-header number-block">Score</div>
<?php echo isset($user_custom_wod_history) ? $user_custom_wod_history : 'No WODs saved yet'; ?>
            </div><!-- /grid-c -->
        </div>
        <!--/content-primary -->		
    </div><!-- content, CustomWodHistory -->
</div><!-- /page, CustomWodHistory -->
<!-- MyBoxHistory -->
<div data-role="page" id="MyBoxHistory">

    <div data-role="header">
        <a href="#Main" data-icon="home" data-iconpos="notext" data-direction="reverse">Home</a>
        <h1>WOD History</h1>
    </div>
    <div data-role="content">		
        <div class="content-primary">	
            <div class="ui-grid-c">
                <div class="ui-block-a mobile-grid-header">&nbsp;</div>
                <div class="ui-block-b mobile-grid-header date-block">Date</div>
                <div class="ui-block-c mobile-grid-header number-block">WOD</div>
                <div class="ui-block-d mobile-grid-header number-block">Score</div>
<?php echo isset($user_box_wod_history) ? $user_box_wod_history : 'No WODs saved yet'; ?>
            </div><!-- /grid-c -->
        </div>
        <!--/content-primary -->		
    </div><!-- content, MyBoxHistory -->
</div><!-- /page, MyBoxHistory -->

<!-- BenchmarkWodHistory -->
<div data-role="page" id="BenchmarkWodHistory">

    <div data-role="header">
        <a href="#Main" data-icon="home" data-iconpos="notext" data-direction="reverse">Home</a>
        <h1>Benchmark WOD History</h1>
    </div>
    <div data-role="content">		
        <div class="content-primary">	
<?php if (isset($user_benchmark_wod_history)): ?>
                <ul data-role="listview" data-filter="true" data-filter-placeholder="Search benchmark WODs..." data-filter-theme="d" data-theme="d" data-divider-theme="d">
    <?php echo $user_benchmark_wod_history; ?>
                </ul>
<?php else: ?>
                <p>
                    No benchmark WODs saved
                </p>
<?php endif; ?>
        </div>
        <!--/content-primary -->		
    </div><!-- content, BenchmarkWodHistory -->
</div><!-- /page, BenchmarkWodHistory -->

            <?php if ($site_admin): ?>
    <!-- ErrorLogs -->
    <div data-role="page" id="ErrorLog">
        <div data-role="header">
            <h1>Admin Functions</h1>
            <a href="#Main" data-icon="home" data-iconpos="notext" data-direction="reverse">Home</a>
        </div><!-- /header -->
        <div data-role="fieldcontain">	
                <?php echo $error_logs; ?>		

    <?php
    echo anchor('#Main', 'Return', array('data-direction' => 'reverse',
        'data-role' => 'button',
        'data-inline' => 'true'));
    ?>
        </div>

    </div><!-- /page, ErrorLogs -->
    <!-- AdminFunctions -->
    <div data-role="page" id="AdminFunctions">
        <div data-role="header">
            <h1>Admin Functions</h1>
            <a href="#Main" data-icon="home" data-iconpos="notext" data-direction="reverse">Home</a>
        </div><!-- /header -->
        <div data-role="fieldcontain">	
            <p>
                <?php echo anchor('#FindAFit', 'Find A Fit', array('data-role' => 'button')); ?>
            </p>
            <p>
    <?php echo anchor('administration_functions/save_box_wod', 'Save Box WOD', array('data-ajax' => 'false',
        'data-role' => 'button'));
    ?>	
            </p>
            <p>
                <?php echo anchor('administration_functions/edit_box_wod', 'Edit Box WOD', array('data-ajax' => 'false',
                    'data-role' => 'button'));
                ?>	
            </p>
            <p>
            <?php
            echo anchor('administration_functions/database_backup', 'Backup Database', array('data-ajax' => 'false',
                'data-role' => 'button',
                'target' => 'new_window'));
            ?>	
            </p>
            <p>
    <?php echo anchor('administration_functions', 'Set member staff', array('data-ajax' => 'false',
        'data-role' => 'button'));
    ?>	
            </p>
            <p>
    <?php echo anchor('administration_functions/member_summary', 'Member Summary', array('data-ajax' => 'false',
        'data-role' => 'button'));
    ?>	
            </p>
            <p>
                <?php echo anchor('#RecentSiteActivity', 'Recent Site Activity', array('data-role' => 'button')); ?>	
            </p>
            <p>
                <?php echo anchor('welcome/site_counts', 'Site Counts', array('data-ajax' => 'false',
                    'data-role' => 'button'));
                ?>	
            </p>
            <p>
    <?php echo anchor('administration_functions/save_benchmark_wod', 'Save CrossFit Benchmark WOD', array('data-ajax' => 'false',
        'data-role' => 'button'));
    ?>	
            </p>
            <p>
    <?php echo anchor('administration_functions/select_benchmark_wod', 'Edit CrossFit Benchmark WOD', array('data-ajax' => 'false',
        'data-role' => 'button'));
    ?>	
            </p>
            <p>
    <?php echo anchor('administration_functions/view_audit_trail', 'View Audit Trail', array('data-ajax' => 'false',
        'data-role' => 'button'));
    ?>	
            </p>
    <?php
    echo anchor('#Main', 'Return', array('data-direction' => 'reverse',
        'data-role' => 'button',
        'data-inline' => 'true'));
    ?>
        </div>

    </div><!-- /page, AdminFunctions -->

    <!-- FindAFit -->
    <div data-role="page" id="FindAFit">

        <div data-role="header">
            <a href="#Main" data-icon="home" data-iconpos="notext" data-direction="reverse">Home</a>
            <h1>FindAFit</h1>
        </div><!-- /header -->

        <div data-role="content">	
            <p>
    <?php echo anchor('find_a_fit/', 'Find A Fit', array('data-ajax' => 'false',
        'data-role' => 'button'));
    ?>	
            </p>
            <p>
    <?php echo anchor('find_a_fit/show_history', 'History', array('data-ajax' => 'false',
        'data-role' => 'button'));
    ?>	
            </p>
        </div><!-- content, FindAFit -->
    </div><!-- /page FindAFit-->


    <!-- Events -->
    <div data-role="page" id="Events">

        <div data-role="header">
            <a href="#Main" data-icon="home" data-iconpos="notext" data-direction="reverse">Home</a>
            <h1>Events</h1>
        </div><!-- /header -->

        <div data-role="content">	
            <p>
    <?php echo anchor('event/save_event', 'Save Event', array('data-ajax' => 'false',
        'data-role' => 'button'));
    ?>	
            </p>
            <p>
                <?php echo anchor('event/select_event/event', 'Edit Event', array('data-ajax' => 'false',
                    'data-role' => 'button'));
                ?>	
            </p>
            <p>
            <?php echo anchor('event/save_event_wod', 'Save Event WOD', array('data-ajax' => 'false',
                'data-role' => 'button'));
            ?>	
            </p>
            <p>
    <?php echo anchor('event/select_event_wod', 'Edit Event WOD', array('data-ajax' => 'false',
        'data-role' => 'button'));
    ?>	
            </p>
            <p>
                <?php echo anchor('event/publish', 'Publish Events', array('data-ajax' => 'false',
                    'data-role' => 'button'));
                ?>	
            </p>
        </div><!-- content, Events -->
    </div><!-- /page Events-->

    <!-- RecentSiteActivity -->
    <div data-role="page" id="RecentSiteActivity">

        <div data-role="header">
            <a href="#Main" data-icon="home" data-iconpos="notext" data-direction="reverse">Home</a>
            <h1>Recent Site Activity</h1>
        </div><!-- /header -->

        <div data-role="content">	
    <?php echo $site_stats; ?>
        </div><!-- content, RecentSiteActivity -->
    </div><!-- /page RecentSiteActivity-->
            <?php endif; ?> <!--End of Admin Section-->


<?php if ($member_is_staff): ?>
    <!-- StaffFunctions -->
    <div data-role="page" id="StaffFunctions">

        <div data-role="header">
            <a href="#Main" data-icon="home" data-iconpos="notext" data-direction="reverse">Home</a>
            <h1>My Box</h1>
        </div><!-- /header -->

        <div data-role="content">
            <p>
    <?php echo anchor('staff/save_box_wod_for_staff', 'Save Box WOD', array('data-ajax' => 'false',
        'data-role' => 'button'));
    ?>	
            </p>
            <p>
            <?php echo anchor('staff/edit_box_wod_for_staff', 'Edit Box WOD', array('data-ajax' => 'false',
                'data-role' => 'button'));
            ?>	
            </p>
    <?php //Special Exception for Katie who will be entering the wods while  ?>
    <?php if ($this->session->userdata('member_id') != '241'): ?>
                <p>
        <?php echo anchor('#BoxStats', 'Box Stats', array('data-role' => 'button')); ?>	
                </p>
                <p>
        <?php echo anchor('staff/daily_wods', 'Daily WOD Results', array('data-ajax' => 'false',
            'data-role' => 'button'));
        ?>	
                </p>
                <p>
                    <?php echo anchor('#Leaderboard', 'Leaderboard', array('data-role' => 'button')); ?>	
                </p>
                <p>
                    <?php echo anchor('#TrainingLog', 'Training Log', array('data-role' => 'button')); ?>	
                </p>
                <p>
                    <?php echo anchor($facebook_link, 'Testing Facebook', array('data-role' => 'button', 'data-theme' => 'b')); ?>	
                </p>
    <?php endif; ?>
        </div><!-- content, StaffFunctions -->
    </div><!-- /page, StaffFunctions -->

    <!--Training Log-->
    <div data-role="page" id="TrainingLog">

        <div data-role="header">
            <a href="#StaffFunctions" data-icon="back" data-iconpos="notext" data-direction="reverse">Home</a>
            <h1>Training Log</h1>
        </div><!-- /header -->

        <div data-role="content">
            <p>
    <?php echo anchor('staff/save_staff_training_log', 'Save Training', array('data-ajax' => 'false',
        'data-role' => 'button'));
    ?>	
            </p>
            <p>
    <?php echo anchor('staff/get_staff_training_log_history', 'Edit Training', array('data-ajax' => 'false',
        'data-role' => 'button'));
    ?>	
            </p>
        </div><!-- content, Training Log -->
    </div><!-- /page, Training Log -->

    <!-- BoxStats -->
    <div data-role="page" id="BoxStats">

        <div data-role="header">
            <a href="#StaffFunctions" data-icon="back" data-iconpos="notext" data-direction="reverse">Home</a>
            <h1>Basic Stats</h1>
        </div><!-- /header -->

        <div data-role="content">
    <?php echo $box_stats; ?>
        </div><!-- content, BoxStats -->
    </div><!-- /page, BoxStats -->
    <!-- Leaderboard -->
    <div data-role="page" id="Leaderboard">

        <div data-role="header">
            <a href="#StaffFunctions" data-icon="back" data-iconpos="notext" data-direction="reverse">Home</a>
            <h1>Leaderboard</h1>
        </div><!-- /header -->

        <div data-role="content">
            <p>
    <?php echo anchor('staff/member_maxes', 'Maxes', array('data-ajax' => 'false',
        'data-role' => 'button'));
    ?>	
            </p>
            <p>
    <?php echo anchor('staff/box_leader_board', 'Benchmark WODs', array('data-ajax' => 'false',
        'data-role' => 'button'));
    ?>	
            </p>
        </div><!-- content, Leaderboard -->
    </div><!-- /page, Leaderboard -->

<?php endif; ?>