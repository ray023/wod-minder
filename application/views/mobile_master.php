<!DOCTYPE html> 
<html> 
	<head> 
		<title><?php echo $title; ?></title> 
		<meta charset="utf-8">
		<meta name="description" content=" WOD-Minder is a web app enabling CrossFit members to maintain WOD scores and Maxes." />
		<meta name="viewport" content="width=device-width, initial-scale=1"> 
		<meta http-equiv="X-UA-Compatible" content="IE=Edge"/>
		<meta name="format-detection" content="telephone=no">

		<?php //Don't allow user to pinch and zoom on browser.  This may not be an optial setting but trying it to fix Barbell calculator issues ?>
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/> 
		
		<link rel="stylesheet" href="http://code.jquery.com/mobile/1.3.0/jquery.mobile-1.3.0.min.css" />
		<link rel="apple-touch-icon-precomposed" href="<?php echo base_url().'apple-touch-icon.png';?>"/>
		
		<?php echo link_tag('css/mobile-main.css' . '?' . time()); ?>
		<?php echo link_tag('css/all-shared.css' . '?' . time()); ?>
		
		<script src="http://code.jquery.com/jquery-1.8.2.min.js"></script>
		<script src="http://code.jquery.com/mobile/1.3.0/jquery.mobile-1.3.0.min.js"></script>
		
		<script src="<?php echo base_url().'js/main_functions.js'. '?' . time();?>"></script>
		<?php header('Content-type: text/html; charset=utf-8');?>
		<script type="text/javascript">
			function base_url() {
				return <?php echo "'".base_url()."'";?>;
			}
		</script>
		<script type="text/javascript">
			$(document).ready(function() {
			  <?php echo isset($doc_ready_call) ? $doc_ready_call : '' ;?>
                                  
			});
			
			<?php echo isset($other_function_call) ? $other_function_call : '' ;?>

		</script>
		<?php if (isset($show_add2home_popup) && $show_add2home_popup): ?>
			<?php echo link_tag('css/add2home.css' . '?' . time()); ?>
			<script src="<?php echo base_url().'js/add2home.js'. '?' . time();?>" charset="utf-8"></script>
		<?php endif; ?>
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
	<?php $this->load->view($view); ?>
</body>
</html>