<?php

class Signup{
    
    private $error = "";
    public function evaluate($data){ # By default they are public only
        foreach($data as $key => $value){
            if(empty($value)){
                $this->error .= $key . " is empty!<br>";
            }

            if($key == 'email'){
                if (!preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/",$value)){
                    $this->error .= "Invalid Email address!<br>";
                }
            }

            if($key == 'first_name'){
                if (is_numeric($value)){
                    $this->error .= "first name can't be a number<br>";
                }

                if (strstr($value, " ")){
                    $this->error .= "first name can't have spaces<br>";
                }
            }

            if($key == 'last_name'){
                if (is_numeric($value)){
                    $this->error .= "last name can't be a number<br>";
                }

                if (strstr($value, " ")){
                    $this->error .= "last name can't have spaces<br>";
                }
            }
        }
        $DB = new Database();

        //check tag name
        $data['tag_name'] = strtoLower($data['first_name'] . $data['last_name']);
        $sql = "select id from users where tag_name = '$data[tag_name]' limit 1";
        $check = $DB->read($sql);
        
        while(is_array($check)){
            $data['tag_name'] = strtoLower($data['first_name'] . $data['last_name']).rand(0,9999);
            $sql = "select id from users where tag_name = '$data[tag_name]' limit 1";
            $check = $DB->read($sql);
        }

        $data['userid']= $this->create_userid();
        //check userid
        $sql = "select id from users where userid = '$data[userid]' limit 1";
        $check = $DB->read($sql);
        
        while(is_array($check)){
            $data['userid']= $this->create_userid();
            $sql = "select id from users where userid = '$data[userid]' limit 1";            
            $check = $DB->read($sql);
        }

        //check email
        $sql = "select id from users where email = '$data[email]' limit 1";
        $check = $DB->read($sql);
        
       if(is_array($check)){
        $this->error .= "Another user is already using this email<br>";
      
        }


        if($this->error == ""){
            #no error
            $this->create_user($data);
        }
        else{
            return $this->error;
        }
    }

    public function create_user($data){

        $first_name = ucfirst($data['first_name']);
        $last_name = ucfirst($data['last_name']);
        $gender = $data['gender'];
        $email = addslashes($data['email']);
        $password = hash("sha1",addslashes($data['password']));
        $userid= $data['userid'];
        $tag_name= $data['tag_name'];
        $date = date("Y-m-d H:i:s");
        $type = 'profile';

        # create these
        $url_address= strtoLower($first_name) . "." . strtoLower($last_name);

        $query = "insert into users(userid, first_name, last_name, gender, email, password, url_address,tag_name,date,type) values('$userid', '$first_name', '$last_name', '$gender', '$email', '$password', '$url_address','$tag_name','$date','$type')";

        $DB = new Database();
        $DB->save($query);
    }

    private function create_userid(){
        $length = rand(4,19);
        $number = "";
        for( $i = 0; $i < $length; $i++){
            $new_rand = rand(0,9);
            $number .= $new_rand;
        }

        return $number;
    }


}