<?php

    class Profile
    {
        function get_profile($id)
        {
            $id = addslashes($id);
            $query = "select * from users where userid = '$id' limit 1";
            $DB = new Database();
            return $DB->read($query);

        }
    }