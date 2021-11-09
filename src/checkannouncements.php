<?php
ini_set('date.timezone', 'Asia/Jakarta');
ini_set('display_errors', false);
ini_set('log_errors', true);
ini_set('html_errors', false);
ini_set('error_reporting', ~E_NOTICE);
ini_set('error_log', __FILE__.'.log');

(PHP_SAPI !== 'cli' OR isset($_SERVER['HTTP_USER_AGENT'])) AND die('cli only');

function SendNotif($messageContent = "")
{
	static $curl;
	$url = "https://api.telegram.org/bot2122979886:AAE3nv_2YwBVFS3kqNKXaURt7UkrJ_seB60/sendMessage?";

	if (empty($messageContent)) {
		$messageContent = "<b>"."Message Content Empty"."</b>";
	}

	$post = [
	   'chat_id' => '-787905256',
	   'text' => $messageContent,
	   'parse_mode' => 'HTML',
	];

	try {
	   $opts = [];
	   $opts['http']['method'] = 'POST';
	   $opts['http']['content'] = http_build_query($post);
	   $opts['http']['header'] = 'Content-type: application/x-www-form-urlencoded';

	   $context = stream_context_create($opts);

	   $response = @file_get_contents($url,false,$context);
	   $response = is_array($response) ? $response : json_decode($response, true);
	   if (empty($response) || !$response['ok']) {
		   return;
	   }

	   return;
	} catch (\Exception $e) {
	   return;
	}
}

function SendNotifError($message = "")
{
	if (!is_string($message)) {
		$message = stripslashes(json_encode($message));
	}

	$messageContent = "<b>"."Error Notice"."</b>"."\n";
	$messageContent .= "<pre>".$message."\r\n</pre>";
	$messageContent .= "<b>"."Time Exec"."</b>"." : ".date('Y-m-d:H:i:s');

	return SendNotif($messageContent);
}

function SendNotifAnnouncement($message = [])
{
	if (empty($message['title']) OR $message['title'] == "") {
		return SendNotifError('Cant Read Latest Announcement');
	}

	if (empty($message['title']) OR $message['tPublish'] == "") {
		return SendNotifError('Cant Read Latest Announcement');
	}

	$messageContent = "<b>"."Latest Announcement"."</b>"."\n";
	$messageContent .= "<pre>".$message['title']."\r\n</pre>";
	$messageContent .= "<b>"."Time Publish"."</b>"." : ".$message['tPublish'];

	return SendNotif($messageContent);
}

function LogData($type = "none", $message = "")
{
	static $logpath;

	$basedir = dirname(__FILE__).DIRECTORY_SEPARATOR.'log';
	if (!file_exists($basedir)) {
		@mkdir($basedir, 0755);
	}
	$logpath = $basedir.DIRECTORY_SEPARATOR.'log_'.date('Y_m_d').'.log';

	try {
		$message = date('H:i:s')." - $type - $message"."\r\n";
		$writelog = fopen($logpath,"a");
		fwrite($writelog, $message);
		fclose($writelog);
	} catch (\Exception $e) {
		throw new \Exception("Error Processing Write Log ".$e->getMessage(), 1);
	}
}
if (!file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR.'homepage.txt')) {
	touch(dirname(__FILE__).DIRECTORY_SEPARATOR.'homepage.txt');
}
$file = @file_get_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.'homepage.txt');
preg_match('/<div id=td_uid_22_618a37d40e28e ([\W\w\d\D\s\S]+)[\/div>$]/m', $file, $match);
if (empty($match)) {
	return SendNotifError('Check uid Changed');
}
if (!is_string($match)) {
	$match = stripslashes(json_encode($match[1]));
}
if (!file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR.'announcementpage.txt')) {
	touch(dirname(__FILE__).DIRECTORY_SEPARATOR.'announcementpage.txt');
}
@file_put_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.'announcementpage.txt',$match);

$file = @file_get_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.'announcementpage.txt');
preg_match('/datetime="([0-9\+\:\-\w]+)[\"$]/s', $file, $matchdatetime);
preg_match('/title="([a-zA-Z0-9\s\(\)\-]+)[\"$]/s', $file, $matchtitle);

$strtotimeLastArticle = strtotime(date('Y-m-d',strtotime($matchdatetime[1])));
$strtotimeNow = strtotime(date('Y-m-d'));

if ($strtotimeLastArticle == $strtotimeNow) {
	SendNotifAnnouncement(['title' => $matchtitle[1], 'tPublish' => $matchdatetime[1]]);
} else {
	LogData('error', 'No Recent Announcements');
}
