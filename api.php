<?php
##Basic vanilla PHP RESTful API example by Nicholas Dooley##
##Remember to give your database.txt file read/write permissions##
##Put your .htaccess in the same directory as the api.php file##
##GET  /api/cars
##POST /api/changecars amount=200

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
 $data = $_GET;	
} else if($method === 'POST') {
 $data = $_REQUEST;	
} else {
 header("HTTP/1.1 404 Not Found");
}

##Other things to do##

//Public rate limit based from request IP or other identifiable information

//Authenticate HMAC (look up public key, generate hmac signature, compare)

//Rate limit calls

//Process call // check user permissions before calling function

$resp = processRequest($method, $data);

function processRequest($requestMethod, $parameters)
    {
	$response = array();
	
        switch ($requestMethod) {
            case 'GET':
	        switch ($parameters['call']) {
		 case 'cars':
                  $response = getCars($parameters);
                  break;				  
		 default:
                  $response = notFoundResponse();
                  break;
		}
		break;
			
	     case 'POST':
		switch ($parameters['call']) {
		  case 'changecars':
                   $response = changeCars($parameters);
                   break;				  
		  default:
                   $response = notFoundResponse();
                   break;
		 }
                break;
         }
	
        header($response['status_code_header']);
	
        if($response['body']) {
         echo $response['body'];
	}

}

function notFoundResponse() {
 $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
 $response['body'] = json_encode(array('error'=>'Not Found'));
 return $response;
}

function incorrectFormatResponse() {
 $response['status_code_header'] = 'HTTP/1.1 400 Bad Request';
 $response['body'] = json_encode(array('error'=>'Bad JSON incorrect format'));
 return $response;
}	

function getCars($parameters) {
  $response = array();
   $response['status_code_header'] = 'HTTP/1.1 200 OK';
   
    $db = fopen("database.txt", "r");
    $cars = fread($db,filesize("database.txt"));
    fclose($db);
	
   $response['body'] = json_encode(array('data'=>array('cars'=>$cars)));	
  return $response;
}

function changeCars($parameters) {
  $response = array();
 if(isset($parameters['amount']) && $parameters['amount'] > 0) {
	 
   $response['status_code_header'] = 'HTTP/1.1 200 OK';
   
   $db = fopen("database.txt", "w");
   fwrite($db, $parameters['amount']);
   fclose($db);
   
   $response['body'] = json_encode(array('data'=>array('message'=>"Car amount changed to ".$parameters['amount'])));	
 } else {
  $response = incorrectFormatResponse();
 }
 return $response;
}

?>
