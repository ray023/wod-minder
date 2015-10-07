<div data-role="page" id="DailyWods">

<div data-role="header" data-theme="f">
		<div data-role="header">
			<h1>Select Today's WOD</h1>
			<a data-rel="back" data-icon="back" data-iconpos="notext" >Home</a>
		</div><!-- /header -->
	</div><!-- /header -->
	<div data-role="content">		
		<div class="content-primary">	
			<ul data-role="listview" data-theme="d" data-divider-theme="d">
				<?php echo $wod_select_html;?>
			</ul>
		</div><!--/content-primary -->		
	</div><!-- content, SaveBoxWod -->
</div><!-- /page, SaveBoxWod -->