/****************************************************

	jQuery MiniPages Plugin v1.0, ©2009 soren121.
	Licensed under the GNU GPL version 3.
	
****************************************************/

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