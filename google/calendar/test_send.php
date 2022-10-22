<?php
// set post fields
$message_data = [
    // "summary" => "นายพเยาว์ สนพลาย",
    // "description" => "ทดสอบ",
    // "start" => "2021-12-28 08:00",
    // "end"=>"2021-12-28 16:30"
    "eventId" => "e25nfv1clvs65fo2cn140rnh1s"
];
// var_dump(json_encode($post));
$url = 'http://10.37.64.1/service/google/calendar/check_event_ven.php';
$headers = array('Method: POST', 'Content-type: application/json');
$message_data = json_encode($message_data);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_POSTFIELDS, $message_data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$result = curl_exec($ch);

// // close the connection, release resources used
curl_close($ch);
$res = json_decode($result);
// echo $res['status'];
// do anything you want with your response
var_dump($res->status);



// $message_data = [                           // google calendar
//     "action"=>"update",
//     "eventId"=>$modelV->comment,
//     "dataEvent"=>[
//         "summary" => Profile::getProfileNameById($modelV->user_id),
//         "colorId" => 1
//         ]                        
//     ];
    // Ven::calendarVen($message_data);      // google calendar


    function addEventVen($message_data){
        // $url = 'http://10.37.64.1/service/google/calendar/calendar.php';
        // $headers = array('Method: POST', 'Content-type: application/json');
        // $message_data = json_encode($message_data);

        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL, $url);
        // curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, $message_data);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // $res = curl_exec($ch);
        // // // close the connection, release resources used
        // curl_close($ch);
        // return $res;
    }

    function removeEventVen($message_data){
        // $url = 'http://10.37.64.1/service/google/calendar/calendar.php';
        // $headers = array('Method: POST', 'Content-type: application/json');
        // $message_data = json_encode($message_data);

        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL, $url);
        // curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, $message_data);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // $res = curl_exec($ch);

        // // // close the connection, release resources used
        // curl_close($ch);    

        // return $res;
    }
    function calendarVen($message_data){
        // $url = 'http://10.37.64.1/service/google/calendar/calendar.php';
        // $headers = array('Method: POST', 'Content-type: application/json');
        // $message_data = json_encode($message_data);

        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL, $url);
        // curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, $message_data);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // $res = curl_exec($ch);

        // // // close the connection, release resources used
        // curl_close($ch);    

        // return $res;
    }

    function checkEventVen($message_data){
        // $url = 'http://10.37.64.1/service/google/calendar/check_event_ven.php';
        // $headers = array('Method: POST', 'Content-type: application/json');
        // $message_data = json_encode($message_data);

        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL, $url);
        // curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, $message_data);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // $res = curl_exec($ch);

        // // // close the connection, release resources used
        // curl_close($ch);    
        // $res = json_decode($result);
        // // var_dump($res->status);
        // return $res->status;
    }

   function venGoogleCalendar($message_data){
        // $url = 'http://10.37.64.1/service/google/calendar/check_event_ven.php';
        // $headers = array('Method: POST', 'Content-type: application/json');
        // $message_data = json_encode($message_data);

        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL, $url);
        // curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, $message_data);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // $res = curl_exec($ch);

        // // // close the connection, release resources used
        // curl_close($ch);    
        // $res = json_decode($result);
        // // var_dump($res->status);
        // return $res->status;
    }