<?php
/*
    LightBlog, a PHP/SQLite blogging platform
    Copyright (C) 2008-2016 The LightBlog Team.
    
    Sources/User.php

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
    Function: user_init

    If the current user is actually logged in, this function will load their
    information for use by their respective functions.

    Parameters:
        none

    Returns:
        void - Nothing is returned by this function.
*/
function user_init()
{
    // Perhaps they are logged in?
    // In which case, we can let the User class do the work for us :-).
    user();
}

/*
    Function: user_login

    Processes the log in request and sets the proper cookies and session
    information to keep them logged in if it is a success.

    Parameters:
        array $options

    Returns:
        array
*/
function user_login($options)
{
    global $dbh;

    loadLanguage('login');

    $messages = array();

    // We need their user name.
    if(empty($options['username']))
    {
        $messages[] = l('Please enter a user name.');
    }
    // Now we need to see if the user exists.
    else
    {
        $user_metadata = $dbh->prepare("
            SELECT
                user_id, user_pass, user_ip
            FROM users
            WHERE LOWER(user_name) = ?
            LIMIT 1
        ");

        $user_metadata->bindValue(1, strtolower($options['username']), PDO::PARAM_STR);

        if(!$user_metadata->execute())
        {
            // We didn't find their user name, but we won't tell them whether it
            // was due to the password being wrong or their user name :P.
            $messages[] = l('Incorrect user name or password.');
        }
        else
        {
            list($user_id, $user_pass, $user_ip) = $user_metadata->fetch(PDO::FETCH_NUM);
        }
    }

    // Make sure we don't have any messages yet.
    if(count($messages) == 0)
    {
        // We need a password.
        if(empty($options['password']))
        {
            $messages[] = l('Please enter a password.');
        }
        // On top of that, it needs to be right!
        elseif(!password_verify($options['password'], $user_pass))
        {
            $messages[] = l('Incorrect user name or password.');
        }
        // They have successfully proved they are who they say they are
        // Now update their password if necessary then update their IP, perhaps.
        else
        {
            // Destroy old session
            session_regenerate_id(true);
            
            $dbh->beginTransaction();
            
            // See if password needs to be rehashed; if so, do that and update the hash in the db
            if(password_needs_rehash($user_pass, PASSWORD_DEFAULT))
            {
                $user_pass = password_hash($options['password'], PASSWORD_DEFAULT);
                $pass_update = $dbh->prepare("UPDATE users SET user_pass = :pass WHERE user_id = :id");
                
                $pass_update->bindParam(":pass", $user_pass, PDO::PARAM_STR);
                $pass_update->bindValue(":id", $user_id, PDO::PARAM_STR);
                $pass_update->execute();
            }
            
            $ip_update = $dbh->prepare("UPDATE users SET user_ip = :ip_addr WHERE user_id = :id");

            $ip_update->bindValue(":ip_addr", user()->ip());
            $ip_update->bindValue(":id", $user_id, PDO::PARAM_STR);
            $ip_update->execute();
            
            $dbh->commit();
            
            // TODO: remember me

            // Along with some basic session information.
            $_SESSION['user_id'] = $user_id;
            $_SESSION['user_pass'] = $user_pass;

            // Alright, now it is time to take them, somewhere...
            if(!empty($options['redir_to']) && utf_strpos($options['redir_to'], '/') !== 0 && utf_strpos($options['redir_to'], '://') === false)
            {
                redirect(get_bloginfo('url'). $options['redir_to']);
            }
            else
            {
                redirect(get_bloginfo('url'). 'admin/dashboard.php');
            }

            $messages[] = l('An unknown error occurred.');
        }
    }

    return $messages;
}

/*
    Function: user_name_allowed

    Determines whether the user name is allowed (as in, it isn't in use by
    another user [checking both log in and display names]).

    Parameters:
        string $name
        int $id - The user's ID (to exclude them from the check).

    Returns:
        bool - Returns true if the name is allowed, false if not.
*/
function user_name_allowed($name, $id = 0)
{
    global $dbh;

    // Alright, let's check!
    $allowed = $dbh->prepare("
        SELECT
            COUNT(*)
        FROM users
        WHERE (LOWER(user_name) = LOWER(:username) OR LOWER(display_name) = LOWER(:username)) AND user_id != :id
        LIMIT 1
    ");

    $allowed->bindValue(":username", utf_htmlspecialchars($name));
    $allowed->bindValue(":id", $id, PDO::PARAM_INT);

    $allowed->execute();

    // If there are no rows, they're free to have at it!
    if($allowed->fetchColumn() == 0)
    {
        return true;
    }
    else
    {
        return false;
    }
}

/*
    Function: user_email_allowed

    Determines whether the email address is allowed (as in, it isn't in use by
    another user and appears to be a valid email address).

    Parameters:
        string $email
        int $id - The user's ID (to exclude them from the check).

    Returns:
        bool - Returns true if the email address is allowed, false if not.
*/
function user_email_allowed($email, $id = 0)
{
    global $dbh;

    // First, check the email address with a regular expression.
    // This ONLY checks to make sure the email is syntactically correct.
    if(!preg_match('~^[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$~i', $email))
    {
        return false;
    }

    // Now to check the database.
    $allowed = $dbh->prepare("
        SELECT
            COUNT(*)
        FROM users
        WHERE LOWER(user_email) = LOWER(:email) AND user_id != :id
        LIMIT 1");

    $allowed->bindValue(":email", utf_htmlspecialchars($email), PDO::PARAM_STR);
    $allowed->bindValue(":id", $id, PDO::PARAM_INT);

    $allowed->execute();

    // If there are no rows, they're free to have at it!
    if($allowed->fetchColumn() == 0)
    {
        return true;
    }
    else
    {
        return false;
    }
}

/*
    Class: User

    Contains all the information about the currently logged in user (or guest).
*/
class User
{
    // Variable: id
    // The user's ID.
    private $id;

    // Variable: name
    // The user's name.
    private $name;

    // Variable: password
    // The user's hashed password.
    private $password;

    // Variable: email
    // The user's email address.
    private $email;

    // Variable: displayName
    // The user's display name.
    private $displayName;

    // Variable: role
    // The user's role (an integer).
    private $role;

    /*
        Constructor: __construct

        Checks to see if the current user is logged in and loads their
        information if they are.

        Parameters:
            none
    */
    public function __construct()
    {
        global $dbh;

        // Set everything to 0 or null.
        $this->id = 0;
        $this->name = null;
        $this->password = null;
        $this->email = null;
        $this->displayName = null;
        $this->role = 0;

        // Maybe they're logged in?
        if(session_status() == PHP_SESSION_ACTIVE)
        {
            $user_id = $_SESSION['user_id'];
            $user_pass = $_SESSION['user_pass'];
            
            // Make sure that their user ID is valid
            // Password hash cannot be intrinsically validated
            if((int)$user_id > 0)
            {
                // Now we need to see if they can log in.
                $user_metadata = $dbh->prepare("
                    SELECT
                        user_id, user_name, user_pass, user_email, display_name, user_role
                    FROM users
                    WHERE user_id = :id AND user_pass = :pass
                    LIMIT 1
                ");

                $user_metadata->bindParam(":id", $user_id, PDO::PARAM_INT);
                $user_metadata->bindParam(":pass", $user_pass, PDO::PARAM_STR);

                $user_metadata->execute();

                $row = $user_metadata->fetch(PDO::FETCH_ASSOC);

                // Did we find anything?
                if(!empty($row))
                {
                    // Now set their information.
                    $this->id = (int)$row['user_id'];
                    $this->name = $row['user_name'];
                    $this->password = $row['user_pass'];
                    $this->email = $row['user_email'];
                    $this->displayName = $row['display_name'];
                    $this->role = (int)$row['user_role'];

                    $_SESSION['user_id'] = $this->id;
                    $_SESSION['user_pass'] = $this->password;
                }
            }

            // They showed signs of activity just now, they're not dead!
            $_SESSION['last_activity'] = time();
        }
    }

    /*
        Function: id

        Returns the user's ID.

        Parameters:
            none

        Returns:
            int - Returns the user's ID (if they are a guest, it will be 0).
    */
    public function id()
    {
        return $this->id;
    }

    /*
        Function: userName

        Returns the user's name they use to log in.

        Parameters:
            none

        Returns:
            string - Returns the users name if they're logged in, otherwise
                             'Guest.'
    */
    public function userName()
    {
        return $this->is_logged() ? $this->name : l('Guest');
    }

    /*
        Function: name

        Returns the user's name (either their display name if set or log in name
        if not.

        Parameters:
            none

        Returns:
            string - Returns their user name if they're logged in, but 'Guest' if
                             they are, take a guess, a guest.
    */
    public function name()
    {
        return $this->is_logged() ? (utf_strlen($this->displayName()) > 0 ? $this->displayName() : $this->userName()) : l('Guest');
    }

    /*
        Function: password

        Returns the user's hashed password.

        Parameters:
            none

        Returns:
            string - Returns their hashed password if they're logged in, but null
                             if not.
    */
    public function password()
    {
        return $this->is_logged() ? $this->password : null;
    }

    /*
        Function: email

        Returns the user's email address.

        Parameters:
            none

        Returns:
            string - Returns their email if they're logged in, but null if not.
    */
    public function email()
    {
        return $this->is_logged() ? $this->email : null;
    }

    /*
        Function: displayName

        Returns the user's display name.

        Parameters:
            none

        Returns:
            string - Returns their display name if they're logged in, but 'Guest'
                             if they're a guest.
    */
    public function displayName()
    {
        return $this->is_logged() ? $this->displayName : l('Guest');
    }

    /*
        Function: role

        Returns the user's role.

        Parameters:
            none

        Returns:
            int - Returns the user's role ID (0 if they're a guest).
    */
    public function role()
    {
        return $this->role;
    }

    /*
        Function: is_logged

        Returns whether the user is logged in.

        Parameters:
            none

        Returns:
            bool - Returns true if they're logged in, false if not.
    */
    public function is_logged()
    {
        return $this->id() > 0;
    }

    /*
        Function: is_guest

        Returns whether the user is a guest.

        Parameters:
            none

        Returns:
            bool - Returns true if they're a guest, false if not.
    */
    public function is_guest()
    {
        return !$this->is_logged();
    }

    /*
        Function: ip

        Returns the user's IP.

        Parameters:
            none

        Returns:
            string - Returns their IP.
    */
    public function ip()
    {
        return isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
    }

    /*
        Function: csrf_token

        Returns the user's current CSRF (Cross-Site Request Forgery) token.

        Parameters:
            bool $urlencode - Whether to URL encode the CSRF token. Defaults to
                                                false.

        Returns:
            string
    */
    public function csrf_token($urlencode = false)
    {
        // Do they not have a token yet? Do we need to regenerate it?
        if(empty($_SESSION['csrf_token']) || empty($_SESSION['last_activity']) || ($_SESSION['last_activity'] + 86400) < time())
        {
            $_SESSION['csrf_token'] = randomString(mt_rand(40, 60));
        }

        return !empty($urlencode) ? urlencode($_SESSION['csrf_token']) : $_SESSION['csrf_token'];
    }
}

/*
    Function: user

    This function will return the current <User> object.

    Parameters:
        none

    Returns:
        object
*/
function user()
{
    if(!isset($GLOBALS['user_obj']) || !is_a($GLOBALS['user_obj'], 'User'))
    {
        $GLOBALS['user_obj'] = new User();
    }

    return $GLOBALS['user_obj'];
}
?>
