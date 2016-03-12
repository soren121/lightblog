<?php
/*
    LightBlog, a PHP/SQLite blogging platform
    Copyright (C) 2008-2016 The LightBlog Team.
    
    Sources/Processors/Processor.Profile.php

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

class Profile extends Processor
{
    public function processor($data)
    {
        // Later on, we will send this as JSON to the browser
        $response = array(
            'result' => 'error',
            'response' => null
        );

        // Can the user do this?
        if(permissions('EditOtherUsers') || (int)$data['uid'] == user()->id())
        {
            // Get current user data from the database
            $sql_user = @$this->dbh->query("SELECT user_pass,user_email,display_name,user_role FROM users WHERE user_id=".(int)$data['uid']);
            if($sql_user == false)
            {
                $response['response'] = array("result" => "error", "response" => "couldn't read from the database.");
                return $response;
            }
            while($row = $sql_user->fetchObject())
            {
                // Make variables from the database query
                $cpassword_db = $row->user_pass;
                $email = $row->user_email;
                $displayname = $row->display_name;
                $role = $row->user_role;
            }

            $sql_user->closeCursor();

            // Check if the current password given in the form matches the actual current password
            if(password_verify($data['cpassword'], $cpassword_db))
            {
                // Make an array for the queries
                $query = array();
                // Are we changing a password? Make sure both fields are filled so we don't accidentally change it!
                if($data['password'] != '' && $data['vpassword'] != '')
                {
                    // Do both password fields match?
                    if($data['password'] === $data['vpassword'])
                    {
                        // Make a new password hash
                        $password = password_hash($data['password'], PASSWORD_DEFAULT);
                        // Add it to the query
                        array_push($query, "SET user_pass='".sqlite_escape_string($password)."'");
                    }
                    else
                    {
                        $response['response'] = "new passwords don't match.";
                    }
                }
                // Are we changing an email address?
                if($data['email'] != $email)
                {
                    // Yup. Add it to the query
                    array_push($query, "SET user_email='".sqlite_escape_string($data['email'])."'");
                }
                // Are we changing a display name?
                if($data['displayname'] != $displayname)
                {
                    // Yup. Add it to the query
                    array_push($query, "SET display_name='".sqlite_escape_string($data['displayname'])."'");
                }
                // Are we changing a role?
                if($data['role'] != $role && permissions('EditOtherUsers'))
                {
                    // Yup. Add it to the query
                    array_push($query, "SET user_role='".sqlite_escape_string($data['role'])."'");
                }
                if(!is_null($response['response']))
                {
                    return $response;
                }
                // Go, query, go!
                $sql_user_update = @$this->dbh->exec("UPDATE users ".implode(',', $query)." WHERE user_id=".(int)$data['uid']);
                // Did it work?
                if($sql_user_update == 0)
                {
                    $response['response'] = "couldn't save data to the database.";
                }
                else
                {
                    // Yes!
                    $response['result'] = "success";
                    $response['response'] = "profile edited.";
                }
            }
            else
            {
                $response['response'] = "current password incorrect.";
            }
        }
        else
        {
            $response['response'] = "not allowed to edit this profile.";
        }
        // Return the news, whether it's good or bad
        return $response;
    }
}

?>
