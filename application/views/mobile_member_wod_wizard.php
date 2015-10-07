<div data-role="page" id="WODWizard">
	<div data-role="header">
		<a href="<?php echo base_url().'index.php'; ?>" data-icon="home" data-ajax="false" data-iconpos="notext" data-direction="reverse">Home</a>
		<h1><?php echo $title; ?></h1>
	</div>
	<div class="content-primary">	
		<form id="_wodWizardForm">
			<fieldset data-role="controlgroup" id="_movementCheckboxes">
				<legend>Select movements:</legend>
				<?php echo $benchmark_wod_movements_html;?>
			</fieldset>
		</form>

	</div><!--/content-primary -->	
	<?php echo $wod_wizard_html; ?>
</div>
Hidden pages of all benchmark wods here