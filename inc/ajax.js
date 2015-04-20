function getRequest(){
	var ajaxRequest;  // The variable that makes Ajax possible!
	
	try{
		// Opera 8.0+, Firefox, Safari
		ajaxRequest = new XMLHttpRequest();
		//ajaxRequest.onreadystatechange = rscFunction();
		return ajaxRequest;
	} catch (e){
		// Internet Explorer Browsers
		try{
			ajaxRequest = new ActiveXObject("Msxml2.XMLHTTP");
			//ajaxRequest.onreadystatechange = rscFunction();
			return ajaxRequest;
		} catch (e) {
			try{
				ajaxRequest = new ActiveXObject("Microsoft.XMLHTTP");
				//ajaxRequest.onreadystatechange = rscFunction();
				return ajaxRequest;
			} catch (e){
				// Something went wrong
				alert("Your browser broke!");
				return false;
			}
		}
	}
}

function getResponse(){
	if (ajaxRequest.readyState == 4) {
		document.getElementById(elemId).innerHTML = ajaxRequest.responseText;
	}
}
