<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
// header("'Access-Control-Allow-Credentials', 'true'");
// header('Content-Type: application/javascript');
// header("Content-Type: application/json; charset=utf-8");
// header("Content-Type: json;");

require_once __DIR__ . '/vendor/autoload.php';

/** Error reporting */
error_reporting(0);
ini_set('display_errors', FALSE);
ini_set('display_startup_errors', FALSE);



define('APPLICATION_NAME', 'E29CKG');
define('CREDENTIALS_PATH', __DIR__ . '/.credentials/calendar.json');
define('CLIENT_SECRET_PATH', __DIR__ . '/client_secret.json');
// // If modifying these scopes, delete your previously saved credentials
// // at ~/.credentials/calendar-php-quickstart.json
define('SCOPES', implode(' ', array(
  Google_Service_Calendar::CALENDAR_EVENTS)
));

date_default_timezone_set("Asia/Bangkok");
// echo date_default_timezone_get();

function getClient() {
    $client = new Google_Client();
    $client->setApplicationName(APPLICATION_NAME);
    $client->setScopes(SCOPES);
    $client->setAuthConfig(CLIENT_SECRET_PATH);
    $client->setAccessType('offline');
      $client->setApprovalPrompt('force');
      $guzzleClient = new \GuzzleHttp\Client(array( 'curl' => array( CURLOPT_SSL_VERIFYPEER => false, ), ));
      $client->setHttpClient($guzzleClient);  
    
    // Load previously authorized credentials from a file.
    $credentialsPath = expandHomeDirectory(CREDENTIALS_PATH);
    if (file_exists($credentialsPath)) {
      $accessToken = json_decode(file_get_contents($credentialsPath), true);
    } else {
      // Request authorization from the user.
      $authUrl = $client->createAuthUrl();
      printf("Open the following link in your browser:\n%s\n", $authUrl);
      print 'Enter verification code: ';
      $authCode = trim(fgets(STDIN));
    
      // Exchange authorization code for an access token.
      $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
    
      // Store the credentials to disk.
      if(!file_exists(dirname($credentialsPath))) {
        mkdir(dirname($credentialsPath), 0700, true);
      }
      file_put_contents($credentialsPath, json_encode($accessToken));
      printf("Credentials saved to %s\n", $credentialsPath);
    }
    $client->setAccessToken($accessToken);
    
    // Refresh the token if it's expired.
    if ($client->isAccessTokenExpired()) {
      $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
      $newAccessToken = $client->getAccessToken();
      $accessToken = array_merge($accessToken, $newAccessToken);
      file_put_contents($credentialsPath, json_encode($accessToken));
  }
    return $client;
  }
    
  /**
   * Expands the home directory alias '~' to the full path.
   * @param string $path the path to expand.
   * @return string the expanded path.
   */
  function expandHomeDirectory($path) {
    $homeDirectory = getenv('HOME');
    if (empty($homeDirectory)) {
      $homeDirectory = getenv('HOMEDRIVE') . getenv('HOMEPATH');
    }
    return str_replace('~', realpath($homeDirectory), $path);
  }

$calendarId = 'jdki3pbi123p12u7uofukjfh84@group.calendar.google.com'; // รหัส calendar หลัก
   // Get the API client and construct the service object.
  // $client = getClient();
  // $service = new Google_Service_Calendar($client);  

/**   Begin----program   */

if($_SERVER['REQUEST_METHOD'] === 'POST'){
  $data = json_decode(file_get_contents("php://input"));
  
  /**  เรียกดูรายละเอียด eventId */
  if($data->action == 'get'){ 
    if(getEvents($calendarId,$data->eventId)){
      $resp = getEvents($calendarId,$data->eventId);
      $sms = "ok.";
      if($resp->status == "cancelled"){
        $sms = "Event นี้ถูกลบไปแล้ว";        
      }
      http_response_code(200);
      echo json_encode(array('status' => $resp->status,'message' => $sms,"resp" =>$resp ));     
      exit(); 
    }
  }
  
  /** เพิ่มข้อมูล remove */
  if($data->action == 'remove'){    
    http_response_code(200);
    echo json_encode(array('status' => true,'message' => 'Ok.',"resp" => removeEvents($calendarId,$data->eventId)));      
    exit();
  }
  
  /** เพิ่มข้อมูล insert */
  if($data->action == 'insert'){    
    $resp = insertEvent($calendarId,$data->dataEvent);
    if($resp['error']){
      $resp = json_decode($resp);
      http_response_code($resp->error->code);
      echo json_encode(array('status' => false,'message' => $resp->error->message,"resp"=>$resp));
      exit();
    }
    http_response_code(200);
    echo json_encode(array('status' => true,'message' => 'Ok.',"resp" => $resp));   
    exit();   
  }

  /** เพิ่มข้อมูล update */
  if($data->action == 'update'){    
    http_response_code(200);
    echo json_encode(array(
      "coo" => $data->dataEvent->colorId,
      "status" => true,
      'message' => 'Ok.',
      "resp" => updateEvent($calendarId,$data->eventId,$data->dataEvent)));      
    exit();
  }
  
  if($data->action == 'list'){    
    $optParams = array(
      'maxResults' => 1,
      'orderBy' => 'startTime',
      'singleEvents' => TRUE,
      'timeMin' => date('c'),
    );
      http_response_code(200);
      echo json_encode(array('data'=>$data,'status' => true,'message' => 'Ok.',"resp" => listEvents($calendarId,$optParams)));      
      // echo json_encode(array('status' => true,'message' => 'Ok.',"resp" => listEvents($calendarId,$data->optParams)));      
      // return var_dump($data->optParams);      
    exit();
  }else{
    http_response_code(405);
    echo json_encode(array('status' => false,'message' => 'Events Not Allowed list'));
    exit();
  }

  http_response_code(405);
  echo json_encode(array('status' => false,'message' => 'Method Not Allowed'));
  exit();
}else{
  http_response_code(405);
  echo json_encode(array('status' => false,'message' => 'Method Not Allowed')); 
}
/** End Process */


function getEvents($calendarId,$eventId){
  $client = getClient();
  $service = new Google_Service_Calendar($client);
  return  $service->events->get($calendarId, $eventId);
}

function removeEvents($calendarId,$eventId){
  $client = getClient();
  $service = new Google_Service_Calendar($client);
  return  $service->events->delete($calendarId, $eventId);
}
function listEvents($calendarId,$optParams){
  $client = getClient();
  $service = new Google_Service_Calendar($client);
  return $service->events->listEvents($calendarId, $optParams); 
}

function insertEvent($calendarId,$dataEvent){
  try{
    $client = getClient();
    $service = new Google_Service_Calendar($client);
    $event = new Google_Service_Calendar_Event($event_data); // สร้าง event object 
    if($dataEvent->summary){
      $event->setSummary($dataEvent->summary);
    }
    if($dataEvent->description){
      $event->setDescription($dataEvent->description);
    }
    if($dataEvent->colorId){
      $event->setColorId($dataEvent->colorId);
    }
    if($dataEvent->start){
      $startD = date("c", strtotime($dataEvent->start)); 
      $start = new Google_Service_Calendar_EventDateTime();
      $start->setDateTime($startD);
      $start->setTimeZone('Asia/Bangkok');
      $event->setStart($start);
    }
    if($dataEvent->end){
      $endD = date("c", strtotime($dataEvent->end)); 
      $end = new Google_Service_Calendar_EventDateTime();
      $end->setDateTime($endD);
      $end->setTimeZone('Asia/Bangkok');
      $event->setEnd($end);
    }
    $event = $service->events->insert($calendarId, $event); // ทำคำสั่งเพิ่มข้อมูล
    return $event;
  } catch (Google_Service_Exception $e) {
    syslog(LOG_ERR, $e->getMessage());
    return $e->getMessage();
  }  
}

function updateEvent($calendarId,$eventId,$dataEvent){        
  $client = getClient();
  $service = new Google_Service_Calendar($client);
  $event = new Google_Service_Calendar_Event($event_data); // สร้าง event object 
  $event = $service->events->get($calendarId,$eventId);
  if($dataEvent->summary){
    $event->setSummary($dataEvent->summary);
  }
  if($dataEvent->description){
    $event->setDescription($dataEvent->description);
  }
  if($dataEvent->colorId){
    $event->setColorId($dataEvent->colorId);
  }
  if($dataEvent->start){
    $startD = date("c", strtotime($dataEvent->start)); 
    $start = new Google_Service_Calendar_EventDateTime();
    $start->setDateTime($startD);
    $start->setTimeZone('Asia/Bangkok');
    $event->setStart($start);
  }
  if($dataEvent->end){
    $endD = date("c", strtotime($dataEvent->end)); 
    $end = new Google_Service_Calendar_EventDateTime();
    $end->setDateTime($endD);
    $end->setTimeZone('Asia/Bangkok');
    $event->setEnd($end);
  }

  try {
    $updateEvent = $service->events->update($calendarId, $eventId, $event);
    return $updateEvent;
  } catch (Google_Service_Exception $e) {
    syslog(LOG_ERR, $e->getMessage());
    return $e->getMessage();
  }
}


?>