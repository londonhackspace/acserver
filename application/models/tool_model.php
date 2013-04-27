<?php

	class tool_model extends CI_Model
	{
		public function __construct()
		{
			$this->load->database();
		}
		
		public function get_status($tool_id){
			$query = $this->db->get_where('tools',array('tool_id' => $tool_id));
			$results = $query->row_array();
			if(!empty($results)){
                                print_r($results);
				return $results[0]['status'];
                        }else
				return 0;
		}
	}
?>