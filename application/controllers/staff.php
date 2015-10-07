<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Staff Class
 *
 * @package wod-minder
 * @subpackage controller
 * @category Staff
 * @author Ray Nowell
 *
 */
class Staff extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('form_validation');
    }

    /**
     * Index Page for this controller.
     *
     * @access public
     */
    public function index() {

        redirect('welcome/index/TRUE');
    }

    public function save_staff_training_log() {
        //$this->output->enable_profiler(TRUE);
        define('ID_VALUE', 3);

        //$this->output->enable_profiler(TRUE);
        if (!$this->logged_in)
            redirect('member/login');

        $error_message = '';
        $this->load->model('Box_model');
        $box_id = $this->Box_model->is_member_staff();
        if (!$box_id)
            redirect('welcome/index/TRUE'); //user is not a staff member

        $id_value = $this->uri->segment(ID_VALUE);
        $this->load->model('Staff_model');

        $staff_training_log_data = '';
        if ($id_value == '') { //This will be a new wod to save
            $staff_training_log_data['bct_id'] = '';
            $staff_training_log_data['box_id'] = $box_id;
            $staff_training_log_data['training_date'] = date('m/d/y');
            $staff_training_log_data['class_size'] = '';
            $staff_training_log_data['note'] = '';
        } else { //Saving an existing staff traning log
            $log_data = $this->Staff_model->get_staff_training_log($id_value);
            $staff_training_log_data['bstl_id'] = $log_data->bstl_id;
            $staff_training_log_data['bct_id'] = $log_data->bct_id;
            $staff_training_log_data['box_id'] = $log_data->box_id;
            $staff_training_log_data['training_date'] = $log_data->training_date;
            $staff_training_log_data['class_size'] = $log_data->class_size;
            $staff_training_log_data['note'] = $log_data->note;
        }


        $this->form_validation->set_rules('bct_id', 'bct_id', 'trim');
        $this->form_validation->set_rules('training_date', 'Training Date', 'trim');
        $this->form_validation->set_rules('class_size', 'Class Size', 'trim');
        $this->form_validation->set_rules('note', 'Note', 'trim');

        if ($this->form_validation->run() == TRUE) {
            //Determines insert or update in data layer
            if ($id_value != '')
                $data['bstl_id'] = $id_value;

            $data['member_id'] = $this->session->userdata('member_id');
            $data['box_id'] = $box_id;
            $data['bct_id'] = $this->input->post('bct_id');
            $data['training_date'] = $this->make_us_date_mysql_friendly($this->input->post('training_date'));
            $data['class_size'] = $this->input->post('class_size');
            $data['note'] = $this->input->post('note');

            $ret_val = $this->Staff_model->save_staff_training_log($data);
            if ($ret_val['success']) {
                $this->session->set_flashdata('good_message', 'Training Time Logged.');
                redirect('welcome/index/TRUE');
            } else
                $error_message = $ret_val['message'];
        }

        $data['title'] = 'Training Log';
        $data['heading'] = 'Save Training Log';
        $data['view'] = 'mobile_staff_training_log_save';
        $data['id_value'] = $id_value;

        $data['error_message'] = (validation_errors()) ? validation_errors() : $error_message;

        $this->load->helper('form');
        $form_data = $this->_get_staff_training_log_form_data($staff_training_log_data);

        $data = array_merge($data, $form_data);

        $this->load->vars($data);
        $this->load->view('mobile_master', $data);
    }

    public function get_staff_training_log_history() {

        if (!$this->logged_in)
            redirect('member/login');

        //START AUDIT
        $this->load->model('Audit_model');
        $audit_data['controller'] = 'staff_controller';
        $audit_data['ip_address'] = $_SERVER['REMOTE_ADDR'];
        $audit_data['member_id'] = $this->session->userdata('member_id');
        $audit_data['member_name'] = $this->session->userdata('display_name');
        $audit_data['short_description'] = 'Get Training Log History';
        $this->Audit_model->save_audit_log($audit_data);
        //END AUDIT

        $this->load->model('Staff_model');
        $staff_training_log_history_array = $this->Staff_model->get_staff_training_log_history();

        $staff_training_log_history = '';
        $alt_row = 0;

        foreach ($staff_training_log_history_array as $row) {
            $is_odd = $alt_row % 2 == 1;
            $alt_row_class = $is_odd ? 'alternate-row' : '';

            $delete_link = '<a href="#" data-ajax="false" data-role="button" data-icon="delete" data-iconpos="notext" >' . $row['bstl_id'] . '</a>';
            $staff_training_log_history .= '<div class="ui-block-a ' . $alt_row_class . '">' . $delete_link . '</div>';
            $edit_link = '<a href="' . base_url() . 'index.php/staff/save_staff_training_log/' . $row['bstl_id'] . '" data-ajax="false">' . $row['training_date'] . '</a>';
            $staff_training_log_history .= '<div class="ui-block-b  date-block  grid-row-with-image ' . $alt_row_class . '">' . $edit_link . '</div>';
            $staff_training_log_history .= '<div class="ui-block-c number-block grid-row-with-image ' . $alt_row_class . '">' . $row['class_time_description'] . '</div>';

            $alt_row++;
        }

        if ($staff_training_log_history === '')
            $data['staff_training_log_history'] = 'No Training Logs Saved';
        else
            $data['staff_training_log_history'] = $staff_training_log_history;

        $data['doc_ready_call'] = 'mobile_staff_training_log_history_doc_ready();';
        $data['title'] = 'Training Log';
        $data['heading'] = 'Training Log';
        $data['view'] = 'mobile_staff_training_log_history';

        $this->load->vars($data);
        $this->load->view('mobile_master', $data);

        return;
    }

    public function delete_staff_training_log() {
        $delete_id = $this->uri->segment(3);

        $this->load->model('Staff_model');
        $staff_training_log_history = $this->Staff_model->get_staff_training_log_history($delete_id);
        if (!$staff_training_log_history) {
            $this->session->set_flashdata('error_message', 'Record not found');
            redirect('welcome/index/TRUE');
        }

        $ret_val = $this->Staff_model->delete_training_log($delete_id);

        $this->session->set_flashdata('error_message', 'Record deleted');
        redirect('welcome/index/TRUE');
    }

    public function save_box_wod_for_staff() {
        //$this->output->enable_profiler(TRUE);
        if (!$this->logged_in)
            redirect('member/login');

        $error_message = '';
        $this->load->model('Box_model');
        $box_id = $this->Box_model->is_member_staff();
        $social_media_data = $this->Box_model->get_box_social_media_data($box_id);
        if (!$box_id)
            redirect('welcome/index/TRUE'); //user is not a staff member

        $bw_id = $this->uri->segment(3);

        $this->form_validation->set_rules('wod_date', 'Date', 'trim|required');
        $this->form_validation->set_rules('simple_title', 'WOD Name', 'trim');
        $this->form_validation->set_rules('simple_description', 'WOD Description', 'trim');
        $this->form_validation->set_rules('daily_message', 'Daily Message', 'trim');
        $this->form_validation->set_rules('image_caption', 'Image Caption', 'trim');
        $this->form_validation->set_rules('buy_in', 'Buy-In', 'trim');
        $this->form_validation->set_rules('cash_out', 'Cash Out', 'trim');
        $this->form_validation->set_rules('score_type', 'Score Type', 'trim|required');
        $this->form_validation->set_rules('wod_type_id', 'WOD Type', 'trim');
        $this->form_validation->set_rules('scale_id', 'Scale', 'trim');
        $this->form_validation->set_rules('wod_id', 'wod id', 'trim');
        $this->form_validation->set_rules('bwt_id', 'bwt id', 'trim');
        $this->form_validation->set_rules('image-source', 'Image Source', 'trim');
        $this->form_validation->set_rules('image_link', 'Image Link', 'trim');

        //Did user select an image to upload the file?
        $image_data = FALSE;
        $image_ok = TRUE;
        $unlink_existing_image = FALSE;

        if (isset($_FILES['userfile']) && $_FILES['userfile']['name'] != '' && $this->input->post('image-source') === 'pc') {
            $config['upload_path'] = './uploads/';
            $config['allowed_types'] = 'gif|jpg|png|jpeg';
            $config['max_size'] = '7160'; //7MB
            $this->load->library('upload', $config);

            if (!$this->upload->do_upload()) {
                $image_error = $this->upload->display_errors();
                $image_ok = false;
            } else
                $image_data = array('upload_data' => $this->upload->data());
        }

        if ($image_ok && $this->form_validation->run() == TRUE) {
            if ($bw_id != '')
                $data['bw_id'] = $bw_id;

            $wod_date = $this->input->post('wod_date');
            $data['wod_date'] = $this->make_us_date_mysql_friendly($wod_date);
            $data['box_id'] = $box_id;
            $wod_name = $this->input->post('simple_title');
            $wod_name = $wod_name === '' ? str_replace('/', '.', $wod_date) : $wod_name;
            $data['simple_title'] = $wod_name;
            $data['simple_description'] = $this->input->post('simple_description');
            $data['daily_message'] = $this->input->post('daily_message');
            $data['image_caption'] = $this->input->post('image_caption');
            $data['buy_in'] = $this->input->post('buy_in');
            $data['cash_out'] = $this->input->post('cash_out');
            $data['score_type'] = $this->input->post('score_type');
            $data['form_uniqid'] = $this->input->post('form_uniqid');
            $image_source = $this->input->post('image-source');
            $post_to_facebook = $this->input->post('post_to_facebook') === 'yes';

            $wod_type_id = $this->input->post('wod_type_id');
            if ($wod_type_id > 0)
                $data['wod_type_id'] = $wod_type_id;

            $bwt_id = $this->input->post('bwt_id');
            if ($bwt_id > 0)
                $data['bwt_id'] = $bwt_id;

            //If saving a box_wod, do not save scale and vice versa
            $scale_id = $this->input->post('scale_id');
            $data['wod_id'] = $this->input->post('wod_id');

            if ($scale_id > 0)
                $data['scale_id'] = $scale_id;
            else
                $data['scale_id'] = '';
            if ($image_source === 'web') {
                $data['image_link'] = $this->input->post('image_link');
                $data['image_name'] = '';
                $unlink_existing_image = TRUE;
            } else
                $data['image_link'] = '';

            if (!$image_data) {
                //do nothing.  no image.
            } else {
                $destination_folder = '/staff_images/box_wod/';
                $file_prefix = 'box_wod';
                define("IMAGE_MAX_HEIGHT", "9000"); //Height not an issue for the box wod image.  Somtimes, trainer has "tall" image showing proper movements.
                define("IMAGE_MAX_WIDTH", "900");
                $new_image_name = $this->process_image($image_data['upload_data'], $destination_folder, $file_prefix, IMAGE_MAX_HEIGHT, IMAGE_MAX_WIDTH);

                $data['image_name'] = $new_image_name;
                $unlink_existing_image = TRUE;
            }

            if ($unlink_existing_image) {
                //See if there is an exising image to overwrite
                $bw_data = FALSE;
                if ($bw_id != '')
                    $bw_data = $this->Box_model->get_box_wod($bw_id);

                if ($bw_data != null && $bw_data->image_name !== '') {
                    //Drop the old image (and thumbnail):
                    $file_name = $_SERVER['DOCUMENT_ROOT'] . '/staff_images/box_wod/' . $bw_data->image_name;

                    if (file_exists($file_name)) {
                        chown($file_name, 666); //Insert an Invalid UserId to set to Nobody Owern; 666 is my standard for "Nobody"
                        unlink($file_name);
                    }

                    $file_name = $_SERVER['DOCUMENT_ROOT'] . '/staff_images/box_wod/thumbnail/' . str_replace('.jpg', '_thumb.jpg', $bw_data->image_name);
                    if (file_exists($file_name)) {
                        chown($file_name, 666); //Insert an Invalid UserId to set to Nobody Owern; 666 is my standard for "Nobody"
                        unlink($file_name);
                    }
                }
            }

            $ret_val = $this->Box_model->save_box_wod($data, $box_id);
            if ($ret_val['success']) {

                if (!!$social_media_data && $social_media_data['sm_package'] && $post_to_facebook && !$ret_val['suspect_duplicate']) {
                    if ($social_media_data['facebook_page_id'] !== '') {
                        $page_session = $this->_connect_to_facebook($social_media_data['facebook_page_id']);
                        $facebook_post_array = null;
                        if ($image_data || $data['image_link'] != '') {
                            $image_name = '';
                            if ($data['image_link'] != '')
                                $image_name = $data['image_link'];
                            else
                                $image_name = base_url() . 'staff_images/box_wod/' . $data['image_name'];

                            //NOTE:  You can add the following fields to give a more descriptive example:  'description'=>'my description', 'caption'=>'Daily Image',
                            //       And yes, use 'link' and not 'picture' parameter
                            $facebook_post_array = array('message' => $this->input->post('facebook_text'), 'name' => 'Daily Image', 'description' => $data['image_caption'], 'link' => $image_name, 'access_token' => $page_session['access_token'], 'cb' => '');
                        } else
                            $facebook_post_array = array('message' => $this->input->post('facebook_text'), 'access_token' => $page_session['access_token'], 'cb' => '');

                        $page_session['facebook_var']->api('/' . $social_media_data['facebook_page_id'] . '/feed', 'post', $facebook_post_array);
                    }

                    if ($social_media_data['twitter_id'] !== '')
                        log_message('debug', date('H:i:s'), " Create twitter code");
                }

                $this->session->set_flashdata('good_message', 'Box WOD saved.');
                redirect('welcome/index/TRUE');
            } else
                $error_message = $ret_val['message'];
        }

        //form_uniqid doesn't apply to updates but setting here so no errors show up in HTML
        $data['form_uniqid'] = uniqid();
        if ($bw_id != '') {
            $bw = $this->Box_model->get_box_wod($bw_id);
            $box_wod['bw_id'] = $bw->bw_id;
            $box_wod['wod_date'] = $this->mysql_to_human($bw->wod_date);
            $box_wod['daily_message'] = $bw->daily_message;
            $box_wod['image_caption'] = $bw->image_caption;
            $box_wod['buy_in'] = $bw->buy_in;
            $box_wod['cash_out'] = $bw->cash_out;
            $box_wod['simple_title'] = $bw->simple_title;
            $box_wod['simple_description'] = $bw->simple_description;
            $box_wod['wod_id'] = $bw->wod_id;
            $box_wod['scale_id'] = $bw->scale_id;
            $box_wod['score_type'] = $bw->score_type;
            $box_wod['wod_type_id'] = $bw->wod_type_id;
            $box_wod['image_name'] = $bw->image_name;
            $box_wod['bwt_id'] = $bw->bwt_id;

            $data['use_wizard'] = FALSE; //Don't use wizard when user is editing an existing box wod
        } else {
            $box_wod['bw_id'] = '';
            $box_wod['wod_date'] = date('m/d/y');
            $box_wod['daily_message'] = '';
            $box_wod['image_caption'] = '';
            $box_wod['buy_in'] = '';
            $box_wod['cash_out'] = '';
            $box_wod['simple_title'] = '';
            $box_wod['simple_description'] = '';
            $box_wod['wod_id'] = '';
            $box_wod['scale_id'] = '';
            $box_wod['score_type'] = '';
            $box_wod['wod_type_id'] = '';
            $box_wod['image_name'] = '';
            $box_wod['bwt_id'] = '';

            $data['use_wizard'] = TRUE; //Creating new box_wod; use wizard
        }

        $data['title'] = 'Save WOD';
        $data['heading'] = 'Save WOD';
        $data['view'] = 'mobile_staff_box_wod_save';
        $data['other_function_call'] = 'box_wod_save_page_init();';

        $data['error_message'] = (validation_errors()) ? validation_errors() : $error_message;
        if (!$image_ok)
            $data['error_message'] .= $image_error;

        if ($data['use_wizard'] && strlen($data['error_message']) > 0)
            $data['use_wizard'] = FALSE;
        else //var use_wizard has passed all checks, load all lists required to show the wizard
            $wizard_data = $this->_get_box_wod_wizard_data();

        $this->load->helper('form');
        $box_wod_data = $this->_get_box_wod_form_data($box_wod);
        $box_wod_data['bw_id'] = $box_wod['bw_id'];

        if ($data['use_wizard']) {
            if (!$social_media_data) {
                $error_message = 'No box data found for your box; contact ray023@gmail.com';
                $this->session->set_flashdata('error_message', $error_message);
                redirect('welcome/index/TRUE');
            }

            if ($social_media_data['sm_package']) {
                if ($social_media_data['facebook_page_id'] !== '')
                    $wizard_data['facebook_user'] = $this->_connect_to_facebook($social_media_data['facebook_page_id']);

                if ($social_media_data['twitter_id'] !== '')
                    log_message('debug', date('H:i:s'), " Create twitter code");
            }

            $data = array_merge($data, $box_wod_data, $wizard_data, $social_media_data);
        } else
            $data = array_merge($data, $box_wod_data);

        $this->load->vars($data);
        $this->load->view('mobile_master', $data);
    }

    public function edit_box_wod_for_staff() {
        if (!$this->logged_in)
            redirect('member/login');

        $this->load->model('Box_model');
        $box_id = $this->Box_model->is_member_staff();
        if (!$box_id)
            redirect('welcome/index/TRUE'); //user is not a staff member


        $data['title'] = 'Edit Box WOD';
        $data['heading'] = 'Edit Box WOD';
        $data['view'] = 'mobile_staff_edit_box_wod';

        $this->load->model('Box_model');
        $box_wod_list_for_staff_array = $this->Box_model->get_box_wods_for_staff($box_id);

        $box_wod_list = '';

        foreach ($box_wod_list_for_staff_array as $row) {
            $wod_name = ($row['tier_name'] == '' ? '' : $row['tier_name'] . ':  ') . $row['simple_title'];
            $box_wod_list .= '<li><a data-ajax="false" href="' . base_url() . 'index.php/staff/save_box_wod_for_staff/' . $row['bw_id'] . '">' . $wod_name . '</a><span class="ui-li-count">' . $this->mysql_to_human($row['wod_date']) . '</span></li>';
        }

        $data['box_wod_list'] = $box_wod_list;

        $this->load->vars($data);
        $this->load->view('mobile_master', $data);

        return;
    }

    //Gives staff member a readable interface to see a snapshot of all member maxes
    public function member_maxes() {
        //$this->output->enable_profiler(TRUE);
        $this->load->model('Box_model');
        $member_box_id = $this->Box_model->is_member_staff();

        //START AUDIT
        $this->load->model('Audit_model');
        $audit_data['controller'] = 'staff_controller';
        $audit_data['ip_address'] = $_SERVER['REMOTE_ADDR'];
        $audit_data['member_id'] = $this->session->userdata('member_id');
        $audit_data['member_name'] = $this->session->userdata('display_name');
        $audit_data['short_description'] = 'Staff Member Maxes';
        $this->Audit_model->save_audit_log($audit_data);
        //END AUDIT

        if (!$member_box_id)
            redirect('welcome/index/TRUE');

        $data['member_exercises'] = $this->_get_member_exercises($member_box_id);
        $data['member_max_details'] = $this->_get_member_max_details($member_box_id);

        $data['title'] = 'Member Maxes';
        $data['heading'] = 'Member Maxes';
        $data['view'] = 'mobile_staff_member_maxes';

        $this->load->vars($data);
        $this->load->view('mobile_master', $data);
    }

    public function daily_wods() {
        $this->load->model('Box_model');
        $member_box_id = $this->Box_model->is_member_staff();

        //START AUDIT
        $this->load->model('Audit_model');
        $audit_data['controller'] = 'staff_controller';
        $audit_data['ip_address'] = $_SERVER['REMOTE_ADDR'];
        $audit_data['member_id'] = $this->session->userdata('member_id');
        $audit_data['member_name'] = $this->session->userdata('display_name');
        $audit_data['short_description'] = 'Staff Daily WODs';
        $this->Audit_model->save_audit_log($audit_data);
        //END AUDIT

        if (!$member_box_id)
            redirect('welcome/index/TRUE');

        $data['daily_box_wods'] = $this->_get_daily_box_wods($member_box_id);
        $data['daily_wod_details'] = $this->_get_daily_box_wod_details($member_box_id);

        $data['title'] = 'Daily WODs';
        $data['heading'] = 'Daily WODs';
        $data['view'] = 'mobile_staff_daily_wods';

        $this->load->vars($data);
        $this->load->view('mobile_master', $data);
    }

    //Return the rx'ed benchmarked wod scores for a box
    public function box_leader_board() {

        $this->load->model('Box_model');
        $member_box_id = $this->Box_model->is_member_staff();

        //START AUDIT
        $this->load->model('Audit_model');
        $audit_data['controller'] = 'staff_controller';
        $audit_data['ip_address'] = $_SERVER['REMOTE_ADDR'];
        $audit_data['member_id'] = $this->session->userdata('member_id');
        $audit_data['member_name'] = $this->session->userdata('display_name');
        $audit_data['short_description'] = 'Box Leader Board';
        $this->Audit_model->save_audit_log($audit_data);
        //END AUDIT

        if (!$member_box_id)
            redirect('welcome/index/TRUE');

        $leader_board_array = $this->_get_leader_board($member_box_id);

        $data['wod_list'] = $leader_board_array['wod_list'];
        $data['wod_details'] = $leader_board_array['wod_details'];

        $data['title'] = 'WOD Leader Board';
        $data['heading'] = 'WOD Leader Board';
        $data['view'] = 'mobile_staff_leaderboard';

        $this->load->vars($data);
        $this->load->view('mobile_master', $data);
    }

    //Makes sure user is connected and has permission to page
    private function _connect_to_facebook($facebook_page_id = '') {
        if ($facebook_page_id === '')
            return false;

        require $_SERVER['DOCUMENT_ROOT'] . '/library/facebook.php';

        $facebook = new Facebook(array(
            'appId' => 'app_id',
            'secret' => 'secret',
            'cookie' => true,
        ));

        $user = $facebook->getUser();

        if ($user) {
            try {
                // Proceed knowing you have a logged in user who's authenticated.
                $user_profile = $facebook->api('/me');
            } catch (FacebookApiException $e) {
                log_message('error', 'FACEBOOK ERROR:' . $e);
                error_log($e);
                $user = null;
            }
        }

        // Login user if null
        if (!$user) {
            $loginUrl = $facebook->getLoginUrl(array('scope' => 'publish_actions, manage_pages'));
            die('<script> top.location.href="' . $loginUrl . '";</script>');
        }

        $access_token = FALSE;
        $page_found = FALSE;
        $fb_accounts = $facebook->api('/me/accounts');

        //START AUDIT
        $fb_audit_data = '';
        foreach ($fb_accounts['data'] as $internal_array) {
            foreach ($internal_array as $key => $value) {
                $fb_audit_data = $fb_audit_data . "$key|$value NEW_LINE";
            }
        }
        $this->load->model('Audit_model');
        $audit_data['controller'] = 'staff_controller';
        $audit_data['ip_address'] = $_SERVER['REMOTE_ADDR'];
        $audit_data['member_id'] = $this->session->userdata('member_id');
        $audit_data['member_name'] = $this->session->userdata('display_name');
        $audit_data['short_description'] = 'FB Audit Data';
        $audit_data['full_info'] = $fb_audit_data;
        $this->Audit_model->save_audit_log($audit_data);
        //END AUDIT

        foreach ($fb_accounts['data'] as $my_array) {
            if ($my_array['id'] === $facebook_page_id) {
                $access_token = $my_array['access_token'];
                $page_found = TRUE;
                break;
            }
        }

        $user_profile['page_admin'] = $page_found;
        $user_profile['access_token'] = $access_token;
        $user_profile['facebook_var'] = $facebook; //kinda whacky...just tryting to make it work
        return $user_profile; //user is logged in and has rights to page
    }

    private function _get_box_wod_form_data($member_box_wod) {
        $data = null;

        $data['daily_message'] = array(
            'name' => 'daily_message',
            'id' => '_dailyMessage',
            'autocomplete' => 'off',
            'value' => set_value('daily_message', $member_box_wod['daily_message'])
        );

        $data['image_name'] = array(
            'name' => 'image_name',
            'id' => '_imageName',
            'autocomplete' => 'off',
            'value' => set_value('no_image_filler.jpg', $member_box_wod['image_name']),
        );

        $data['image_caption'] = array(
            'name' => 'image_caption',
            'id' => '_imageCaption',
            'autocomplete' => 'off',
            'value' => set_value('image_caption', $member_box_wod['image_caption'])
        );


        $data['wod_date'] = array(
            'name' => 'wod_date',
            'id' => '_wodDate',
            'autocomplete' => 'off',
            'value' => set_value('wod_date', $member_box_wod['wod_date'])
        );

        $benchmark_wod_options = $this->_get_benchmark_wod_lookup(TRUE); //true is blank row
        $benchmark_wod_attrib = 'id = "_benchmarkWod"';
        $data['benchmark_wod_dropdown'] = form_dropdown('wod_id', $benchmark_wod_options, set_value('wod_id', $member_box_wod['wod_id']), $benchmark_wod_attrib);

        $scale_options = $this->_get_scale_lookup(); //true is blank row
        $scale_attrib = 'id = "_scale"';
        $data['scale_dropdown'] = form_dropdown('scale_id', $scale_options, set_value('scale_id', $member_box_wod['scale_id']), $scale_attrib);

        $box_id = $this->Box_model->is_member_staff();
        $box_tier_array = $this->Box_model->get_box_tiers($box_id);
        if (!$box_tier_array)
            $data['box_tier_dropdown'] = FALSE;
        else {
            $box_tier_options = $this->set_lookup($box_tier_array, 'bwt_id', 'tier_name', BLANK_ROW);
            $box_tier_attrib = 'id = "_tier"';
            $data['box_tier_dropdown'] = form_dropdown('bwt_id', $box_tier_options, set_value('bwt_id', $member_box_wod['bwt_id']), $box_tier_attrib);
        }

        $wod_type_options = $this->_get_wod_type_lookup(TRUE); //true is blank row
        $wod_type_attrib = 'id = "_wodType"';
        $data['wod_type_dropdown'] = form_dropdown('wod_type_id', $wod_type_options, set_value('wod_type_id', $member_box_wod['wod_type_id']), $wod_type_attrib);

        $data['buy_in'] = array(
            'name' => 'buy_in',
            'id' => '_buyIn',
            'autocomplete' => 'off',
            'value' => set_value('buy_in', $member_box_wod['buy_in'])
        );

        $data['cash_out'] = array(
            'name' => 'cash_out',
            'id' => '_cashOut',
            'autocomplete' => 'off',
            'value' => set_value('cash_out', $member_box_wod['cash_out'])
        );

        $data['simple_title'] = array(
            'name' => 'simple_title',
            'id' => '_simpleTitle',
            'autocomplete' => 'off',
            'value' => set_value('simple_title', $member_box_wod['simple_title'])
        );

        $data['simple_description'] = array(
            'name' => 'simple_description',
            'id' => '_simpleDescription',
            'autocomplete' => 'off',
            'value' => set_value('simple_description', $member_box_wod['simple_description'])
        );

        $score_type_options = array(
            '' => '',
            'T' => 'For Time', //Integer, stored in seconds, T tells UI to display minutes second
            'I' => 'Reps/Round Count', //Integer values stored
            'W' => 'Maximum Weight', //Decimal value stored (e.g. 105.5 lbs)
            'O' => 'Other', //Unknown way to score...becomes free text field
        );
        $score_type_attrib = 'id = "_scoreType" data-native-menu="false"';
        $data['score_type_dropdown'] = form_dropdown('score_type', $score_type_options, set_value('score_type', $member_box_wod['score_type']), $score_type_attrib);


        $data['submit'] = array(
            'class' => 'ui-btn-hidden',
            'id' => '_submit',
            'value' => 'Save',
            'aria-disabled' => 'false',
            'data-inline' => 'true',
            'data-theme' => 'b',
            'type' => 'Submit',
        );

        return $data;
    }

    //User will be presnted with a wizard for saving box wods
    private function _get_box_wod_wizard_data() {
        $this->load->model('Box_model');
        $this->load->model('Wod_model');
        $this->load->model('Scale_model');

        $wizard_data = null;
        $pick_day_list = '';
        $benchmark_wod_list = '';
        $hidden_wod_info = '';
        $score_type_list = '';
        $scale_list = '';
        $box_tier_list = '';
        $box_id = $this->Box_model->is_member_staff();

        $today_text = "Today - " . date("l - m/d/Y", mktime(0, 0, 0, date("m"), date("d"), date("Y")));
        $tomorrow = mktime(0, 0, 0, date("m"), date("d") + 1, date("Y"));
        $tomorrow_text = "Tomorrow - " . date("l - m/d/Y", $tomorrow);
        $other_day_text = "Some other date";

        $day_array = array($tomorrow_text, $today_text, $other_day_text);
        foreach ($day_array as $day_text)
            $pick_day_list .= '<li><a class="day-link" href="#SocialMediaConnectPage">' . $day_text . '</a></li>';

        $box_tier_array = $this->Box_model->get_box_tiers($box_id);
        if (!$box_tier_array)
            $box_tier_list = false;
        else
            foreach ($box_tier_array as $row)
                $box_tier_list .= '<li><a id="bwt_id_' . $row['bwt_id'] . '" class="tier-link" href="#DailyMessagePage">' . $row['tier_name'] . '</a></li>';

        //Get list of Benchmark WODs
        $benchmark_wod_array = $this->Wod_model->get_benchmark_wod_list();

        foreach ($benchmark_wod_array as $row) {
            //score_type description title
            $hidden_wod_info .= '<div class="hidden-data" id="wod_data_' . $row['wod_id'] . '"  data-wod-title="' . $row['title'] . '" data-score-type="' . $row['score_type'] . '" data-wod-description="' . str_replace('<br>', "\r\n", str_replace('"', '&Prime;', $row['description'])) . '" ></div>';
            $benchmark_wod_list .= '<li><a id="wod_id_' . $row['wod_id'] . '" class="benchmark-wod-link" href="#BuyInPage">' . $row['title'] . '</a></li>';
        }


        $score_type_array = array(
            'T' => 'For Time', //Integer, stored in seconds, T tells UI to display minutes second
            'I' => 'Reps/Round Count', //Integer values stored
            'W' => 'Maximum Weight', //Decimal value stored (e.g. 105.5 lbs)
            'O' => 'Other', //Unknown way to score...becomes free text field
        );

        foreach ($score_type_array as $key => $value)
            $score_type_list .= '<li><a id="score_type_' . $key . '" class="score-type-link" href="#PickScalePage">' . $value . '</a></li>';

        $scale_array = $this->Scale_model->get_scale_list($box_id);

        foreach ($scale_array as $row)
            $scale_list .= '<li><a id="scale_id_' . $row['scale_id'] . '" class="scale-link" href="#WodPage">' . $row['scale_name'] . '</a></li>';

        $wizard_data['box_tier_list'] = $box_tier_list;
        $wizard_data['scale_list'] = $scale_list;
        $wizard_data['pick_day_list'] = $pick_day_list;
        $wizard_data['benchmark_wod_list'] = $benchmark_wod_list;
        $wizard_data['score_type_list'] = $score_type_list;
        $wizard_data['hidden_wod_info'] = $hidden_wod_info;

        return $wizard_data;
    }

    private function _get_member_exercises($box_id = 0) {
        $this->load->model('Box_model');
        $member_exercises = $this->Box_model->get_member_exercises_for_staff($box_id);
        if (!$member_exercises)
            return 'No Maxes are saved for the box you\'ve selected.';

        $member_exercise_list = '';
        foreach ($member_exercises as $row)
            $member_exercise_list .= '<li><a href="#exercise_id_' . $row['exercise_id'] . '">' . $row['exercise'] . '</a><span class="ui-li-count">' . $row['member_count'] . '</span></li>';

        return $member_exercise_list;
    }

    private function _get_member_max_details($box_id = 0) {
        $this->load->model('Box_model');
        $member_max_details = $this->Box_model->get_member_max_details($box_id);
        if (!$member_max_details)
            return 'No Maxes are saved for the box you\'ve selected.';

        $return_html = '';
        $page_data = '';
        $current_exercise_id = 0;
        $row_count = 0;
        $previous_exercise_id = '';
        $page_closer = '</div></div></div>';
        foreach ($member_max_details as $row) {
            if ($current_exercise_id != $row['exercise_id']) {
                if ($current_exercise_id != 0) {
                    $page_data = str_replace('PREVIOUS_exercise_id', 'exercise_id_' . $previous_exercise_id, $page_data);
                    $page_data = str_replace('NEXT_exercise_id', 'exercise_id_' . $row['exercise_id'], $page_data);
                    $return_html .= $page_data . $page_closer;
                    $previous_exercise_id = $current_exercise_id;
                }

                $row_count = 0;
                $current_exercise_id = $row['exercise_id'];
                $page_data = '<div data-role="page" id="exercise_id_' . $row['exercise_id'] . '">' .
                        '<div data-role="header">
										<a href="#MaxPicker" data-icon="back" data-iconpos="notext" data-direction="reverse">Go back and pick WOD</a>
										<h1>' . $row['exercise'] . '</h1>
									</div><!-- /header -->';
                $page_data .= '<div data-role="content">' .
                        '<div data-role="fieldcontain">';
                $page_data .= anchor('staff/member_maxs#' . 'PREVIOUS_exercise_id', 'Previous', array('data-role' => 'button', 'data-inline' => 'true'));
                $page_data .= anchor('staff/member_maxs#' . 'NEXT_exercise_id', 'Next', array('data-role' => 'button', 'data-inline' => 'true'));
                $page_data .= '</div>';
                $page_data .= '<div class="ui-grid-b">';
                $page_data .= '<div class="ui-block-a mobile-grid-header">Member</div>';
                $page_data .= '<div class="ui-block-b mobile-grid-header date-block">Max Date</div>';
                $page_data .= '<div class="ui-block-c mobile-grid-header number-block">Value</div>';
            }
            $alt_row_class = $row_count % 2 == 1 ? 'alternate-row' : '';
            $page_data .= '<div class="ui-block-a ' . $alt_row_class . ' ">' . $row['full_name'] . '</div>';
            $page_data .= '<div class="ui-block-b date-block ' . $alt_row_class . ' ">' . $row['max_date'] . '</div>';
            $page_data .= '<div class="ui-block-c number-block ' . $alt_row_class . ' ">' . $row['max_value'] . '</div>'; //Indicate scaled RX with asterisk (*)
            $row_count++;
        }

        $page_data = str_replace('PREVIOUS_exercise_id', 'exercise_id_' . $previous_exercise_id, $page_data);
        $page_data = str_replace('NEXT_exercise_id', '', $page_data);
        $return_html .= $page_data . $page_closer;

        return $return_html;
    }

    private function _get_scale_lookup() {

        $this->load->model('Scale_model');
        $this->load->model('Box_model');

        $box_id = $this->Box_model->is_member_staff();

        $scale_list_lookup = $this->Scale_model->get_scale_list($box_id);
        return $this->set_lookup($scale_list_lookup, 'scale_id', 'scale_name', BLANK_ROW);
    }

    //Returns a list of the benchmark Crossfit WODs that may apply to the box WOD (Heroes, Girls and Other)
    private function _get_benchmark_wod_lookup($blank_row = false) {

        $this->load->model('Wod_model');

        $wod_type_list_lookup = $this->Wod_model->get_benchmark_wod_list();
        return $this->set_lookup($wod_type_list_lookup, 'wod_id', 'title', $blank_row ? BLANK_ROW : false);
    }

    private function _get_wod_type_lookup($blank_row = false) {

        $this->load->model('Wod_model');

        $wod_type_list_lookup = $this->Wod_model->get_wod_type_list();
        return $this->set_lookup($wod_type_list_lookup, 'wod_type_id', 'title', $blank_row ? BLANK_ROW : false);
    }

    private function _get_leader_board($box_id = 0) {

        $this->load->model('Box_model');
        $leader_board = $this->Box_model->get_leader_board_for_staff($box_id);
        if (!$leader_board)
            return 'No rx\'d Benchmark WODs saved.';

        $return_html = '';
        $page_data = '';
        $current_wod_id = 0;
        $row_count = 0;
        $previous_wod_id = '';
        $member_array = array(); //Only show user's best score with this array
        $page_closer = '</div><p>This is a list of current user\'s best rx score for this WOD.</p></div></div>';




        $leader_board_list = '';
        foreach ($leader_board as $row) {
            if ($row['wod_id'] != $current_wod_id) {
                if ($current_wod_id != 0) {
                    $page_data = str_replace('PREVIOUS_WOD_ID', 'wod_id_' . $previous_wod_id, $page_data);
                    $page_data = str_replace('NEXT_WOD_ID', 'wod_id_' . $row['wod_id'], $page_data);
                    $return_html .= $page_data . $page_closer;
                    $previous_wod_id = $current_wod_id;
                }

                $member_array = array();
                $leader_board_list .= '<li><a href="#wod_id_' . $row['wod_id'] . '">' . $row['wod_name'] . '</a></li>';
                $row_count = 0;
                $current_wod_id = $row['wod_id'];
                $page_data = '<div data-role="page" id="wod_id_' . $row['wod_id'] . '">' .
                        '<div data-role="header">
										<a href="#WODPicker" data-icon="back" data-iconpos="notext" data-direction="reverse">Go back and pick WOD</a>
										<h1>' . $row['wod_name'] . '</h1>
									</div><!-- /header -->';
                $page_data .= '<div data-role="content">' .
                        '<div data-role="fieldcontain">';
                $page_data .= anchor('staff/box_leader_board#' . 'PREVIOUS_WOD_ID', 'Previous', array('data-role' => 'button', 'data-inline' => 'true'));
                $page_data .= anchor('staff/box_leader_board#' . 'NEXT_WOD_ID', 'Next', array('data-role' => 'button', 'data-inline' => 'true'));
                $page_data .= '</div>';
                $page_data .= '<div class="ui-grid-b">';
                $page_data .= '<div class="ui-block-a mobile-grid-header">Member</div>';
                $page_data .= '<div class="ui-block-b mobile-grid-header number-block">Date</div>';
                $page_data .= '<div class="ui-block-c mobile-grid-header number-block">Score</div>';
            }

            if (!in_array($row['member_id'], $member_array)) {
                array_push($member_array, $row['member_id']);
                $alt_row_class = $row_count % 2 == 1 ? 'alternate-row' : '';
                $page_data .= '<div class="ui-block-a ' . $alt_row_class . ' ">' . $row['full_name'] . '</div>';
                $page_data .= '<div class="ui-block-b number-block ' . $alt_row_class . ' ">' . $this->mysql_to_human($row['wod_date']) . '</div>';
                $page_data .= '<div class="ui-block-c number-block ' . $alt_row_class . ' ">' . $row['score'] . '</div>';
                $row_count++;
            }
        }

        $page_data = str_replace('PREVIOUS_WOD_ID', 'wod_id_' . $previous_wod_id, $page_data);
        $page_data = str_replace('NEXT_WOD_ID', '', $page_data);
        $return_html .= $page_data . $page_closer;

        return array('wod_list' => $leader_board_list,
            'wod_details' => $return_html);
    }

    private function _get_daily_box_wods($box_id = 0) {
        $this->load->model('Box_model');
        $daily_box_wods = $this->Box_model->get_daily_box_wods_for_staff($box_id);
        if (!$daily_box_wods)
            return 'No WODs are saved for the box you\'ve selected.';
        $box_wod_list = '';
        foreach ($daily_box_wods as $row)
            $box_wod_list .= '<li><a href="#bw_id_' . $row['bw_id'] . '">' . $row['simple_title'] . '</a><span class="ui-li-count">' . $this->mysql_to_human($row['wod_date']) . '</span></li>';

        return $box_wod_list;
    }

    private function _get_daily_box_wod_details($box_id = 0) {
        $this->load->model('Box_model');
        $daily_wod_details = $this->Box_model->get_daily_box_wod_details($box_id);
        if (!$daily_wod_details)
            return 'No WODs are saved for the box you\'ve selected.';

        $score_type_options = array(
            '' => '',
            'T' => 'For Time', //Integer, stored in seconds, T tells UI to display minutes second
            'I' => 'Reps/Round Count', //Integer values stored
            'W' => 'Maximum Weight', //Decimal value stored (e.g. 105.5 lbs)
            'O' => 'Other', //Unknown way to score...becomes free text field
        );

        $rating_html = '';
        $return_html = '';
        $page_data = '';
        $current_bw_id = 0;
        $current_scale = '';
        $row_count = 0;
        $previous_bw_id = '';
        $page_closer = '</div>';
        foreach ($daily_wod_details as $row) {
            if ($current_bw_id != $row['bw_id']) {
                if ($current_bw_id != 0) {
                    $page_data = str_replace('PREVIOUS_BW_ID', 'bw_id_' . $previous_bw_id, $page_data);
                    $page_data = str_replace('NEXT_BW_ID', 'bw_id_' . $row['bw_id'], $page_data);
                    $rating_html = $this->_get_box_wod_rating_html($current_bw_id);
                    $return_html .= $page_data . $page_closer . $rating_html . '</div></div>';
                    $previous_bw_id = $current_bw_id;
                }

                $row_count = 0;
                $current_scale = '';
                $current_bw_id = $row['bw_id'];
                $page_data = '<div data-role="page" id="bw_id_' . $row['bw_id'] . '">' .
                        '<div data-role="header">
										<a href="#WODPicker" data-icon="back" data-iconpos="notext" data-direction="reverse">Go back and pick WOD</a>
										<h1>' . $row['simple_title'] . '</h1>
									</div><!-- /header -->';
                $page_data .= '<div data-role="content">' .
                        '<div data-role="fieldcontain">';
                $page_data .= anchor('staff/daily_wods#' . 'PREVIOUS_BW_ID', 'Previous', array('data-role' => 'button', 'data-inline' => 'true'));
                $page_data .= anchor('staff/daily_wods#' . 'NEXT_BW_ID', 'Next', array('data-role' => 'button', 'data-inline' => 'true'));
                $page_data .= '</div>';
                $page_data .= '<div data-role="fieldcontain">
											<b>Date:</b>  ' . $this->mysql_to_human($row['wod_date']) . '    ' . '
											<b>Score Type:</b>  ' . $score_type_options[$row['score_type']] . '
										</div><h1>Individual Scores</h1>';
            }

            if ($current_scale !== $row['scale_option']) {
                if ($current_scale !== '')
                    $page_data .= '</div>';

                $page_data .= '<h3>' . $row['scale_option'] . '</h3>';


                $page_data .= '<div class="ui-grid-a">';
                $page_data .= '<div class="ui-block-a mobile-grid-header">Member</div>';
                $page_data .= '<div class="ui-block-b mobile-grid-header number-block">Score</div>';
                $current_scale = $row['scale_option'];

                $row_count = 0;
            }


            $alt_row_class = $row_count % 2 == 1 ? 'alternate-row' : '';
            $page_data .= '<div class="ui-block-a ' . $alt_row_class . ' ">' . $row['full_name'] . '</div>';
            $page_data .= '<div class="ui-block-b number-block ' . $alt_row_class . ' ">' . $row['score'] . '</div>';
            $row_count++;
        }

        $page_data = str_replace('PREVIOUS_BW_ID', 'bw_id_' . $previous_bw_id, $page_data);
        $page_data = str_replace('NEXT_BW_ID', '', $page_data);
        $rating_html = $this->_get_box_wod_rating_html($current_bw_id);
        $return_html .= $page_data . $page_closer . $rating_html . '</div></div>';

        return $return_html;
    }

    private function _get_box_wod_rating_html($bw_id = 0) {
        $rating_html = '';
        $box_wod_rating_array = $this->Box_model->get_box_wod_rating($bw_id);
        $box_wod_rating_grid = '';
        if (!!$box_wod_rating_array) {
            $row_count = 0;

            foreach ($box_wod_rating_array as $row) {
                $alt_row_class = $row_count % 2 == 1 ? 'alternate-row' : '';

                $box_wod_rating_grid .= '<div class="ui-block-a ' . $alt_row_class . '">' . $row['rating'] . '</div>';
                $box_wod_rating_grid .= '<div class="ui-block-b ' . $alt_row_class . ' number-block">' . $row['votes'] . '</div>';
                $box_wod_rating_grid .= '<div class="ui-block-c ' . $alt_row_class . ' number-block">' . $row['percentage'] . '</div>';

                $row_count++;
            }

            $rating_html = '<h1>Ratings</h1>' .
                    '<div class="ui-grid-b">' .
                    '<div class="ui-block-a mobile-grid-header">Rating</div>' .
                    '<div class="ui-block-b mobile-grid-header number-block">Votes</div>' .
                    '<div class="ui-block-c mobile-grid-header number-block">Percentage</div>' . $box_wod_rating_grid .
                    '</div>';
        }

        return $rating_html;
    }

    private function _get_staff_training_log_form_data($staff_training_log_data) {
        $data = null;

        $class_time_options = $this->_get_class_time_lookup($staff_training_log_data['box_id']); //true is blank row
        $class_time_attrib = ' id = "_classTime" data-native-menu="true"';
        $data['class_time_dropdown'] = form_dropdown('bct_id', $class_time_options, set_value('bct_id', $staff_training_log_data['bct_id']), $class_time_attrib);

        $data['training_date'] = array(
            'name' => 'training_date',
            'id' => '_trainingDate',
            'autocomplete' => 'off',
            'value' => set_value('training_date', $staff_training_log_data['training_date'])
        );


        $data['class_size'] = array(
            'name' => 'class_size',
            'id' => '_classSize',
            'value' => set_value('class_size', $staff_training_log_data['class_size'])
        );

        $data['note'] = array(
            'name' => 'note',
            'id' => '_note',
            'value' => set_value('note', $staff_training_log_data['note'])
        );





        $data['submit'] = array(
            'id' => '_submit',
            'class' => 'ui-btn-hidden',
            'value' => 'Save',
            'aria-disabled' => 'false',
            'data-inline' => 'true',
            'data-theme' => 'b',
            'type' => 'Submit',
        );

        return $data;
    }

    private function _get_class_time_lookup($box_id = 0) {

        $this->load->model('Staff_model');

        $box_class_time_list_lookup = $this->Staff_model->get_class_time_list($box_id);
        return $this->set_lookup($box_class_time_list_lookup, 'bct_id', 'class_time_description', TRUE);
    }

}

/* End of file staff.php */
/* Location: ./application/controllers/staff.php */