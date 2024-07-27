<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class HomeController extends MY_Controller {
	public function __construct(){
		parent::__construct();
			//default load modal on call controller
			$this->load->model('HomeModal');
		}
	// defaul method 
	public function index(){
		$this->load->view('home');
	}

	//this function get all data as i want form url
	public function fetchPreview(){
		//retrive data from get post method
		$url = $this->input->post('url');

		//this function process this url and return all data
		$data = $this->processUrl($url);

		//after getting all data pass this function and return into json form 
		echo $this->outputJson($data);
	}
	
	//submit record method
	public function submitRecord(){	
		$data = $this->input->post('data');
		$url = $this->input->post('url');
		// if data missing then return with warning message
		if(empty($data) || empty($url)){
			return $this->responseStruct([],"Data Missing.",0);
		}
		$decodeData = json_decode($data,true);
		
		//push url in array
		$decodeData['url'] = $url;
		
		//call save record function
		$response = $this->HomeModal->save_preview($decodeData);

		//return status
		echo $this->outputJson($response);
	} 

	//get list page data method
	public function listRecord($page_number = 1) {	
		// Load pagination library
		$this->load->library('pagination');
		
		//call paggination method
		$config = $this->paginationConfig("recordlist");
		
		//initialize pagination
		$this->pagination->initialize($config);
		
		//get all cover data from "0"
		$offset = ($page_number > 0) ? ($page_number - 1) * $config["per_page"] : 0;
		
		//create paggination link using ci3 function
		$data["links"] = $this->pagination->create_links();

		//add class pagelink to find pagniation number in jquery
		$data["links"] = preg_replace("/<a /ism", "<a class='pagelink' ", $data["links"]);
		
		// Fetch records using the model method
		$data["previews"] = $this->HomeModal->fetchRecord($config["per_page"], $offset);
	
		// Send data as JSON
		echo $this->outputJson($data);
		
	}	
}
