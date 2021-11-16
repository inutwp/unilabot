<?php
ini_set('date.timezone', 'Asia/Jakarta');
ini_set('display_errors', false);
ini_set('log_errors', true);
ini_set('html_errors', false);
ini_set('error_reporting', ~E_NOTICE);
ini_set('error_log', __FILE__ . '.log');

(PHP_SAPI !== 'cli' or isset($_SERVER['HTTP_USER_AGENT'])) and die('cli only');

function SendNotif($messageContent = "")
{
	static $curl;
	$url = "https://api.telegram.org/bot2122979886:AAE3nv_2YwBVFS3kqNKXaURt7UkrJ_seB60/sendMessage?";

	if (empty($messageContent)) {
		$messageContent = "<b>" . "Message Content Empty" . "</b>";
	}

	$post = [
		'chat_id' => '-781514645',
		'text' => $messageContent,
		'parse_mode' => 'HTML',
	];

	try {
		$opts = [];
		$opts['http']['method'] = 'POST';
		$opts['http']['content'] = http_build_query($post);
		$opts['http']['header'] = 'Content-type: application/x-www-form-urlencoded';

		$context = stream_context_create($opts);

		$response = @file_get_contents($url, false, $context);
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

	$messageContent = "<b>" . "Error Notice" . "</b>" . "\n";
	$messageContent .= "<pre>" . $message . "\r\n</pre>";
	$messageContent .= "<b>" . "Time Exec" . "</b>" . " : " . date('Y-m-d:H:i:s');

	return SendNotif($messageContent);
}

function SendNotifAnnouncement($message = [])
{
	if (empty($message['title']) or $message['title'] == "") {
		return SendNotifError('Cant Read Latest Announcement');
	}

	if (empty($message['tPublish']) or $message['tPublish'] == "") {
		$message['tPublish'] = "";
	}

	if (empty($message['link']) or $message['link'] == "") {
		$message['link'] = "";
	}

	$messageContent = "<b>" . "Latest Announcement" . "</b>" . "\n";
	$messageContent .= '<a href="' . $message['link'] . '"><b>' . $message['title'] . '</b></a>' . "\r\n";
	$messageContent .= "<b>" . "Time Publish" . "</b>" . " : \r\n" . date('d-m-Y H:i:s', strtotime($message['tPublish'])) . "\r\n";

	return SendNotif($messageContent);
}

function CreateLogDir()
{
	$logdir = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'log';
	if (!file_exists($logdir)) {
		@mkdir($logdir, 0755);
	}
	return $logdir;
}

function LogData($type = "none", $message = "")
{
	static $logpath;

	$logdir = CreateLogDir();
	$logpath = $logdir . DIRECTORY_SEPARATOR . 'log_' . date('Y_m_d') . '.log';

	try {
		$message = date('H:i:s') . " - $type - $message" . "\r\n";
		$writelog = fopen($logpath, "a");
		fwrite($writelog, $message);
		fclose($writelog);
	} catch (\Exception $e) {
		throw new \Exception("Error Processing Write Log " . $e->getMessage(), 1);
	}
}

function CheckHasBeenSent($messagetitle = "")
{
	$messagetitle = base64_encode($messagetitle);

	$logdir = CreateLogDir();
	$file = $logdir . DIRECTORY_SEPARATOR . 'check_' . date('Y_m_d') . '.txt';

	clearstatcache($file);

	if (file_exists($file)) {
		$read = @file_get_contents($file);
		if (empty($read)) {
			$write = fopen($file, "w");
			fwrite($write, $messagetitle);
			fclose($write);
			return false;
		} else {
			if ($messagetitle == $read) {
				return true;
			} else {
				$write = fopen($file, "w");
				fwrite($write, $messagetitle);
				fclose($write);
				return false;
			}
		}
	} else {
		$write = fopen($file, "w");
		fwrite($write, $messagetitle);
		fclose($write);
		return false;
	}
}

if (!file_exists(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'homepage.txt')) {
	touch(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'homepage.txt');
}
$file = @file_get_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'homepage.txt');
preg_match('/<div id=td_uid_6_348njhj34hj([\W\w\d\D\s\S]+)[\/div>$]/m', $file, $match);
if (empty($match)) {
	preg_match('/Pengumuman\sTerbaru([\w\W\d\D]+)[td_block_inner\"\>$]/s', $file, $matchblock);
	preg_match('/<div id=([a-z0-9\_]+)\sclass/s', $matchblock[1], $match);
	preg_match("/<div id=$match[1]([\W\w\d\D\s\S]+)[\/div>$]/m", $file, $match);
	if (empty($match)) {
		return SendNotifError('Check uid Changed');
	}
}
if (!is_string($match)) {
	$match = stripslashes(json_encode($match[1]));
}
if (!file_exists(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'announcementpage.txt')) {
	touch(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'announcementpage.txt');
}
@file_put_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'announcementpage.txt', $match);

$file = @file_get_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'announcementpage.txt');
preg_match('/datetime="([0-9\+\:\-\w]+)[\"$]/s', $file, $matchdatetime);
preg_match('/title="([a-zA-Z0-9\s\(\)\-\&\#\;?]+)[\"$]/s', $file, $matchtitle);
preg_match('/<a\shref="([a-zA-Z0-9\:\/\.\-]+)[\"$]/s', $file, $matchlink);

$announcementTitle = htmlspecialchars_decode($matchtitle[1]);
$announcementTime = $matchdatetime[1];
$announcementLink = $matchlink[1];

$strtotimeLastArticle = strtotime(date('Y-m-d', strtotime($announcementTime)));
$strtotimeNow = strtotime(date('Y-m-d'));

if ($strtotimeLastArticle == $strtotimeNow && !CheckHasBeenSent($announcementTitle)) {
	SendNotifAnnouncement([
		'title' => $announcementTitle,
		'tPublish' => $announcementTime,
		'link' => $announcementLink
	]);
} else {
	LogData('info', 'No Recent Announcements');
}
