<?php
/*  sendmail.php script

License: GPLv3
http://tablefield.com
http://sendmail-php.googlecode.com

    Usage:

    1. Use POST form. See example below.
    2. Edit the lines with "define" commands.
    3. The keyword is used against direct access to sendmail.php by robots. Add it to the form as hidden input.
    4. Post parameters (* for mandatory):
        * email - 'from' email address
          subject - optionally set the subject of the email. It will be Added to the SUBJECT defined parameter below.
        * msg - the content of the email to be sent
          returnurl - the address to redirect after sending
      
          lbl1
          msg1
          lbl2
          msg2
          lbl3
          msg3
          ...lbl99 msg99 - Additional fields to be sent be email. Will be added to the email with \n\r between them.

    Form Example:
    <form name="frm" method="POST" action="sendmail.php">
        <input type=hidden name="pita"       value="humus">
        <input type=hidden name="lbl1"       value="(Sent from homepage)">
        
        <h1>Contact us</h1>
        Your email: <input name=email type=text size=40 />
        
        <br>
        Select issue: <select name=subject size=1>
            <OPTION value="Message from website: general">General</OPTION>
            <OPTION value="Message from website: booking">Booking</OPTION>
            <OPTION value="Message from website: bugs"   >Bug report</OPTION>
        </select><br>

        <br>
        Your message:<br>
        <textarea name=msg cols=80 rows=10></textarea>
        
        <br>
        <input type=hidden name=lbl2 value="My Name: " />
        Your Name: <input type=text name=msg2 size=60>
        
        <br>
        <input type=hidden name=lbl3 value="My Phone: " />
        Phone number: <input type=text name=msg3 size=60>
        
        <br>
        <input type=hidden name=lbl4 value="My Rating" />
        Rating: <select size=1 name=msg4>
                <option value="N/A"></option>
                <option value="1">Lowest</option>
                <option value="2">2 stars</option>
                <option value="3">3 stars</option>
                <option value="4">4 stars</option>
                <option value="5">5 stars!</option>
            </select>
        
        <br>
        <input type=hidden name=msg11 value="">
        <input type=checkbox name="" id="cb1" onclick="if (this.checked) {this.form.msg11.value='*PLEASE SUBSCRIBE ME*';} else {this.form.msg11.value='';}"><label for="cb1">Subscribe to our newsletter?</label>

        <input type=submit value="SEND" />

    </form>

*/


////////////////////////////////////////////////////////////////
// Adjust These Values Accoarding To Your Needs:              //
////////////////////////////////////////////////////////////////

// please enter random numbers of your choice. it will be used for log file names.
define('RANDOM','97867543');

// when set, send the form post msg to this email address
define('SENDTO','info@example.com');          

// when set, put this email address in cc
define('SENDTO_CC','copy@example.com');   

// when set, put this email address in bcc
define('SENDTO_BCC','');  

// when sending an email use this as the prefix of the subject line
// The following is used for gmail conversations, for easy tracking of every message
$aeiou=array('a','e','i','o','u');
$code = chr(mt_rand(ord('a'),ord('z'))).$aeiou[mt_rand(0,4)].chr(mt_rand(ord('a'),ord('z'))).$aeiou[mt_rand(0,4)].chr(mt_rand(ord('a'),ord('z')));
define('SUBJECT',"Message from site ($code) ");

// send html type emails? default false
define('HTML_EMAIL',false);

// charset to use when sending the email
define('CHARSET','UTF-8');


// message to show when everything was ok.
define('MSG_SENT'    ,'Thank you! Your message was sent. We will reply as soon as possible.');

// message to show when email failed but message written to UNSENT_FILE. 
define('MSG_WRITTEN' ,'Thank you! Your message was written.');

// message to show when both email and writing to text file had failed. 
define('MSG_FAILED'  ,'Error - Your message was not sent! Please contact us at '.SENDTO);

// message to show when email field is empty 
define('MSG_NOEMAIL' ,'Please write down your email address for reply');

// message to show when msg field is empty
define('MSG_NOMSG'   ,'Please write down your message');

// message to show when a security test is failed
define('MSG_ERROR'   ,'error 71'); 

// delimiter between each message in the log.
define('DELIMITER'   ,"\n<hr>\n"); 

// template for inserting log file entries.
// It can be HTML or XML
define('LOGTMPL'     ,"<div class=sendmailEntry><span class=cDate><h4 class=hDate>Date:</h4><i class=iDate>[date]</i></span><span class=cFrom><h4 class=hFrom>From:</h4><i class=iFrom>[from]</i></span><span class=cTo><h4 class=hTo>To:</h4><i class=iTo>[to]</i></span><span class=cCC><h4 class=hCC>cc: </h4><i class=iCC>[cc]</i></span><span class=cBCC><h4 class=hBCC>bcc: </h4><i class=iBCC>[bcc]</i></span><span class=cSubject><h4 class=hSubject>Subject:</h4><i class=iSubject>[subject]</i></span><span class=cMsg><h4 class=hMsg>Message:</h4><i class=iMsg>[msg]</i></span></div>");
// $log = DELIMITER.str_replace(array('[date]','[from]','[to]','[cc]','[bcc]','[subject]','[msg]'),array(date('Y-m-d H:i:s'),s($_POST['email']),SENDTO,SENDTO_CC,SENDTO_BCC,$subject,s($message)),LOGTMPL);

define('METHOD','alert'); // options: 'alert','ajax','text','quiet'
                          //   alert = show javasript alert message, then javascript redirect back to returnurl.
                          //   ajax  = just write the message
                          //   text  = write down the message, and after WAIT seconds jump to returnurl (using javascript, fallback using META-refresh)
                          //   quiet = write nothing, do nothing

// always pause for this amount of seconds when sending a message
define('PAUSE',2);

// on 'text' METHOD wait this amount of seconds before redirecting
define('WAIT',5);

// Redirect to this url by default.
define('RETURNURL','http://www.example.com/messagesent');
  // Use form "returnurl" field to override this.
  // Returnurl from POST can only redirect to somewhere on the same domain.
  // When both POST[returnurl] and this are not set, it goes back to the referer url.
  // For security reasons DO NOT define RETURNURL as hardcoded SERVER[HTTP_REFERER]

define('RETURNURLFAIL','');
// When message failed sendmsg will try to return to previous page or referrer
// Unless you write some full url in here

define('KEYWORD_FIELD','foo');    // When set, a security test will check that the form POST data includes a field name KEYWORD_FIELD,
define('KEYWORD_VALUE','bar');   // and it's value equals KEYWORD_VALUE

define('SENT_FILE','sent_'.RANDOM.'.htm');      // when set, backup all sent emails to this file.
define('UNSENT_FILE','notsent_'.RANDOM.'.htm'); // when set, save all failed emails to this file.
define('ERRORS_FILE','errors_'.RANDOM.'.htm');  // when set, save all errors and security related messages to this file.
// for security reasons any <script <embed <object <frame <iframe inside the message or errors file will be replaced by «
