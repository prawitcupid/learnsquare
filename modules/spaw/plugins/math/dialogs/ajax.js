//edit by Watee wichiennit 03/01/2008


// This script establishes and handles dynamic Ajax calls
// You must define:
/*
function processReqChange() {
    // only if req shows "loaded"
    if (req.readyState == 4) {
        // only if "OK"
        if (req.status == 200) {
            // ...processing statements go here...
        } else {
            alert("There was a problem retrieving the XML data:\n" +
                req.statusText);
        }
    }
}
*/

var isIE = false;

// global request and XML document objects
var req;

// retrieve XML document (reusable generic function);
// parameter is URL string (relative or complete) to
// an .xml file whose Content-Type is a valid XML
// type, such as text/xml; XML source must be from
// same domain as HTML file
function loadXMLDoc(url, process_fn) {
	// branch for native XMLHttpRequest object
	alert("aaaaa");
	alert(url);
	if (window.XMLHttpRequest) {
			req = new XMLHttpRequest();
			req.onreadystatechange = process_fn;
			req.open("GET", url, true);
			req.send(null);
	// branch for IE/Windows ActiveX version
	} else if (window.ActiveXObject) {
			isIE = true;
			req = new ActiveXObject("Microsoft.XMLHTTP");
			if (req) {
					req.onreadystatechange = process_fn;
					req.open("GET", url, true);
					req.send();
			}
	}
}

// This function is used to extract XML data from the returned AJAX string (in XML format)
function getElementTextNS(prefix, local, parentElem, index) {
    var result = "";
    if (prefix && isIE) {
        // IE/Windows way of handling namespaces
        result = parentElem.getElementsByTagName(prefix + ":" + local)[index];
    } else {
        result = parentElem.getElementsByTagName(local)[index];
    }
    if (result) {
        // get text, accounting for possible
        // whitespace (carriage return) text nodes 
        if (result.childNodes.length > 1) {
            return result.childNodes[1].nodeValue;
        } else {
            return result.firstChild.nodeValue;    		
        }
    } else {
        return "n/a";
    }
}