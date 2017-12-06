<?php
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', __FILE__.'.log');


// (string) $message - message to be passed to Slack
// (string) $room - room in which to write the message, too
// (string) $icon - You can set up custom emoji icons to use with each message
function slack($message, $room = "engineering", $icon = ":robot:") {
        //$message = urlencode($message);

        $message = str_replace('&', '&amp;', $message);
        $message = str_replace('<','&lt;', $message);
        $message = str_replace('>','&gt;', $message);
    
        $room = ($room) ? $room : "engineering";
        $data = "payload=" . json_encode(array(
                "channel"       =>  "#deploy-dev",
                "username"      =>  'deploy-bot',
                "text"          =>  $message,
//                "icon_emoji"    =>  ':monkey_face:',
//              "icon_emoji"    =>  ':robot_face:',
//              "icon_url"          =>  'http://lorempixel.com/48/48',
                "as_user"       => false
        ));

/*
        var_dump(json_encode(array(
                "channel"       =>  "#deploy-dev",
                "username"      =>  'deploy-bot',
                "text"          =>  $message,
                "icon_emoji"    =>  ':monkey_face:',
//              "icon_emoji"    =>  ':robot_face:',
//              "icon_url"          =>  'http://lorempixel.com/48/48',
                "as_user"       => false
        )));
*/  
  
    // You can get your webhook endpoint from your Slack settings
        $ch = curl_init("https://hooks.slack.com/services/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");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
    
    // Laravel-specific log writing method
        // Log::info("Sent to Slack: " . $message, array('context' => 'Notifications'));
        return $result;
    }







$date = '['.date('Y-m-d H:i:s')."]";

ob_start();
echo $date."\n";
//echo "REQUEST_URI:"."\n";
//echo  $_SERVER['REQUEST_URI'];
//echo "_REQUEST:\n";
//echo var_dump($_REQUEST);
$data = @file_get_contents("php://input");
echo $data;
$x = ob_get_contents();


$data      = json_decode($data, true);

var_dump($data);

$filetime  = filemtime('request.log');
$yesterday = strtotime("today") - 1;




$repositoryName   = $data['project']['name'];
$repositoryBranch = explode('/', $data['ref']);

$repositoryBranch = end($repositoryBranch);


if ($repositoryBranch && $repositoryName){
//$cmd = "../install_release.sh x $repositoryName $repositoryBranch 2>&1";

    $cmd = 'find -H /work/www/dev -name .git -type d -not -path "*/#DEPRECATED/*" -mindepth 1 -maxdepth 4 -execdir /work/hook/try_install.sh x '.$repositoryName.' '.$repositoryBranch.' \\; 2>&1';

    $result = shell_exec($cmd);

    var_dump($result);


//$result = shell_exec("/usr/local/bin/bash -c '/work/scripts/staging/install_release.sh x $repositoryName $repositoryBranch'");
    
    $msg = "$date\n"."Пуш в центральный репозиторий завершен. $repositoryName $repositoryBranch $result";
    var_dump(slack($msg));
} else {
    if (!$repositoryName){
        slack('Не указан репозиторий, странно');
    } else {
        slack('Нет ветки, странно');
    }
}


/*
$curl = <<<EOT
curl -X POST -H 'Content-type: application/json' \
--data '{"text":"$date\n$repositoryName", "username": "deploy-bot"}' \
 https://hooks.slack.com/services/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
EOT;

shell_exec($curl);
*/
$x .= ob_get_contents();
ob_end_clean();



file_put_contents('request.log', $x , ($filetime < $yesterday)?:FILE_APPEND);



#$REPOSITORY_GROUP  $1
#$REPOSITORY_NAME   $2
#$REPOSITORY_BRANCH $3

