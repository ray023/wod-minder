<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

session_start();

require "/var/www/library/Facebook/FacebookSDKException.php";
require "/var/www/library/Facebook/FacebookRequestException.php";
require "/var/www/library/Facebook/FacebookAuthorizationException.php";
require "/var/www/library/Facebook/FacebookSignedRequestFromInputHelper.php";
require "/var/www/library/Facebook/FacebookCanvasLoginHelper.php";
require "/var/www/library/Facebook/FacebookClientException.php";
require "/var/www/library/Facebook/FacebookJavaScriptLoginHelper.php";
require "/var/www/library/Facebook/FacebookOtherException.php";
require "/var/www/library/Facebook/FacebookPageTabHelper.php";
require "/var/www/library/Facebook/FacebookPermissionException.php";
require "/var/www/library/Facebook/FacebookPermissions.php";
require "/var/www/library/Facebook/FacebookRedirectLoginHelper.php";
require "/var/www/library/Facebook/FacebookRequest.php";
require "/var/www/library/Facebook/FacebookResponse.php";
require "/var/www/library/Facebook/FacebookServerException.php";

require "/var/www/library/Facebook/Entities/AccessToken.php";
require "/var/www/library/Facebook/HttpClients/FacebookHttpable.php";
require "/var/www/library/Facebook/HttpClients/FacebookCurl.php";
require "/var/www/library/Facebook/HttpClients/FacebookCurlHttpClient.php";

require "/var/www/library/Facebook/FacebookSession.php";
require "/var/www/library/Facebook/FacebookThrottleException.php";
require "/var/www/library/Facebook/GraphObject.php";
require "/var/www/library/Facebook/GraphAlbum.php";
require "/var/www/library/Facebook/GraphLocation.php";
require "/var/www/library/Facebook/GraphPage.php";
require "/var/www/library/Facebook/GraphSessionInfo.php";
require "/var/www/library/Facebook/GraphUser.php";
require "/var/www/library/Facebook/GraphUserPage.php";

use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\GraphUser;
use Facebook\FacebookRequestException;
use Facebook\FacebookRedirectLoginHelper;

/**
 * Welcome Class
 * 
 * @package wod-minder
 * @subpackage controller
 * @category main-screen
 * @author Ray Nowell
 * 
 */
class Welcome extends MY_Controller {

    /**
     * Index Page for this controller.
     *
     * @access public
     */
    public function index() {
        //$this->output->enable_profiler(TRUE);

        $this->load->library('user_agent');

        define('FORCE_MOBILE_SEGMENT', 3);

        $force_mobile = $this->uri->segment(FORCE_MOBILE_SEGMENT);

        if (!$this->logged_in)
            redirect('member/login');

        $this->load->model('Member_model');
        $member_info = $this->Member_model->get_member();

        $data['site_admin'] = $this->is_admin;
        $data['display_name'] = $this->display_name;

        $data['title'] = 'WOD Minder';
        $data['heading'] = 'Welcome';
        $data['view'] = 'mobile_welcome';

        $data['error_message'] = $this->session->flashdata('error_message');
        $data['good_message'] = $this->session->flashdata('good_message');
        $data['show_add2home_popup'] = $this->session->flashdata('show_add2home_popup');

        $data['other_function_call'] = 'welcome_page_init();';

        $data['exercise_list'] = $this->_get_exercise_list();

        $user_max_snapshot = $this->_get_user_max_snapshot();
        if ($user_max_snapshot != FALSE) {
            $data['single_rep_max_snapshot'] = $user_max_snapshot['single_rep_max_snapshot'];
            $data['user_max_snapshot'] = $user_max_snapshot['user_max_snapshot'];
        }

        $user_max_history = $this->_get_user_max_history();
        if ($user_max_history != FALSE)
            $data['user_max_history'] = $user_max_history;

        $user_box_wod_history = $this->_get_user_box_wod_history();
        if ($user_box_wod_history != FALSE)
            $data['user_box_wod_history'] = $user_box_wod_history;

        $user_custom_wod_history = $this->_get_user_custom_wod_history();
        if ($user_custom_wod_history != FALSE)
            $data['user_custom_wod_history'] = $user_custom_wod_history;

        $user_benchmark_wod_history = $this->_get_user_benchmark_wod_history();
        if ($user_benchmark_wod_history != FALSE)
            $data['user_benchmark_wod_history'] = $user_benchmark_wod_history;

        $data['barbell_grid'] = $this->_get_barbell_calculator();

        $box_id = $this->session->userdata('member_box_id');
        $this->load->model('Box_model');

        $wod_list = $this->Box_model->get_list_of_todays_wods($box_id);
        $data['display_todays_wod'] = !!$wod_list;

        //for staff members only
        $data['member_is_staff'] = false;
        $box_stats = $this->_get_box_stats();
        if ($box_stats) {
            $data['member_is_staff'] = true;
            $data['box_stats'] = $box_stats;
            $data['facebook_link'] = $this->_get_facebook_url();
        }

        //for administrators only
        if ($this->is_admin) {
            $site_stats = $this->_get_site_stats();
            if ($site_stats)
                $data['site_stats'] = $site_stats;

            $data['error_logs'] = $this->_get_error_logs();
        }

        $data['box_wod_list'] = $this->_get_box_wod();
        $data['benchmark_wod_list'] = $this->_get_benchmark_wod();
        $data['is_competitor'] = $member_info->is_competitor;

        $this->load->vars($data);
        $this->load->view('mobile_master', $data);

        return;
    }

    //Just a public page to show the site's stats
    public function site_counts() {
        if (!$this->is_admin)
            redirect('welcome/index/TRUE');

        $this->load->model('Box_model');

        $site_count_array = $this->Box_model->site_counts();
        $alt_row = 0;
        $site_count_grid = '';
        foreach ($site_count_array as $row) {
            $is_odd = $alt_row % 2 == 1;
            $alt_row_class = $is_odd ? 'alternate-row' : '';

            $site_count_grid .= '<div class="ui-block-a ' . $alt_row_class . '">' . $row['metric'] . '</div>';
            $site_count_grid .= '<div class="ui-block-b  date-block  ' . $alt_row_class . '">' . $row['the_count'] . '</div>';

            $alt_row++;
        }


        $active_member_count_array = $this->Box_model->active_member_counts();
        $alt_row = 0;
        $active_member_count_grid = '';
        foreach ($active_member_count_array as $row) {
            $is_odd = $alt_row % 2 == 1;
            $alt_row_class = $is_odd ? 'alternate-row' : '';

            $active_member_count_grid .= '<div class="ui-block-a ' . $alt_row_class . '">' . $row['active_member_type'] . '</div>';
            $active_member_count_grid .= '<div class="ui-block-b  date-block  ' . $alt_row_class . '">' . $row['member_count'] . '</div>';

            $alt_row++;
        }

        $power_user_count_array = $this->Box_model->get_power_users();
        $alt_row = 0;
        $power_user_count_grid = '';
        foreach ($power_user_count_array as $row) {
            $is_odd = $alt_row % 2 == 1;
            $alt_row_class = $is_odd ? 'alternate-row' : '';

            $power_user_count_grid .= '<div class="ui-block-a ' . $alt_row_class . '">' . $row['user_name'] . '</div>';
            $power_user_count_grid .= '<div class="ui-block-b  ' . $alt_row_class . '">' . $row['box_name'] . '</div>';
            $power_user_count_grid .= '<div class="ui-block-c  date-block  ' . $alt_row_class . '">' . $row['wod_count'] . '</div>';

            $alt_row++;
        }


        $inactive_power_user_count_array = $this->Box_model->get_power_users(TRUE);
        $alt_row = 0;
        $inactive_power_user_count_grid = '';
        foreach ($inactive_power_user_count_array as $row) {
            $is_odd = $alt_row % 2 == 1;
            $alt_row_class = $is_odd ? 'alternate-row' : '';

            $inactive_power_user_count_grid .= '<div class="ui-block-a ' . $alt_row_class . '">' . $row['user_name'] . '</div>';
            $inactive_power_user_count_grid .= '<div class="ui-block-b  ' . $alt_row_class . '">' . $row['box_name'] . '</div>';
            $inactive_power_user_count_grid .= '<div class="ui-block-c  date-block  ' . $alt_row_class . '">' . $this->mysql_to_human($row['max_modified_date']) . '</div>';
            $inactive_power_user_count_grid .= '<div class="ui-block-d  date-block  ' . $alt_row_class . '">' . $row['wod_count'] . '</div>';

            $alt_row++;
        }



        $data['title'] = 'WOD Minder Site Stats';
        $data['site_count_grid'] = $site_count_grid;
        $data['active_member_count_grid'] = $active_member_count_grid;
        $data['power_user_count_grid'] = $power_user_count_grid;
        $data['inactive_power_user_count_grid'] = $inactive_power_user_count_grid;
        $data['view'] = 'mobile_public_site_counts';

        $this->load->vars($data);
        $this->load->view('mobile_master', $data);
    }

    public function ajax_audit() {
        define('CONTROLLER', 3);
        define('SHORT_DESC', 4);

        $controller = $this->uri->segment(CONTROLLER);
        $short_description = $this->uri->segment(SHORT_DESC);

        //START AUDIT
        $this->load->model('Audit_model');
        $audit_data['controller'] = $controller;
        $audit_data['ip_address'] = $_SERVER['REMOTE_ADDR'];
        $audit_data['member_id'] = $this->session->userdata('member_id');
        $audit_data['member_name'] = $this->session->userdata('display_name');
        $audit_data['short_description'] = $short_description;

        $this->Audit_model->save_audit_log($audit_data);
        //END AUDIT
    }

    private function _get_facebook_url() {

        $appId =  'appid';
        $secret = 'secret';
        FacebookSession::setDefaultApplication($appId, $secret);

        // login helper with redirect_uri
        $helper = new FacebookRedirectLoginHelper('http://app.wod-minder.com/index.php/staff/save_box_wod_for_staff');

        return $helper->getLoginUrl();
    }

    //An administration-run only function that gets the site's most recent activity
    private function _get_site_stats() {
        $this->load->model('Box_model');

        $return_html = '';
        $get_recent_activity_array = $this->Box_model->get_recenty_activity();

        $recenty_activity_grid = '';
        $row_count = 0;

        foreach ($get_recent_activity_array as $row) {
            $alt_row_class = $row_count % 2 == 1 ? 'alternate-row' : '';

            $recenty_activity_grid .= '<div class="ui-block-a ' . $alt_row_class . '">' . $row['activity_type'] . '</div>';
            $recenty_activity_grid .= '<div class="ui-block-b ' . $alt_row_class . '">' . $row['user_name'] . '</div>';
            $recenty_activity_grid .= '<div class="ui-block-c ' . $alt_row_class . '">' . $row['the_title'] . '</div>';
            $recenty_activity_grid .= '<div class="ui-block-d ' . $alt_row_class . '">' . $row['the_value'] . '</div>';
            $recenty_activity_grid .= '<div class="ui-block-e number-block ' . $alt_row_class . '">' . $row['the_date'] . '</div>';


            $row_count++;
        }

        if (strlen($recenty_activity_grid) > 0) {
            $header = '<h3>Recenty Activity (except Ray and Jennifer)</h3>';
            $header .= '<div class="ui-grid-d" data-theme="e">';
            $header .= '<div class="ui-block-a mobile-grid-header"><b>Activity</b></div>';
            $header .= '<div class="ui-block-b mobile-grid-header"><b>User</b></div>';
            $header .= '<div class="ui-block-c mobile-grid-header "><b>Title</b></div>';
            $header .= '<div class="ui-block-d mobile-grid-header "><b>The Value</b></div>';
            $header .= '<div class="ui-block-e mobile-grid-header number-block"><b>Date</b></div>';
            $return_html .= $header . $recenty_activity_grid . '</div>';
        }

        return $return_html;
    }

    private function _get_error_logs() {
        $ret_val = '';
        $line_to_remove = "<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>";
        $directory = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR;

        if (!is_dir($directory))
            exit('Invalid diretory path for log files');

        $files = array();

        foreach (scandir($directory) as $file) {
            if ('.' === $file)
                continue;
            if ('..' === $file)
                continue;
            if ('index.html' === $file)
                continue;

            $files[] = $file;
        }

        foreach ($files as $my_file) {
            //echo $my_file.'<br>';

            $file_contents = str_replace($line_to_remove, "", file_get_contents($directory . $my_file));
            $file_contents = str_replace('\r\n', "<br>", $file_contents);
            $file_contents = str_replace('ERROR - ', "<h4>Error</h4>", $file_contents);

            $ret_val .= '<div data-role="collapsible">
				<h3>' . $my_file . '</h3>
				<p>' . $file_contents . '</p>
				<p>' . anchor('administration_functions/delete_log_file/' . $my_file, 'Delete File', array('data-ajax' => 'false', 'data-role' => 'button')) . '</p>
			</div>';
        }

        return $ret_val;
    }

    private function _get_box_stats() {
        $this->load->model('Box_model');
        $member_box_id = $this->Box_model->is_member_staff();

        if (!$member_box_id)
            return FALSE;

        $return_html = '';
        $member_wod_count_grid = '';
        $member_max_count_grid = '';
        $member_paleo_count_grid = '';
        $basic_stats_array = $this->Box_model->get_basic_stats($member_box_id);

        $basic_stats_grid = '';
        $row_count = 0;

        foreach ($basic_stats_array as $row) {
            $alt_row_class = $row_count % 2 == 1 ? 'alternate-row' : '';

            $basic_stats_grid .= '<div class="ui-block-a ' . $alt_row_class . '">' . $row['stat_name'] . '</div>';
            $basic_stats_grid .= '<div class="ui-block-b ' . $alt_row_class . ' date-block">' . $row['stat_value'] . '</div>';

            $row_count++;
        }

        //Get Member WOD Count
        $member_wod_count_array = $this->Box_model->get_member_wod_count($member_box_id);
        if (!!$member_wod_count_array) {
            $row_count = 0;
            $box_count_total = 0;

            foreach ($member_wod_count_array as $row) {
                $alt_row_class = $row_count % 2 == 1 ? 'alternate-row' : '';

                $member_wod_count_grid .= '<div class="ui-block-a ' . $alt_row_class . '">' . $row['full_name'] . '</div>';
                $member_wod_count_grid .= '<div class="ui-block-b ' . $alt_row_class . ' date-block">' . $row['box_wod_count'] . '</div>';

                $box_count_total += $row['box_wod_count'];
                $row_count++;
            }
        }

        //Get Member Max Count
        $member_max_count_array = $this->Box_model->get_member_max_count($member_box_id);
        if (!!$member_max_count_array) {
            $max_count_total = 0;
            $row_count = 0;

            foreach ($member_max_count_array as $row) {
                $alt_row_class = $row_count % 2 == 1 ? 'alternate-row' : '';

                $member_max_count_grid .= '<div class="ui-block-a ' . $alt_row_class . '">' . $row['full_name'] . '</div>';
                $member_max_count_grid .= '<div class="ui-block-b ' . $alt_row_class . ' date-block">' . $row['max_saved_count'] . '</div>';

                $max_count_total += $row['max_saved_count'];
                $row_count++;
            }
        }

        if (strlen($basic_stats_grid) > 0) {
            $header = '<div data-role="collapsible" data-collapsed="false">';
            $header .= '<h3>Basic Stats</h3>';
            $header .= '<div class="ui-grid-a" data-theme="e">';
            $header .= '<div class="ui-block-a mobile-grid-header"><b>Name</b></div>';
            $header .= '<div class="ui-block-b mobile-grid-header number-block"><b>Count</b></div>';
            $return_html .= $header . $basic_stats_grid . '</div></div>';
        }
        if (strlen($member_wod_count_grid) > 0) {
            $total_count_row = '<div class="ui-block-a total-row">Total</div>';
            $total_count_row .= '<div class="ui-block-b total-row number-block">' . $box_count_total . '</div>';


            $header = '<div data-role="collapsible" >';
            $header .= '<h3>Member WOD Count</h3>';
            $header .= '<div class="ui-grid-a" data-theme="e">';
            $header .= '<div class="ui-block-a mobile-grid-header"><b>Name</b></div>';
            $header .= '<div class="ui-block-b mobile-grid-header number-block"><b>Count</b></div>';
            $return_html .= $header . $member_wod_count_grid . $total_count_row . '</div></div>';
        }
        if (strlen($member_max_count_grid) > 0) {
            $total_count_row = '<div class="ui-block-a total-row">Total</div>';
            $total_count_row .= '<div class="ui-block-b total-row number-block">' . $max_count_total . '</div>';

            $header = '<div data-role="collapsible" >';
            $header .= '<h3>Member Max Count</h3>';
            $header .= '<div class="ui-grid-a" data-theme="e">';
            $header .= '<div class="ui-block-a mobile-grid-header"><b>Name</b></div>';
            $header .= '<div class="ui-block-b mobile-grid-header number-block"><b>Total</b></div>';
            $return_html .= $header . $member_max_count_grid . $total_count_row . '</div></div>';
        }


        return $return_html;
    }

    private function _get_barbell_calculator($set_weight = 0) {

        $plate_class_array = array('0.25', '2.5', '5', '10', '15', '25', '35', '45');
        $barbell_calculator = '<div class="ui-grid-b" >';

        $ASCII_A = 97;
        $ASCII_C = 99;
        $current_block = $ASCII_A;

        foreach ($plate_class_array as $value) {
            $barbell_calculator .= '<div class="ui-block-' . chr($current_block) . '">';
            $barbell_calculator .= '<a href="#" class="plate-button" data-role="button" data-corners="false">' . $value . ' #</a>';
            $barbell_calculator .= '</div>';

            $current_block = $current_block === $ASCII_C ? $ASCII_A : $current_block + 1;
        }

        $barbell_calculator .= '</div><!-- /grid-b -->';
        $barbell_calculator .= '<a href="#" id="undo" data-role="button" data-mini="true" data-corners="false">Undo</a>';
        $barbell_calculator .= '<div id="plates_on_bar"></div>';
        return $barbell_calculator;
    }

    private function _get_user_max_history() {
        $max_types = array('T' => 'by Time', 'R' => 'by Reps', 'W' => 'by Weight', 'I' => 'by Inches');
        $divider_row = '<li data-role="list-divider">REPLACE_ME</li>';
        $max_history = '';

        $this->load->model('Exercise_model');
        $history_array = $this->Exercise_model->get_distinct_user_lifts();
        if (!$history_array)
            return FALSE;
        $current_max_type = '';
        foreach ($history_array as $row) {
            if ($current_max_type != $row['max_type']) {
                $max_history .= str_replace('REPLACE_ME', $max_types[$row['max_type']], $divider_row);
                $current_max_type = $row['max_type'];
            }
            $max_history .= '<li><a data-ajax="false" href="' . base_url() . 'index.php/member/exercise_history/' . $row['exercise_id'] . '">' . $row['title'] . '</a><span class="ui-li-count">' . $row['lift_count'] . '</span></li>';
        }

        return $max_history;
    }

    private function _get_user_max_snapshot() {
        //Single rep maxes get special treatment.  They will be used in the UI to calculate & of maxes in the max snapshot screen
        define("SINGLE_REP_MAX", 0);
        $header = '';
        $user_max_snapshot['single_rep_max_snapshot'] = '';
        $user_max_snapshot['user_max_snapshot'] = '';

        /*
          $user_max_snapshot	=	'<div class="ui-block-a"><b>Exercise</b></div>';
          $user_max_snapshot	.=	'<div class="ui-block-b"><b>Lift Date</b></div>';
          $user_max_snapshot	.=	'<div class="ui-block-c number-block"><b>Weight(#)</b></div>';
         */

        $this->load->model('Exercise_model');
        $snapshot_array = $this->Exercise_model->get_user_max_snapshot();

        if ($snapshot_array == FALSE)
            return FALSE;

        $single_rep_max_grid = '';
        $multi_rep_max_grid = '';
        $time_max_grid = '';
        $other_rep_max_grid = '';
        $inches_max_grid = '';
        $current_max_type = '';
        $row_count = 0;

        foreach ($snapshot_array as $row) {
            if ($row['max_type'] !== $current_max_type) {
                $current_max_type = $row['max_type'];
                $row_count = 0;
            }
            $alt_row_class = $row_count % 2 == 1 ? 'alternate-row' : '';
            if ($row['super_order'] == 0) {
                $single_rep_max_grid .= '<div class="ui-block-a ' . $alt_row_class . '">' . $row['exercise'] . '</div>';
                $single_rep_max_grid .= '<div class="ui-block-b ' . $alt_row_class . ' date-block">' . $row['max_date'] . '</div>';
                $single_rep_max_grid .= '<div class="ui-block-c ' . $alt_row_class . ' number-block"><div class="original-value">' . $row['max_value'] . '</div><div class="calculated-value"> <a href="#">' . $row['max_value'] . '</a></div></div>';
            } else {

                switch ($row['max_type']) {
                    case 'W':
                        $multi_rep_max_grid .= '<div class="ui-block-a ' . $alt_row_class . ' ">' . $row['exercise'] . ', ' . $row['max_rep'] . '</div>';
                        $multi_rep_max_grid .= '<div class="ui-block-b ' . $alt_row_class . ' date-block">' . $row['max_date'] . '</div>';
                        $multi_rep_max_grid .= '<div class="ui-block-c ' . $alt_row_class . ' number-block">' . $row['max_value'] . '</div>';
                        break;
                    case 'T':
                        $time_max_grid .= '<div class="ui-block-a ' . $alt_row_class . ' ">' . $row['exercise'] . '</div>';
                        $time_max_grid .= '<div class="ui-block-b ' . $alt_row_class . ' date-block">' . $row['max_date'] . '</div>';
                        $time_max_grid .= '<div class="ui-block-c ' . $alt_row_class . ' number-block">' . $row['max_value'] . '</div>';
                        break;
                    case 'R':
                        $other_rep_max_grid .= '<div class="ui-block-a ' . $alt_row_class . ' ">' . $row['exercise'] . '</div>';
                        $other_rep_max_grid .= '<div class="ui-block-b ' . $alt_row_class . ' date-block">' . $row['max_date'] . '</div>';
                        $other_rep_max_grid .= '<div class="ui-block-c ' . $alt_row_class . ' number-block">' . $row['max_value'] . '</div>';
                        ;
                        break;
                    case 'I':
                        $inches_max_grid .= '<div class="ui-block-a ' . $alt_row_class . ' ">' . $row['exercise'] . '</div>';
                        $inches_max_grid .= '<div class="ui-block-b ' . $alt_row_class . ' date-block">' . $row['max_date'] . '</div>';
                        $inches_max_grid .= '<div class="ui-block-c ' . $alt_row_class . ' number-block">' . $row['max_value'] . '</div>';
                        ;
                        break;
                }
            }
            $row_count++;
        }

        if (strlen($single_rep_max_grid) > 0) {
            $header = '<h3>Single Rep Max</h3>';
            $header .= '<div class="ui-grid-b" data-theme="e">';
            $header .= '<div class="ui-block-a mobile-grid-header"><b>Exercise</b></div>';
            $header .= '<div class="ui-block-b mobile-grid-header date-block"><b>Lift Date</b></div>';
            $header .= '<div class="ui-block-c  mobile-grid-header number-block"><b>Weight(#)</b></div>';
            $user_max_snapshot['single_rep_max_snapshot'] = $header . $single_rep_max_grid . '</div>';
        }
        if (strlen($multi_rep_max_grid) > 0) {
            $header = '<h3>Multiple Rep Max</h3>';
            $header .= '<div class="ui-grid-b" data-theme="e">';
            $header .= '<div class="ui-block-a mobile-grid-header"><b>Exercise</b></div>';
            $header .= '<div class="ui-block-b mobile-grid-header date-block"><b>Lift Date</b></div>';
            $header .= '<div class="ui-block-c mobile-grid-header number-block"><b>Weight(#)</b></div>';
            $user_max_snapshot['user_max_snapshot'] .= $header . $multi_rep_max_grid . '</div>';
        }
        if (strlen($time_max_grid) > 0) {
            $header = '<h3>Run Times</h3>';
            $header .= '<div class="ui-grid-b" data-theme="e">';
            $header .= '<div class="ui-block-a mobile-grid-header"><b>Exercise</b></div>';
            $header .= '<div class="ui-block-b mobile-grid-header date-block"><b>Date</b></div>';
            $header .= '<div class="ui-block-c mobile-grid-header number-block"><b>Time</b></div>';
            $user_max_snapshot['user_max_snapshot'] .= $header . $time_max_grid . '</div>';
        }
        if (strlen($other_rep_max_grid) > 0) {
            $header = '<h3>Reps in 1-minute</h3>';
            $header .= '<div class="ui-grid-b" data-theme="e">';
            $header .= '<div class="ui-block-a mobile-grid-header"><b>Exercise</b></div>';
            $header .= '<div class="ui-block-b mobile-grid-header date-block"><b>Date</b></div>';
            $header .= '<div class="ui-block-c mobile-grid-header number-block"><b>Max Reps</b></div>';
            $user_max_snapshot['user_max_snapshot'] .= $header . $other_rep_max_grid . '</div>';
        }
        if (strlen($inches_max_grid) > 0) {
            $header = '<h3>by Inches</h3>';
            $header .= '<div class="ui-grid-b" data-theme="e">';
            $header .= '<div class="ui-block-a mobile-grid-header"><b>Exercise</b></div>';
            $header .= '<div class="ui-block-b mobile-grid-header date-block"><b>Date</b></div>';
            $header .= '<div class="ui-block-c mobile-grid-header number-block"><b>Inches</b></div>';
            $user_max_snapshot['user_max_snapshot'] .= $header . $inches_max_grid . '</div>';
        }

        return $user_max_snapshot;
    }

    private function _get_exercise_list() {
        $max_types = array('T' => 'by Time', 'R' => 'by Reps', 'W' => 'by Weight', 'I' => 'by Inches');
        $divider_row = '<li data-role="list-divider">REPLACE_ME</li>';
        $exercise_list = '';
        $this->load->model('Exercise_model');
        $exercise_list_array = $this->Exercise_model->get_exercise_list($this->session->userdata('member_id'));
        $current_max_type = '';
        foreach ($exercise_list_array as $row) {
            $data_theme = 'data-theme="' . ($row['recorded_max'] ? 'e' : 'c') . '"';
            if ($current_max_type != $row['max_type']) {
                $exercise_list .= str_replace('REPLACE_ME', $max_types[$row['max_type']], $divider_row);
                $current_max_type = $row['max_type'];
            }
            $exercise_list .= '<li ' . $data_theme . ' data-filtertext="' . $max_types[$row['max_type']] . ' ' . $row['title'] . '"><a data-ajax="false" href="' . base_url() . 'index.php/exercise/save_member_max/exercise_id/' . $row['exercise_id'] . '">' . $row['title'] . '</a></li>';
        }

        return $exercise_list;
    }

    private function _get_box_wod() {
        $this->load->model('Box_model');
        $box_wod_array = $this->Box_model->get_box_wod_by_members_box($this->session->userdata('member_box_id'), $this->session->userdata('member_id'));
        if (!$box_wod_array)
            return 'No WODs are saved for the box you\'ve selected.';
        $box_wod_list = '';
        foreach ($box_wod_array as $row) {
            $wod_name = ($row['tier_name'] == '' ? '' : $row['tier_name'] . ':  ') . $row['simple_title'];
            $data_theme = 'data-theme="' . ($row['recorded_wod'] ? 'e' : 'c') . '"';
            $box_wod_list .= '<li ' . $data_theme . '><a data-ajax="false" href="' . base_url() . 'index.php/wod/save_member_box_wod/' . $row['bw_id'] . '">' . $wod_name . '</a><span class="ui-li-count">' . $this->mysql_to_human($row['wod_date']) . '</span></li>';
        }
        return $box_wod_list;
    }

    private function _get_user_benchmark_wod_history() {
        $benchmark_wod_categorys = array('G' => 'Girl', 'H' => 'Hero', 'O' => 'Other');
        $divider_row = '<li data-role="list-divider">REPLACE_ME</li>';
        $benchmark_wod_history = '';

        $this->load->model('Wod_model');
        $history_array = $this->Wod_model->get_member_benchmark_wods();
        if (!$history_array)
            return FALSE;
        $current_benchmark_wod_category = '';
        foreach ($history_array as $row) {
            if ($current_benchmark_wod_category != $row['category']) {
                $benchmark_wod_history .= str_replace('REPLACE_ME', $benchmark_wod_categorys[$row['category']], $divider_row);
                $current_benchmark_wod_category = $row['category'];
            }
            $benchmark_wod_history .= '<li><a data-ajax="false" href="' . base_url() . 'index.php/member/benchmark_wod_history/' . $row['wod_id'] . '">' . $row['title'] . '</a><span class="ui-li-count">' . $row['wod_count'] . '</span></li>';
        }

        return $benchmark_wod_history;
    }

    private function _get_user_box_wod_history() {

        if (!$this->logged_in)
            redirect('member/login');

        $this->load->model('Wod_model');
        $box_wod_history_array = $this->Wod_model->get_member_box_wods();

        $box_wod_history = '';
        $alt_row = 0;
        foreach ($box_wod_history_array as $row) {
            $is_odd = $alt_row % 2 == 1;
            $alt_row_class = $is_odd ? 'alternate-row' : '';

            $delete_link = '<a href="#" data-ajax="false" data-role="button" data-icon="delete" data-iconpos="notext" >' . $row['mw_id'] . '</a>';
            $box_wod_history .= '<div class="ui-block-a ' . $alt_row_class . '">' . $delete_link . '</div>';
            $edit_link = '<a href="' . base_url() . 'index.php/wod/save_member_box_wod/' . $row['bw_id'] . '" data-ajax="false">' . $this->mysql_to_human($row['wod_date']) . '</a>';
            $box_wod_history .= '<div class="ui-block-b  date-block  grid-row-with-image ' . $alt_row_class . '">' . $edit_link . '</div>';
            $box_wod_history .= '<div class="ui-block-c number-block grid-row-with-image ' . $alt_row_class . '">' . $row['simple_title'] . '</div>';
            $box_wod_history .= '<div class="ui-block-d number-block grid-row-with-image ' . $alt_row_class . '">' . $row['score'] . '</div>';

            $alt_row++;
        }


        return $box_wod_history;
    }

    private function _get_user_custom_wod_history() {

        $this->load->model('Wod_model');
        $custom_wod_history_array = $this->Wod_model->get_member_custom_wod_history();

        $custom_wod_history = '';
        $alt_row = 0;
        foreach ($custom_wod_history_array as $row) {
            $is_odd = $alt_row % 2 == 1;
            $alt_row_class = $is_odd ? 'alternate-row' : '';

            $delete_link = '<a href="#" data-ajax="false" data-role="button" data-icon="delete" data-iconpos="notext" >' . $row['mw_id'] . '</a>';
            $custom_wod_history .= '<div class="ui-block-a ' . $alt_row_class . '">' . $delete_link . '</div>';
            $edit_link = '<a href="' . base_url() . 'index.php/wod/save_member_custom_wod/' . $row['mw_id'] . '" data-ajax="false">' . $this->mysql_to_human($row['wod_date']) . '</a>';
            $custom_wod_history .= '<div class="ui-block-b  date-block  grid-row-with-image ' . $alt_row_class . '">' . $edit_link . '</div>';
            $custom_wod_history .= '<div class="ui-block-c number-block grid-row-with-image ' . $alt_row_class . '">' . $row['custom_title'] . '</div>';
            $custom_wod_history .= '<div class="ui-block-d number-block grid-row-with-image ' . $alt_row_class . '">' . $row['score'] . '</div>';

            $alt_row++;
        }


        return $custom_wod_history;
    }

    //Benchmark WODs are not tied to any Box. 
    private function _get_benchmark_wod() {
        $this->load->model('Wod_model');
        $benchmark_wod_array = $this->Wod_model->get_benchmark_wod();
        $wod_list = '';
        foreach ($benchmark_wod_array as $row)
            $wod_list .= '<li><a data-ajax="false" href="' . base_url() . 'index.php/wod/save_member_benchmark_wod/wod_id/' . $row['wod_id'] . '">' . $row['wod_name'] . '</a></li>';

        return $wod_list;
    }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */