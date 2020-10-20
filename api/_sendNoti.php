<?php
 function sendNotification($token,$orders=[],$title= "Title",$body = "Body",$link="", $icon = '../img/logos/logo.png',$data = []){
  global $con;
  foreach($orders as $order){
            $sql = "select * from orders where orders.id =  ?";
            $result =getData($con,$sql,[$order]);
            if(count($result) > 0){
              $sql = "insert into notification (title,body,for_client,staff_id,client_id,order_id)
              values(?,?,?,?,?,?)";
              setData($con,$sql,[$title,$body,0,$result[0]['manager_id'],0,$order]);
              setData($con,$sql,[$title,$body,0,$result[0]['driver_id'],0,$order]);
              setData($con,$sql,[$title,$body,1,0,$result[0]['client_id'],$order]);
            }
     }
 $apikey = 'AAAAX39_76o:APA91bEwobrGZyJSJYoNYPQPa-UgPXsM1kF-r-LiLMcMv8ja-bN4s3q4VRI9_zmpV2XgLwUrWekJa1l1rhOSLJBbdAZeGD2xS3gNiFJpTyWYBEw5Yhz-vDTVyqyxUXD9HrZohdX0oV1E';
 $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
 //$token= 'cPScyOX3Nrwg42amVXmMib:APA91bE1P6WUCfddxjyW07dULVN62eu3reGXyy7IJioK66QMqz4lkQaCSgdPLa2JVBFMm-NtKU0FU7nn8P43md8W8x4vgpa5T8J9tYyzgyt8noZjp3TNMtDcUIswgS9dG1HyrK0YLadk';
     $notification = [
            'title' =>$title,
            'body' => $body,
            'icon' =>$icon,
            "vibrate"=> [300,100,400,100,400,100,400],
            'sound' => 'mySound',
            'click_action' => $link
        ];
        $extraNotificationData = ["link" => $link,"moredata" =>$data];

        $fcmNotification = [
            'registration_ids' => $token, //multple token array
            //'to'        => $token, //single token
            'notification' => $notification,
            'data' => $extraNotificationData
        ];

        $headers = [
            'Authorization: key=' . $apikey,
            'Content-Type: application/json'
        ];
        try{
            $notification = [
             'body'   => $body,
             'title'  =>$title,
             "sound"=>'default',
             'subtitle'=> $order,
             'vibrate'=> [300,100,400,100,400,100,400],
             'vibrationPattern'=> [300,100,400,100,400,100,400],
             'data' => $extraNotificationData
            ];
            require_once '../vendor/autoload.php';
            $channelName = 'haydermohamedaliweaakalialiweaakalihellosafarticabogauallylayer';
            // You can quickly bootup an expo instance
            $expo = ExponentPhpSDK\Expo::normalSetup();
            // Subscribe the recipient to the server
            foreach($token as $v){
              $recipient= 'ExponentPushToken['.$v.']';
              $expo->subscribe($channelName, $recipient);
            }
            // Notify an interest with a notification
            $r = $expo->notify([$channelName], $notification);
        } catch (Exception $e) {
            $r = $e;
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$fcmUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
        $result = curl_exec($ch);
        curl_close($ch);
        $f = [$result,$r];

        return $f;
 }

?>