<?php

	class Node_model extends CI_Model
	{
		public function __construct()
		{
		$this->load->database();
		}
		
		public function get_permissions($node, $card){
			$query = $this->db->get_where('permissions',array('node' => $node, 'card' => $card));
			$results = $query->row_array();
			if(!empty($results['permission']))
				return $results['permission'];
			else
				return 0;
		}
	}
?>