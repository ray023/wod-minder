<div data-role="page">
	<div data-role="header">
		<a href="<?php echo base_url(); ?>index.php/welcome/index/TRUE" data-ajax ="false" data-icon="home" data-iconpos="notext" data-direction="reverse">Home</a>
		<h1><?php echo $title; ?></h1>
	</div>
        <h2>Eligible Members</h2>
	<div class="ui-grid-a">
		<div class="ui-block-a mobile-grid-header">Name</div>
		<div class="ui-block-b mobile-grid-header ">Box</div>
		<?php echo $eligible_members_grid;?>
	</div><!-- /grid-c -->
</div>
