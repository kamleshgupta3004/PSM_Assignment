<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {
  public function __construct() {
	parent::__construct();
  }
  

  public function processUrl($url) {
    //check url is missing or not
    if (empty($url)) {
      return $this->responseStruct([],"URL Is Missing.",0);
    }

    // Trim any white space from left right
    $url = trim($url);

    // Validate URL format
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
      return $this->responseStruct([],"Invalid URL format.",0);
    }
    //exception handling error try{}catch{}
    try {
        $html = @file_get_contents($url);
        // if html record not found then handle by catch case and get whats error
        if ($html === false) throw new Exception("Failed to retrieve data from URL.");

        //extract all record as i want passing by html source code
        $data = $this->extractMetaData($html);

        // after extract data return data by pass data into function struction way 
        return $this->responseStruct($data, "Data Fetch Successfully.", 1);
    } catch (Exception $e) {
        // if found unknow bug handle and return error message
        return $this->responseStruct(array(),$e->getMessage(), 0);
    }
 }

 // extract all content from this method
 private function extractMetaData($html){
  //declear blank return array;
  $return = array();	
  
  // Create a new DOMDocument instance
  $doc = new DOMDocument();

  // Suppress warnings with @ in case the HTML is malformed.
  @$doc->loadHTML($html);

  // DOMXPath provides a way to query the DOM using XPath expressions.
  $xpath = new DOMXPath($doc);
  
  //extract data from using this function here pass html(source code),doc code and xpath
  $return['title'] = $this->getMetaContent($html,$doc,$xpath,'//meta[@property="og:title"]/@content','title');
  $return['description'] = $this->getMetaContent($html,$doc,$xpath,'//meta[@name="description"]/@content','//meta[@property="og:description"]/@content');
  $return['image'] = $this->getMetaContent($html,$doc,$xpath,'//meta[@property="og:image"]/@content','img');
  
  //return all data 
  return $return;
 }

private function getMetaContent($html, $doc, $xpath, $metadata, $find) {
  // Extract content from meta tags using XPath
  $metacontent = $xpath->query($metadata)->item(0);
  if ($metacontent) {
      return $metacontent->nodeValue;
  }

  // Handle 'description' specifically if not found in the meta tags
  if (strpos($find, 'description') !== false) {
      $metacontent = $xpath->query($metadata)->item(0);
      return $metacontent ? $metacontent->nodeValue : '';
  }

  // Handle image tags
  if ($find === 'img') {
      // Extract image source URL using regex
      if (preg_match('/<img\s+src=["\']([^"\']+\.(jpg|jpeg|png|gif))["\']/i', $html, $result)) {
          $img = $result[1]; // Extract URL from regex result

          //genrate full url if missing somthing from image url
          $imageurl = $this->makeImgUrl($img);
          return $imageurl;
      } else {
          // Fallback to getting the src attribute from the DOM if regex fails
          $content = $doc->getElementsByTagName($find)->item(0);
          if ($content) {
             //get image url form src
            $img = $content->getAttribute('src');
             //genrate full url if missing somthing from image url
            $imageurl = $this->makeImgUrl($img);
             return $imageurl;
          }
      }
  } else {
      // Handle other tags or content
      $content = $doc->getElementsByTagName($find)->item(0);
      return $content ? $content->nodeValue : '';
  }

  // Return an empty string if no content is found
  return '';
}

public function makeImgUrl($imgurl){
  // Retrieve domain name from the URL
  $domain = $this->getDomain();
  //check https is available on url
  if(preg_match("/^https*\:\/\//i", $imgurl)){
    return $imgurl;
  }else if(!preg_match("/^\//",$imgurl)){ //of url not avaialbe corrrect format then
      $imgurl = "/".$imgurl;
      return  $domain . $imgurl;
  }
  return $imgurl;
}

public function responseStruct($data=[], $message='',$status=0) {
    return ['status' => $status, 'message' => $message, 'data' => $data];
}
public function outputJson($data) {
 return json_encode($data);
}
 
 // Method to fetch URL content using cURL
 public function fetchUrlContent($url) {
  $ch = curl_init();

  // Set cURL options
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // Return the content as a string
  curl_setopt($ch, CURLOPT_TIMEOUT, 30); // Set timeout
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Follow redirects
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification (adjust as needed)
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // Disable host verification (adjust as needed)

  // Execute cURL request
  $result = curl_exec($ch);

  // Check for cURL errors
  if (curl_errno($ch)) {
      log_message('error', 'cURL Error: ' . curl_error($ch));
      $result = false;
  }

  // Close cURL session
  curl_close($ch);

  return $result;
}

public function getDomain() {
  // Retrieve URL from POST request
  $url = $this->input->post('url');

  // Parse the URL to extract components
  $parsedUrl = parse_url($url);

  // Extract the domain (host) from the parsed URL
  $domain = isset($parsedUrl['host']) ? $parsedUrl['host'] : '';

  // Determine the scheme from the parsed URL or default to 'https'
  $scheme = isset($parsedUrl['scheme']) ? $parsedUrl['scheme'] : 'https';

  // Construct the domain with the scheme
  return $scheme . '://' . $domain;
}

 // method to configure pagination
 public function paginationConfig($paginationLink) {
  return [
      "base_url" => base_url() . $paginationLink, // Base URL for pagination links
      "total_rows" => $this->HomeModal->countRecord(), // Total number of records
      "per_page" => 10, // Number of records per page
      "uri_segment" => 2, // URI segment for page number
      'use_page_numbers' => TRUE // Enable page numbers in URL
  ];
}

}

