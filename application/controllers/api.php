<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once("application/libraries/REST_Controller.php");

class Api extends REST_Controller {

	public function __construct()
	{
		parent::__construct();
	}
        
        /*
         *  Get card permissions
         *  GET /[nodeID]/card/
         *  i.e.
         *  GET /1/card/04FF7922E40080
         *  returns
         *  0 - no permissions
         *  1 - user
         *  2 - maintainer
         */
	public function card_get()
	{
            $this->load->model('Card_model');
            $node =  (int)$this->uri->segment(1);
            $card =  $this->uri->segment(3);
            $this->uri->segment(3) ."\n";
            if($node && $card){
		$result = $this->Card_model->get_permissions($node, $card);
                if($result){
                    $this->response($result);
                }else print 0;
            } print 0;
	}
	
	public function card_post()
	{
		
        $this->load->model('Card_model');
		
		foreach ($this->post() as $card_to_add => $card_added_by){
			if(($card_to_add != "format") && ($card_to_add != "node")){
				if (get_permissions($this->post("node"), $card_added_by) == 2){
					//TODO add user to the model and check in maintainer is adding a card that is not a maintainer on this node already
					return 1;
				}else{
					return 0;
				}
			}
		}
		
		
		
		// $data = $this->Card_model->get_permissions($this->get('node'), $this->get('card'));
        // echo $data;
        //$this->response($data);
	}
        
        public function permissions_get()
	{
		
        $this->load->model('Node_model');
		$data = $this->Node_model->get_permissions($this->get('node'), $this->get('card'));
        echo $data;
        //$this->response($data);
	}
        
        /*
         * Check tool status
            GET /[nodeID]/status/
            Check if the ACNode has been remotely taken out of service, or put back in service
            returns
            0 - out of service
            1 - in service

         */
        public function status_get()
	{
		
        $this->load->model('Node_model');
		$data = $this->Node_model->get_permissions($this->get('node'), $this->get('card'));
        echo $data;
        //$this->response($data);
	}
        
        /*
         *    Report tool status
         *    PUT /[nodeID]/status/
         *    i.e.
         *    PUT /1/status/
         *    1
         *    0 - out of service
         *    1 - in service
         *    returns
         *    0 - not saved
         *    1 - saved

         */
        public function status_put()
	{
		
        $this->load->model('Node_model');
		$data = $this->Node_model->get_permissions($this->get('node'), $this->get('card'));
        echo $data;
        //$this->response($data);
	}
        
        public function toolusetime_get()
	{
            
        }
        
        public function toolusetime_put()
	{
            
        }
        
        public function case_get()
	{
            
        }
        
        public function case_put()
	{
            
        }
        
        public function sync_get()
	{
             $this->response(1);
        }
        
        public function getpermissions_get(){
            $carddb_str = file_get_contents("/var/www/acserver/carddb.json");
            $users=json_decode($carddb_str,true);
            
            foreach ($users as $user) {
               if($user['nick'] == "As Seen On TV: Solexious"){
                   print_r($user);
               }
            }
            
            
            //$this->response($json_a);
        }
        
        public function page_missing_get()
	{
             $this->response("0");
        }
        
}