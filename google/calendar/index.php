<?php
require_once __DIR__ . '/vendor/autoload.php';
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Google Calendar API </title>
    <meta charset='utf-8' />
  </head>
  <body>
    <p>Google Calendar API </p>
      
<?php
define('APPLICATION_NAME', 'CALENDAR-PHP');
define('CREDENTIALS_PATH', __DIR__ . '/.credentials/calendar.json');
define('CLIENT_SECRET_PATH', __DIR__ . '/client_secret.json');
// If modifying these scopes, delete your previously saved credentials
// at ~/.credentials/calendar-php-quickstart.json
define('SCOPES', implode(' ', array(
  Google_Service_Calendar::CALENDAR_READONLY)
));
  
/*if (php_sapi_name() != 'cli') {
  throw new Exception('This application must be run on the command line.');
}*/
  
/**
 * Returns an authorized API client.
 * @return Google_Client the authorized client object
 */
function getClient() {
  $client = new Google_Client();
  $client->setApplicationName(APPLICATION_NAME);
  $client->setScopes(SCOPES);
  $client->setAuthConfig(CLIENT_SECRET_PATH);
  $client->setAccessType('offline');
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
    file_put_contents($credentialsPath, json_encode($client->getAccessToken()));
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
  

// Print the next 10 events on the user's calendar.
$calendarId = 'primary';
$optParams = array(
  'maxResults' => 10,
  'orderBy' => 'startTime',
  'singleEvents' => TRUE,
  'timeMin' => date('c'),
);
// ส่วนของการลบข้อมูล
if(isset($_GET['eventId']) && $_GET['eventId']!=""){
    $eventId = $_GET['eventId'];
    $service->events->delete($calendarId, $eventId);
    header("Location:calendar.php");
    exit;
} 
// ส่วนของการดึงข้อมูลมาแสดง
$results = $service->events->listEvents($calendarId, $optParams); 
if (count($results->getItems()) == 0) {
  print "No upcoming events found.\n";
} else {
  print "Upcoming events:\n";
  foreach ($results->getItems() as $event) {
    $start = $event->start->dateTime;
    if (empty($start)) {
      $start = $event->start->date;
    }
    printf("%s (%s)\n", $event->getSummary(), $start);
    print("<a href='?eventId=".$event->getId()."'> ".$event->getId() ." Delete</a>");
    printf('<br>');
  }
}

?>
<form id ="addEvent" method="post">
     Summary:<input type="text" name="summary"><br>
     Description:<textarea name="description" rows="3" cols="50"></textarea><br>
     Start Date:<input type="date" name="start"><br>
     End Date:<input type="date" name="end"><br>
     <button type="submit">Send</button>
 </form>
 <div id="divResp" >
            
            </div>
  
<script src="https://code.jquery.com/jquery-3.6.0.js" ></script>    
<script src="js/jquery.serializejson.js" ></script>  
<script>

  $(function(){    
    $("#addEvent").submit(function(e){
      event.preventDefault()
      var data = JSON.stringify($('#addEvent').serializeJSON());
      // var data = JSON.stringify({"day":"2021-12-13"});
      console.log(data);
      $.ajax({
        url     : "http://10.37.64.1/service/google/calendar/add_event.php",
        type    : "POST",
        contentType : "application/json",
        dataType    : "json",
        data    : data,
        success : function(response, textStatus, xhr){
          console.log(xhr.status);          
          console.log(response);          
            // genResp(response.data); 
                               
        },
        error : function(){
          console.log('error : ' + JSON.stringify(data));
          genRespDiv('error : ')
        }        
      });         
    })
  }); 

</script>
  </body>
</html>