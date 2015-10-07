<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Feed extends MY_Controller {

    function __construct() 
    {
        parent::__construct();
        $this->load->helper('xml');	
    }
	
    function index()
    {
        
        
        $data = '';
        define('BOX_ID',3);
        $box_id	=	$this->uri->segment(BOX_ID);

        $this->load->model('Box_model');
        $box_info = $this->Box_model->get_box_info($box_id);
        
        if(!$box_info)
        {
            echo 'No wod info found for box id supplied';
            return;;
        }
        $data['box_name']   =   $box_info->box_name;
        $data['box_url']   =   $box_info->box_url;
        
        
        $this->load->model('Rss_model');
        $item_list_array = $this->Rss_model->get_daily_wod_list($box_id);
        $item_list_html = '';
        $data['last_build_date'] = '';
        foreach($item_list_array as $row)
        {
            if ($data['last_build_date'] == '')
                $data['last_build_date'] = $row['last_build_date'];
            
            $item_list_html .= '<item>';
            $item_list_html .= '<title>'.$row['title'].'</title>';
            $item_list_html .= '<link>'.$data['box_url'].'</link>';
            $item_list_html .= '<comments>'.$data['box_url'].'</comments>';
            $item_list_html .= '<pubDate>'.$row['pub_date'].'</pubDate>';
            $item_list_html .= '<dc:creator><![CDATA['.$row['creator'].']]></dc:creator>';
            $item_list_html .= '<category><![CDATA[WOD]]></category>';
            $item_list_html .= '<guid isPermaLink="false">'.$box_info->box_url.'</guid>';
            $item_list_html .= '<description><![CDATA['.strip_tags($row['wod_html']).']]></description>';
            $item_list_html .= '<content:encoded><![CDATA['.$row['wod_html'].']]></content:encoded>';
            $item_list_html .= '<wfw:commentRss>'.$box_info->box_url.'</wfw:commentRss>';
            $item_list_html .= '<slash:comments>0</slash:comments>';
            $item_list_html .= '</item>';
            
        }
        
        $data['box_id'] = $box_id;
        $data['item_list_html'] = $item_list_html;
        
        header("Content-Type: application/rss+xml; charset=ISO-8859-1");
        $this->load->view('rss_view', $data);
    }

}
?>