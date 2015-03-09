<?php
/*
Plugin Name: Print Espresso Registrations
Description: A plugin to easily print the registrations from a particular event from Event Espresso 4
Author: James Emmrich
Version: 0.5
Author URI: http://about.me/jemmrich/
Plugin URI: https://github.com/jemmrich/PrintEspressoRegistrations
*/

// Add plugin to the side menu.
add_action('admin_menu', 'per_setup_menu');
function per_setup_menu(){
    add_menu_page( 'Print Registrations', 'Print Registrations', 'manage_options', 'print-registrations', 'PrintEspressoRegistrations::init' );
}

// Add the shortcut menu in the admin top bar.
add_action('admin_bar_menu', 'per_admin_menu', 1000);
function per_admin_menu(){
    global $wp_admin_bar;
    if(!is_super_admin() || !is_admin_bar_showing()) return;
    
    $siteurl = site_url();
    
    $argsParent = array(
        'id' => 'per-registrations',
        'title' => 'Print Registrations',
        'href' => $siteurl.'/wp-admin/admin.php?page=print-registrations'
    );
    $wp_admin_bar->add_menu($argsParent);
}


class PrintEspressoRegistrations{
    public function init(){
        
        // Stream the PDF if it was requested...
        $action = $_GET["action"];
        if(isset($action) && $action == "getpdf"){
            PrintEspressoRegistrations::renderPDF($event_id);
            return true;
        }
        
        // Show the HTML report...
        $events = PrintEspressoRegistrations::getEvents();

        require("views/admin.php");
        
        $event_id = $_GET["event_id"];
        if(isset($event_id)){
            $event = PrintEspressoRegistrations::getEvent($event_id);
            PrintEspressoRegistrations::renderRegistrations($event->post_title, $event->DTT_EVT_start, $event_id);
        }
    }
    
    
    /**
     * renderRegistrations
     *
     * Renders a table of all the registrations from a particular event.
     *
     * @param string $event_name Name of the event
     * @param string $event_date Event date
     * @param int $event_id about this param
     */
    private function renderRegistrations($event_name, $event_date, $event_id){
        global $wpdb;
        
        $attendees = PrintEspressoRegistrations::getRegistrations($event_id);
        
        $report_html = PrintEspressoRegistrations::renderReport($event_id);
        
        require("views/registrations.php");
    }

    /**
     * getEvents
     *
     * Retrieves all the events in Event Espresso which are currently published in an array sorted by descending.
     * If there are no events, returns false.
     *
     * @return array of event objects
     */
    private function getEvents(){
        global $wpdb;
        
        $sql = "SELECT 
                    ID , 
                    post_title ,
                    DTT_EVT_start ,
                    DTT_EVT_end
                FROM 
                    {$wpdb->prefix}posts, 
                    {$wpdb->prefix}esp_datetime
                WHERE 
                    post_type='espresso_events' AND 
                    post_status='publish' AND 
                    {$wpdb->prefix}esp_datetime.EVT_ID = {$wpdb->prefix}posts.ID
                ORDER BY post_date DESC";
        $events = $wpdb->get_results($wpdb->prepare($sql, 0));
        
        if(sizeof($events) == 0)
            return false;
            
        return $events;
    }
    
    
    /**
     * getRegistrations
     *
     * Retrieves all the events in Event Espresso which are currently published in an array sorted by descending.
     *
     * @param int $event_id about this param
     * @return array of registration objects
     */
    private function getRegistrations($event_id){
        global $wpdb;
        
        $sql = "SELECT
                {$wpdb->prefix}esp_registration.ATT_ID, 
                ATT_fname, 
                ATT_lname, 
                ATT_phone, 
                ATT_email, 
                REG_date, 
                REG_code, 
                REG_final_price,
                TXN_paid,
                TXN_total 
            FROM
                {$wpdb->prefix}esp_attendee_meta 
            LEFT JOIN
                {$wpdb->prefix}esp_registration 
            ON
                {$wpdb->prefix}esp_attendee_meta.ATT_ID = {$wpdb->prefix}esp_registration.ATT_ID 
            LEFT JOIN 
	            {$wpdb->prefix}esp_transaction 
            ON 
	            {$wpdb->prefix}esp_transaction.TXN_ID = {$wpdb->prefix}esp_registration.TXN_ID 
            WHERE
                {$wpdb->prefix}esp_registration.EVT_ID = %d AND 
                {$wpdb->prefix}esp_registration.REG_deleted = 0";
        
        $attendees = $wpdb->get_results($wpdb->prepare($sql, $event_id));
        
        if(sizeof($attendees) == 0)
            return false;
        
        return $attendees;
    }
    
    
    /**
     * getEvent
     *
     * Retrieves information about a particular event.
     *
     * @param int $event_id  ID of the event in event espresso.
     * @return object event
     */
    private function getEvent($event_id){
        global $wpdb;
        
        $sql = "SELECT
                    post_title ,
                    DTT_EVT_start ,
                    DTT_EVT_end 
                FROM
                    {$wpdb->prefix}posts, 
                    {$wpdb->prefix}esp_datetime 
                WHERE 
                    ID=%d AND
                    {$wpdb->prefix}esp_datetime.EVT_ID = %d
                LIMIT 1";
        $event = $wpdb->get_results($wpdb->prepare($sql, $event_id, $event_id));
            
        if(sizeof($event) == 0)
            return false;
        
        return $event[0];
    }
    
    
    /**
     * renderReport
     *
     * Produces an HTML report of the event_id passed.
     *
     * @param int $event_id  ID of the event in event espresso.
     * @return string html
     */
    private function renderReport($event_id){
        global $wpdb;
        
        $attendees = PrintEspressoRegistrations::getRegistrations($event_id);
        
        ob_start();
        
        require("views/report_template.php");
        
        $html_report = ob_get_contents();
        
        ob_end_clean();
        
        return $html_report;
    }
    
    
    /**
     * renderPDF
     *
     * Produces an PDF and streams it to the browser for the report of the event_id passed.
     *
     * @param int $event_id  ID of the event in event espresso.
     * @return bool true
     */
    private function renderPDF($event_id){
        global $wpdb;
        
        $html_report = PrintEspressoRegistrations::renderReport($event_id);
        $event = PrintEspressoRegistrations::getEvent($event_id);
        
        require("libs/dompdf/dompdf.php");
        $dompdf = new DOMPDF();
        $dompdf->load_html($html_report);
        $dompdf->set_paper("letter", "portrait");
        $dompdf->render();

        $dompdf->stream($event->post_title . " - " . date("Y-m-d", strtotime($event->DTT_EVT_start)) . ".pdf", array("Attachment" => true));

        return true;
    }
}
