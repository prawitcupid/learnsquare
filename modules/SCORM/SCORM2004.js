function SCORM_2004_API() 
{
	//Variable
	var cmi;

    var errorCode = 0;
    
    var Initialized = false;

	//Session Methods
	function Initialize(param)
	{
		if (param != "") 
		{
			errorCode = 102;
			Initialized = false;
			return false;
		}
		if(Initialized == true)
		{
			errorCode = 103;
			return false;
		}
		cmi._version = "1.0";
		cmi.comments_from_learner._children = "";
		cmi.comments_from_learner._count = 0;
		String[] cmi.comments_from_learner = new String[5];
		Initialized = true;
		return true;
	}
	function Terminate(param)
	{
			if(Initialized == false)
			{
				errorCode = 112;
				return false;
			}

	}
	//End Session Methods
	//Data transfer Methods
	function GetValue(param)
	{

	}
	function SetValue(para1,para2)
	{

	}
	function Commit(param)
	{

	}
	//End Data transfer Methods
	//Support Methods
	function GetLastError()
	{

	}
	function GetErrorString(param)
	{

	}
	function GetDiagnostic(param)
	{

	}
	//End Support Methods
}

var API_1484_11 = new SCORM_2004_API() ;