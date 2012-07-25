var g_bShowApiErrors = true; 
var g_strAPINotFound = "Management system interface not found."; 
var g_strAPITooDeep = "Cannot find API - too deeply nested."; 
var g_strAPIInitFailed = "Found API but LMSInitialize failed."; 
var g_strAPISetError = "Trying to set value but API not available."; 
var g_nSCO_ScoreMin = 0; // must be a number 
var g_nSCO_ScoreMax = 100; // must be a number > nSCO_Score_Min 
var g_SCO_MasteryScore = 100; // value by default; may be set to null 
var g_bMasteryScoreInitialized = false;
var g_nFindAPITries = 0; 
var g_objAPI = null; 
var g_bInitDone = false; 
var g_bFinishDone = false; 
var g_bSCOBrowse = false; 
var g_dtmInitialized = new Date(); // will be adjusted after initialize 
function AlertUserOfAPIError(strText) 
{ 
		if (g_bShowApiErrors) 
			alert(strText) 
} 
function FindAPI(win) 
{ 
	while ((win.API == null) && (win.parent != null) && (win.parent != win)) 
	{ 
		g_nFindAPITries ++; 
		if (g_nFindAPITries > 500) 
		{ 
			AlertUserOfAPIError(g_strAPITooDeep); 
			return null; 
		} 
		win = win.parent; 
	} 
	return win.API; 
} 
function APIOK() 
{ 
	return ((typeof(g_objAPI)!= "undefined") && (g_objAPI != null)) 
} 
function SCOInitialize() 
{ 
	var err = true; 
	if (!g_bInitDone) 
	{ 
		if ((window.parent) && (window.parent != window))
		{ 
			g_objAPI = FindAPI(window.parent) ;
		}
		if ((g_objAPI == null) && (window.opener != null)) 
		{ 
			g_objAPI = FindAPI(window.opener) ;
		} 
		if (!APIOK()) 
		{ 
			AlertUserOfAPIError(g_strAPINotFound); 
			err = false ;
		} 
		else 
		{ 
			err = g_objAPI.LMSInitialize(""); 
			if (err == "true") 
			{ 
				g_bSCOBrowse = (g_objAPI.LMSGetValue("cmi.core.lesson_mode") == "browse"); 
				if (!g_bSCOBrowse) 
				{ 
					if (g_objAPI.LMSGetValue("cmi.core.lesson_status") == "not attempted") 
					{ 
						err = g_objAPI.LMSSetValue("cmi.core.lesson_status","incomplete") ;
					}
				} 
			} 
			else 
			{ 
				AlertUserOfAPIError(g_strAPIInitFailed) 
			} 
		} 
	} 
	if (typeof(SCOInitData) != "undefined") 
	{ 
		// The SCOInitData function can be defined in another script of the SCO 
		SCOInitData() ;
	} 
	g_dtmInitialized = new Date(); 
	return (err + "") 		// Force type to string 
} 
function SCOFinish() 
{ 
	if ((APIOK()) && (g_bFinishDone == false)) 
	{ 
		if (typeof(SCOSaveData) != "undefined")
		{ 
			SCOReportSessionTime() // The SCOSaveData function can be defined in another script of the SCO 
			SCOSaveData(); 
		} 
		g_bFinishDone = (g_objAPI.LMSFinish("") == "true"); 
	} 
	return (g_bFinishDone + "" ) // Force type to string 
} 
// Call these catcher functions rather than trying to call the API adapter directly 
function SCOGetValue(nam) 
{
	return ((APIOK())?g_objAPI.LMSGetValue(nam.toString()):"")
} 
function SCOCommit(parm) 
{
	return ((APIOK())?g_objAPI.LMSCommit(""):"false")
} 
function SCOGetLastError(parm)
{
	return ((APIOK())?g_objAPI.LMSGetLastError(""):"-1")
} 
function SCOGetErrorString(n)
{
	return ((APIOK())?g_objAPI.LMSGetErrorString(n):"No API")
} 
function SCOGetDiagnostic(p)
{
	return ((APIOK())?g_objAPI.LMSGetDiagnostic(p):"No API")
} 
//LMSSetValue is implemented with more complex data 
//management logic through the SCOSetValue function below 
var g_bMinScoreAcquired = false; 
var g_bMaxScoreAcquired = false; 
function SCOSetValue(nam,val)
{ 
	// Special logic to manage some special values 
	var err = ""; 
	if (!APIOK())
	{ 
		AlertUserOfAPIError(g_strAPISetError + "\n" + nam + "\n" + val); 
		err = "false" 
	} 
	else if (nam == "cmi.core.score.raw") 
		err = privReportRawScore(val) 
	if (err == "")
	{ 
		err = g_objAPI.LMSSetValue(nam,val.toString() + ""); 
		if (err != "true") 
			return err 
	} 
	if (nam == "cmi.core.score.min")
	{ 
		g_bMinScoreAcquired = true; 
		g_nSCO_ScoreMin = val 
	} 
	else if (nam == "cmi.core.score.max")
	{ 
		g_bMaxScoreAcquired = true; 
		g_nSCO_ScoreMax = val 
	} 
	return err 
} 
function privReportRawScore(nRaw) 
{ 
	// called only by SCOSetValue 
	if (isNaN(nRaw)) 
		return "false"; 
	if (!g_bMinScoreAcquired)
	{
		if (g_objAPI.LMSSetValue("cmi.core.score.min",g_nSCO_ScoreMin+"")!="true")
		{ 
			return "false" 
		} 
	} 
	if (!g_bMaxScoreAcquired)
	{ 
		if (g_objAPI.LMSSetValue("cmi.core.score.max",g_nSCO_ScoreMax+"")!="true")
		{ 
			return "false"
		} 
	} 
	if (g_objAPI.LMSSetValue("cmi.core.score.raw", nRaw)!= "true") 
		return "false"; 
	g_bMinScoreAcquired = false; 
	g_bMaxScoreAcquired = false; 
	if (!g_bMasteryScoreInitialized)
	{ 
		var nMasteryScore = parseInt(SCOGetValue("cmi.student_data.mastery_score"),10); 
		if (!isNaN(nMasteryScore)) 
			g_SCO_MasteryScore = nMasteryScore 
	} 
	if (isNaN(g_SCO_MasteryScore)) 
		return "false"; 
	var stat = (nRaw >= g_SCO_MasteryScore? "passed" : "failed"); 
	if (SCOSetValue("cmi.core.lesson_status",stat) != "true") 
		return "false"; 
	return "true" 
} 
function MillisecondsToCMIDuration(n) 
{ 
		//Convert duration from milliseconds to 0000:00:00.00 format 
		var hms = ""; 
		var dtm = new Date(); 
		dtm.setTime(n); 
		var h = "000" + Math.floor(n / 3600000); 
		var m = "0" + dtm.getMinutes(); 
		var s = "0" + dtm.getSeconds(); 
		var cs = "0" + Math.round(dtm.getMilliseconds() / 10); 
		hms = h.substr(h.length-4)+":"+m.substr(m.length-2)+":"; 
		hms += s.substr(s.length-2)+"."+cs.substr(cs.length-2); 
		return hms 
} 
function SCOReportSessionTime() 
{ 
	// SCOReportSessionTime is called automatically by this script, 
	// but you may also call it at any time also from another SCO script 
	var dtm = new Date(); 
	var n = dtm.getTime() - g_dtmInitialized.getTime(); 
	return SCOSetValue("cmi.core.session_time",MillisecondsToCMIDuration(n)) 
} 
function SCOSetStatusCompleted()
{ 
	// Since only the designer of a SCO knows what completed means, another 
	// script of the SCO may call SCOSetStatusComplete to set completed status. 
	// The function checks that the SCO was not launched in browse mode, and 
	// avoids clobbering a "passed" or "failed" status since those imply "completed". 
	var stat = SCOGetValue("cmi.core.lesson_status"); 
	if (!g_bSCOBrowse) 
	{
		if ((stat!="completed") && (stat != "passed") && (stat != "failed"))
		{ 
			return SCOSetValue("cmi.core.lesson_status","completed") 
		} 
	} 
	else 
		return "false" 
}