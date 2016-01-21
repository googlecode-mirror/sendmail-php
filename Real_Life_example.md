
```
<form id="contactMegi" action="sendmail.php" method="post" target="_top" onsubmit="return checkForm(this)">
<input name="subject" type="hidden" value="">
<input name="mysecret" type="hidden" value="open sesami">
<input name="returnurl" type="hidden" value="http://www.megiadam.com/">
<input name="lbl1" type="hidden" value="שמי: ">
<input name="lbl2" type="hidden" value="טלפון: ">
<input name="lbl3" type="hidden" value="עיר מגורים: ">
<input name="lbl4" type="hidden" value="">
<input name="lbl5" type="hidden" value="">
<input name="lbl6" type="hidden" value="">
<input name="lbl7" type="hidden" value="">
<input name="msg4a" type="hidden">
<input name="msg5a" type="hidden">
<input name="msg6a" type="hidden">
<p class="frmRow" style="width: 100%; text-align: center;">שליחת הודעה</p>
<p class="frmRow">שם:<br>
<input name="msg1" size="26" type="text"></p>
<p class="frmRow">הודעה: <textarea style="display: block; margin-bottom: 10px;" cols="56" rows="4" name="msg7"></textarea></p>
<p class="frmRow">כתובת האימייל שלך:<br>
<input name="email" size="26" type="text"></p>
<p class="frmRow">או מספר טלפון לחזרה:<br>
<input style="margin-right: 3px;" name="msg2" size="26" type="text"></p>
<p class="frmRow">עיר מגורים:<br>
<input style="margin-right: 3px;" name="msg3" size="26" type="text"></p>
<p class="frmRow">סמן/י במה הנך מעוניין/ת</p>
<p class="frmRow"><label><br>
<input name="msg4" value="מתעניין/ת בקורס" type="checkbox">מתעניין/ת בקורס?</label></p>
<p class="frmRow"><label><br>
<input name="msg5" value="מתעניין/ת בפגישת ייעוץ" type="checkbox">מתעניין/ת בפגישת ייעוץ?</label></p>
<p class="frmRow"><label><br>
<input name="msg6" value="מעוניין/ת להזמין את הספר" type="checkbox">מעוניין/ת להזמין את הספר "לומדים אסטרולוגיה"?</label></p>
<p class="frmRow" style="width: 100%; text-align: center;">
<input class="button" style="padding: 0 1px; margin-top: 10px;" type="submit" value="שלח/י הודעה"></p>
</form>
```