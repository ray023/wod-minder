<?php $this->load->helper('form'); ?>
<div data-role="page">
	<div data-role="header">
		<a href="<?php echo base_url(); ?>index.php/welcome/index/TRUE" data-ajax ="false" data-icon="home" data-iconpos="notext" data-direction="reverse">Home</a>
		<h1><?php echo $title;?></h1>
	</div>
        <h1>General Counts</h1>
	<div class="ui-grid-a">
		<div class="ui-block-a mobile-grid-header">Metric</div>
		<div class="ui-block-b mobile-grid-header date-block">Count</div>
		<?php echo $site_count_grid;?>
	</div><!-- /grid-a -->
        <h1>Active Member Counts</h1>
        <p>This is a distinct count of members who have saved a record in WM for the past 21 days</p>
	<div class="ui-grid-a">
		<div class="ui-block-a mobile-grid-header">Metric</div>
		<div class="ui-block-b mobile-grid-header date-block">Count</div>
		<?php echo $active_member_count_grid;?>
	</div><!-- /grid-a -->
        <h1>Power Users</h1>
        <p>A list of all users and their wod counts for the past 21 days.</p>
		<p>Planned to limit this to users with just 50 or more wods saved, but usage is so low right now it doesn't matter.</p>
	<div class="ui-grid-b">
		<div class="ui-block-a mobile-grid-header">Name</div>
		<div class="ui-block-b mobile-grid-header">Box</div>
		<div class="ui-block-c mobile-grid-header date-block">WOD Count</div>
		<?php echo $power_user_count_grid;?>
	</div><!-- /grid-a -->
        <h1>Inactive Power Users</h1>
		<p>Users with a 50+ wod count who haven't saved a wod in over 21 days.</p>
	<div class="ui-grid-c">
		<div class="ui-block-a mobile-grid-header">Name</div>
		<div class="ui-block-b mobile-grid-header">Box</div>
		<div class="ui-block-c mobile-grid-header date-block">Date</div>
		<div class="ui-block-d mobile-grid-header date-block">WOD Count</div>
		<?php echo $inactive_power_user_count_grid;?>
	</div><!-- /grid-a -->
	<div>
	<?php	echo anchor('welcome/index/TRUE', 'Return', array(	'data-ajax'=>'false',
															'data-role'=>'button',
															'data-inline'=>'true'));
	?>
	</div>
</div>
