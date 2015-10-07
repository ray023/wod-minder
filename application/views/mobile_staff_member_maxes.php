<div data-role="page" id="MaxPicker">
	<div data-role="header">
		<a href="<?php echo base_url().'index.php'; ?>" data-icon="home" data-ajax="false" data-iconpos="notext" data-direction="reverse">Home</a>
		<h1><?php echo $title; ?></h1>
	</div>
	<div class="content-primary">	
		<ul data-role="listview" data-filter="true" data-filter-placeholder="Pick an Exercise..." data-filter-theme="d" data-theme="d" data-divider-theme="d">
			<?php echo $member_exercises;?>
		</ul>
	</div><!--/content-primary -->	
</div>
<?php echo $member_max_details;?>