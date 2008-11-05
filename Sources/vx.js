/* vX JS library by antimatter15 and inportb */
var _=_?_:{}
_.X=function(u,f,p,x){x=new(this.ActiveXObject?ActiveXObject:XMLHttpRequest)('Microsoft.XMLHTTP');x.open(p?'POST':'GET',u,!0);p?x.setRequestHeader('Content-type','application/x-www-form-urlencoded'):0;x.onreadystatechange=function(){x.readyState==4&&f?f(x.responseText,x):f};x.send(p)}
_.A=function(v,n,c,u,y){u=0;return y=setInterval(function(){c(u/v);++u>v?clearInterval(y):0},n)}
_.L=function(n,d,y,k,h){y=(d?d:document).getElementsByTagName("*");h=[];for(k=y.length;k--;)_.I(n,y[k].className.split(" "))<0?0:h.push(y[k]);return h}
_.I=function(v,a,i){for(i=a.length;i--&&a[i]!=v;);return i}
_.C=function(j,c){if(c)return _.S(_.S(j),!0);function p(){};p.prototype=j;return new p()}
_.S=function(j,d,t){if(d)return eval('('+j+')');if(!j)return j+'';t=[];if(j.pop){for(x in j)t.push(_.S(j[x]));j='['+t.join(',')+']'}else if(typeof j=='object'){for(x in j)t.push(x+':'+_.S(j[x]));j='{'+t.join(',')+'}'}else if(j.split)j="'"+j.replace(/\'/g,"\\'")+"'";return j}
_.E=function(e,t,f,r){if(e.attachEvent?(r?e.detachEvent('on'+t,e[t+f]):!0):(r?e.removeEventListener(t,f,!1):e.addEventListener(t,f,!1))){e['e'+t+f]=f;e[t+f]=function(){e['e'+t+f](window.event)};e.attachEvent('on'+t,e[t+f])}}
_.T=function(o,a,y){for(y in a)o[y]=a[y];return o}
_.F=function(d,h,f,i){d=d=='in';_.A(f?f:15,i?i:50,function(a){a=(d?0:1)+(d?1:-1)*a;h.style.opacity=a;h.style.filter='alpha(opacity='+100*a+')'})}
_.G=function(e){return e.style?e:document.getElementById(e)}
_.H=function(s,d,t){t=document.createElement('textarea');t.innerHTML=s;return d?t.value:t.innerHTML}
_.N=function(n,p,r){p=n.split('.');r=window;for(i in p){if(!r[p[i]])r[p[i]]={};r=r[p[i]]}return r}
_.Q=function(j,y,x){y='';for(x in j)y+='&'+x+'='+encodeURIComponent(j[x]);return y.substr(1)}
_.U=function(l,f){(f=function(){eval(l.splice(0,1)[0])})();return l}
_.R=function(f){/(?!.*?ati|.*?kit)^moz|ope/i.test(navigator.userAgent)?_.E(document,'DOMContentLoaded',f):setTimeout(f,0)}
_.M=function(t,d,x){for(x in d)t=t.split("{"+x+"}").join(d[x]);return t}
_.HC=function(e,c){return _.I(c,e.className.split(" "))<0}
_.AC=function(e,c){if(!_.hasclass(e,c))e.className+=" "+c}
_.RC=function(e,c){e.className=_.arrayremove(e.className.join(" "),c).join(" ")}
_.RA=function(e,o){for(f=e.length;f--;)e[f]==o?f.splice(f,1):0;return e}