<?php
/*  sendmail script by softov.org
	version 20100523
	
License: GPLv3
http://softov.org/webdevtools

	Usage - see sendmail.config.php and sendmail.demo.html
*/

require_once('sendmail.config.php');

// orimail - a shortcut to mail() function that makes it easier
function orimail($subject, $msg, $from, $to, $cc = '', $bcc = '', $replyto = '', $moreheaders = '', $html = HTML_EMAIL) 
{
	$subject = substr($subject, 0, 70);
	$msg = wordwrap($msg, 70);

	$headers = '';
	if (!empty($from))    $headers .= "From: $from\r\n";
	if (!empty($replyto)) $headers .= "Reply-To: $replyto\r\n";

	//Each of these 3 lines cause the mail to be sent wrong. the html becomes text and the headers are visible as msg.
	//$headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
	//if ($html) $headers .= 'MIME-Version: 1.0' . "\r\n";
	//$headers .= "To: $to\r\n";

	if ($html) $headers .= 'Content-type: text/html; charset='.CHARSET."\r\n";
	if (!empty($cc)) $headers .= "Cc: $cc\r\n";
	if (!empty($bcc)) $headers .= "Bcc: $bcc\r\n";
	$headers .= $moreheaders;
	return mail($to, $subject, $msg, $headers, '-f'.$from);
}

// s -- safer: disable xss tags like <script <iframe <object   etc
function s($str)
{
	return str_ireplace(array('<script', '<embed', '<object','<frame','<iframe'), array('«script', '«embed', '«object','«frame','«iframe'),$str);
}

function secError($msg) 
{
	if (ERRORS_FILE != '' && ERRORS_FILE != false) {
		if ($f = fopen(ERRORS_FILE, "a")) {

            $log = DELIMITER."Date: ".date('Y-m-d H:i:s')."\r\n$msg\r\n POST=".s(var_export($_POST, true))."\r\n SERVER=".s(var_export($_SERVER, true))."\r\n";
			fwrite($f, $log);
			fclose($f);
		}
	}
	die(MSG_ERROR);
}

if (count($_POST)==0)
{
	secError("POST is empty");
}
 
/*
$okmail;     // was mail sent ok?
$okfile;     // was log file written ok?
$oksec;      // security tests result
$returnurl;  // the url to redirect after sending
$subject;    // the subject of the email
$log;        // the line to write to message log
*/

// init vars
$okmail = false;
$okfile = false;
$oksec = true;
$message = '';
$log = '';

// get current host (no leading www, no http://)
$host = strtolower(preg_replace('@^www\.@i', '', $_SERVER['HTTP_HOST']));
$hostreturn = strtolower(preg_replace('@^www\.@i', '', parse_url(RETURNURL, PHP_URL_HOST)));

if (isset($_SERVER['HTTP_REFERER'])) 
{
	$referer = $_SERVER['HTTP_REFERER'];
	$hostreferer = strtolower(preg_replace('@^www\.@i', '', parse_url($referer, PHP_URL_HOST)));
} else {
	$referer = '';
	$hostreferer = '';
}

// test post keyword
if ((!KEYWORD_FIELD && (!isset($_POST[KEYWORD_FIELD]) || ($_POST[KEYWORD_FIELD] != KEYWORD_VALUE)))) 
{
	secError("POST keyword missing or incorrect");
} else {
	// test http referer from current domain
	if ($hostreferer == '') 
	{
		secError("HTTP_REFERER is not set");
	} else {
		if ($hostreferer != $host && $hostreferer != $hostreturn) 
		{
			secError("HTTP_REFERER is different from the allowed domain");
		} else {
			// set returnurl
			$returnurl = RETURNURL;
			if (!empty($_POST['returnurl'])) 
			{
				$returnurl = $_POST['returnurl'];
				$hostreturn = strtolower(preg_replace('@^www\.@i', '', parse_url(RETURNURL, PHP_URL_HOST)));
			}

			if (empty($returnurl)) 
			{
				$returnurl = $_SERVER['HTTP_REFERER'];
				$hostreturn = strtolower(preg_replace('@^www\.@i', '', parse_url(RETURNURL, PHP_URL_HOST)));
			}

			// protect against xsite scripting attack in returnurl field
			if (($returnurl != RETURNURL) && ($host != $hostreturn)) 
			{
				$_SERVER['-- $host'] = $host;
				$_SERVER['-- $hostreturn'] = $hostreturn;
				$_SERVER['-- $returnurl'] = $returnurl;
				$_SERVER['-- RETURNURL'] = RETURNURL;
				secError("RETURNURL lead outside of the allowed domain");
			} else {
				// test all needed post data fields received
				if (empty($_POST['email'])) 
				{
					$msg = MSG_NOEMAIL;
					$returnurl = RETURNURLFAIL;
				} else {
					$messageinput = ''; 
					// build the email message content
					for ($i=0;$i<99;$i++) 
					{
						if (!empty($_POST["msg$i"]))
						{
							if (!empty($_POST["lbl$i"]))
								$messageinput .= $_POST["lbl$i"];
							$messageinput .= $_POST["msg$i"].NEWLINE;
						}
					}//for msg-i lbl-i
					if (!empty($_POST['msg']))     $messageinput .= $_POST['msg'].NEWLINE;
					if (!empty($_POST['message'])) $messageinput .= $_POST['message'].NEWLINE;

					if (MSG_NOMSG!='' && MSG_NOMSG!=false && empty($messageinput)) 
					{
						$msg = MSG_NOMSG;
						$returnurl = RETURNURLFAIL;
					} else {
						// set subject
						if (empty($_POST['subject'])) 
						{
							$subject = SUBJECT;
						} else {
							$subject = SUBJECT.' '.$_POST['subject'];
						}

						// set from address
						if (empty($_POST['email']))
						{
							$from=SENDTO;
						} else {
							$from=$_POST['email'];
						}
	
						// try to send the email
						if ((SENDTO == '') || (SENDTO == false)) 
						{
							$okmail = true;
						} else {
							$okmail = orimail($subject, s($messageinput), $from, SENDTO, SENDTO_CC, SENDTO_BCC);
						}
	
						// write to log file
						if ($okmail) 
						{
							$filename = SENT_FILE;
						} else {
							$filename = UNSENT_FILE;
						}
	
						if (!empty($filename)) {
							if (filesize($filename)<1) {
								file_put_contents($filename,LOGHEADER);
								@chmod($filename,0666);
							}
							$log = str_replace(array('[okmail]','[date]','[from]','[to]','[cc]','[bcc]','[subject]','[msg]'),array(1*$okmail,date('Y-m-d H:i:s'),s($_POST['email']),SENDTO,SENDTO_CC,SENDTO_BCC,$subject,s($messageinput)),LOGTMPL);
							if ($f = fopen($filename, "a")) 
							{
								$okfile = fwrite($f, $log) > 0;
								fclose($f);
							} else {
								$okfile = false;
							}
						}
	
						// set the correct message
						if ($okmail) 
						{
							$msg = MSG_SENT;
						} else {
							if ($okfile) {
								$msg = MSG_WRITTEN;
							} else {
								$msg = MSG_FAILED;
								$returnurl = RETURNURLFAIL;
							}
						} //okmail
					} //test post msg fields
				} // test no email given

				// output section //

				// quiet method
				if (METHOD == 'quiet') 
				{
					die(''); // show not feedback whatsoever
				}

				// ajax method
				if (METHOD == 'ajax') // just write down the message and quit
				{
					die($msg);
				}

				// set javascript redirection command
				if (empty($returnurl)) 
				{
					$js = "window.history.go(-1);";
					$href = "Click [Back] on your browser";
				} else {
					$js = "document.location.replace(\"$returnurl\");";
					$href = "Click to continue: <A href=\"$returnurl\">$referer</A>";
				}

				// alert method
				if (METHOD == 'alert') 
				{
					if (defined(CHARSET)) {
						header('Content-Type: text/html; charset='.CHARSET);
					} else {
						header('Content-Type: text/html;');
					}
					echo "<HTML><HEAD>";
					if (defined(CHARSET)) 
						echo "<META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=".CHARSET."\">";
					echo "</HEAD><BODY>";
					echo "
						<script type='text/javascript'>			
								alert(\"".$msg."\");
								$js
							</script>
							<noscript>
								$msg
								<br/>Click to continue: <A href=\"$returnurl\">$returnurl</A>
							</noscript>";
					echo "</BODY></HTML>";
					exit;
				} //method alert

				// text method
				if (METHOD == 'text') 
				{
					if (defined(CHARSET)) {
						header('Content-Type: text/html; charset='.CHARSET);
					} else {
						header('Content-Type: text/html;');
					}
					echo "<HTML><HEAD>";
					if (defined(CHARSET)) 
						echo "<META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=".CHARSET."\">";
					if (!empty($returnurl)) echo "<META http-equiv=\"refresh\" content=\"". (WAIT + 1).";url=$returnurl\" />";
					if (defined(CHARSET)) echo "<META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=".CHARSET."\">";
					echo "</HEAD><BODY>$msg";
					echo "
								<script type='text/javascript'>
									setTimeout(".WAIT."*1000,function() { $js });
								</script>
								<noscript>
									Click to continue: <A href=\"$returnurl\">$returnurl</A>
								</noscript>";
					echo "</BODY></HTML>";
					exit;
				} //method text

				secError("Unknown METHOD defined: ".METHOD);

			} //security test returnurl
		} //security referer like current domain or return url domain
	} //security referer exists
} //security test keyword

