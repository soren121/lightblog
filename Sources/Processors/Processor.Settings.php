<?php
/*
    LightBlog, a PHP/SQLite blogging platform
    Copyright (C) 2008-2016 The LightBlog Team.
    
    Sources/Processors/Processor.Settings.php

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

class Settings extends Processor
{
    public function processor($data)
    {
        // We will collect the response here.
        $response = array(
            'result' => 'error',
            'response' => array(),
        );

        $options = array();

        // A title is required.
        if(empty($data['title']) || utf_strlen($data['title']) == 0)
        {
            $response['response'][] = 'A blog title is required.';
        }
        else
        {
            $options['title'] = utf_htmlspecialchars($data['title']);
        }

        // Same goes for the URL. It also needs to be valid.
        if(empty($data['url']) || !is_url($data['url']))
        {
            $response['response'][] = 'A valid URL is required.';
        }
        else
        {
            // We also want it to have a trailing slash.
            $options['url'] = utf_substr($data['url'], -1, 1) == '/' ? $data['url'] : $data['url']. '/';
        }

        // Make sure the time zone is valid.
        if(empty($data['timezone']) || !timezone_open($data['timezone']))
        {
            $response['response'][] = 'Invalid time zone selected.';
        }
        else
        {
            $options['timezone'] = $data['timezone'];
            date_default_timezone_set($data['timezone']);
        }

        // Now for the date...
        if(empty($data['date']) || ($data['date'] == 'custom' && empty($data['custom_date'])))
        {
            $response['response'][] = 'Invalid date format.';
        }
        else
        {
            $options['date_format'] = utf_htmlspecialchars($data['date'] == 'custom' ? $data['custom_date'] : $data['date']);
        }

        // Then time formatting.
        if(empty($data['time']) || ($data['time'] == 'custom' && empty($data['custom_time'])))
        {
            $response['response'][] = 'Invalid time format.';
        }
        else
        {
            $options['time_format'] = utf_htmlspecialchars($data['time'] == 'custom' ? $data['custom_time'] : $data['time']);
        }

        // Were there any issues?
        if(count($response['response']) == 0)
        {
            // Nope, so we can save the settings.
            $this->dbh->beginTransaction();

            $save_query = $this->dbh->prepare("
                INSERT OR REPLACE INTO
                    settings
                (variable, value)
                VALUES(
                    :option,
                    :value
                )
            ");

            foreach($options as $option => &$value)
            {
                $GLOBALS['bloginfo_data'][$option] = $value;

                $save_query->bindParam(":option", $option, PDO::PARAM_STR);
                $save_query->bindParam(":value", $value, PDO::PARAM_STR);
                $save_query->execute();
            }

            if($this->dbh->commit())
            {
                $response['result'] = 'success';
                $response['response'] = 'Settings saved.';
            }
            else
            {
                $response['response'] = $error_message;
            }
        }

        return $response;
    }
}

?>
