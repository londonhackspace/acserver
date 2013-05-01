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
		
		public function add_or_update_user($user_id, $user_nick){
			// If the user id is new, we add them, otherwise we force the details
			// to match the authorative source
            $query = $this->db->where(array('user_id' =>$user_id))->get('users');
            if ($query->num_rows() == 0){
                // Create a new entry in the users table
                $this->db->insert('users',array('user_id' => $user_id, 'nick' => $user_nick));
                return $this->db->insert_id();
            } else {
                // Update the nick to match the website nick
                $this->db->where('user_id', $user_id);
                $this->db->update('users',array('nick' => $user_nick));
                $row = $query->row_array();
                return $row["user_id"];
            }
		}
	}
?>