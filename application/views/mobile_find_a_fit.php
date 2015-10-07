<div data-role="page">
	<div data-role="header">
		<a href="#_popupNested" id="_optionButton" data-rel="popup" data-icon="gear" data-iconpos="notext" class="ui-btn-left" data-transition="slidedown">Options</a>
		<div data-role="popup" id="_popupNested" data-theme="none">
			<div data-role="collapsibleset" data-theme="b" data-content-theme="a" data-collapsed-icon="arrow-r" data-expanded-icon="arrow-d" style="margin:0; width:250px;">
				<div id="_resultMenu" data-role="collapsible" data-inset="false">
				<h2>Result Count</h2>
					<ul data-role="controlgroup">
						<li><a href="#" class="ui-btn result-count-button" data-rel="dialog">5</a></li>
						<li><a href="#" class="ui-btn result-count-button" data-rel="dialog">10</a></li>
						<li><a href="#" class="ui-btn result-count-button" data-rel="dialog">20</a></li>
						<li><a href="#" class="ui-btn result-count-button" data-rel="dialog">50</a></li>
					</ul>
				</div>
				<div id="_sourceMenu" data-role="collapsible" data-inset="false">
				<h2>Source</h2>
					<ul data-role="controlgroup">
						<li><a href="#" class="ui-btn source-button" data-rel="dialog">Current Location</a></li>
						<li><a href="#" class="ui-btn source-button" data-rel="dialog">Address</a></li>
					</ul>
				</div>
				<div id="_feedbackMenu" data-role="collapsible" data-inset="false">
				<h2>Feedback</h2>
					<ul data-role="controlgroup">
						<li><a href="#" id="_showFeedbackFields" class="ui-btn " data-rel="dialog">Send Feedback</a></li>
						<li><a href="#" id="_gotoFacebook" class="ui-btn " data-rel="dialog">Facebook</a></li>
					</ul>
				</div>
			</div>
		</div><!-- /popup -->
		<a href="#" id="_refreshOnLocationIcon" data-ajax ="false" data-icon="refresh" data-iconpos="notext" class="ui-btn-right refresh-on-location-button">Refresh</a>
		<h1><?php echo $title; ?></h1>
	</div>
	<div id ='nearest_facilities_div' data-role="content">
		<div id="_resultsDiv">
			<input type="hidden" id="_resultCount" value="5"/>
			<input type="hidden" id="_sourceType" value="Location"/>
			<input type="text" id="_addressField" placeholder="Address" value=""  />
			<button id="_searchFits" class="ui-btn ui-mini">Go</button>
			<div id="geolocation">Loading geolocation...</div>
			<div>
				<button id="_refreshOnLocationButton" class="refresh-on-location-button">Refresh</button>
			</div>
		</div>
		<div id="_feedbackDiv" style="display:none;">
			<label for="_userFeedback">Feedback:</label>
			<textarea id="_userFeedback" placeholder="Question/Comment/Request/Complaint"></textarea>
			<button id="_submitFeedback">Submit Feedback</button>
			<button id="_cancelFeedback">Cancel</button>
		</div>
	</div>
</div>