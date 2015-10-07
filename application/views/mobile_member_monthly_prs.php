<div data-role="page" id="PrPicker">
	<div data-role="header">
		<a href="<?php echo base_url().'index.php'; ?>" data-icon="home" data-ajax="false" data-iconpos="notext" data-direction="reverse">Home</a>
		<h1><?php echo $title; ?></h1>
	</div>
	<div class="content-primary">	
		<ul data-role="listview" data-theme="d" data-divider-theme="d">
			<?php echo $pr_month_list;?>
		</ul>
	</div><!--/content-primary -->	
</div>
<?php echo $pr_details;?>