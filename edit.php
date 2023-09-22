<?php


include("classes/autoload.php");


$login = new Login();
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

$ERROR = "";
if (isset($URL[1])) {
    $Post = new Post();
    $ROW = $Post->get_one_post($URL[1]);
    if (!$ROW) {
        $ERROR = "No such post was found! 5";
    } else {
        if ($ROW['userid'] != $_SESSION['mybook_userid']) {
            $ERROR = "Access denied! you cant delete this post!";
        }
    }
} else {
    $ERROR = "No such post was found!";
}
if (isset($_SERVER['HTTP_REFERER']) && !strstr($_SERVER['HTTP_REFERER'], "/edit/")) {
    $_SESSION['return_to'] = $_SERVER['HTTP_REFERER'];
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    
    $Post->edit_post($_POST, $_FILES);
    header("Location: " . $_SESSION['return_to']);
    die;
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete | Mybook</title>
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

                    <form method="post" enctype="form/multipart/form-data">
                        <?php


                        if ($ERROR != "") {
                            echo $ERROR;
                        } else {
                            echo "Edit Post<br><BR>";
                            echo '  
                            <textarea name="post" placeholder="Whats on your mind?">' . $ROW['post'] . '</textarea>
                            <input type = "file" name = "file">';



                            echo "<input type='hidden' name='postid' value='$ROW[postid]'>";
                            echo "<input type='submit' id='post_button' value='Save' style='cursor:pointer;'>";

                            if (file_exists($ROW['image'])) {
                                $image_class = new Image();

                                $ext = pathinfo($ROW['image'], PATHINFO_EXTENSION);
                                $ext = strtolower($ext);

                                if ($ext == "jpg" || $ext == "jpeg") {
                                    $post_image = $image_class->get_thumb_post($ROW['image']);
                                    echo "<br><br><div style='text-align:center;'><img src = '".ROOT.$post_image."' style = 'width:50%'></div>";
                                } elseif ($ext == "mp4") {

                                    echo "<video controls style = 'width:100%;' autoplay>
                
                                    <source src = '" . ROOT . "$ROW[image]'>

                                    </video>";
                                }
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