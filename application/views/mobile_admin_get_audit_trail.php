<div data-role="page">
	<div data-role="header">
		<h1>Audit Trail</h1>
	</div>
	<div class="content-primary">	
		<div class="ui-grid-b">
			<div class="ui-block-a mobile-grid-header">Date</div>
			<div class="ui-block-b mobile-grid-header">Member</div>
			<div class="ui-block-c mobile-grid-header ">Action</div>
			<?php echo $audit_log_list;?>
		</div>
	</div><!--/content-primary -->	
</div>