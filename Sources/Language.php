<?php
/*
    LightBlog, a PHP/SQLite blogging platform
    Copyright (C) 2008-2016 The LightBlog Team.
    
    Sources/Language.php

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

/*
    Function: l

    This translates any given text from English into the current systems
    language.

    Parameters:
        string $text - The text to translate.
        mixed ... - You can pass additional parameters that contain values to be
                    replaced in $text. See the notes for more details.

    Returns:
        string - Returns a translated string. If the language is English or
                 there is no translation available then the original string
                 passed in $text will be used.

    Note:
        The $text value is passed through PHP's <www.php.net/sprintf> function,
        so any specifier can be used that is available with the sprintf
        function.

        These will be the most common specifiers:
            %s - A string, as passed.
            %u - unsigned integer
            %f - floating point
            %% - literal % sign
*/
function l($text)
{
    // Will we need to pass this through sprintf?
    if(func_num_args() > 1)
    {
        $args = func_get_args();
    }

    // We may need to get a translation. Only if the language is set and not
    // en-US. Also make sure there are translations loaded, along with one we
    // can use.
    if(get_bloginfo('language') !== false && get_bloginfo('language') !== 'en-US' && isset($GLOBALS['translation_map'][basename(get_bloginfo('language'))]) && is_array($GLOBALS['translation_map'][basename(get_bloginfo('language'))]))
    {
        // Do we have a valid replacement?
        if(isset($GLOBALS['translation_map'][basename(get_bloginfo('language'))][sha1($text)]))
        {
            $text = $GLOBALS['translation_map'][basename(get_bloginfo('language'))][sha1($text)];

            // We may need to pop this into the $args array.
            if(isset($args))
            {
                $args[0] = $text;
            }
        }
    }

    // Now return the text, possibly passing it through sprintf if needed.
    return isset($args) ? call_user_func_array('sprintf', $args) : $text;
}

/*
    Function: loadLanguage

    Loads the translations from the specified language files based on the
    current system language.

    Parameters:
        string $source - The name of the file (without .php or any directory) to
        load translations from.

    Returns:
        bool - Returns true on success, false on failure.

    Note:
        This function may be called multiple times as previously loaded
        translations will be retained (though newer values will be overwritten).
*/
function loadLanguage($source)
{
    // We will return true if there is no set language or if it is en-US.
    if(get_bloginfo('language') === false || get_bloginfo('language') == 'en-US')
    {
        return false;
    }

    // Make sure the language option is valid.
    if(!file_exists(ABSPATH. '/Sources/Languages/'. basename(get_bloginfo('language'))))
    {
        trigger_error('Invalid language option specified ('. utf_htmlspecialchars(basename(get_bloginfo('language'))). ')', E_USER_NOTICE);

        return false;
    }

    // Is there a translation map array yet?
    if(!isset($GLOBALS['translation_map']))
    {
        $GLOBALS['translation_map'] = array();
    }

    // Now let's see if the source file we want to load exists.
    if(!file_exists(ABSPATH. '/Sources/Languages/'. basename(get_bloginfo('language')). '/'. basename(utf_strtolower($source)). '.php'))
    {
        return false;
    }

    // Okay, now include the PHP file, it should contain a $translations
    // array.
    require_once(ABSPATH. '/Sources/Languages/'. basename(get_bloginfo('language')). '/'. utf_strtolower(basename($source)). '.php');

    if(isset($translations) && is_array($translations))
    {
        // Merge the loaded translations with the current, and we're done!
        $GLOBALS['translation_map'][basename(get_bloginfo('language'))] = array_merge($GLOBALS['translation_map'][basename(get_bloginfo('language'))], $translations);

        return true;
    }
    else
    {
        return false;
    }
}

// We will go ahead and load system wide translations (common ones, that is).
loadLanguage('core');
?>
