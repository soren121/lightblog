/* vX AJAX Post Form JS plugin by soren121 */
_.Y=function(c,r){var t=_.G(c),z={},y=t.getElementsByTagName("*");
for(var i=y.length;i--;) z[y[i].name]=y[i].value
_.X(t.action,r,'?'+_.Q(z),t.method=="post")
}
function ajaxifyForm(f){_.E(_.G(f),"submit",function(e){e.preventDefault();_.Y(f)})}