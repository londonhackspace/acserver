<?php

    class acnode_model extends CI_Model
    {
        public function __construct()
        {
            $this->load->database();
        }
        
        public function get_status($node_id){
            $query = $this->db->get_where('acnodes',array('acnode_id' => $node_id));
            $row = $query->row_array();
            
            $tool_id = $row["tool_id"];

            $query = $this->db->get_where('tools',array('tool_id' => $tool_id));
            $row = $query->row_array();

            return (int) $row["status"];
            
        }
    }
?>