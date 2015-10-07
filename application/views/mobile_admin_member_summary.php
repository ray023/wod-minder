<div data-role="page">
	<div data-role="header">
		<h1><?php echo $title; ?></h1>
	</div>
		<div class="content-primary">	
		<?php if (!$member_list):?>
			<?php echo $member_html;?>
			<a href="<?php echo base_url().'index.php/administration_functions/destroy_user/'.$member_id;?>" data-ajax ="false" data-role="button" onclick="return confirm('Are you SURE about this?\nMaxes, WODs and Weight Log will be removed for this person.');">Complete erase this member and their history</a>
		<?php else :?>
			<div class="content-primary">	
				<ul data-role="listview" data-filter="true" data-filter-placeholder="Search members..." data-filter-theme="d"data-theme="d" data-divider-theme="d">
					<?php echo $member_list;?>
				</ul>
			</div><!--/content-primary -->	
			<?php endif; ?>
		</div><!--/content-primary -->	
</div>