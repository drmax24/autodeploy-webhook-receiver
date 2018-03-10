<?php
chdir(__DIR__);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', __FILE__ . '.log');


require_once("../vendor/autoload.php");


$dotenv = new Dotenv\Dotenv( __DIR__.'/..');
$dotenv->load();


function slack_new($message)
{

    $settings = [
        'username' => 'deply-bot',
        'channel' => '#deploy_dev',
        'link_names' => true,
    ];

    $client = new Maknz\Slack\Client('https://hooks.slack.com/services/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', $settings);
    //$client->send('Hello world!');
}


// (string) $message - message to be passed to Slack
// (string) $room - room in which to write the message, too
// (string) $icon - You can set up custom emoji icons to use with each message
function slack($message, $room = "engineering", $icon = ":robot:")
{
    //$message = urlencode($message);
    $message = sanitizeString($message);
    //$message = preg_replace('/[^\00-\255]+/u', '', $message);

    $message = str_replace('&', '&amp;', $message);
    $message = str_replace('<', '&lt;', $message);
    $message = str_replace('>', '&gt;', $message);

    $data = "payload=" . json_encode(array(
            "channel" => "#deploy-dev",
            "username" => 'deploy-bot',
            "text" => $message,
            "icon_emoji" => ':monkey_face:',
//              "icon_emoji"    =>  ':robot_face:',
//              "icon_url"          =>  'http://lorempixel.com/48/48',
            "as_user" => false
        ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);


    var_dump(json_encode(array(
        "channel" => "#deploy-dev",
        "username" => 'deploy-bot',
        "text" => $message,
        "icon_emoji" => ':monkey_face:',
//              "icon_emoji"    =>  ':robot_face:',
//              "icon_url"          =>  'http://lorempixel.com/48/48',
        "as_user" => false
    )));


    // You can get your webhook endpoint from your Slack settings
    $ch = curl_init("https://hooks.slack.com/services/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");
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);

    return $result;
}


$date = '[' . date('Y-m-d H:i:s') . "]";

ob_start();
echo $date . "\n";
//echo "REQUEST_URI:"."\n";
//echo  $_SERVER['REQUEST_URI'];
//echo "_REQUEST:\n";
//echo var_dump($_REQUEST);
$data = @file_get_contents("php://input");
echo $data;


$logData = ob_get_contents();


$data = json_decode($data, true);

//var_dump($data);

$filetime = filemtime('request.log');
$yesterday = strtotime("today") - 1;


$repositoryName = explode('/', $data['project']['ssh_url']);
$repositoryName = end($repositoryName);
$repositoryName = explode('.git', $repositoryName);
$repositoryName = $repositoryName[0];


$repositoryBranch = explode('/', $data['ref']);
$repositoryBranch = end($repositoryBranch);


if ($repositoryBranch && $repositoryName) {

    $cmd = 'find -H '.env('SEARCH_DIR').' '.env('SEARCH_DIR2', '').' -name .git -type d -not -path "*/#DEPRECATED/*" -mindepth 1 -maxdepth 4 -execdir /work/hook/try_install.sh x ' . $repositoryName . ' ' . $repositoryBranch . ' \\; 2>&1';
    //$cmd = 'find -H '.env('SEARCH_DIR').' '.env('SEARCH_DIR2', '').' -name .git -type d -not -path "*/#DEPRECATED/*" -mindepth 1 -maxdepth 4';
    //var_dump('cmd:');
    //var_dump($cmd);

    $result = shell_exec($cmd);
    $dirs = explode("\n", $result);


    slack(':rotating_light: ' . $date . ' ' . $data['user_name'] . ' произвел пуш в ' . $repositoryName . ' ' . $repositoryBranch);
    slack($result);

} else {
    if (!$repositoryName) {
        slack('Не указан репозиторий, странно. ssh_url: ' . $data['project']['ssh_url']);
    } elseif (!$repositoryBranch) {
        slack('Нет ветки, странно');
    } else {
        slack('неизвестная ошибка');
    }
}

function fixEncoding($string, $encoding = 'UTF-8')
{
    // removes xD800-xDFFF, xFEFF, xFFFF, x110000 and higher
    $string = @iconv('UTF-16', $encoding . '//IGNORE', iconv($encoding, 'UTF-16//IGNORE', $string)); // intentionally @
    $string = str_replace("\xEF\xBB\xBF", '', $string); // remove UTF-8 BOM
    return $string;
}


function sanitizeString($string)
{
    $del = array('', '', '', '', '', '');

    // same as ASCII
    $search = array(
        '/[\x00-\x08]/',
        //'/[\x09]/',   // TAB
        //'/[\x0A]/',   // LF //Line Feed
        '/\x1B/',
        //'/\x1C/',   // FF: Form Feed - new page for printer
        //'/[\x1D]/',   // CR: Carriage Return
        '/[\x1E-\x1F]/',
        '/[\x7F]/',     // DELETE
    );
    


    $newString = preg_replace($search, $del, $string);
    $newString = fixEncoding($newString);


    if ($newString !== $string) {
        echo $newString;
        echo "fired\n";
    }

    return $newString;
}



$logData .= ob_get_contents();
ob_end_clean();


file_put_contents('request.log', $logData, ($filetime < $yesterday) ?: FILE_APPEND);



#$REPOSITORY_GROUP  $1
#$REPOSITORY_NAME   $2
#$REPOSITORY_BRANCH $3

