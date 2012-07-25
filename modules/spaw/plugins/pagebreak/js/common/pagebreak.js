// Page Break plugin
function SpawPGpagebreak()
{
}

SpawPGpagebreak.insertPageBreakClick = function(editor, tbi, sender)
{
  if (tbi.is_enabled)
  {
	var pdoc = editor.getActivePageDoc();
    	var hr = pdoc.createElement("div");
	hr.innerHTML = "{PAGE}";
    	editor.insertNodeAtSelection(hr);
  }
}

SpawPGpagebreak.isPageBreakEnabled = function(editor, tbi)
{

	return editor.isInDesignMode();

}
