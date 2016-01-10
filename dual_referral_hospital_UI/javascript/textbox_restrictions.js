var digitsOnly = /[1234567890]/g;
var integerOnly = /[0-9\.]/g;
var alphaOnly = /[A-Za-z]/g;
var usernameOnly = /[0-9A-Za-z\._-]/g;

function restrictInput(myfield, e, restrictionType){
   if (!e) var e = window.event
  if (e.keyCode) code = e.keyCode;
  else if (e.which) code = e.which;
  var character = String.fromCharCode(code);
  // if they pressed esc... remove focus from field...
  if (code==27) { this.blur(); return false; }
  // ignore if they are press other keys
  // strange because code: 39 is the down key AND ' key...
  // and DEL also equals .

  if (!e.ctrlKey && code!=36 && code!=37 && code!=38 && (code!=39 || (code==39 && character=="'")) && code!=40) {
    if (character.match(restrictionType)) {
      return true;
    } else {
      if(code==8||code==9){
        return true;
      }
      return false;
    }
  }
  return false;
}

function ValidateAlpha(evt)
{
    
			var charCode = (evt.which) ? evt.which : window.Event.keyCode; 
            //alert(charCode);
			if (charCode <= 13 || charCode==32) 
            { 
			  return true; 
            } 
            else 
            { 
			
			if(charCode<97||charCode>112){
			
                var keyChar = String.fromCharCode(charCode); 
                var re = /[a-zA-Z]/ ;
				
                return re.test(keyChar); 
            
			}else{
				return false;
			}
			}
}

function validateEmail(emailField){
        var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;

        if (reg.test(emailField.value) == false) 
        {
            alert('Invalid Email Address');
			document.getElementById("email").value = "";
			document.getElementById("refer_email").value = "";
            return false;
        }

        return true;

}