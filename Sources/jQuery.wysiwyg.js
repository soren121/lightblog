/**
 * WYSIWYG - jQuery plugin 0.8
 *
 * Copyright (c) 2008-2009 Juan M Martinez
 * http://plugins.jquery.com/project/jWYSIWYG
 *
 * Dual licensed under the MIT and GPL licenses:
 *   http://www.opensource.org/licenses/mit-license.php
 *   http://www.gnu.org/licenses/gpl.html
 *
 * $Id: $
 */

/*jslint browser: true, forin: true */

(function(b){var c=function(e,d){this.init(e,d)};var a=function(e){var d=b(e).get(0);if(d.nodeName.toLowerCase()=="iframe"){return d.contentWindow.document}return d};b.fn.documentSelection=function(){var d=this.get(0);if(d.contentWindow.document.selection){return d.contentWindow.document.selection.createRange().text}else{return d.contentWindow.getSelection().toString()}};b.fn.wysiwyg=function(e){if(arguments.length>0&&arguments[0].constructor==String){var g=arguments[0].toString();var j=[];for(var f=1;f<arguments.length;f++){j[f-1]=arguments[f]}if(g=="enabled"){return this.data("wysiwyg")!==null}if(g in c){return this.each(function(){b.data(this,"wysiwyg").designMode();c[g].apply(this,j)})}else{return this}}var d={};if(e&&e.controls){d=e.controls;delete e.controls}e=b.extend({},b.fn.wysiwyg.defaults,e);e.controls=b.extend(true,e.controls,b.fn.wysiwyg.controls);for(var h in d){if(h in e.controls){b.extend(e.controls[h],d[h])}else{e.controls[h]=d[h]}}return this.each(function(){new c(this,e)})};b.fn.wysiwyg.defaults={html:'<?xml version="1.0" encoding="UTF-8"?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"><html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">STYLE_SHEET</head><body style="margin: 0px;">INITIAL_CONTENT</body></html>',formTableHtml:'<form class="wysiwyg"><fieldset><legend>Insert table</legend><label>Count of columns: <input type="text" name="colCount" value="3" /></label><label><br />Count of rows: <input type="text" name="rowCount" value="3" /></label><input type="submit" class="button" value="Insert table" /> <input type="reset" value="Cancel" /></fieldset></form>',formImageHtml:'<form class="wysiwyg"><fieldset><legend>Insert Image</legend><label>Image URL: <input type="text" name="url" value="http://" /></label><label>Image Title: <input type="text" name="imagetitle" value="" /></label><label>Image Description: <input type="text" name="description" value="" /></label><input type="submit" class="button" value="Insert Image" /> <input type="reset" value="Cancel" /></fieldset></form>',formWidth:440,formHeight:270,tableFiller:"Lorem ipsum",css:{},debug:false,autoSave:true,rmUnwantedBr:true,brIE:true,messages:{nonSelection:"select the text you wish to link"},events:{},controls:{},resizeOptions:false};b.fn.wysiwyg.controls={bold:{visible:true,tags:["b","strong"],css:{fontWeight:"bold"},tooltip:"Bold"},italic:{visible:true,tags:["i","em"],css:{fontStyle:"italic"},tooltip:"Italic"},strikeThrough:{visible:true,tags:["s","strike"],css:{textDecoration:"line-through"},tooltip:"Strike-through"},underline:{visible:true,tags:["u"],css:{textDecoration:"underline"},tooltip:"Underline"},justifyLeft:{visible:true,groupIndex:1,css:{textAlign:"left"},tooltip:"Justify Left"},justifyCenter:{visible:true,tags:["center"],css:{textAlign:"center"},tooltip:"Justify Center"},justifyRight:{visible:true,css:{textAlign:"right"},tooltip:"Justify Right"},justifyFull:{visible:true,css:{textAlign:"justify"},tooltip:"Justify Full"},indent:{groupIndex:2,visible:true,tooltip:"Indent"},outdent:{visible:true,tooltip:"Outdent"},subscript:{groupIndex:3,visible:true,tags:["sub"],tooltip:"Subscript"},superscript:{visible:true,tags:["sup"],tooltip:"Superscript"},undo:{groupIndex:4,visible:true,tooltip:"Undo"},redo:{visible:true,tooltip:"Redo"},insertOrderedList:{groupIndex:5,visible:true,tags:["ol"],tooltip:"Insert Ordered List"},insertUnorderedList:{visible:true,tags:["ul"],tooltip:"Insert Unordered List"},insertHorizontalRule:{visible:true,tags:["hr"],tooltip:"Insert Horizontal Rule"},createLink:{groupIndex:6,visible:true,exec:function(){var e=b(this.editor).documentSelection();if(e.length>0){if(b.browser.msie){this.focus();this.editorDoc.execCommand("createLink",true,null)}else{var d=prompt("URL","http://");if(d&&d.length>0){this.editorDoc.execCommand("unlink",false,[]);this.editorDoc.execCommand("createLink",false,d)}}}else{if(this.options.messages.nonSelection){alert(this.options.messages.nonSelection)}}},tags:["a"],tooltip:"Create link"},insertImage:{visible:true,exec:function(){if(b.modal){var d=this;b.modal(b.fn.wysiwyg.defaults.formImageHtml,{onShow:function(g){b("input:submit",g.data).click(function(k){k.preventDefault();var i=b('input[name="url"]',g.data).val();var l=b('input[name="imagetitle"]',g.data).val();var j=b('input[name="description"]',g.data).val();var h="<img src='"+i+"' title='"+l+"' alt='"+j+"' />";d.insertHtml(h);b.modal.close()});b("input:reset",g.data).click(function(h){h.preventDefault();b.modal.close()})},maxWidth:b.fn.wysiwyg.defaults.formWidth,maxHeight:b.fn.wysiwyg.defaults.formHeight,overlayClose:true})}else{if(b.fn.dialog){var d=this;var e=b(b.fn.wysiwyg.defaults.formImageHtml).appendTo("body");e.dialog({modal:true,width:b.fn.wysiwyg.defaults.formWidth,height:b.fn.wysiwyg.defaults.formHeight,open:function(g,h){b("input:submit",b(this)).click(function(l){l.preventDefault();var j=b('input[name="url"]',e).val();var m=b('input[name="imagetitle"]',e).val();var k=b('input[name="description"]',e).val();var i="<img src='"+j+"' title='"+m+"' alt='"+k+"' />";d.insertHtml(i);b(e).dialog("close")});b("input:reset",b(this)).click(function(i){i.preventDefault();b(e).dialog("close")})},close:function(g,h){b(this).dialog("destroy")}})}else{if(b.browser.msie){this.focus();this.editorDoc.execCommand("insertImage",true,null)}else{var f=prompt("URL","http://");if(f&&f.length>0){this.editorDoc.execCommand("insertImage",false,f)}}}}},tags:["img"],tooltip:"Insert image"},insertTable:{visible:true,exec:function(){if(b.fn.modal){var e=this;b.modal(b.fn.wysiwyg.defaults.formTableHtml,{onShow:function(h){b("input:submit",h.data).click(function(k){k.preventDefault();var i=b('input[name="rowCount"]',h.data).val();var j=b('input[name="colCount"]',h.data).val();e.insertTable(j,i,b.fn.wysiwyg.defaults.tableFiller);b.modal.close()});b("input:reset",h.data).click(function(i){i.preventDefault();b.modal.close()})},maxWidth:b.fn.wysiwyg.defaults.formWidth,maxHeight:b.fn.wysiwyg.defaults.formHeight,overlayClose:true})}else{if(b.fn.dialog){var e=this;var f=b(b.fn.wysiwyg.defaults.formTableHtml).appendTo("body");f.dialog({modal:true,width:b.fn.wysiwyg.defaults.formWidth,height:b.fn.wysiwyg.defaults.formHeight,open:function(h,i){b("input:submit",b(this)).click(function(l){l.preventDefault();var j=b('input[name="rowCount"]',f).val();var k=b('input[name="colCount"]',f).val();e.insertTable(k,j,b.fn.wysiwyg.defaults.tableFiller);b(f).dialog("close")});b("input:reset",b(this)).click(function(j){j.preventDefault();b(f).dialog("close")})},close:function(h,i){b(this).dialog("destroy")}})}else{var g=prompt("Count of columns","3");var d=prompt("Count of rows","3");this.insertTable(g,d,b.fn.wysiwyg.defaults.tableFiller)}}},tags:["table"],tooltip:"Insert table"},h1:{visible:true,groupIndex:7,className:"h1",command:b.browser.msie?"FormatBlock":"heading","arguments":[b.browser.msie?"<h1>":"h1"],tags:["h1"],tooltip:"Header 1"},h2:{visible:true,className:"h2",command:b.browser.msie?"FormatBlock":"heading","arguments":[b.browser.msie?"<h2>":"h2"],tags:["h2"],tooltip:"Header 2"},h3:{visible:true,className:"h3",command:b.browser.msie?"FormatBlock":"heading","arguments":[b.browser.msie?"<h3>":"h3"],tags:["h3"],tooltip:"Header 3"},cut:{groupIndex:8,visible:false,tooltip:"Cut"},copy:{visible:false,tooltip:"Copy"},paste:{visible:false,tooltip:"Paste"},increaseFontSize:{groupIndex:9,visible:false&&!(b.browser.msie),tags:["big"],tooltip:"Increase font size"},decreaseFontSize:{visible:false&&!(b.browser.msie),tags:["small"],tooltip:"Decrease font size"},removeFormat:{visible:true,exec:function(){if(b.browser.msie){this.focus()}this.editorDoc.execCommand("removeFormat",false,[]);this.editorDoc.execCommand("unlink",false,[])},tooltip:"Remove formatting"},html:{groupIndex:10,visible:false,exec:function(){if(this.viewHTML){this.setContent(b(this.original).val());b(this.original).hide()}else{this.saveContent();b(this.original).show()}this.viewHTML=!(this.viewHTML)},tooltip:"View source code"}};b.extend(c,{insertImage:function(g,f){var e=b.data(this,"wysiwyg");if(e.constructor==c&&g&&g.length>0){if(b.browser.msie){e.focus()}if(f){e.editorDoc.execCommand("insertImage",false,"#jwysiwyg#");var d=e.getElementByAttributeValue("img","src","#jwysiwyg#");if(d){d.src=g;for(var h in f){d.setAttribute(h,f[h])}}}else{e.editorDoc.execCommand("insertImage",false,g)}}},createLink:function(f){var d=b.data(this,"wysiwyg");if(d.constructor==c&&f&&f.length>0){var e=b(d.editor).documentSelection();if(e.length>0){if(b.browser.msie){d.focus()}d.editorDoc.execCommand("unlink",false,[]);d.editorDoc.execCommand("createLink",false,f)}else{if(d.options.messages.nonSelection){alert(d.options.messages.nonSelection)}}}},insertHtml:function(d){var e=b.data(this,"wysiwyg");e.insertHtml(d)},insertTable:function(f,d,e){b.data(this,"wysiwyg").insertTable(f,d,e)},setContent:function(d){var e=b.data(this,"wysiwyg");e.setContent(d);e.saveContent()},clear:function(){var d=b.data(this,"wysiwyg");d.setContent("");d.saveContent()},removeFormat:function(){var d=b.data(this,"wysiwyg");d.removeFormat()},save:function(){var d=b.data(this,"wysiwyg");d.saveContent()},document:function(){var d=b.data(this,"wysiwyg");return b(d.editorDoc)},destroy:function(){var d=b.data(this,"wysiwyg");d.destroy()}});b.extend(c.prototype,{original:null,options:{},element:null,editor:null,removeFormat:function(){if(b.browser.msie){this.focus()}this.editorDoc.execCommand("removeFormat",false,[]);this.editorDoc.execCommand("unlink",false,[])},destroy:function(){b(this.element).remove();b.removeData(this.original,"wysiwyg");b(this.original).show()},focus:function(){b(this.editorDoc.body).focus()},init:function(g,f){var e=this;this.editor=g;this.options=f||{};b.data(g,"wysiwyg",this);var j=g.width||g.clientWidth||0;var i=g.height||g.clientHeight||0;if(g.nodeName.toLowerCase()=="textarea"){this.original=g;if(j===0&&g.cols){j=(g.cols*8)+21}if(i===0&&g.rows){i=(g.rows*16)+16}this.editor=b(location.protocol=="https:"?'<iframe src="javascript:false;"></iframe>':"<iframe></iframe>").css({minHeight:(i-6).toString()+"px",width:(j-8).toString()+"px"}).attr("frameborder","0");this.editor.attr("tabindex",b(g).attr("tabindex"));if(b.browser.msie){this.editor.css("height",(i).toString()+"px")}}var d=this.panel=b('<ul role="menu" class="panel"></ul>');this.appendControls();this.element=b("<div></div>").css({width:(j>0)?(j).toString()+"px":"100%"}).addClass("wysiwyg").append(d).append(b("<div><!-- --></div>").css({clear:"both"})).append(this.editor);b(g).hide().before(this.element);this.viewHTML=false;this.initialHeight=i-8;this.initialContent=b(g).val();this.initFrame();if(this.options.resizeOptions&&b.fn.resizable){this.element.resizable(b.extend(true,{alsoResize:this.editor},this.options.resizeOptions))}var h=b(g).closest("form");if(this.options.autoSave){h.submit(function(){e.saveContent()})}h.bind("reset",function(){e.setContent(e.initialContent);e.saveContent()})},initFrame:function(){var d=this;var e="";if(this.options.css&&this.options.css.constructor==String){e='<link rel="stylesheet" type="text/css" media="screen" href="'+this.options.css+'" />'}this.editorDoc=a(this.editor);this.editorDoc_designMode=false;this.designMode();this.editorDoc.open();this.editorDoc.write(this.options.html.replace(/INITIAL_CONTENT/,function(){return d.initialContent}).replace(/STYLE_SHEET/,function(){return e}));this.editorDoc.close();if(b.browser.msie){window.setTimeout(function(){b(d.editorDoc.body).css("border","none")},0)}b(this.editorDoc).click(function(f){d.checkTargets(f.target?f.target:f.srcElement)});b(this.original).focus(function(){if(!b.browser.msie){d.focus()}});if(!b.browser.msie){b(this.editorDoc).keydown(function(f){if(f.ctrlKey){switch(f.keyCode){case 66:this.execCommand("Bold",false,false);return false;case 73:this.execCommand("Italic",false,false);return false;case 85:this.execCommand("Underline",false,false);return false}}return true})}else{if(this.options.brIE){b(this.editorDoc).keydown(function(g){if(g.keyCode==13){var f=d.getRange();f.pasteHTML("<br />");f.collapse(false);f.select();return false}return true})}}if(this.options.autoSave){b(this.editorDoc).keydown(function(){d.saveContent()}).keyup(function(){d.saveContent()}).mousedown(function(){d.saveContent()})}if(this.options.css){window.setTimeout(function(){if(d.options.css.constructor==String){}else{b(d.editorDoc).find("body").css(d.options.css)}},0)}if(this.initialContent.length===0){this.setContent("")}b.each(this.options.events,function(f,g){b(d.editorDoc).bind(f,g)})},designMode:function(){var e=3;var f;var d=this;var g=this.editorDoc;f=function(){if(a(d.editor)!==g){d.initFrame();return}try{g.designMode="on"}catch(h){}e--;if(e>0&&b.browser.mozilla){setTimeout(f,100)}};f();this.editorDoc_designMode=true},getSelection:function(){return(window.getSelection)?window.getSelection():document.selection},getRange:function(){var d=this.getSelection();if(!(d)){return null}return(d.rangeCount>0)?d.getRangeAt(0):d.createRange()},getContent:function(){return b(a(this.editor)).find("body").html()},setContent:function(d){b(a(this.editor)).find("body").html(d)},insertHtml:function(d){if(d&&d.length>0){if(b.browser.msie){this.focus();this.editorDoc.execCommand("insertImage",false,"#jwysiwyg#");var e=this.getElementByAttributeValue("img","src","#jwysiwyg#");if(e){b(e).replaceWith(d)}}else{this.editorDoc.execCommand("insertHTML",false,d)}}},insertTable:function(k,d,h){if(isNaN(d)||isNaN(k)||d==null||k==null){return}k=parseInt(k,10);d=parseInt(d,10);if(h===null){h="&nbsp;"}h="<td>"+h+"</td>";var g=['<table border="1" style="width: 100%;"><tbody>'];for(var f=d;f>0;f--){g.push("<tr>");for(var e=k;e>0;e--){g.push(h)}g.push("</tr>")}g.push("</tbody></table>");this.insertHtml(g.join(""))},saveContent:function(){if(this.original){var d=this.getContent();if(this.options.rmUnwantedBr){d=(d.substr(-4)=="<br>")?d.substr(0,d.length-4):d}b(this.original).val(d)}},withoutCss:function(){if(b.browser.mozilla){try{this.editorDoc.execCommand("styleWithCSS",false,false)}catch(f){try{this.editorDoc.execCommand("useCSS",false,true)}catch(d){}}}},appendMenu:function(i,e,g,f,h){var d=this;e=e||[];b("<li></li>").append(b('<a role="menuitem" tabindex="-1" href="javascript:;">'+(g||i)+"</a>").addClass(g||i).attr("title",h)).click(function(){if(f){f.apply(d)}else{d.focus();d.withoutCss();d.editorDoc.execCommand(i,false,e)}if(d.options.autoSave){d.saveContent()}this.blur()}).appendTo(this.panel)},appendMenuSeparator:function(){b('<li role="separator" class="separator"></li>').appendTo(this.panel)},appendControls:function(){var g=0;var d=true;for(var e in this.options.controls){var f=this.options.controls[e];if(f.groupIndex&&g!=f.groupIndex){g=f.groupIndex;d=false}if(!f.visible){continue}if(!d){this.appendMenuSeparator();d=true}this.appendMenu(f.command||e,f["arguments"]||[],f.className||f.command||e||"empty",f.exec,f.tooltip||f.command||e||"")}},checkTargets:function(f){for(var e in this.options.controls){var i=this.options.controls[e];var h=i.className||i.command||e||"empty";b("."+h,this.panel).removeClass("active");if(i.tags){var j=f;do{if(j.nodeType!=1){break}if(b.inArray(j.tagName.toLowerCase(),i.tags)!=-1){b("."+h,this.panel).addClass("active")}}while((j=j.parentNode))}if(i.css){var g=b(f);do{if(g[0].nodeType!=1){break}for(var d in i.css){if(g.css(d).toString().toLowerCase()==i.css[d]){b("."+h,this.panel).addClass("active")}}}while((g=g.parent()))}}},getElementByAttributeValue:function(f,d,g){var j=this.editorDoc.getElementsByTagName(f);for(var e=0;e<j.length;e++){var h=j[e].getAttribute(d);if(b.browser.msie){h=h.substr(h.length-g.length)}if(h==g){return j[e]}}return false}})})(jQuery);