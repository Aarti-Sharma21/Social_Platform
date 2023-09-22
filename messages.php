<?php


include("classes/autoload.php");
$image_class = new Image();

$ERROR = "";
$login = new Login();
$user_data = $login->check_login($_SESSION['mybook_userid']);

$USER = $user_data;
if (isset($URL[1]) && is_numeric($URL[1])) {
    $profile = new Profile();
    $profile_data = $profile->get_profile($URL[1]);

    if (is_array($profile_data)) {
        $user_data = $profile_data[0];
    }
}
$msg_class = new Messages();

//new message//check if thread already exists

if (isset($URL[1]) && $URL[1] == "new") {
    $old_thread = $msg_class->read($URL[2]);

    if (is_array($old_thread)) {
        //redirect the user
        header("Location: " . ROOT . "messages/read/" . $URL[2]);
        die;
    }
}

if ($ERROR == "" && $_SERVER['REQUEST_METHOD'] == "POST") {


    $user_class = new User();
    if (is_array($user_class->get_user($URL[2]))) {

        $ERROR = $msg_class->send($_POST, $_FILES, $URL[2]);
        header("Location: " . ROOT . "messages/read/" . $URL[2]);
        die;
    } else {
        $ERROR = "The requested user could not be found!";
    }
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages | Mybook</title>
    <style type="text/css">
        #blue_bar {
            height: 50px;
            background-color: #405d9b;
            color: #d9dfeb;
        }

        #search_box {
            width: 400px;
            height: 20px;
            border-radius: 5px;
            border: none;
            padding: 4px;
            font-size: 14px;
            background-image: url(search.png);
            background-repeat: no-repeat;
            background-position: right;
        }

        #profile_pic {
            width: 150px;
            border-radius: 50%;
            border: solid 2px white;
        }

        #menu_buttons {
            width: 100px;
            display: inline-block;
            margin: 2px;
        }

        #friends_img {
            width: 75px;
            float: left;
            margin: 8px;
        }

        #friends_bar {
            min-height: 400px;
            margin-top: 20px;
            color: #aaa;
            padding: 8px;
            text-align: center;
            font-size: 20px;
            color: #405d9b;
        }

        #friends {
            clear: both;
            font-size: 12px;
            font-weight: bold;
            color: #405d9b;
        }

        textarea {
            width: 100%;
            border: none;
            font-family: tahoma;
            font-size: 14px;
            height: 60px;
        }

        #post_button {
            float: right;
            background-color: #405d9b;
            border: none;
            color: white;
            font-size: 14px;
            padding: 4px;
            border-radius: 2px;
            width: 50px;
        }

        #post_bar {
            margin-top: 20px;
            background-color: white;
            padding: 10px;
        }

        #posts {
            padding: 4px;
            font-size: 13px;
            display: flex;
            margin-bottom: 20px;
        }

        #message_left {
            padding: 4px;
            font-size: 13px;
            display: flex;
            margin: 8px;
            width: 60%;
            float: left;
            border-radius: 10px;
        }

        #message_thread {
            padding: 4px;
            font-size: 13px;
            display: flex;
            margin: 8px;
            border-radius: 10px;
        }

        #message_right {
            padding: 4px;
            font-size: 13px;
            display: flex;
            margin: 8px;
            width: 60%;
            float: right;
            border-radius: 10px;
        }
    </style>
</head>

<body style="font-family:tahoma;background-color:#d0d8e4;">

    <!--top bar -->
    <?php
    include("header.php");
    ?>

    <!--cover area-->
    <div style="width:800px; margin:auto;min-height:400px;">

        <!-- below cover area -->
        <div style="display:flex;">


            <!-- posts area-->
            <div style="min-height:400px; flex:2.5;padding:20px;padding-right:0px;">

                <div style="border:solid thin #aaa;padding:10px; background-color:white;">

                    <form method="post" enctype="multipart/form-data">
                        <?php


                        if ($ERROR != "") {
                            echo $ERROR;
                        } else {
                            if (isset($URL[1]) && $URL[1] == "read") {
                                echo "Chatting with:<br><BR>";
                                if (isset($URL[2]) && is_numeric($URL[2])) {
                                    $data = $msg_class->read($URL[2]);
                                    $user = new User();
                                    $FRIEND_ROW = $user->get_user($URL[2]);
                                    include "user.php";

                                        echo "<a href='".ROOT."messages'>";
                                        echo '<input id="post_button" type ="button" style = "margin:4px;width:auto;cursor:pointer;" value = "All messages">';
                                        echo "</a>";
                                    if(is_array($data)) {
                                    echo "<a href='".ROOT."delete/thread/".$data[0]['msgid']."'>";
                                    echo '<input id="post_button" type ="button" style = "margin:4px;width:auto;cursor:pointer;background-color:#b76d40;" value = "Delete Thread">';
                                    echo "</a>";
                                    }


                                    echo '
                                    <div>';
                                    $user = new User();
                                    if(is_array($data))
                                    {
                                    foreach ($data as $MESSAGE) {
                                        $ROW_USER = $user->get_user($MESSAGE['sender']);
                                        if (i_own_content($MESSAGE)) {
                                            include("message_right.php");
                                        } else {
                                            
                                            include("message_left.php");
                                        }
                                        
                                    }
                                }

                                    echo '
                                    </div>';

                                    echo '
                                <div style="border:solid thin #aaa;padding:10px; background-color:white;">

                                
                                    <textarea name="message" placeholder="Write your message here"></textarea>
                                    <input type="file" name="file">
                                    <input type="submit" id="post_button" value="Send" style="cursor:pointer;">
                                    <br>
                                </form>
                            </div>
                            ';
                                } else {
                                    echo "That user could not be found<br><br>";
                                }
                            } else 
                            if (isset($URL[1]) && $URL[1] == "new") {
                                echo "Start New Message with:<br><BR>";
                                if (isset($URL[2]) && is_numeric($URL[2])) {
                                    $user = new User();
                                    $FRIEND_ROW = $user->get_user($URL[2]);
                                    include "user.php";
                                    echo '
                                <div style="border:solid thin #aaa;padding:10px; background-color:white;">

                                
                                    <textarea name="message" placeholder="Write your message here"></textarea>
                                    <input type="file" name="file">
                                    <input type="submit" id="post_button" value="Send" style="cursor:pointer;">
                                    <br>
                                </form>
                            </div>
                            ';
                                } else {
                                    echo "That user could not be found<br><br>";
                                }
                            } else {

                                echo "Messages<br><BR>";
                                $data = $msg_class->read_threads();
                                $user = new User();
                                $me =  esc($_SESSION['mybook_userid']);

                                if(is_array($data))
                                {        
                        foreach($data as $MESSAGE)
                                {
                                    $myid = ($MESSAGE['sender'] == $me) ? $MESSAGE['receiver']:$MESSAGE['sender'];
                                    $ROW_USER = $user->get_user($myid);
                                    include("thread.php");
                                }
                            }
                            else{
                                echo "You have no messages!";
                            }
                                echo "<br style = 'clear:both;'>";
                            }
                        }
                        ?>
                        <br>
                    </form>
                </div>

            </div>
        </div>
    </div>
</body>

</html>