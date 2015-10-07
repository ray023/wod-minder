<div data-role="page" id="WODPicker">
	<div data-role="header">
		<a href="<?php echo base_url().'index.php'; ?>" data-icon="home" data-ajax="false" data-iconpos="notext" data-direction="reverse">Home</a>
		<h1><?php echo $title; ?></h1>
	</div>
	<div class="content-primary">	
		<ul data-role="listview" data-filter="true" data-filter-placeholder="Pick a WOD..." data-filter-theme="d" data-theme="d" data-divider-theme="d">
			<?php echo $wod_list;?>
		</ul>
	</div><!--/content-primary -->	
</div>
<?php echo $wod_details;?>