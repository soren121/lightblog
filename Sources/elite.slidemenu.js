/******************************************************************************************
	Multi-Level Drop-Down Script <http://www.leigeber.com/2008/11/drop-down-menu/>
	Written by Michael Leigeber of leigeber.com -- Licensed under the CC-BY 3.0 license
	Ported to the elite.js library by The LightBlog Team
******************************************************************************************/

$.fn.menu = function(id) {
	var a = c, s= $(id).tag('ul'), l = $(s).length, i = 0, this.n = n, this.h = [], this.c = [];
	for(i; i < l; i++) {
		var h = s[i].parentNode;
		this.h[i] = h;
		this.c[i] = s[i];
		h.onmouseover = new Function(this.n+'.st('+i+',true)');
		h.onmouseout = new Function(this.n+'.st('+i+')');
	}
	function st(x, f) {
		var c = this.c[x], h = this.h[x], p = $(h).tag('a')[0];
		clearInterval(c.t);
		c.style.overflow = 'hidden';
		if(f) {
			$(p).addClass(a);
			if(!c.mh) {
				c.style.display = 'block';
				c.style.height = '';
				c.mh = c.offsetHeight;
				c.style.height = 0;
			}
			if(c.mh==c.offsetHeight) {
				c.style.overflow='visible';
			}
			else {
				c.style.zIndex = z;
				z++;
				c.t = setInterval(function() { sl(c, 1); }, t);
			}
		}
		else {
			$(p).removeClass(a);
			c.t = setInterval(function() { sl(c,-1); }, t);
		}
	}
	function sl(c, f) {
		var h = c.offsetHeight;
		if((h <= 0 && f != 1) || (h >= c.mh && f==1)) {
			if(f == 1) {
				$(c).show();
			}
			clearInterval(c.t);
			return;
		}
		if(f == 1) {
			$(c).fade(1).slide(1);
		}
		else {
			$(c).fade(0).slide(0);
		}
	}
};