<?php

    class tool_model extends CI_Model
    {
        public function __construct()
        {
            $this->load->database();
        }
        
        public function set_tool_status_for_acnode_id($acnode_id, $status) {
            $this->db->where('tool_id', $this->get_tool_id_for_acnode_id($acnode_id));
            $this->db->update('tools', array('status' => $status));
            return 1;
        }
        
        public function log_usage($acnode_id, $user_id, $card_unique_identifier, $logged_event, $time) {
            
            $this->db->insert('toolusage',
                array(
                    'tool_id' => $this->get_tool_id_for_acnode_id($acnode_id),
                    'user_id' => $user_id,
                    'card_unique_identifier' => $card_unique_identifier,
                    'logged_event' => $logged_event,
                    'time' => $time
                )
            );
            
            return 1;
            
        }
        
        /* Need to centralise this function between this code and card_model.php */
        public function get_tool_id_for_acnode_id($acnode_id) {
            $query = $this->db->get_where('acnodes',array('acnode_id' => $acnode_id));
            $row = $query->row_array();
            
            return (int) $row['tool_id'];
        }
        
        /* Log when the case has been opened or closed */
        public function log_case_status_change($acnode_id, $status) {
            $narrative = 'Case changed status to ';
            if ($status == 0) {
                $narrative .= "Closed";
            } else {
                $narrative .= "Open";
            }
            $this->log_usage($acnode_id, NULL, NULL, $narrative, 0);
        }
        
        public function get_last_tool_status($tool_id) {
            
            $query = $this->db->order_by("logged_at","desc")->limit(1)
                    ->get_where('toolusage',array('tool_id' => $tool_id));

            $row = $query->row_array();
            
            if(!empty($row))
                return  $row['logged_event'];
            else
                return null;
        }
        
        public function get_all_tools() {
            
            
            $query = $this->db->get_where('tools',array('tool_id <>' => '999'));
            
            if(!empty($query->result_array()))
                return $query->result_array();
            else
                return null;
        }
        
        public function get_all_tools_for_user($user_id) {
            
            
            $this->db->select('tools.tool_id, tools.name, tools.status, tools.status_message,permissions.permission');
            $this->db->join('permissions', 
                    'permissions.tool_id = tools.tool_id AND user_id='.$user_id,'left outer');
           
            $query = $this->db->get_where('tools',array('tools.tool_id <>' => '999'));
            
            if(!empty($query->result_array()))
                return $query->result_array();
            else
                return null;
        }

        
    }
?>