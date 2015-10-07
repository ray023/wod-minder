<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report extends MY_Controller {

	function __construct() 
	{
		parent::__construct();
	}
			
	/**
	 * Index Page for this controller.
	 *
	 * @access public
	 */
	public function index()
	{
		
		if (!$this->logged_in)
			redirect ('member/login');
		
		//START AUDIT
		$this->load->model('Audit_model');
		$audit_data['controller']	=	'report_controller';
		$audit_data['ip_address']	=	$_SERVER['REMOTE_ADDR'];
		$audit_data['member_id']	=	$this->session->userdata('member_id');
		$audit_data['member_name']	=	$this->session->userdata('display_name');
		$audit_data['short_description']	=	'Get CrossFit Summary';
		$this->Audit_model->save_audit_log($audit_data);
		//END AUDIT
		
		//See if user has at least one required record
		$member_id = $this->session->userdata('member_id');
		$weight_record_count	=	0;
		$wod_record_count		=	0;
		$max_record_count		=	0;
		
		$this->load->model('Wod_model');
		$wod_record_count		=	$this->Wod_model->get_record_count($member_id);
		$this->load->model('Exercise_model');
		$max_record_count		=	$this->Exercise_model->get_record_count($member_id);
		
		$data['show_report']	=	FALSE;
		$data['wod_data_html']	=	'';
		$data['max_data_html']	=	'';
		$data['goal_data_html']	=	'';
		
		$data['title'] = 'CrossFit Summary';
		
		if ($max_record_count == 0 || $wod_record_count == 0)
		{
			$data['missing']	=	'';
			if ($wod_record_count == 0)
				$data['missing']	.=	'<li>WOD</li>';
			if ($max_record_count == 0)
				$data['missing']	.=	'<li>Max</li>';

		}
		else
		{
			$data['show_report']	=	TRUE;
			$data['wod_data_html'] = $this->_get_wod_data();
			$data['max_data_html'] = $this->_get_max_data();		
			$data['goal_data_html'] = $this->_get_goal_data();
		}
		
		$this->load->vars($data);
		$this->load->view('crossfit_summary', $data);
		return;
	}
	
	private function _get_max_data()
	{
		$this->load->model('Report_model');
		$report_data = $this->Report_model->get_max_report_data();
		
		$data = '';
		$max_data_html = '';
                
		$sum_total = 0;
		
		if (!!$report_data['max_count_by_type'])
		{

			$max_data_html .= '<div data-role="collapsible" data-collapsed="false" data-theme="b" data-content-theme="a"> ';
			$max_data_html .= '<h4>Max Count</h4>';
			$max_data_html .= '<div class="ui-grid-a" >';
			$max_data_html .= '<div class="ui-block-a  mobile-grid-header ">Place</div>';
			$max_data_html .= '<div class="ui-block-b mobile-grid-header number-block ">Count</div>';
			
			$alt_row = 0;
			foreach($report_data['max_count_by_type'] as $row) 
			{
				$alt_row_class	=	$alt_row%2==1	? 'alternate-row' : '';
				$max_data_html .= '<div class="ui-block-a '.$alt_row_class.'">'.$row['max_type_text'].'</div>';
				$max_data_html .= '<div class="ui-block-b number-block '.$alt_row_class.'">'.$row['count_max_type'].'</div>';
				$sum_total = $sum_total + $row['count_max_type'];
				$alt_row++;
			}
			$max_data_html .= '<div class="ui-block-a  mobile-grid-header ">Total</div>';
			$max_data_html .= '<div class="ui-block-b mobile-grid-header number-block ">'.$sum_total.'</div>';

			$max_data_html .= '</div></div>';
		}
		
		$sum_total = 0;
		
		if (!!$report_data['lift_percentage_change'])
		{
			$max_data_html .= '<div data-role="collapsible" data-collapsed="false" data-theme="b" data-content-theme="a"> ';
			$max_data_html .= '<h4>Most Improved Lifts</h4>';
			$max_data_html .= '<div class="ui-grid-c" >';
			$max_data_html .= '<div class="ui-block-a  mobile-grid-header ">Lift</div>';
			$max_data_html .= '<div class="ui-block-b mobile-grid-header number-block ">First</div>';
			$max_data_html .= '<div class="ui-block-c  mobile-grid-header  number-block ">Latest</div>';
			$max_data_html .= '<div class="ui-block-d mobile-grid-header number-block ">% Change</div>';
			$alt_row = 0;
			foreach($report_data['lift_percentage_change'] as $row) 
			{
				$alt_row_class	=	$alt_row%2==1	? 'alternate-row' : '';				
				$max_data_html .= '<div class="ui-block-a '.$alt_row_class.'">'.$row['exercise'].'</div>';
				$max_data_html .= '<div class="ui-block-b number-block '.$alt_row_class.'">'.$row['flq_value'].'</div>';
				$max_data_html .= '<div class="ui-block-c number-block '.$alt_row_class.'">'.$row['llq_value'].'</div>';
				$max_data_html .= '<div class="ui-block-d number-block '.$alt_row_class.'">'.$row['percentage_change'].'%</div>';
				
				$alt_row++;
			}
			$max_data_html .= '</div></div>';
		}
		
		if (!!$report_data['maxes_most_saved'])
		{
			$max_data_html .= '<div data-role="collapsible" data-collapsed="false" data-theme="b" data-content-theme="a"> ';
			$max_data_html .= '<h4>Lifts Most Saved</h4>';
			$max_data_html .= '<div class="ui-grid-a" >';
			$max_data_html .= '<div class="ui-block-a  mobile-grid-header ">Title</div>';
			$max_data_html .= '<div class="ui-block-b mobile-grid-header number-block ">Count</div>';
			$alt_row = 0;
			foreach($report_data['maxes_most_saved'] as $row) 
			{
				$alt_row_class	=	$alt_row%2==1	? 'alternate-row' : '';				
				$max_data_html .= '<div class="ui-block-a '.$alt_row_class.'">'.$row['title'].'</div>';
				$max_data_html .= '<div class="ui-block-b number-block '.$alt_row_class.'">'.$row['save_count'].'</div>';
				
				$alt_row++;
			}
			$max_data_html .= '</div></div>';
		}
		
		
		$sum_total = 0;
		
		if (!!$report_data['heaviest_lifts'])
		{

			$max_data_html .= '<div data-role="collapsible" data-collapsed="false" data-theme="b" data-content-theme="a"> ';
			$max_data_html .= '<h4>The WOD-Minder Total</h4>';
			$max_data_html .= '<a href="#wodMinderTotalInfo" data-role="button" data-rel="popup" data-icon="info" data-mini="true" data-inline="true">WOD-Minder Total Info</a>';
			$max_data_html .= '<div data-role="popup" id="wodMinderTotalInfo" class="ui-content" data-theme="a">';
			$max_data_html .= '<a href="#" data-rel="back" data-role="button" data-theme="a" data-icon="delete" data-iconpos="notext" class="ui-btn-right">Close</a>';
			$max_data_html .= '<p>The WOD-Minder total takes your top 5 strongest, single-rep lifts and sums the weight.<p>';
			$max_data_html .= '</div>';
			$max_data_html .= '<div class="ui-grid-a" >';
			$max_data_html .= '<div class="ui-block-a  mobile-grid-header ">Lift</div>';
			$max_data_html .= '<div class="ui-block-b mobile-grid-header number-block ">Load</div>';
			
			$alt_row = 0;
			foreach($report_data['heaviest_lifts'] as $row) 
			{
				$alt_row_class	=	$alt_row%2==1	? 'alternate-row' : '';
				$max_data_html .= '<div class="ui-block-a '.$alt_row_class.'">'.$row['title'].'</div>';
				$max_data_html .= '<div class="ui-block-b number-block '.$alt_row_class.'">'.$row['best_max_value'].'</div>';
				$sum_total = $sum_total + $row['best_max_value'];
				$alt_row++;
			}
			$max_data_html .= '<div class="ui-block-a  mobile-grid-header ">Total</div>';
			$max_data_html .= '<div class="ui-block-b mobile-grid-header number-block ">'.number_format($sum_total, 1, '.', ',').'</div>';

			$max_data_html .= '</div></div>';
		}
		
		if (!!$report_data['old_max_dates'])
		{
			$max_data_html .= '<div data-role="collapsible" data-collapsed="false" data-theme="b" data-content-theme="a"> ';
			$max_data_html .= '<h4>Old Maxes</h4>';
			$max_data_html .= '<a href="#oldMaxesInfo" data-role="button" data-rel="popup" data-icon="info" data-mini="true" data-inline="true">Old Max Info</a>';
			$max_data_html .= '<div data-role="popup" id="oldMaxesInfo" class="ui-content" data-theme="a">';
			$max_data_html .= '<a href="#" data-rel="back" data-role="button" data-theme="a" data-icon="delete" data-iconpos="notext" class="ui-btn-right">Close</a>';
			$max_data_html .= '<p>This is a list of lifts you have not saved in the past 90 days or longer.</p><p>Is it time to record a new PR?<p>';
			$max_data_html .= '</div>';
			$max_data_html .= '<div class="ui-grid-b" >';
			$max_data_html .= '<div class="ui-block-a  mobile-grid-header ">Lift</div>';
			$max_data_html .= '<div class="ui-block-b mobile-grid-header number-block ">Last Lift Date</div>';
			$max_data_html .= '<div class="ui-block-c  mobile-grid-header  number-block ">Days Since Lift</div>';
			$alt_row = 0;
			foreach($report_data['old_max_dates'] as $row) 
			{
				$alt_row_class	=	$alt_row%2==1	? 'alternate-row' : '';				
				$max_data_html .= '<div class="ui-block-a '.$alt_row_class.'">'.$row['exercise'].'</div>';
				$max_data_html .= '<div class="ui-block-b number-block '.$alt_row_class.'">'.$row['max_lift_date'].'</div>';
				$max_data_html .= '<div class="ui-block-c number-block '.$alt_row_class.'">'.$row['days_since_lift'].'</div>';
				
				$alt_row++;
			}
			$max_data_html .= '</div></div>';
		}
		/* This might be clutter
		if (!!$report_data['top_pr_months'])
		{
			$max_data_html .= '<div data-role="collapsible" data-collapsed="false" data-theme="b" data-content-theme="a"> ';
			$max_data_html .= '<h4>Top PR Months</h4>';
			$max_data_html .= '<div class="ui-grid-a" >';
			$max_data_html .= '<div class="ui-block-a  mobile-grid-header ">Year-Month</div>';
			$max_data_html .= '<div class="ui-block-b mobile-grid-header number-block ">Count</div>';
			$alt_row = 0;
			foreach($report_data['top_pr_months'] as $row) 
			{
				$alt_row_class	=	$alt_row%2==1	? 'alternate-row' : '';				
				$max_data_html .= '<div class="ui-block-a '.$alt_row_class.'">'.$row['lift_year_month'].'</div>';
				$max_data_html .= '<div class="ui-block-b number-block '.$alt_row_class.'">'.$row['count_lift_year_month'].'</div>';
				
				$alt_row++;
			}
			$max_data_html .= '</div></div>';
		}
		 * 
		 */
		
		//Relative Strength Headers
		$max_data_html .= '<div data-role="collapsible" data-collapsed="false" data-theme="b" data-content-theme="a"> ';
		$max_data_html .= '<h4>Relative Strength</h4>';
		$max_data_html .= '<a href="#relativeStrengthInfo" data-role="button" data-rel="popup" data-icon="info" data-mini="true" data-inline="true">About Relative Strength</a>';
		$max_data_html .= '<div data-role="popup" id="relativeStrengthInfo" class="ui-content" data-theme="a">';
		$max_data_html .= '<a href="#" data-rel="back" data-role="button" data-theme="a" data-icon="delete" data-iconpos="notext" class="ui-btn-right">Close</a>';
		$max_data_html .= '<p>Relative strength is your power expressed in terms of your body weight.</p><p>For example, if you weigh 175 and deadlift 350, then your relative strength is 350/175 (or 2.0).<p>If you are putting on lean muscle mass, your body weight will be going up slowly, but your strength will increase more rapidly, and this number will increase.<p>';
		$max_data_html .= '<p>My personal goals are at least 2.0 for Deadlift, 1.5 for Backsquat and 1.3 for Power Clean.  Your goals may vary.</p>';
		$max_data_html .= '</div>';
		if (!$report_data['relative_strength'])
		{
			$max_data_html .= "<p>Relative Strength requires the following:</p>".
								"<ul>".
								"<li><a data-ajax=\"false\" href=\"".base_url()."index.php/weight/save_member_weight/  \">A Weight Journal record</a></li>".
								"<li>A Weighted Max (e.g. Power Clean, DeadLift, Bench Press, etc.)</li>".
								"</ul>";
		}
		else
		{
			$weight_value = $report_data['relative_strength'][0]['weight'];
			$max_data_html .= '<p><strong>Current Body Weight:</strong> '.$weight_value.'</p>';
			$max_data_html .= '<div class="ui-grid-b" >';
			$max_data_html .= '<div class="ui-block-a  mobile-grid-header ">Lift</div>';
			$max_data_html .= '<div class="ui-block-b mobile-grid-header number-block ">Max</div>';
			$max_data_html .= '<div class="ui-block-c  mobile-grid-header  number-block ">RS</div>';
			$alt_row = 0;
			foreach($report_data['relative_strength'] as $row) 
			{
				$alt_row_class	=	$alt_row%2==1	? 'alternate-row' : '';				
				$max_data_html .= '<div class="ui-block-a '.$alt_row_class.'">'.$row['exercise'].'</div>';
				$max_data_html .= '<div class="ui-block-b number-block '.$alt_row_class.'">'.$row['max_value'].'</div>';
				$max_data_html .= '<div class="ui-block-c number-block '.$alt_row_class.'">'.$row['rs_ratio'].'</div>';
				
				$alt_row++;
			}
			$max_data_html .= '</div>';
		}
		$max_data_html .= '</div>';
		
		return $max_data_html;
	}
	private function _get_wod_data()
	{
		$this->load->model('Report_model');
		$report_data = $this->Report_model->get_wod_report_data();
		
		$data = '';
		$wod_data_html = '';
                
		$sum_total = 0;
		
		if (!!$report_data['where_wods_saved_array'])
		{
			$wod_data_html .= '<div data-role="collapsible" data-collapsed="false" data-theme="b" data-content-theme="a" > ';
			$wod_data_html .= '<h4>WOD Count</h4>';
			$wod_data_html .= '<div class="ui-grid-a" >';
			$wod_data_html .= '<div class="ui-block-a  mobile-grid-header ">Place</div>';
			$wod_data_html .= '<div class="ui-block-b mobile-grid-header number-block ">Count</div>';
			
			$alt_row = 0;
			foreach($report_data['where_wods_saved_array'] as $row) 
			{
				$alt_row_class	=	$alt_row%2==1	? 'alternate-row' : '';
				$wod_data_html .= '<div class="ui-block-a '.$alt_row_class.'">'.$row['place'].'</div>';
				$wod_data_html .= '<div class="ui-block-b number-block '.$alt_row_class.'">'.$row['wod_count'].'</div>';
				$sum_total = $sum_total + $row['wod_count'];
				$alt_row++;
			}
			$wod_data_html .= '<div class="ui-block-a  mobile-grid-header ">Total</div>';
			$wod_data_html .= '<div class="ui-block-b mobile-grid-header number-block ">'.$sum_total.'</div>';

			$wod_data_html .= '</div></div>';
		}
		
		$sum_total = 0;
		/* this might be clutter
		 * 
		if (!!$report_data['box_wod_count_month_years'])
		{
			$wod_data_html .= '<div data-role="collapsible" data-collapsed="false" data-theme="b" data-content-theme="a"> ';
			$wod_data_html .= '<h4>Count of WODs by Month/Year</h4>';
			$wod_data_html .= '<div class="ui-grid-a" >';
			$wod_data_html .= '<div class="ui-block-a  mobile-grid-header ">Month Year</div>';
			$wod_data_html .= '<div class="ui-block-b mobile-grid-header number-block ">Count</div>';
			$alt_row = 0;
			foreach($report_data['box_wod_count_month_years'] as $row) 
			{
				$alt_row_class	=	$alt_row%2==1	? 'alternate-row' : '';
				$wod_data_html .= '<div class="ui-block-a '.$alt_row_class.'">'.$row['lift_year_month'].'</div>';
				$wod_data_html .= '<div class="ui-block-b number-block '.$alt_row_class.'">'.$row['count_of_monthly_wods'].'</div>';
				$sum_total = $sum_total + $row['count_of_monthly_wods'];
				$alt_row++;
			}
			$wod_data_html .= '<div class="ui-block-a  mobile-grid-header ">Total</div>';
			$wod_data_html .= '<div class="ui-block-b mobile-grid-header number-block ">'.$sum_total.'</div>';

			$wod_data_html .= '</div></div>';

		}
		*/
		
		$sum_total = 0;
		if (!!$report_data['when_you_wod'])
		{
			$wod_data_html .= '<div data-role="collapsible" data-collapsed="false" data-theme="b" data-content-theme="a"> ';
			$wod_data_html .= '<h4>When You WOD at Your Box</h4>';
			$wod_data_html .= '<div class="ui-grid-b" >';
			$wod_data_html .= '<div class="ui-block-a  mobile-grid-header number-block ">Class Time</div>';
			$wod_data_html .= '<div class="ui-block-b mobile-grid-header number-block ">Count</div>';
			$wod_data_html .= '<div class="ui-block-c mobile-grid-header number-block ">Percentage</div>';

			$alt_row = 0;
			foreach($report_data['when_you_wod'] as $row) 
			{				
				$alt_row_class	=	$alt_row%2==1	? 'alternate-row' : '';
				$wod_data_html .= '<div class="ui-block-a number-block '.$alt_row_class.'">'.$row['class_time'].'</div>';
				$wod_data_html .= '<div class="ui-block-b number-block '.$alt_row_class.'">'.$row['ct_count'].'</div>';
				$wod_data_html .= '<div class="ui-block-c number-block '.$alt_row_class.'">'.$row['ct_percent'].'</div>';

				$sum_total = $sum_total + $row['ct_count'];
				$alt_row++;
			}
			
			$wod_data_html .= '<div class="ui-block-a  mobile-grid-header  number-block ">Total</div>';
			$wod_data_html .= '<div class="ui-block-b mobile-grid-header number-block ">'.$sum_total.'</div>';
			$wod_data_html .= '<div class="ui-block-c mobile-grid-header number-block ">100%</div>';
			$wod_data_html .= '</div></div>';
			
		}
		
		$sum_total = 0;
		if (!!$report_data['benchmark_wod_count'])
		{
			$wod_data_html .= '<div data-role="collapsible" data-collapsed="false" data-theme="b" data-content-theme="a"> ';
			$wod_data_html .= '<h4>Benchmark WODs Breakdown</h4>';
			$wod_data_html .= '<div class="ui-grid-b" >';
			$wod_data_html .= '<div class="ui-block-a  mobile-grid-header ">Month Year</div>';
			$wod_data_html .= '<div class="ui-block-b mobile-grid-header number-block ">Count</div>';
			$wod_data_html .= '<div class="ui-block-c mobile-grid-header number-block ">Avg. Rating</div>';

			$alt_row = 0;
			foreach($report_data['benchmark_wod_count'] as $row) 
			{				
				$alt_row_class	=	$alt_row%2==1	? 'alternate-row' : '';
				$wod_data_html .= '<div class="ui-block-a '.$alt_row_class.'">'.$row['wod_name'].'</div>';
				$wod_data_html .= '<div class="ui-block-b number-block '.$alt_row_class.'">'.$row['benchmark_wod_count_total'].'</div>';
				$wod_data_html .= '<div class="ui-block-c number-block '.$alt_row_class.'">'.$row['avg_rating'].'</div>';

				$sum_total = $sum_total + $row['benchmark_wod_count_total'];
				$alt_row++;
			}
			
			$wod_data_html .= '<div class="ui-block-a  mobile-grid-header ">Total</div>';
			$wod_data_html .= '<div class="ui-block-b mobile-grid-header number-block ">'.$sum_total.'</div>';
			$wod_data_html .= '<div class="ui-block-c mobile-grid-header number-block ">&nbsp;</div>';
			$wod_data_html .= '</div></div>';
			
		}
		/* REN:  THIS REPORT MIGHT BE CLUTTER AND OF NO REAL VALUE
		if (!!$report_data['benchmark_wod_locations'])
		{
			
			$wod_data_html .= '<div data-role="collapsible" data-collapsed="false" data-theme="b" data-content-theme="a"> ';
			$wod_data_html .= '<h4>Benchmark WOD Locations</h4>';
			$wod_data_html .= '<div class="ui-grid-b" >';
			$wod_data_html .= '<div class="ui-block-a  mobile-grid-header ">Location</div>';
			$wod_data_html .= '<div class="ui-block-b mobile-grid-header number-block ">Count</div>';
			
			$alt_row = 0;
			foreach($report_data['benchmark_wod_locations'] as $row) 
			{
				$alt_row_class	=	$alt_row%2==1	? 'alternate-row' : '';
				$wod_data_html .= '<div class="ui-block-a '.$alt_row_class.'">'.$row['box_name'].'</div>';
				$wod_data_html .= '<div class="ui-block-b number-block '.$alt_row_class.'">'.$row['benchmark_wod_count'].'</div>';
				$alt_row++;
			}
			$wod_data_html .= '</div></div>';
		}		
		*/
		
		$sum_total = 0;
		if (!!$report_data['wods_by_type'])
		{			
			$wod_data_html .= '<div data-role="collapsible" data-collapsed="false" data-theme="b" data-content-theme="a"> ';
			$wod_data_html .= '<h4>Box WODs By Type</h4>';
			$wod_data_html .= '<div class="ui-grid-b" >';
			$wod_data_html .= '<div class="ui-block-a  mobile-grid-header ">Score Type</div>';
			$wod_data_html .= '<div class="ui-block-b mobile-grid-header number-block ">Count</div>';
			$wod_data_html .= '<div class="ui-block-c mobile-grid-header number-block ">Avg. Rating</div>';

			
			$alt_row = 0;
			foreach($report_data['wods_by_type'] as $row) 
			{
				$alt_row_class	=	$alt_row%2==1	? 'alternate-row' : '';
				$wod_data_html .= '<div class="ui-block-a '.$alt_row_class.'">'.$row['score_type_text'].'</div>';
				$wod_data_html .= '<div class="ui-block-b number-block '.$alt_row_class.'">'.$row['count_score_type'].'</div>';
				$wod_data_html .= '<div class="ui-block-c number-block '.$alt_row_class.'">'.$row['avg_rating'].'</div>';				
				$sum_total = $sum_total + $row['count_score_type'];
				$alt_row++;
			}
			
			$wod_data_html .= '<div class="ui-block-a  mobile-grid-header ">Total</div>';
			$wod_data_html .= '<div class="ui-block-b mobile-grid-header number-block ">'.$sum_total.'</div>';
			$wod_data_html .= '<div class="ui-block-c mobile-grid-header number-block ">&nbsp;</div>';

			$wod_data_html .= '</div></div>';
		}
		
		if (!!$report_data['timed_wod_array'])
		{
			
			$wod_data_html .= '<div data-role="collapsible" data-collapsed="false" data-theme="b" data-content-theme="a"> ';
			$wod_data_html .= '<h4>WODs For Time Breakdown</h4>';
			$wod_data_html .= '<div class="ui-grid-c" >';
			$wod_data_html .= '<div class="ui-block-a  mobile-grid-header ">Category</div>';
			$wod_data_html .= '<div class="ui-block-b mobile-grid-header number-block ">Count</div>';
			$wod_data_html .= '<div class="ui-block-c mobile-grid-header number-block ">Time (hh:mm:ss)</div>';
			$wod_data_html .= '<div class="ui-block-d mobile-grid-header number-block ">Avg. Rating</div>';

			
			$alt_row = 0;
			foreach($report_data['timed_wod_array'] as $row) 
			{
				if (count($row) > 0)
				{

					if ($row['time_title'] != 'Total')
						$alt_row_class	=	$alt_row%2==1	? 'alternate-row' : '';
					else
						$alt_row_class	=	'mobile-grid-header';

					$wod_data_html .= '<div class="ui-block-a '.$alt_row_class.'">'.$row['time_title'].'</div>';
					$wod_data_html .= '<div class="ui-block-b number-block '.$alt_row_class.'">'.$row['wod_count'].'</div>';
					$wod_data_html .= '<div class="ui-block-c number-block '.$alt_row_class.'">'.$row['time_spent_wodding'].'</div>';
					$wod_data_html .= '<div class="ui-block-d number-block '.$alt_row_class.'">'.$row['avg_rating'].'</div>';
					$alt_row++;
				}
			}
			$wod_data_html .= '</div></div>';
		}
		
		if (!!$report_data['quickest_wods'])
		{
			$wod_data_html .= '<div data-role="collapsible" data-collapsed="false" data-theme="b" data-content-theme="a"> ';
			$wod_data_html .= '<h4>Top 5 Quickies</h4>';
			$wod_data_html .= '<div class="ui-grid-d" >';
			$wod_data_html .= '<div class="ui-block-a  mobile-grid-header ">Date</div>';
			$wod_data_html .= '<div class="ui-block-b mobile-grid-header number-block ">Box</div>';
			$wod_data_html .= '<div class="ui-block-c  mobile-grid-header  number-block ">WOD</div>';
			$wod_data_html .= '<div class="ui-block-d mobile-grid-header number-block ">Time</div>';
			$wod_data_html .= '<div class="ui-block-e mobile-grid-header number-block ">Rating</div>';
			$alt_row = 0;
			foreach($report_data['quickest_wods'] as $row) 
			{
				$alt_row_class	=	$alt_row%2==1	? 'alternate-row' : '';				
				$wod_data_html .= '<div class="ui-block-a '.$alt_row_class.'">'.$row['wod_date'].'</div>';
				$wod_data_html .= '<div class="ui-block-b number-block '.$alt_row_class.'">'.$row['box_name'].'</div>';
				$wod_data_html .= '<div class="ui-block-c number-block '.$alt_row_class.'">'.$row['wod_name'].'</div>';
				$wod_data_html .= '<div class="ui-block-d number-block '.$alt_row_class.'">'.$row['score_time'].'</div>';
				$wod_data_html .= '<div class="ui-block-e number-block '.$alt_row_class.'">'.$row['rating'].'</div>';
				
				$alt_row++;
			}
			$wod_data_html .= '</div></div>';
		}
		
		if (!!$report_data['longest_wods'])
		{
			
			$wod_data_html .= '<div data-role="collapsible" data-collapsed="false" data-theme="b" data-content-theme="a"> ';
			$wod_data_html .= '<h4>Top 5 Longies</h4>';
			$wod_data_html .= '<div class="ui-grid-d" >';
			$wod_data_html .= '<div class="ui-block-a  mobile-grid-header ">Date</div>';
			$wod_data_html .= '<div class="ui-block-b mobile-grid-header number-block ">Box</div>';
			$wod_data_html .= '<div class="ui-block-c  mobile-grid-header  number-block ">WOD</div>';
			$wod_data_html .= '<div class="ui-block-d mobile-grid-header number-block ">Time</div>';
			$wod_data_html .= '<div class="ui-block-e mobile-grid-header number-block ">Rating</div>';
			
			$alt_row = 0;
			foreach($report_data['longest_wods'] as $row) 
			{
				$alt_row_class	=	$alt_row%2==1	? 'alternate-row' : '';				
				$wod_data_html .= '<div class="ui-block-a '.$alt_row_class.'">'.$row['wod_date'].'</div>';
				$wod_data_html .= '<div class="ui-block-b number-block '.$alt_row_class.'">'.$row['box_name'].'</div>';
				$wod_data_html .= '<div class="ui-block-c number-block '.$alt_row_class.'">'.$row['wod_name'].'</div>';
				$wod_data_html .= '<div class="ui-block-d number-block '.$alt_row_class.'">'.$row['score_time'].'</div>';
				$wod_data_html .= '<div class="ui-block-e number-block '.$alt_row_class.'">'.$row['rating'].'</div>';
				
				$alt_row++;
			}
			$wod_data_html .= '</div></div>';

		}
		
		return $wod_data_html;
	}
	private function _get_goal_data()
	{
		$this->load->model('Report_model');
		$report_data = $this->Report_model->get_goal_report_data();

		$data = '';
		$goal_data_html = '';

		$sum_total = 0;

		if (!!$report_data['crossfit_total'])
		{

			$goal_data_html .= '<div data-role="collapsible" data-collapsed="false" data-theme="b" data-content-theme="a"> ';
			$goal_data_html .= '<h4>CrossFit Total</h4>';
			$goal_data_html .= '<a href="#crossfitTotalInfo" data-role="button" data-rel="popup" data-icon="info" data-mini="true" data-inline="true">CrossFit Total Info</a>';
			$goal_data_html .= '<div data-role="popup" id="crossfitTotalInfo" class="ui-content" data-theme="a">';
			$goal_data_html .= '<a href="#" data-rel="back" data-role="button" data-theme="a" data-icon="delete" data-iconpos="notext" class="ui-btn-right">Close</a>';
			$goal_data_html .= '<p>The CrossFit Total is a sum of your best Shoulder Press, Deadlift and Back Squat.  The values below are your best 1-rep maxes for these lifts.<p>';
			$goal_data_html .= '</div>';
			$goal_data_html .= '<div class="ui-grid-a" >';
			$goal_data_html .= '<div class="ui-block-a  mobile-grid-header ">Lift</div>';
			$goal_data_html .= '<div class="ui-block-b mobile-grid-header number-block ">Load</div>';

			$alt_row = 0;
			foreach($report_data['crossfit_total'] as $row) 
			{
				$alt_row_class	=	$alt_row%2==1	? 'alternate-row' : '';
				$goal_data_html .= '<div class="ui-block-a '.$alt_row_class.'">'.$row['title'].'</div>';
				$goal_data_html .= '<div class="ui-block-b number-block '.$alt_row_class.'">'.$row['max_value'].'</div>';
				$sum_total = $sum_total + $row['max_value'];
				$alt_row++;
			}
			$goal_data_html .= '<div class="ui-block-a  mobile-grid-header ">Beat this score</div>';
			$goal_data_html .= '<div class="ui-block-b mobile-grid-header number-block ">'.number_format($sum_total, 1, '.', ',').'</div>';

			$goal_data_html .= '</div></div>';
		}
		
		$sum_total = 0;
		if (!!$report_data['new_crossfit_total'])
		{

			$goal_data_html .= '<div data-role="collapsible" data-collapsed="false" data-theme="b" data-content-theme="a"> ';
			$goal_data_html .= '<h4>New CrossFit Total</h4>';
			$goal_data_html .= '<a href="#newcrossfitTotalInfo" data-role="button" data-rel="popup" data-icon="info" data-mini="true" data-inline="true">New CrossFit Total Info</a>';
			$goal_data_html .= '<div data-role="popup" id="newcrossfitTotalInfo" class="ui-content" data-theme="a">';
			$goal_data_html .= '<a href="#" data-rel="back" data-role="button" data-theme="a" data-icon="delete" data-iconpos="notext" class="ui-btn-right">Close</a>';
			$goal_data_html .= '<p>The New CrossFit Total is a sum of your best Over-Head Squat, Bench Press and Clean (Power or Squat).  The values below are your best 1-rep maxes for these lifts.<p>';
			$goal_data_html .= '</div>';
			$goal_data_html .= '<div class="ui-grid-a" >';
			$goal_data_html .= '<div class="ui-block-a  mobile-grid-header ">Lift</div>';
			$goal_data_html .= '<div class="ui-block-b mobile-grid-header number-block ">Load</div>';

			$best_clean_value		= 0;
			$best_clean_title	= '';
			$alt_row = 0;
			foreach($report_data['new_crossfit_total'] as $row) 
			{
				if (strpos($row['title'],'Clean') !== false)
				{
					if ($row['max_value'] > $best_clean_value)
					{
						$best_clean_value	=	$row['max_value'];
						$best_clean_title	=	$row['title'];						
					}
				}
				else
				{
					$alt_row_class	=	$alt_row%2==1	? 'alternate-row' : '';
					$goal_data_html .= '<div class="ui-block-a '.$alt_row_class.'">'.$row['title'].'</div>';
					$goal_data_html .= '<div class="ui-block-b number-block '.$alt_row_class.'">'.$row['max_value'].'</div>';
					$sum_total = $sum_total + $row['max_value'];
					$alt_row++;
				}
			}
			
			if ($best_clean_title != '')
			{
				$alt_row_class	=	$alt_row%2==1	? 'alternate-row' : '';
				$goal_data_html .= '<div class="ui-block-a '.$alt_row_class.'">'.$best_clean_title.'</div>';
				$goal_data_html .= '<div class="ui-block-b number-block '.$alt_row_class.'">'.$best_clean_value.'</div>';
				$sum_total = $sum_total + $best_clean_value;
			}
			
			
			$goal_data_html .= '<div class="ui-block-a  mobile-grid-header ">Beat this score</div>';
			$goal_data_html .= '<div class="ui-block-b mobile-grid-header number-block ">'.number_format($sum_total, 1, '.', ',').'</div>';

			$goal_data_html .= '</div></div>';
		}
		
		return $goal_data_html;
	}
}

/* End of file wod.php */
/* Location: /application/controllers/report.php */