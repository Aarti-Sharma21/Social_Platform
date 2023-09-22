<?php


include("classes/autoload.php");


$login = new Login();
$user_data = $login->check_login($_SESSION['mybook_userid']);
if (isset($_POST['find']))
 {
    $find = addslashes($_POST['find']);
     
    $sql = "select * from users where first_name like '%$find%' || last_name like '%$find%' limit 30";
    $DB  = new Database();
    $results = $DB->read($sql);
     
}

$Post = new Post();
$likes = false;
$ERROR = "";
if (isset($_GET['id']) && isset($_GET['type'])) {
    $likes = $Post->get_likes($_GET['id'], $_GET['type']);
} else {
    $ERROR = "No information was found!";
}




?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search | Mybook</title>
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


                    <?php

                    $User = new User();
                    $image_class = new Image();

                    if (is_array($results)) {
                        foreach ($results as $row) {
                            $FRIEND_ROW = $User->get_user($row['userid']);
                            if($FRIEND_ROW['owner'] == 0)
                            include("user.php");
                            else
                            include("group.inc.php");
                        }
                    } else {
                        echo "no result were found";
                    }


                    ?>
                    <br style = "clear:both;">

                </div>

            </div>
        </div>
    </div>
</body>

</html>