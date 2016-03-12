/*
    LightBlog, a PHP/SQLite blogging platform
    Copyright (C) 2008-2016 The LightBlog Team.
    
    Sources/js/Form.js

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

$('form').ajaxForm(
{
    dataType: 'json',
    data: {
        ajax : 'true'
    },
    timeout: 2000,
    beforeSubmit: function() {
        $('#ajaxresponse').html('<img src="style/new/loading.gif" alt="Saving" />');
        return true;
    },
    error: function() {
        $('#ajaxresponse').html('<span class="result">AJAX request failed;<br />(Client failure/not JSON-encoded).</span>');
    },
    success: function(data) {
        $('#ajaxresponse').html(data.response);
    }
});
