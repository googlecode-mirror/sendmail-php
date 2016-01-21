**bold**=required non empty

**`RANDOM`** - Please enter 5-20 random numbers. it will be used for log file names. This helps protect your log from being accessed by a random bot.

`SENDTO` - Send the form post msg to this email address. When empty NO EMAILS WILL BE SENT! Messages will be written to the log file anyway. To send only to CC/BCC addresses, use something like 'undisclosed@example.com'.

`SENDTO_CC` - When sending an email put this in the CC field.

`SENDTO_BCC` - When sending an email put this in the BCC field.

`SUBJECT` - When sending an email use this as the prefix of the subject line

`HTML_EMAIL` - Send html type emails ?

`CHARSET` - Use this charset when sending the email

**`MSG_SENT`** - Message to show to the user when everything was ok.

**`MSG_WRITTEN`** - Message to show to the user when email failed but message written to UNSENT\_FILE.

**`MSG_FAILED`** - Message to show to the user when both email and writing to text file had failed.

**`MSG_NOEMAIL`** - Message to show to the user when email field is empty

**`MSG_NOMSG`** - Message to show to the user when msg field is empty

**`MSG_ERROR`** - Message to show to the user when a security test is failed

`DELIMITER` - In Log files: delimiter between each message

`LOGTMPL` - In Log files: Template for inserting the messages entries. Possible fields are: `[date] [from] [to] [cc] [bcc] [subject]`

**`METHOD`** - When send mail finish or fail, how should I inform the user ?
> Possible values: `alert`,`ajax`,`text`,`quiet`.
> > `alert` = show javasript alert message, then javascript redirect back to returnurl.
> > `ajax`  = just write the clean message
> > `text`  = write down the message, and after `WAIT` seconds jump to returnurl (using javascript, fallback using meta-refresh)
> > `quiet` = write nothing...

**`PAUSE`** - Always pause for this amount of seconds when sending a message

`WAIT` - On 'text' METHOD wait this amount of seconds before redirecting to `RETURNURL`

`RETURNURL` - Redirect here after successful sending of the message

> Use form post value "returnurl" field to override this.
> returnurl on POST can only redirect to somewhere on the same domain.
> When both `POST[returnurl]` and this are not set, it goes back to the referer url.
> For security reasons DO NOT define `RETURNURL` as hardcoded `SERVER[HTTP_REFERER]`

`RETURNURLFAIL` - Redirect here after failing to send the message. When missing sendmail will try to return to previous page or referrer.

`KEYWORD_FIELD`,`KEYWORD_VALUE` - When set, sendmail will ignore any requests that doesn't have the post value `KEYWORD_FIELD` set to `KEYWORD_VALUE`. It is a very basic, minimal security measure.

`SENT_FILE` - Log file name for successfully sent messages. Optional.
`UNSENT_FILE` - Log file name for messages that failed to send. Optional.
`ERRORS_FILE` - Log file name for errors. Optional.
> Note that for security reasons any <script <embed <object <frame <iframe inside the message or errors file will be replaced by «script etc
> For a single log file just enter the same file name three times.
> For security reasons it is advised to add a set of numbers to the files names.