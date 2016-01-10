function Register()
{
  //alert("inside doctor registration");
  
  var doctor_mobile=document.getElementById('mobile').value;
  //alert(doctor_mobile);
  if(doctor_mobile=="" || doctor_mobile.length==0 || doctor_mobile.length<10)
  {
    alert("Please enter a valid mobile number");
	mobile.focus();
	return false;
  }
  
  var doctor_title=document.getElementById('doc_title').value;
  if(doctor_title=='' || doctor_title=='Select'){
	  alert("Please enter the doctor title");
  }
  
  var doctor_name=document.getElementById('name').value;
  if(doctor_name=="" && doctor_name.length<4)
  {
    alert("Plese enter the doctor name");
	name.focus();
	return false;
  }
  
  var doctor_dob=document.getElementById('dob').value;
  //alert(doctor_dob);
  //alert(doctor_dob.indexOf("dd"));
  //alert(doctor_dob.indexOf("mm"));
  //alert(doctor_dob.indexOf("yyyy"));
  if(doctor_dob=="")
  {
    alert("Please enter valid date of birth");
	dob.focus();
	return false;
  }
  var doctor_email=document.getElementById('email').value;
  if(doctor_email==""){
   alert("Please enter the email");
   email.focus();
   return false;
  }
  /*var doctor_specialization=document.getElementById('Specialization').value;
  if(doctor_specialization==""){
    alert("Please enter doctor Specialization");
	Specialization.focus();
	return false;
  }*/
  var doctor_qualification=document.getElementById('qualification').value;
  if(doctor_qualification==""){
    alert("Please enter doctor qualification");
	qualification.focus();
	return false;
  }
  
  var doctor_spec=document.getElementById('specialization').value;
  if (doctor_spec=="" || doctor_spec=="Select"){
	    alert("Please enter doctor specialization and other if you enter other specialization");
	    specialization.focus();
		return false;
	  }
  if(doctor_spec=="OTHER"){
	  var spec_other=document.getElementById('other_spec').value;
	  if(spec_other==""){
		  alert("Please enter doctor specialization and other if you enter other specialization");
		  specialization.focus();
		  return false;
	  }
	  else{
		  doctor_spec=spec_other;
	  }
  }
  
 
  
  
  var doctor_country=document.getElementById('country').value;
  if(doctor_country==""){
    alert("Please enter doctor's country");
	country.focus();
	return false;
  }
  
  var doctor_state=document.getElementById('state').value;
  if(doctor_state==""){
    alert("Please enter doctor's state");
	state.focus();
	return false;
  }
  
  var doctor_city=document.getElementById('city').value;
  if(doctor_city==""){
    alert("Please enter doctor's city");
	city.focus();
	return false;
  }
  
  var doctor_address=document.getElementById('address').value;
  if(doctor_address==""){
    alert("Please enter doctor's address");
	address.focus();
	return false;
  }
  
  var doctor_exp=document.getElementById('yexp').value;
  if(doctor_exp==""){
    alert("Please enter doctor experience");
	yexp.focus();
	return false;
  }
  
  var hos_name=document.getElementById('hospital_name').value;
  if(hos_name==""){
   alert("Please enter the hospital name");
   return false;
  }
  
  var license_number=document.getElementById('license_number').value;
  if(license_number==""){
   alert("Please enter the license number");
   return false;
  }
  
  var my_type=document.getElementById('my_type').value;
  if(my_type==""){
   alert("Please enter the type value");
   return false;
  }
  
  var country_code=document.getElementById('country_code').value;
  if(country_code==""){
   alert("Please enter the country code");
   return false;
  }
  
  var doctor_pass=document.getElementById('pass1').value;
  var doctor_conf_pass=document.getElementById('pass2').value;
  if(doctor_pass=="")
  {
     alert("Please enter your password");
	 pass1.focus();
	 return false;
  }
  if(doctor_conf_pass=="")
  {
     alert("Please enter your confrm password field");
	 pass2.focus();
	 return false;
  }
  if(doctor_pass!=doctor_conf_pass)
  {
     alert("Password and confirm password field does not match");
	 return false;
  }
  
  var hos_id=document.getElementById('hospital_id').value;
  //var doctor_name=doctor_name;
  
  $.ajax({
    method: "POST",
    url: "doc_registration_UI.php",
    data: { "Doctor_mobile_number": doctor_mobile, "Doctor_name": doctor_name, "Doctor_dob": doctor_dob, "Doctor_email": doctor_email, "Doctor_specialization": doctor_spec, "Doctor_qualification": doctor_qualification,"Doctor_HospitalName":hos_name, "Doctor_password": doctor_pass, "Doctor_photograph": "", "Doctor_yxp": doctor_exp,"Doctor_Country":doctor_country,"Doctor_State":doctor_state,"Doctor_City":doctor_city,"license_number":license_number,"type":my_type,"country_code":country_code,"Doctor_Address":doctor_address,"Hospital_id":hos_id,"doctor_title":doctor_title},
	cache: false,
	dataType: 'JSON',
	success: function (msg){
	  //alert(msg);
	  if(msg['status']=='Success'){
	     alert("Hospital doctor registered successfully");
		 window.location.href="referralio_home.php";
	  }else if(msg['status']=='Failure'){
	     alert("Doctor having this mobile number is already registered with this hospital");
		 window.location.href="referralio_home.php";
	  }else{
		 //alert(msg['msg']);
	     alert("Hospital doctor could not be registered.Please try again Later");
		 return false;
	  }
	}
  });
  
}

function Refer(){
  
  //alert("inside refer");
  
  var refer_doctor_mobile=document.getElementById('refer_mobile').value;
  if(refer_doctor_mobile=="" || refer_doctor_mobile.length==0 || refer_doctor_mobile.length<10)
  {
    alert("Please enter a valid mobile number");
	refer_mobile.focus();
	return false;
  }
  
  var doctor_title=document.getElementById('doc_title1').value;
  if(doctor_title=='' || doctor_title=='Select'){
	  alert("Please enter the doctor title");
  }
  
  var refer_doctor_name=document.getElementById('refer_name').value;
  if(refer_doctor_name=="" && refer_doctor_name.length<4)
  {
    alert("Plese enter the doctor name");
	refer_name.focus();
	return false;
  }
   
  var refer_doctor_email=document.getElementById('refer_email').value;
  if(refer_doctor_email==""){
   /*alert("Please enter the email");
   refer_email.focus();
   return false;*/
	  refer_doctor_email="";
  }
  //alert("reached");
  
  var hos_id=document.getElementById('hospital_id').value;
  
  $.ajax({
    method: "POST",
    url: "refering_in_hos.php",
    data: { "mobile": refer_doctor_mobile, "name": refer_doctor_name, "email": refer_doctor_email,"hospital_id":hos_id,"doctor_title":doctor_title},
	cache: false,
	dataType: 'JSON',
	success: function (msg){
      //alert(msg);
	  if(msg['status']=='Success'){
	     alert("Referring doctor registered successfully");
		 window.location.href="referralio_home.php";
	  }else if(msg['status']=='Failed'){
	     alert("This doctor is already added as a referring doctor");
		 window.location.href="referralio_home.php";
	  }else{
	     alert("Referring doctor could not be registered.Please try again");
		 return false;
	  }
	}
  });  
   
}


function sendB2B_referin(){
 
 var msg=document.getElementById('msg_refer_in').value;
 //alert(msg);
  if(msg=="" || msg.length==0)
  {
    alert("Please enter a message to send");
	msg_refer_in.focus();
	return false;
  }
  
  var hos_id=document.getElementById('hospital_id').value;
  var doc_type="refer-in";
  
  $.ajax({
    method: "POST",
    url: "send_mobile_msg.php",
    data: { "msg": msg, "hospital_id": hos_id, "doctor_type": doc_type},
	cache: false,
	dataType: 'JSON',
	success: function (msg){
	  //alert(msg);
	  //console.log(msg);
	  if(msg['status']=='Success'){
	     alert(msg['msg']);
		 window.location.href="referralio_home.php";
	  }else{
	     alert("Message could not be sent");
		 return false;
	  }
	}
  });
 
}

function sendB2B_referout(){

  var msg=document.getElementById('msg_refer_out').value;
  //alert(msg);
  if(msg=="" || msg.length==0)
  {
    alert("Please enter a message to send");
	msg_refer_in.focus();
	return false;
  }
  
  var hos_id=document.getElementById('hospital_id').value;
  //alert("hospital _id:"+hos_id);
  var doc_type="refer-out";
  
  $.ajax({
    method: "POST",
    url: "send_mobile_msg.php",
    data: { "msg": msg, "hospital_id": hos_id, "doctor_type": doc_type},
	cache: false,
	dataType: 'JSON',
	success: function (msg){
	  //alert(msg);
	  if(msg['status']=='Success'){
	     alert(msg['msg']);
		 window.location.href="referralio_home.php";
	  }else{
	     alert("Message could not be sent");
		 return false;
	  }
	}
  });
}


function sendB2B(){

  var msg=document.getElementById('spec_msg').value;
 //alert(msg);
  if(msg=="" || msg.length==0)
  {
    alert("Please enter a message to send");
	msg_refer_in.focus();
	return false;
  }
  
  var doc_id=document.getElementById('doc_identifier').value;
  if(doc_id=="" || doc_id=="Selected"){
   alert("Select the doctor");
   return false;
  }
  var hos_id=document.getElementById('hospital_id').value;
  //alert(hos_id);dataType: 'JSON',
  
  
  $.ajax({
    method: "POST",
    url: "send_broadcast_message.php",
    data: { "msg": msg, "hospital_id": hos_id, "doc_id": doc_id},
	cache: false,
	success: function (msg){
	  //alert("Msg is ="+msg);
	  /*if(msg['status']=='Success'){
	     alert("Broadcast push message sent successfully");
		 window.location.href="referralio_home.php";
	  }else{
	     alert("Message could not be sent");
		 return false;
	  }*/
	  alert("Broadcast push message sent successfully");
      window.location.href="referralio_home.php";
	}
  });
}


function delete_referring_doc(id){
	
	var doc_id=id;
	//alert(doc_id);
	var hos_id=document.getElementById('hospital_id').value;
	
	$.ajax({
    method: "POST",
    url: "delete_referring_docs.php",
    data: {"hospital_id": hos_id,"doc_id": doc_id},
	cache: false,
	dataType: 'JSON',
	success: function (msg){
	  //alert(msg);
	  if(msg['status']=='Success'){
	     alert(msg['msg']);
		 window.location.href="referralio_home.php";
	  }else{
	     alert("Message could not be sent");
		 return false;
	  }
	}
  });
	
}

function delete_hospital_doc(id){
	
	var doc_id=id;
	//alert(doc_id);
	var hos_id=document.getElementById('hospital_id').value;
	
	$.ajax({
    method: "POST",
    url: "del_hospital_docs.php",
    data: {"hospital_id": hos_id,"doc_id": doc_id},
	cache: false,
	dataType: 'JSON',
	success: function (msg){
	  //alert(msg);
	  if(msg['status']=='Success'){
	     alert(msg['msg']);
		 window.location.href="referralio_home.php";
	  }else{
	     alert("Message could not be sent");
		 return false;
	  }
	}
  });
	
}

function delete_hospital_user_all(id){
	
	var doc_id=id;
	//alert(doc_id);
	var hos_id=document.getElementById('hospital_id').value;
	
	var x=confirm("Are you sure you want to delete this user");
	if(x==true){
		
		$.ajax({
		    method: "POST",
		    url: "del_hos_user_all.php",
		    data: {"hospital_id": hos_id,"doc_id": doc_id},
			cache: false,
			dataType: 'JSON',
			success: function (msg){
			  //alert(msg);
			  if(msg['status']=='Success'){
			     alert(msg['msg']);
				 window.location.href="referralio_home.php";
			  }else{
			     alert("Message could not be sent");
				 return false;
			  }
			}
		  });
		
	}else{
		
		return false;
		
	}
	
	
	
}

function sendB2B_personal(){

  var msg=document.getElementById('spec_msg1').value;
 //alert(msg);
  if(msg=="" || msg.length==0)
  {
    alert("Please enter a message to send");
	msg_refer_in.focus();
	return false;
  }
  
  var doc_id=document.getElementById('doc_identifier1').value;
  if(doc_id=="" || doc_id=="Selected"){
   alert("Select the doctor");
   return false;
  }
  //var hos_id=document.getElementById('hospital_id').value;
  //alert(hos_id);
  
  
  $.ajax({
    method: "POST",
    url: "send_personal_broadcast.php",
    data: {"msg": msg,"doc_id": doc_id},
	cache: false,
	dataType: 'JSON',
	success: function (msg){
	  //alert(msg);
	  if(msg['status']=='Success'){
	     alert(msg['msg']);
		 window.location.href="admin_add.php";
	  }else{
	     alert("Message could not be sent");
		 return false;
	  }
	}
  });
}

function add_admin_transaction(){
	//alert("123");
	var pat_name=document.getElementById('tran_name').value;
	if(pat_name==""){
		alert("Patient name cannot be empty");
		return false;
	}
	var pat_mobile=document.getElementById('tran_mobile').value;
	if(pat_mobile=="" || pat_mobile.length==0 || pat_mobile.length<10){
		alert("Please provide proper patient mobile number");
		return false;
	}
	var pat_age=document.getElementById('tran_age').value;
	if(pat_age==""){
		alert("Patient age cannot be empty");
		return false;
	}
	var pat_gender=document.getElementById('tran_gender').value;
	if(pat_gender=="" || pat_gender=="Select"){
		alert("Patient gender cannot be empty");
		return false;
	}
	var pat_loc=document.getElementById('tran_location').value;
	if(pat_loc==""){
		alert("Patient location cannot be empty");
		return false;
	}
	//alert("chk before");
	var hos_doc_Id=document.getElementById('tran_hos_doc_id').value;
	//alert("hos_doc_Id"+hos_doc_Id);
	if(hos_doc_Id=="Select"){
		alert("Please select hospital doctor");
		return false;
	}
	var ref_doc_id=document.getElementById('tran_ref_doc_id').value;
	//alert("ref_doc_id"+ref_doc_id);
	if(ref_doc_id=="Select"){
		alert("Please select referring doctor");
		return false;
	}
	var pat_notes=document.getElementById('tran_notes').value;
	if(pat_notes==""){
		alert("Notes cannot be empty");
		return false;
	}
	
	var hos_id=document.getElementById('hospital_id').value; 
	
	$.ajax({
	    method: "POST",
	    url: "add_trans_admin.php",
	    data: {"pat_name": pat_name,"pat_mobile": pat_mobile,"pat_age": pat_age,"pat_gender": pat_gender,"pat_loc": pat_loc,"hos_doc_Id": hos_doc_Id,"ref_doc_id": ref_doc_id,"pat_notes": pat_notes,"hos_id":hos_id},
		cache: false,
		dataType: 'JSON',
		success: function (msg){
		  //alert(msg);
		  if(msg['status']=='Success'){
		     alert(msg['msg']);
			 window.location.href="referralio_home.php";
		  }else{
		     alert("Transaction could not be added");
			 return false;
		  }
		}
	  });
	
	
}

function change_secondary(sel,id){
	//alert("inside status");
	//alert(id);
	var status=  sel.value;
	//alert(status);
	if(status == "Select"){
		alert("Please select a status for updating it");
		return false;
	}
	var confirmupdate=confirm("are you sure you want to change the secondary status of this patient?");
	if(confirmupdate == true){
		
		$.ajax({
		    method: "POST",
		    url: "update_secondary_status.php",
		    data: {"pat_id": id,"status": status},
			cache: false,
			dataType: 'JSON',
			success: function (msg){
			  //alert(msg);
			  if(msg['status']=='Success'){
			     alert(msg['msg']);
			     if(status == 'Discharged'){
			    	 document.getElementById('uploadp').style.display='block';
			     }
				 window.location.href="referralio_home.php";
			  }else{
			     alert("Secondary status updated for the doctor");
				 return false;
			  }
			}
		  });
		
	}else{
		return false;
	}
}


function add_referring_2_hospital_user(){
	
	var hospital_id=document.getElementById('hospital_id').value;
	var stub_id=document.getElementById('stub_id').value;
	var refer_select=document.getElementById('refer_select').value;
	alert("Development in progress...");
	return false;
}


function add_hospital_user(){
	//alert("inside hospital_IUser");
	
	var user_mobile=document.getElementById('user_mobile').value;
	  //alert(user_mobile);
	  if(user_mobile=="" || user_mobile.length==0 || user_mobile.length<10)
	  {
	    alert("Please enter a valid mobile number");
		user_mobile.focus();
		return false;
	  }
	  
	  var doctor_title=document.getElementById('doc_title2').value;
	  if(doctor_title=='' || doctor_title=='Select'){
		  alert("Please enter the user title");
	  }
	  
	  var user_name=document.getElementById('user_name').value;
	  if(user_name=="" && user_name.length<4)
	  {
	    alert("Plese enter the user name");
		user_name.focus();
		return false;
	  }
	  
	  var user_email=document.getElementById('user_email').value;
	  if(user_email==""){
	   alert("Please enter the email");
	   user_email.focus();
	   return false;
	  }
	  
	  var user_pass1=document.getElementById('user_pass1').value;
	  var user_pass2=document.getElementById('user_pass2').value;
	  if(user_pass1=="")
	  {
	     alert("Please enter your password");
	     user_pass1.focus();
		 return false;
	  }
	  if(user_pass2=="")
	  {
	     alert("Please enter your confrm password field");
	     user_pass2.focus();
		 return false;
	  }
	  if(user_pass1!=user_pass2)
	  {
	     alert("Password and confirm password field does not match");
		 return false;
	  }
	
	  var hos_id=document.getElementById('hospital_id').value;
	  
	  $.ajax({
		    method: "POST",
		    url: "hospital_user_registration_UI.php",
		    data: { "user_mobile": user_mobile, "user_name": user_name,"user_email": user_email,"user_password": user_pass1,"hospital_id": hos_id,"doctor_title":doctor_title},
			cache: false,
			dataType: 'JSON',
			success: function (msg){
			  //alert(msg);
			  if(msg['status']=='Success'){
			     alert("Hospital User registered successfully");
				 window.location.href="referralio_home.php";
			  }else if(msg['status']=='Failure'){
			     alert(msg['msg']);
				 window.location.href="referralio_home.php";
			  }else{
			     alert(msg['msg']);
				 return false;
			  }
			}
		  });
	
}


function add_referring_2_hospital_user(hospital_user_id,hospital_id,referring_doc_id){
	
	var hospital_user_id=hospital_user_id;
	var hospital_id=hospital_id;
	var referring_doc_id=referring_doc_id;
	//alert(hospital_user_id);
	//alert(hospital_id);
	//alert(referring_doc_id.value);
	
	if(referring_doc_id.value=="Select")
	{
		alert("Please select the referring doctor");
		return false;
	}
	
	//alert(refer_select.length);
	//alert("Development in progress...");
	
	var confirmupdate=confirm("are you sure you want to map this referring doctor to this hospital user?");
	if(confirmupdate == true){
		
		$.ajax({
		    method: "POST",
		    url: "updateHospitalUserMapping.php",
		    data: {"hospital_user_id": hospital_user_id,"hospital_id": hospital_id,"referring_doc_id":referring_doc_id.value},
			cache: false,
			dataType: 'JSON',
			success: function (msg){
			  //alert(msg);
			  if(msg['status']=='Success'){
			     alert(msg['msg']);
				 window.location.href="referralio_home.php";
			  }else{
			     alert("Could not map the user.Please try again later");
				 return false;
			  }
			}
		  });
		
	}else{
		return false;
	}
	
	return false;
}


function delete_referring_2_hospital_user(hospital_user_id,hospital_id,referring_doc_id){
	
	var hospital_user_id=hospital_user_id;
	var hospital_id=hospital_id;
	var referring_doc_id=referring_doc_id;
	//alert(hospital_user_id);
	//alert(hospital_id);
	//alert(referring_doc_id.value);
	
	if(referring_doc_id.value=="Select")
	{
		alert("Please select the referring doctor");
		return false;
	}
	
	//alert(refer_select.length);
	//alert("Development in progress...");
	//return false;
	
	var confirmupdate=confirm("are you sure you want to delete mapping of this referring doctor to this hospital user?");
	if(confirmupdate == true){
		
		$.ajax({
		    method: "POST",
		    url: "deleteHospitalUserMapping.php",
		    data: {"hospital_user_id": hospital_user_id,"hospital_id": hospital_id,"referring_doc_id":referring_doc_id.value},
			cache: false,
			dataType: 'JSON',
			success: function (msg){
			  //alert(msg);
			  if(msg['status']=='Success'){
			     alert(msg['msg']);
				 window.location.href="referralio_home.php";
			  }else{
			     alert("Could not map the user.Please try again later");
				 return false;
			  }
			}
		  });
		
	}else{
		return false;
	}
	
	return false;
}



function sendTransactionMessaging(patientid){
	
	//alert("Development in progress");
	//return false;
	
	var hosname=document.getElementById('hosname').value;
	//alert(hosname);
	
	var message=document.getElementById('newTransactionMsg_'+patientid).value;
	//alert("message is=====>"+patientid);
	
	if(message==""){
		alert("Please enter a message");
		return false;
	}
	message=hosname+": "+message;
	//alert(message);
	//return false;
	$.ajax({
	    method: "POST",
	    url: "updateTransactionMessageInfo.php",
	    data: {"patientid": patientid,"message": message},
		cache: false,
		dataType: 'JSON',
		success: function (msg){
		  //alert(msg);
		  if(msg['status']=='Success'){
		     alert(msg['msg']);
			 window.location.href="referralio_home.php";
		  }else{
		     alert("Could not map the user.Please try again later");
			 return false;
		  }
		}
	  });
	
	
}

function add_help(){
	//alert("inside addhelp");
	var name=document.getElementById('name_help').value;
	if(name == ""){
	    alert("Please enter name");
	    return false;
	}
	var mob_number=document.getElementById('mobile_help').value;
	if( mob_number == ""){
	    alert("Please enter mobile number");
	    return false;
	}
	var extra_notes=document.getElementById('extra_notes').value;
	/*if( extra_notes == ""){
	    alert("Please enter extra_notes");
	    return false;
	}*/
	var hospital_id=document.getElementById('hospital_id').value;
	var action=document.getElementById('action').value;
	//alert(name);
	//alert(mob_number);
	//alert(action);
	//alert(hospital_id);
	//return false;
	$.ajax({
	    method: "POST",
	    url: "modify_add_help.php",
	    data: {"name": name,"mob_number": mob_number,"extra_notes":extra_notes,"action": action,"hospital_id": hospital_id},
		cache: false,
		dataType: 'JSON',
		success: function (msg){
		  //alert(msg);
		  if(msg['status']=='Success'){
		     alert(msg['msg']);
			 window.location.href="referralio_home.php";
		  }else{
			 alert(msg['msg']);
			 return false;
		  }
		}
	  });
	
	
	return false;
}


function add_mng(){
	//alert("inside addhelp");
	var name=document.getElementById('mng_name_help').value;
	if(name == ""){
	    alert("Please enter name");
	    return false;
	}
	var mob_number=document.getElementById('mng_mobile_help').value;
	if( mob_number == ""){
	    alert("Please enter mobile number");
	    return false;
	}
	var pass=document.getElementById('mng_pass_help').value;
	if( pass == ""){
	    alert("Please enter extra_notes");
	    return false;
	}
	var hospital_id=document.getElementById('hospital_id').value;
	//var action=document.getElementById('action').value;
	//alert(name);
	//alert(mob_number);
	//alert(pass);
	//alert(action);
	//alert(hospital_id);
	//alert('Development in Progress');
	//return false;
	$.ajax({
	    method: "POST",
	    url: "add_management.php",
	    data: {"name": name,"mob_number": mob_number,"pass":pass,"hospital_id": hospital_id},
		cache: false,
		dataType: 'JSON',
		success: function (msg){
		  //alert(msg);
		  if(msg['status']=='Success'){
		     alert(msg['msg']);
			 window.location.href="referralio_home.php";
		  }else{
			 alert(msg['msg']);
			 return false;
		  }
		}
	  });
	
	
	return false;
}

function UpdatePatient(){
	//alert("Development in progress");
	var patientname=document.getElementById('patient_name').value;
	var patientage=document.getElementById('patient_age').value;
	var patientgender=document.getElementById('patient_gender').value;
	var patientlocation=document.getElementById('patient_loc').value;
	var patientmobile=document.getElementById('mobile').value;
	var patientnotes=document.getElementById('patient_notes').value;
	var patientid=document.getElementById('patient_id').value;
	if(patientname == '' ||  patientage== '' || patientgender== '' || patientlocation == '' || patientmobile == '' || patientid == '')
	{
		alert("Please enter the values that are required");
		return false;
	}
	//alert(patientname);
	//alert(patientage);
	//alert(patientgender);
	//alert(patientlocation);
	//alert(patientmobile);
	//alert("patinetid==>"+patientid);
	//return false;
	$.ajax({
	    method: "POST",
	    url: "updatepatient.php",
	    data: {"name": patientname,"age": patientage,"patientgender": patientgender,"patientlocation": patientlocation,"patientmobile": patientmobile,"patientnotes":patientnotes,"pat_id": patientid},
		cache: false,
		dataType: 'JSON',
		success: function (msg){
		  //alert(msg);
		  if(msg['status']=='Success'){
		     alert(msg['msg']);
			 window.location.href="referralio_home.php";
		  }else{
			 alert(msg['msg']);
			 return false;
		  }
		}
	  });
	
}

function modal_change_pass(){
	//var user_pass1=document.getElementsByName('user_pass1')[0].value;//('user_pass1').value;
	var user_pass1=$('#user_pass1',$('#changepassword')).val();
	//alert(user_pass1);
	var user_pass2=$('#user_pass2',$('#changepassword')).val();
	//var user_pass2=document.getElementsByName('user_pass2');//.value;
	//alert(user_pass2);
	var user_pass3=document.getElementById('user_pass3').value;
	//alert(user_pass3);
	var hospital_id=document.getElementById('hospital_id').value;
	//alert(hospital_id);
	
	if(user_pass1=="" || user_pass2=="" || user_pass3==""){
		alert("Please enter all fields");
		return false;
	}
	if(user_pass2 != user_pass3){
		alert("New password and confirm password does not match.Please try again");
		return false;
	}
	
	$.ajax({
	    method: "POST",
	    url: "updatehospitaladminpass.php",
	    data: {"oldpass": user_pass1,"newpass":user_pass2,"hos_id": hospital_id},
		cache: false,
		dataType: 'JSON',
		success: function (msg){
		  //alert(msg);
		  if(msg['status']=='Success'){
		     alert(msg['msg']);
			 window.location.href="referralio_home.php";
		  }else{
			 alert(msg['msg']);
			 return false;
		  }
		}
	  });

}


function Update_Doctor(){
	alert('test');
	var name=document.getElementById('doc_name').value;
	if(name==''){
		alert('doctor name cannot be empty');
		return false;
	}
	var email=document.getElementById('doc_email').value;
	if(email==''){
		alert('doctor email cannot be empty');
		return false;
	}
	
	var mobile=document.getElementById('doctor_mobile_number').value;
	if(mobile==''){
		alert('mobile number empty.Please contact administrator');
		return false;
	}
	
	alert('after data');
	$.ajax({
	    method: "POST",
	    url: "updaterefdoc.php",
	    data: {"name": name,"email":email,"mobile": mobile},
		cache: false,
		dataType: 'JSON',
		success: function (msg){
		  //alert(msg);
		  if(msg['status']=='Success'){
		     alert(msg['msg']);
			 window.location.href="referralio_home.php";
		  }else{
			 alert(msg['msg']);
			 return false;
		  }
		}
	  });
	
	
}
