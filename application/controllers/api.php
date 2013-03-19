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
		$data = $this->Card_model->get_permissions($this->get('node'), $this->get('card'));
        echo $data;
        //$this->response($data);
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
	
}