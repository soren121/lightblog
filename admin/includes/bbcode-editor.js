var textarea;
var content;
document.write("<link href=\"includes/bbstyles.css\" rel=\"stylesheet\" type=\"text/css\">");


function Init(obj,width,height, val) {
	document.write("<img class=\"button\" src=\"includes/bold.png\" name=\"btnBold\" onClick=\"doAddTags('[b]','[/b]')\" />"); 
    document.write("<img class=\"button\" src=\"includes/italic.png\" name=\"btnItalic\" onClick=\"doAddTags('[i]','[/i]')\" />"); 
	document.write("<img class=\"button\" src=\"includes/underline.png\" name=\"btnUnderline\" onClick=\"doAddTags('[u]','[/u]')\" />"); 
	document.write("<img class=\"button\" src=\"includes/strike.png\" name=\"btnStrike\" onClick=\"doAddTags('[s]','[/s]')\" />"); 
	document.write("<img class=\"button\" src=\"includes/size.png\" name=\"btnSize\" onClick=\"doSize()\" />"); 
	document.write("<img class=\"button\" src=\"includes/orderedlist.png\" name=\"btnOl\" onClick=\"doAddTags('[ol][li]','[/li][/ol]')\" />"); 
	document.write("<img class=\"button\" src=\"includes/unorderedlist.png\" name=\"btnUl\" onClick=\"doAddTags('[ul][li]','[/li][/ul]')\" />");
	document.write("<img class=\"button\" src=\"includes/link.png\" name=\"btnLink\" onClick=\"doURL()\" />");
	document.write("<img class=\"button\" src=\"includes/image.png\" name=\"btnPicture\" onClick=\"doImage()\" />");
	document.write("<img class=\"button\" src=\"includes/quote.png\" name=\"btnQuote\" onClick=\"doAddTags('[quote]','[/quote]')\" />"); 
	document.write("<br>");
	document.write("<textarea id=\""+ obj +"\" name = \"" + obj + "\" cols=\"" + width + "\" rows=\"" + height + "\"></textarea>");
	
	textarea = document.getElementById(obj);
	textarea.value = val;
		}

function doImage()
{

var url = prompt('Enter the Image URL:','http://');

	if (document.selection) 
			{
				textarea.focus();
				var sel = document.selection.createRange();
				sel.text = '[img]' + url + '[/img]';
			}
   else 
    {
		var len = textarea.value.length;
	    var start = textarea.selectionStart;
		var end = textarea.selectionEnd;
		
        var sel = textarea.value.substring(start, end);
	    //alert(sel);
		var rep = '[img]' + url + '[/img]';
        textarea.value =  textarea.value.substring(0,start) + rep + textarea.value.substring(end,len);
	}

}

function doURL()
{

var url = prompt('Enter the URL:','http://');

	if (document.selection) 
			{
				textarea.focus();
				var sel = document.selection.createRange();
				//alert(sel.text);
				sel.text = '[url=' + url + ']' + sel.text + '[/url]';
			}
   else 
    {
		var len = textarea.value.length;
	    var start = textarea.selectionStart;
		var end = textarea.selectionEnd;
		
        var sel = textarea.value.substring(start, end);
	    //alert(sel);
		var rep = '[url=' + url + ']' + sel + '[/url]';;
        textarea.value =  textarea.value.substring(0,start) + rep + textarea.value.substring(end,len);
	}
}

function doSize()
{

var pt = prompt('Enter the size of the text:','12');

	if (document.selection) 
			{
				textarea.focus();
				var sel = document.selection.createRange();
				//alert(sel.text);
				sel.text = '[size=' + pt + ']' + sel.text + '[/size]';
			}
   else 
    {
		var len = textarea.value.length;
	    var start = textarea.selectionStart;
		var end = textarea.selectionEnd;
		
        var sel = textarea.value.substring(start, end);
	    //alert(sel);
		var rep = '[size=' + pt + ']' + sel + '[/size]';;
        textarea.value =  textarea.value.substring(0,start) + rep + textarea.value.substring(end,len);
	}
}

function doAddTags(tag1,tag2)
{

	// Code for IE
		if (document.selection) 
			{
				textarea.focus();
				var sel = document.selection.createRange();
				//alert(sel.text);
				sel.text = tag1 + sel.text + tag2;
			}
   else 
    {  // Code for Mozilla Firefox
		var len = textarea.value.length;
	    var start = textarea.selectionStart;
		var end = textarea.selectionEnd;
		
        var sel = textarea.value.substring(start, end);
	    //alert(sel);
		var rep = tag1 + sel + tag2;
        textarea.value =  textarea.value.substring(0,start) + rep + textarea.value.substring(end,len);
	}
}