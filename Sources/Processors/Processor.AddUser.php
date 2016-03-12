<?php
/*
    LightBlog, a PHP/SQLite blogging platform
    Copyright (C) 2008-2016 The LightBlog Team.
    
    Sources/Processors/Processor.AddUser.php

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

class AddUser extends Processor
{
    public function processor($data)
    {
        // Later on, we will send this as JSON to the browser.
        $response = array(
            'result' => 'error',
            'response' => null
        );

        // They need to have permission too.
        if(permissions('AddUsers'))
        {
            $options = array();

            // Make sure they gave us a user name.
            if(empty($data['username']) || utf_strlen(trim($data['username'])) == 0)
            {
                $response['response'] = 'Please enter a user name.';
            }
            // Make sure that the user name isn't in use.
            elseif(!user_name_allowed($data['username']))
            {
                // Uh oh! That's no good.
                $response['response'] = 'That user name is already in use.';
            }
            else
            {
                $options['username'] = utf_htmlspecialchars(trim($data['username']));
            }

            // How about their password?
            if($response['response'] === null && (empty($data['password']) || utf_strlen($data['password']) < 6))
            {
                $response['response'] = 'The password must be at least 6 characters.';
            }
            elseif($response['response'] === null)
            {
                $options['password'] = $data['password'];
            }

            // Make sure they verified the password (no typo!).
            if($response['response'] === null && isset($options['password']) && (empty($data['vpassword']) || $data['vpassword'] != $options['password']))
            {
                $response['response'] = 'The passwords do not match.';
            }

            // Now, the email address!
            if($response['response'] === null && empty($data['email']))
            {
                $response['response'] = 'Please enter an email address.';
            }
            //elseif($response['response'] === null && !user_email_allowed($data['email']))
            //{
            //    $response['response'] = 'That email address is already in use.';
            //}
            elseif($response['response'] === null)
            {
                $options['email'] = utf_htmlspecialchars($data['email']);
            }

            // Now for their display name... That is, if it's set (if it's not, we
            // will use their username.
            $data['displayname'] = !empty($data['displayname']) && utf_strlen(trim($data['displayname'])) > 0 ? trim($data['displayname']) : (isset($data['username']) ? trim($data['username']) : '');
            if($response['response'] === null && utf_strlen($data['displayname']) == 0)
            {
                $response['response'] = 'Please enter a display name.';
            }
            elseif($response['response'] === null && !user_name_allowed($data['displayname']))
            {
                $response['response'] = 'That display name is already in use.';
            }
            elseif($response['response'] === null)
            {
                $options['displayname'] = utf_htmlspecialchars($data['displayname']);
            }

            // Then their role.
            if($response['response'] === null && (empty($data['role']) || !in_array((int)$data['role'], array(1, 2, 3), true)))
            {
                $response['response'] = 'Please select a valid role.';
            }
            elseif($response['response'] === null)
            {
                $options['role'] = (int)$data['role'];
            }

            // Is everything okay? May we create the user now?
            if($response['response'] === null)
            {
                // Now hash their password.
                $options['password'] = password_hash($options['password'], PASSWORD_DEFAULT);

                // Then their IP address.
                $options['ip'] = user()->ip();

                $adduser = $this->dbh->prepare("
                    INSERT INTO
                        users
                            (user_name,
                            user_pass,
                            user_email,
                            display_name,
                            user_role,
                            user_ip,
                            user_activated,
                            user_created)
                    VALUES(
                        ?,
                        ?,
                        ?,
                        ?,
                        ?,
                        ?,
                        1,
                        '".time()."'
                    )
                ");

                // Then sanitize everything.
                foreach ($options as $key => $val)
                {
                    $adduser->bindValue($key, $val);
                }

                // Did we create it?
                if($adduser->execute())
                {
                    // Yes!
                    $response['result'] = 'success';
                    $response['response'] = 'User '. utf_htmlspecialchars(trim($data['username'])). ' created.';
                }
                else
                {
                    $e = $adduser->errorInfo();
                    $response['response'] = $e[2];
                }
            }
        }
        else
        {
            $response['response'] = 'You&#039;re not allowed to add users.';
        }

        return $response;
    }
}

?>
