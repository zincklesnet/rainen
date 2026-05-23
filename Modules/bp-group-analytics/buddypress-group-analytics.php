<?php
// Exit if accessed directly
if (!defined('ABSPATH'))
    exit;

/**
 * @author Vivek Sharma
 * @since  1.0
 * @version 1.1
 */
if (class_exists('BP_Group_Extension')) : // Recommended, to prevent problems during upgrade or when Groups are disabled

    class BP_Group_Analytics_Plugin_Extension extends BP_Group_Extension {

        protected $members;

        function __construct() {
            global $bp;
            $this->name =  __('Analytics', 'bp-group-analytics');
            $this->slug = BP_GROUP_ANALYTICS_SLUG;

            /* For internal identification */
            $this->id = 'group_analytics';
            //$this->format_notification_function = 'bp_group_analytics_format_notifications';

            if ($bp->groups->current_group) {
                $this->nav_item_name = $this->name;
                $this->nav_item_position = 101;
            }

            $this->admin_name =  __('Analytics', 'bp-group-analytics');
            $this->admin_slug = BP_GROUP_ANALYTICS_SLUG;

            if(!is_admin())
                $this->members = $this->_get_group_members();
        }

        /*
         * Return group members for the current group or given group ID
         * @param int group ID
         * @return array member IDs
         */
        protected function _get_group_members($group_id = 0){
            global $bp;
            $members = array();

            if (empty($group_id)) {
                $group_id = $bp->groups->current_group->id;
            }

            $has_members_str = array(
                'group_id' => $group_id,
                'per_page' => 0,
                'exclude_admins_mods' => 0,
            );

            if ( bp_group_has_members( $has_members_str ) ) {
                while ( bp_group_members() ) : bp_group_the_member();
                    $members[]= bp_get_group_member_id();
                endwhile;
            }

            return $members;

        }

        /*
         * Get xprofile field data by field ID
         * @param int field ID
         * @return array a array of counts by profile field grouped by values
         */
        protected function _get_xprofile_field_data($field_id){
            global $bp;
            $results = array();
            if(!empty($this->members)){
                foreach($this->members as $member_id){
                    $field_data = xprofile_get_field_data( $field_id , $member_id);
                    $results = $this->_update_item_key($results,$field_data);
                }
            }
            return $results;
        }

        protected function _update_item_key($array = array(), $key = 'N/A'){
            if ($key == "") $key = __('N/A', 'bp-group-analytics');
            if(!empty($array)){
                if (in_array($key, array_keys($array))) {
                    $count = $array[$key][1];
                    $count = $count+1;
                    $array[$key] = array($key,$count);
                } else {
                    $array[$key] = array($key,1);
                }
            } else {
                $array[$key] = array($key,1);
            }
            return $array;
        }

        protected function _display_field_chart($title, $container_id, $data){
            $chartHTML = "";
            $data_variable = $container_id."_data";
            $chartHTML .=  '<h3>'.__($title, 'bp-group-analytics').'</h3><div id="'.$container_id.'" class="chart_style"></div>';
            $chartHTML .= '<script>var '.$data_variable.' = '.wp_json_encode(array_values($data)).' </script>';
            $chartHTML .= $this->_chartScript($container_id, $data_variable);
            return $chartHTML;
        }

        protected function _chartScript($container_id, $data) {

            $chartScript = "<script type='text/javascript'>google.charts.load('current', {'packages':['corechart']});google.charts.setOnLoadCallback(drawChart);
                function drawChart() {
                    var data = new google.visualization.DataTable();
                    data.addColumn('string', 'Field');
                    data.addColumn('number', 'Count');
                    data.addRows(".$data.");
                    var options = {chartArea:{left:20,top:20,bottom:20,right:20,width:'100%',height:'100%'}};
                    var chart = new google.visualization.PieChart(document.getElementById('".$container_id."'));
                    chart.draw(data, options);
                }
            </script>";

            return $chartScript;
        }

        public function display_chart($field_id, $title, $index){
            $field_id = intval($field_id);
            $data = $this->_get_xprofile_field_data($field_id);
            if(empty($data)) return false;
            $contents = $this->_display_field_chart($title,"div_".$field_id,$data);
            return apply_filters("bp_group_analytics_filter_chart_html",$contents, $index);

        }

        function create_screen($group_id = null) { }

        function create_screen_save($group_id = null) { }

        function edit_screen($group_id = null) { }

        function edit_screen_save($group_id = null) {   }

        /**
         * @version 1.0
         * @since version 1.1
         * @author Vivek Sharma
         */
        function display($group_id = null) {
            do_action('bp_group_analytics_display');
            $this->bp_group_analytics_display_from_saved_meta();
        }

        function bp_group_analytics_display_from_saved_meta(){
            $xprofile_selected_fields_value = get_option('BP_GROUP_ANALYTICS_OPTIONS_META_TITLE');
            $xprofile_selected_fields = array();
            if(!empty($xprofile_selected_fields_value)){
                $xprofile_selected_fields = explode(",",$xprofile_selected_fields_value);
                $index = 0;
                foreach($xprofile_selected_fields as $field){
                    $field_data = explode("|",$field);
                    echo $this->display_chart($field_data[0],$field_data[1],$index);
                    $index++;
                }
            } else {
                echo "<p>". __('Please add profile fields from plugin admin settings.', 'bp-group-analytics')."</p>";
            }

        }

    }
    /**
     * @author Vivek Sharma
     * @since 1.0
     * @version 1.1
     */
    function bp_group_analytics_include_files() {
        require ( dirname(__FILE__) . '/include/cssjs.php' );
        require ( dirname(__FILE__) . '/include/admin.php' );
    }

    bp_register_group_extension('BP_Group_Analytics_Plugin_Extension');
endif; // class_exists( 'BP_Group_Extension' )

