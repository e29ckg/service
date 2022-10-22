<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header("'Access-Control-Allow-Credentials', 'true'");
header('Content-Type: application/javascript');
header("Content-Type: application/json; charset=utf-8");
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

  // Get the API client and construct the service object.
$client = getClient();
$service = new Google_Service_Calendar($client);
  
// Begin----program //

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $data = json_decode(file_get_contents("php://input"));

    $summary = $data->summary;
    $description = $data->description;
    $start = $data->start;
    $end = $data->end;

    $start = date("c", strtotime($start));
    $end = date("c", strtotime($end));
    // echo $summary . ' : ' .$description .' '. $start . ' - ' . $end;
// ecit();
    if($summary != "" && $description !="" && $start !="" && $end !=""){
        $event_data = array(
            'summary' => $summary, // หัวเรื่อง
            'description' => $description, // รายละเอียด
            'start' => array( 
              'dateTime' => $start,
              'timeZone' => 'Asia/Bangkok',
            ),
            'end' => array( 
              'dateTime' => $end,
              'timeZone' => 'Asia/Bangkok',
            ),
          );
        
          $event = new Google_Service_Calendar_Event($event_data); // สร้าง event object
 
          $calendarId = 'primary'; // calendar หลัก
          $event = $service->events->insert($calendarId, $event); // ทำคำสั่งเพิ่มข้อมูล
        
      http_response_code(200);
      echo json_encode(array("status" => true,"massege"=>"Seccess","id"=>$event->id));
    //   exit();
    }else{
        http_response_code(204);
      echo json_encode(array('status' => false,'massege'=>'No Data'));
    }

}else{
    http_response_code(405);
    echo json_encode(array('status' => false,'massage' => 'Method Not Allowed'));
}
 

?>