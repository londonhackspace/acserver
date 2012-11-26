<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once("application/libraries/REST_Controller.php");

class Node extends REST_Controller {

	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -  
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in 
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	public function permissions_get()
	{
		
        $this->load->model('Node_model');
		$data = $this->Node_model->get_permissions($this->get('node'), $this->get('card'));
        echo $data;
        //$this->response($data);
	}
	
}
	
/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */