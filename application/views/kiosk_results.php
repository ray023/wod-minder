<!DOCTYPE html> 
<html>
	<head>
		<title><?php echo $title; ?></title> 
		<meta charset="utf-8">
		<?php echo link_tag('css/kiosk_results.css' . '?' . time()); ?>
		
		<script src="http://code.jquery.com/jquery-1.8.2.min.js"></script>		
		<script src="<?php echo base_url().'js/kiosk.js'. '?' . time();?>"></script>
		<?php header('Content-type: text/html; charset=utf-8');?>

		<style type="text/css">
			body 
			{
				<?php if (isset($background_image_url)): ?>
					background-image:url(<?php echo $background_image_url;?>);
				<?php endif; ?>
			} 
			.header
			{
				<?php if (isset($css_header_background_url)): ?>
					background-image:url(<?php echo $css_header_background_url;?>);
				<?php endif; ?>
					
			  text-align: center;
			  height: 160px;
			}
		</style>
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
	</head>
	<body>
		<input type="hidden" id="_boxId" value="<?php echo $box_id; ?>"/>
		<input type="hidden" id="_lastButtonUpdate"/>
		<input type="hidden" id="_lastWodUpdate"/>
		<div class="header">
			<?php if (isset($logo)): ?>
				<img src="<?php echo $logo;?>"/>
			<?php endif; ?>
		</div>
		<div id="_resultsMenu">
			Result menu buttons load here
		</div>
		<div id="_resultsContainer">
			WOD Results Load Here.
		</div>
		<div id="_hideUs">
			Divs holding the actual results.  Hidden
		</div>
	</body>
</html>