/**
 @preserve CLEditor Advanced Table Plugin v1.0.0
 http://premiumsoftware.net/cleditor
 requires CLEditor v1.2.2 or later

 Copyright 2010, Sergio Drago
 Dual licensed under the MIT or GPL Version 2 licenses.

 Based on Chris Landowski's Table Plugin v1.0.2
*/

(function(b){b.cleditor.buttons.table={name:"table",css:{backgroundPosition:"24px 0px"},title:"Insert Table",command:"inserthtml",popupName:"table",popupClass:"cleditorPrompt",popupContent:'<table cellpadding=0 cellspacing=0><tr><td style="padding-right:6px;">Cols:<br /><input type=text value=4 size=12 /></td><td style="padding-right:6px;">Rows:<br /><input type=text value=4 size=12 /></td></tr><tr><td style="padding-right:6px;">Cell Spacing:<br /><input type=text value=2 size=12 /></td><td style="padding-right:6px;">Cell Padding:<br /><input type=text value=2 size=12 /></td></tr><tr><td style="padding-right:6px;">Border:<br /><input type=text value=1 size=12 /></td><td style="padding-right:6px;">Style (CSS):<br /><input type=text size=12 /></td></tr></table><br /><input type=button value=Submit  />',buttonClick:a};b.cleditor.defaultOptions.controls=b.cleditor.defaultOptions.controls.replace("rule ","rule table ");function a(d,c){b(c.popup).children(":button").unbind("click").bind("click",function(i){var h=c.editor;var m=b(c.popup).find(":text"),k=parseInt(m[0].value),o=parseInt(m[1].value),j=parseInt(m[2].value),l=parseInt(m[3].value),f=parseInt(m[4].value),n=m[5].value;if(parseInt(k)<1||!parseInt(k)){k=0}if(parseInt(o)<1||!parseInt(o)){o=0}if(parseInt(j)<1||!parseInt(j)){j=0}if(parseInt(l)<1||!parseInt(l)){l=0}if(parseInt(f)<1||!parseInt(f)){f=0}var g;if(k>0&&o>0){g="<table border="+f+" cellpadding="+l+" cellspacing="+j+(n?' style="'+n+'"':"")+">";for(y=0;y<o;y++){g+="<tr>";for(x=0;x<k;x++){g+="<td>"+x+","+y+"</td>"}g+="</tr>"}g+="</table><br />"}if(g){h.execCommand(c.command,g,null,c.button)}m[0].value="4";m[1].value="4";m[2].value="2";m[3].value="2";m[4].value="1";m[5].value="";h.hidePopups();h.focus()})}})(jQuery);