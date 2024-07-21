<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class HomeController extends MY_Controller {
	public function __construct(){
		parent::__construct();
			 
		}
	
	public function index()
	{
		$this->load->view('home');
	}

	public function fetchPreview(){
		$return = array("status" => "0","message" => "","data" => []);		
		$url = $this->input->post('url');
		// if url missing then return with warning message
		if($url){
			try {
				//retrive all html by using this
				$html = @file_get_contents($url);
				// Check if data retrieval was successful
				if ($html === false) {
					throw new Exception("Failed to retrieve data from URL.");
				}
				$doc = new DOMDocument();
				@$doc->loadHTML($html);
				$xpath = new DOMXPath($doc);
				// if Failed to fetch HTML content from URL.
				if (is_object($doc)){
					// Initialize variables for title, description, and image
					$title = "";
					$description = "";
					$image = "";
					
					// Retrieve title from title tag or meta property="og:title"
					$metaTitle = $xpath->query('//meta[@property="og:title"]/@content')->item(0);
					if ($metaTitle) {
						$title = $metaTitle->nodeValue;
					} else {
						$titleTag = $doc->getElementsByTagName('title')->item(0);
						if ($titleTag) {
							$title = $titleTag->nodeValue;
						}
					}
					// Retrieve description from meta name="description" or meta property="og:description"
					$metaDesc = $xpath->query('//meta[@name="description"]/@content')->item(0);
					if ($metaDesc) {
						$description = $metaDesc->nodeValue;
					} else {
						$metaDescOG = $xpath->query('//meta[@property="og:description"]/@content')->item(0);
						if ($metaDescOG) {
							$description = $metaDescOG->nodeValue;
						}
					}

					// Retrieve image URL from meta property="og:image"
					$metaImage = $xpath->query('//meta[@property="og:image"]/@content')->item(0);
					if ($metaImage) {
						$image = $metaImage->nodeValue;
					} else {
						// If OG image tag not found, attempt to find any first image tag in the HTML
						$imgTag = $doc->getElementsByTagName('img')->item(0);
						if ($imgTag) {
							$image = $imgTag->getAttribute('src');
						} 
					}

					$data = array(
						'title' => $title,
						'description' => $description,
						'image' => $image
					);
					$return['status'] = "1";
					$return['message'] = "Data Fetch Successfully.";
					$return['data'] = $data;
				}else{
					$return['message'] = "Failed to fetch HTML.";
				}
			}catch (Exception $e) {
				// Handle exception: Log error, return custom error message
				$return['message'] = $e->getMessage();
			}
		}else{
			$return['message'] = "Url Is Missing.";
		}
        echo json_encode($return);
	}

	//submit record method
	public function submitRecord(){
		$return = array("status" => "0","message" => "","data" => []);		
		$data = $this->input->post('data');
		$url = $this->input->post('url');
		// if data missing then return with warning message
		if($data){
			$decodeData = json_decode($data,true);
			//push url in array
			$decodeData['url'] = $url;
			
			//load modal 
			$this->load->model('HomeModal');
			//call save record function
			$response = $this->HomeModal->save_preview($decodeData);
			//return status
			echo json_encode($response);
		}
	} 

	public function listRecord($page_number = 1) {
		// Load model
		$this->load->model('HomeModal');
	
		// Load pagination library
		$this->load->library('pagination');
	
		// Pagination configuration
		$config = array();
		$config["base_url"] = base_url() . "recordlist";
		$config["total_rows"] = $this->HomeModal->countRecord();
		$config['use_page_numbers'] = TRUE;
		$config["per_page"] = 10;
		$config["uri_segment"] = 2;
		//print_r($config);die;
		$this->pagination->initialize($config);
		
		//get all cover data from "0"
		$offset = ($page_number > 0) ? ($page_number - 1) * $config["per_page"] : 0;
		
		$data["links"] = $this->pagination->create_links();
		//add class pagelink to find pagniation number in jquery
		$data["links"] = preg_replace("/<a /ism", "<a class='pagelink' ", $data["links"]);
		// Fetch records using the model method
		$data["previews"] = $this->HomeModal->fetchRecord($config["per_page"], $offset);
	
		// Send data as JSON
		$this->output->set_content_type('application/json')->set_output(json_encode($data));
	}	
}
