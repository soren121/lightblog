/* vX AJAX Post Form JS plugin by soren121 & antimatter15 */
_.ajaxsubmit=function(c,r){var t=_.G(c),z={},y=t.getElementsByTagName("*");for(var i=y.length;i--;) z[y[i].name]=y[i].value;_.X(t.action,r,'?'+_.Q(z),t.method=="post")}
_.ajaxform=function(f,r){_.E(_.G(f),"submit",function(e){e.preventDefault();_.ajaxsubmit(f,r)})}