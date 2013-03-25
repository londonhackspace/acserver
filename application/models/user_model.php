<?php

	class User_model extends CI_Model
	{
		public function __construct()
		{
			$this->load->database();
		}
		
		public function get_user($card){
			$query = $this->db->get_where('cards',array('card' => $card));
                        $results = $query->row_array();
			if(!empty($results))
				return $results;
			else
				return null;
		}
		
		public function add_user($user_id, $user_nick){
			//Check if the user already exists
                        $query = $this->db->where(array('id' =>$user_id))->get('users');
                        if($query->num_rows() == 0){
                            $this->db->insert('users',array('id' =>$user_id, 'nick' => $user_nick));
                            return $this->db->insert_id();
                        }else{
                            return null;
                            
                        }
                        
		}
	}
?>