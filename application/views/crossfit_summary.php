<!DOCTYPE html> 
<html> 
	<head> 
		<title><?php echo $title; ?></title> 
		<meta charset="utf-8">
		<meta name="description" content="CrossFit Summary of WODs and Maxes." /> 
		<meta name="viewport" content="width=device-width, initial-scale=1"> 
		<meta http-equiv="X-UA-Compatible" content="IE=Edge"/>
		<meta name="format-detection" content="telephone=no">
		

		<link rel="stylesheet" href="http://code.jquery.com/mobile/1.4.2/jquery.mobile-1.4.2.min.css" />
		<link rel="apple-touch-icon-precomposed" href="<?php echo base_url().'apple-touch-icon.png';?>"/>
		<script src="http://code.jquery.com/jquery-1.10.2.min.js"></script>
		<script src="http://code.jquery.com/mobile/1.4.2/jquery.mobile-1.4.2.min.js"></script>
		
		<?php echo link_tag('css/mobile-main.css' . '?' . time()); ?>
		<?php echo link_tag('css/all-shared.css' . '?' . time()); ?>		
		
		<script src="<?php echo base_url().'js/main_functions.js'. '?' . time();?>"></script>
		<?php header('Content-type: text/html; charset=utf-8');?>
		<script type="text/javascript">
			function base_url() {
				return <?php echo "'".base_url()."'";?>;
			}
		</script>
		<script type="text/javascript">
			$(document).ready(function() {
				
			});
			
		</script>
		<?php if ($_SERVER['HTTP_HOST']	=== 'app.wod-minder.com'): ?>
			<script type="text/javascript">

			  var _gaq = _gaq || [];
			  _gaq.push(['_setAccount', 'UA-38416567-1']);
			  _gaq.push(['_trackPageview']);

			  (function() {
				var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
				ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
				var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
			  })();

			</script>
		<?php endif; ?>
	</head>
<body>
	<div data-role="header" data-theme="b">
		<a href="<?php echo base_url(); ?>index.php/welcome/index/TRUE" data-ajax ="false" data-icon="home" data-iconpos="notext" data-direction="reverse">Home</a>
		<h1><?php echo $title; ?></h1>
	</div>
	<?php if (!$show_report): ?>
		In order to view the CrossFit Summary, you need to have at least one record saved in the following area(s):
		<ul>
			<?php echo $missing;?>
		</ul>
	<?php else: ?>
		<div data-role="tabs" id="tabs">
			<div data-role="navbar" data-theme="b">
				<ul>
					<li><a href="#wod" class="ui-btn-active" data-ajax="false">WOD</a></li>
					<li><a href="#max" data-ajax="false">Max</a></li>
					<li><a href="#goal" data-ajax="false">Goals</a></li>
				</ul>
			</div>
			<div id="wod" class="ui-body-d ui-content">
				<h1>WOD Summary</h1>
				<?php
						echo $wod_data_html;
				?>
			</div>
			<div id="max" class="ui-body-d ui-content">
				<h1>Max Summary</h1>
				<?php
						echo $max_data_html;
				?>
			</div>
			<div id="goal" class="ui-body-d ui-content">
				<h1>Goals</h1>
				<?php
						echo $goal_data_html;
				?>
			</div>
		</div>
	<?php endif; ?>
</body>
</html>