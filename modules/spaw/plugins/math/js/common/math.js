// math plugin
//edit by Watee wichiennit 03/01/2008
function SpawPGmath()
{
}
SpawPGmath.mathClick = function(editor, tbi, sender,cid)
{
  if (tbi.is_enabled)
  {
    var a = editor.getSelectedElementByTagName("a");
    editor.stripAbsoluteUrl(a);
    SpawEngine.openDialog('math', 'math', editor, a, '', 'SpawPGmath.mathClickCallback', tbi, sender,cid);
  }
}
SpawPGmath.mathClickCallback = function(editor, result, tbi, sender)
{
  if (result)
  {
    editor.insertNodeAtSelection(result);
  }
  editor.updateToolbar();
}

SpawPGmath.isMathEnabled = function(editor, tbi)
{
	return editor.isInDesignMode();
}
