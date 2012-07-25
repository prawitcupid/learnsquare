// ===================================================================
// Author: Matt Kruse <matt@mattkruse.com>
// WWW: http://www.mattkruse.com/
//
// NOTICE: You may use this code for any purpose, commercial or
// private, without any further permission from the author. You may
// remove this notice from your final code if you wish, however it is
// appreciated by the author if at least my web site address is kept.
//
// You may *NOT* re-distribute this code in any way except through its
// use. That means, you can include it in your product, or your web
// site, or any other form where the code is actually being used. You
// may not put the plain javascript up on your site for download or
// include it in your javascript libraries for download. 
// If you wish to share this code with others, please just point them
// to the URL instead.
// Please DO NOT link directly to my .js files from your site. Copy
// the files to your server and use them there. Thank you.
// ===================================================================

function DynamicOptionList(){if(arguments.length < 2){alert("Not enough arguments in DynamicOptionList()");}this.target = arguments[0];this.dependencies = new Array();for(var i=1;i<arguments.length;i++){this.dependencies[this.dependencies.length] = arguments[i];}this.form = null;this.dependentValues = new Object();this.defaultValues = new Object();this.options = new Object();this.delimiter = "|";this.longestString = "";this.numberOfOptions = 0;this.addOptions = DynamicOptionList_addOptions;this.populate = DynamicOptionList_populate;this.setDelimiter = DynamicOptionList_setDelimiter;this.setDefaultOption = DynamicOptionList_setDefaultOption;this.printOptions = DynamicOptionList_printOptions;this.init = DynamicOptionList_init;}
function DynamicOptionList_setDelimiter(val){this.delimiter = val;}
function DynamicOptionList_setDefaultOption(condition, val){if(typeof this.defaultValues[condition] == "undefined" || this.defaultValues[condition]==null){this.defaultValues[condition] = new Object();}for(var i=1;i<arguments.length;i++){this.defaultValues[condition][arguments[i]]=1;}}
function DynamicOptionList_init(theform){this.form = theform;this.populate();}
function DynamicOptionList_addOptions(dependentValue){if(typeof this.options[dependentValue] != "object"){this.options[dependentValue] = new Array();}for(var i=1;i<arguments.length;i+=2){if(arguments[i].length > this.longestString.length){this.longestString = arguments[i];}this.numberOfOptions++;this.options[dependentValue][this.options[dependentValue].length] = arguments[i];this.options[dependentValue][this.options[dependentValue].length] = arguments[i+1];}}
function DynamicOptionList_printOptions(){if((navigator.appName == 'Netscape') &&(parseInt(navigator.appVersion) <= 4)){var ret = "";for(var i=0;i<this.numberOfOptions;i++){ret += "<OPTION>";}ret += "<OPTION>"
for(var i=0;i<this.longestString.length;i++){ret += "_";}document.writeln(ret);}}
function DynamicOptionList_populate(){var theform = this.form;var i,j,obj,obj2;this.dependentValues = new Object;var dependentValuesInitialized = false;for(i=0;i<this.dependencies.length;i++){var sel = theform[this.dependencies[i]];var selName = sel.name;if(!dependentValuesInitialized){dependentValuesInitialized = true;for(j=0;j<sel.options.length;j++){if(sel.options[j].selected){this.dependentValues[sel.options[j].value] = true;}}}else{var tmpList = new Object();var newList = new Object();for(j=0;j<sel.options.length;j++){if(sel.options[j].selected){tmpList[sel.options[j].value] = true;}}for(obj in this.dependentValues){for(obj2 in tmpList){newList[obj + this.delimiter + obj2] = true;}}this.dependentValues = newList;}}var targetSel = theform[this.target];var targetSelected = new Object();for(i=0;i<targetSel.options.length;i++){if(targetSel.options[i].selected){targetSelected[targetSel.options[i].value] = true;}}targetSel.options.length = 0;for(i in this.dependentValues){if(typeof this.options[i] == "object"){var o = this.options[i];for(j=0;j<o.length;j+=2){var text = o[j];var val = o[j+1];targetSel.options[targetSel.options.length] = new Option(text, val, false, false);if(typeof this.defaultValues[i] != "undefined" && this.defaultValues[i]!=null){for(def in this.defaultValues[i]){if(def == val){targetSelected[val] = true;}}}}}}targetSel.selectedIndex=-1;for(i=0;i<targetSel.options.length;i++){if(targetSelected[targetSel.options[i].value] != null && targetSelected[targetSel.options[i].value]==true){targetSel.options[i].selected = true;}}}

