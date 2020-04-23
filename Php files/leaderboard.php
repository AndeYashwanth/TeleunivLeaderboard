<?php
// check if website is down
$host = 'teleuniv.net';
$username = '';
$password = '';

//check if website is down
if(!$socket =@ fsockopen($host, 80, $errno, $errstr, 30)) {
    $subject = "Teleuniv Notification";
    $message = "Teleuniv website is down.<br>Here is the error code".$errno."<br>".$errorstr;
    $headers = "From:admin@andeyashwanth.tk\r\n";
    $headers .= "Reply-To:admin@andeyashwanth.tk\r\n";
    $headers .= "Content-type: text/html\r\n";
    require "../includes/dbh.inc.php";
    $result = mysqli_query($conn, "SELECT * FROM arjuna_email_notif;");
    foreach ($result as $row){
        mail($row['email'], $subject, $message, $headers);
    }

    fclose($socket);
    exit();
}
define("COOKIE_FILE", "cookie.txt");

// Login the user
$ch = curl_init('https://teleuniv.net/login/index.php');
curl_setopt ($ch, CURLOPT_COOKIEJAR, COOKIE_FILE);
curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/32.0.1700.107 Chrome/32.0.1700.107 Safari/537.36');
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, "username={$username}&password={$password}");
curl_exec ($ch);

// Get the users details
$ch = curl_init('https://teleuniv.net/student/arjuna_ajax_combine.php?trmid=11&trcid=75&trtopicid=4635&topicname=Season-5_Session-04_09-02-2020');
curl_setopt ($ch, CURLOPT_COOKIEFILE, COOKIE_FILE);
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
$html = curl_exec($ch);
// file_put_contents('leaderboard.html', $html);
curl_close($ch);

include('simple_html_dom.php');
// $html = file_get_html("leaderboard.html");
$html = str_get_html($html);
$count = 0;
$cup_array=array();
foreach($html->find('tr') as $a) {
    if($count>13){
        if($a->find('td',1)->find("i",0)){
            $cup_array[] = $a->find('td',3)->find('div',0)->plaintext;
        }
    }
    $count++;
}

var_dump($cup_array);

#reading from file
$arr_from_file = file('leaderboard.txt', FILE_IGNORE_NEW_LINES);
$diff_arr = array_merge(array_diff($cup_array, $arr_from_file), array_diff($arr_from_file, $cup_array));
if(!empty($diff_arr)){
    $subject = "Leaderboard Changed";
    $message.="<b>Current Winners:</b><br>";
    foreach($cup_array as $line){
        $message .= "{$line}<br>";
    }
    $message.="<br><b>Previous Winners:</b><br>";
    foreach($arr_from_file as $line){
        $message .= "{$line}<br>";
    }
    $message.="<br>";

    $headers = "From:admin@andeyashwanth.tk\r\n";
    $headers .= "Reply-To:admin@andeyashwanth.tk\r\n";
    $headers .= "Content-type: text/html\r\n";
    mail("andeyashwanth001@gmail.com", $subject, $message, $headers);
    mail("saitarun2307pagudala@gmail.com", $subject, $message, $headers);

    $fp = fopen('leaderboard.txt', 'w');
    foreach($cup_array as $line){
        fwrite($fp, $line."\r\n");
    }
    fclose($fp);
}
