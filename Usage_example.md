
```
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



```