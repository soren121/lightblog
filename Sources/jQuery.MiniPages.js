/***********************************************

	LightBlog 0.9
	SQLite blogging platform
	
	Sources/jQuery.MiniPages.js
	
	©2009 soren121. All rights reserved.
	Released under the GNU General
	Public License. For all licensing
	information, please see the
	LICENSE.txt document included in this
	distribution.

***********************************************/

jQuery.fn.minipages = function() {
	id = this.attr("id");
	ns = '#' + id + '>.tab:gt(0)';
	jQuery(ns).hide();
}

jQuery.fn.minipageShow = function(id) {
	tid = '#tab' + id;
	jQuery('.tab:visible').hide();
	jQuery(tid).show();
}