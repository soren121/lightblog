<?php
/*********************************************

    LightBlog 0.9
    SQLite blogging platform

    Sources/Processors/Processor.Profile.php

    ©2008-2014 The LightBlog Team. All
    rights reserved. Released under the
    GNU General Public License 3. For
    all licensing information, please
    see the LICENSE.txt document
    included in this distribution.

*********************************************/

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
