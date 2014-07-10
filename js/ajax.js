
var __be_dir="/be/";

function requestPage(url,ajax_params,obj,obj1) {
	var httpRequest;
	if (window.XMLHttpRequest) { // Mozilla, Safari, ...
            httpRequest = new XMLHttpRequest();
            if (httpRequest.overrideMimeType) {
                httpRequest.overrideMimeType('text/xml');
                // See note below about this line
            }
        } else if (window.ActiveXObject) { // IE
            try {
                httpRequest = new ActiveXObject("Msxml2.XMLHTTP");
            } catch (e) {
                try {
                    httpRequest = new ActiveXObject("Microsoft.XMLHTTP");
                } catch (e) {}
            }
        }

        if (!httpRequest) {
            alert('Cannot create an XMLHTTP instance');
            return false;
        }

        httpRequest.onreadystatechange = function() { showPage(httpRequest,obj,obj1); };
        url=__be_dir+url+'/index.php?r='+Math.random();
        httpRequest.open('POST', url, true);
        var parameters="ajax_params="+encodeURI(ajax_params);
        httpRequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        httpRequest.setRequestHeader("Content-length", parameters.length);
        httpRequest.setRequestHeader("Connection", "close");
        httpRequest.send(parameters);

   //     httpRequest.open('POST',url, true);
   //     httpRequest.send(ajax_params);
        
	
}

function showPage(httpRequest,obj,obj1) {
	if (httpRequest.readyState == 4) {
    	if (httpRequest.status == 200) {   
    		obj=document.getElementById(obj);
    		obj.innerHTML=httpRequest.responseText;
    		obj.style.display="block";
    		obj1=document.getElementById(obj1);
    		obj1.setAttribute("is_loaded","1");
    	}
    	else {
    		alert("error");
    	}
	}
    
}

function loadHint(id,url,width,height,title) {
	var httpRequest;
	if (window.XMLHttpRequest) { // Mozilla, Safari, ...
            httpRequest = new XMLHttpRequest();
            if (httpRequest.overrideMimeType) {
                httpRequest.overrideMimeType('text/xml');
                // See note below about this line
            }
        } else if (window.ActiveXObject) { // IE
            try {
                httpRequest = new ActiveXObject("Msxml2.XMLHTTP");
            } catch (e) {
                try {
                    httpRequest = new ActiveXObject("Microsoft.XMLHTTP");
                } catch (e) {}
            }
        }

        if (!httpRequest) {
            alert('Cannot create an XMLHTTP instance');
            return false;
        }

        httpRequest.onreadystatechange = function() { setHint(httpRequest,width,height,title); };
        url=url+'?r='+Math.random()+'&id='+id;
        httpRequest.open('GET',url, true);
        httpRequest.send(null);
        
	
}

function setHint(httpRequest,width,height,title) {
	
	if (httpRequest.readyState == 4) {
    	if (httpRequest.status == 200) {    	
    			var s=httpRequest.responseText.replace(/\n/g, "<br />") ;
    			if(s!="") {
					try {
						win.close();
					}
					catch(e){}
					if(!width||width=="undefined"||width==undefined) {
						width=200;
					}
					if(!height||height=="undefined"||height==undefined) {
						height=150;
					}
					if(!title||title=="undefined"||title==undefined) {
						title="Въпроси"
					}
	    			win = new Window({className: "darkX", title: title, width:width, height:height, destroyOnClose: true, recenterAuto:false});				
					win.getContent().update("<p style='padding:5px;'>"+s+"</p>");
				//	win.setLocation(150,680);
				//	win.show();
				win.showCenter();
	
    			}
    			else {
    				try {
						win.close();
					}catch(e){}
    			}
    		}
        else {
            alert('There was a problem with the request.');
        }
	} else {
	    // still not ready
	}
}