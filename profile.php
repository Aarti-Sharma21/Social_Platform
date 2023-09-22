<?php

include("classes/autoload.php");

$login = new Login();
$_SESSION['mybook_userid'] = isset($_SESSION['mybook_userid'])?$_SESSION['mybook_userid']:0;
$user_data = $login->check_login($_SESSION['mybook_userid'],false);

$USER = $user_data;
if (isset($URL[1]) && is_numeric($URL[1])) {
    $profile = new Profile();
    $profile_data = $profile->get_profile($URL[1]);

    if (is_array($profile_data)) {
        $user_data = $profile_data[0];
    }
}

# posting starts here
if ($_SERVER['REQUEST_METHOD'] == "POST") {

    include("change_image.php");

    if (isset($_POST['first_name'])) {
        $setting_class = new Settings();
        $setting_class->save_settings($_POST, $_SESSION['mybook_userid']);
    } else if (isset($_POST['post'])) {
        $post = new Post();
        $id = $_SESSION['mybook_userid'];
        $result = $post->create_post($id, $_POST, $_FILES);

        if ($result == "") {
            header("Location: ".ROOT."profile");
            die;
        } else {
            echo "<div style ='text-align:center; font-size:12px; color:white; background-color:grey;'>";
            echo "<br>The following occured<br><br>";
            echo $result;
            echo "</div>";
        }
    }
}

#collect posts
$post = new Post();
$id = $user_data['userid'];

$posts = $post->get_posts($id);

# collect friends
$user = new User();
$friends = $user->get_following($user_data['userid'], "user");

$image_class = new Image();

 // check if this is from a notification
 if (isset($URL[2])) {
    notification_seen($URL[2]);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile | Mybook</title>
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

        #textbox {
            width: 100%;
            height: 20px;
            border-radius: 5px;
            border: none;
            padding: 4px;
            font-size: 14px;
            border: solid thin grey;
            margin: 10px;


        }

        #profile_pic {
            width: 150px;
            margin-top: -200px;
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
            background-color: white;
            min-height: 400px;
            margin-top: 20px;
            color: #aaa;
            padding: 8px;
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
            min-width: 50px;
            cursor: pointer;
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

    <?php
    include("header.php");
    ?>

    <!-- Change profile image area -->
    <div id="change_profile_image" style="position:absolute;width:100%;height:100%; background-color:#000000aa;display:none;">
        <div style="max-width:600px;margin:auto;min-height: 400px;flex:2.5;padding:20px;padding-right:0px; ">
            <form method="post" action="<?=ROOT?>profile/profile" enctype="multipart/form-data">
                <div style="border:solid thin #aaa;padding:10px;background-color: white;">
                    <input type="file" name="file"><br>

                    <input type="submit" id="post_button" value="change" style="width:120px;"><br>
                    <br>
                    <div style="text-align:center;">
                        <br><br>
                        <?php


                        echo "<img src='" . ROOT . "$user_data[profile_image]'style = 'max-width:500px;' >";

                        ?>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- Change cover image area -->
    <div id="change_cover_image" style="position:absolute;width:100%;height:100%; background-color:#000000aa;display:none;">
        <div style="max-width:600px;margin:auto;min-height: 400px;flex:2.5;padding:20px;padding-right:0px; ">
            <form method="post" action="<?=ROOT?>profile/cover" enctype="multipart/form-data">
                <div style="border:solid thin #aaa;padding:10px;background-color: white;">
                    <input type="file" name="file"><br>

                    <input type="submit" id="post_button" value="change" style="width:120px;"><br>
                    <br>
                    <div style="text-align:center;">
                        <br><br>
                        <?php


                        echo "<img src='" . ROOT . "$user_data[cover_image]'style = 'max-width:500px;' >";

                        ?>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!--cover area-->
    <div style="width:800px; margin:auto;min-height:400px;">
        <div style="background-color:white; text-align:center; color:#405d9b">
            <?php
            $image = "images/cover_image.jpg";
            if (file_exists($user_data['cover_image'])) {
                $image = $image_class->get_thumb_cover($user_data['cover_image']);
            } 
            ?>
            <img src="<?php echo ROOT. $image ?>" style="width:100%;">


            <span style="font-size:12px;">
                <?php
                $image = "images/user_male.jpg";
                if ($user_data['gender'] == "Female") {
                    $image = "images/user_female.jpg";
                }
                if (file_exists($user_data['profile_image'])) {
                    $image =  $image_class->get_thumb_profile($user_data['profile_image']);
                } 
                ?>
                <img src="<?php echo ROOT . $image ?>" id="profile_pic"><br>

               

                <?php if(i_own_content($user_data)): ?>
                <a  href="<?=ROOT?>change_profile_image/profile/<?php echo $user_data['userid']; ?>" style="color:#f0f; text-decoration:none;">Change Profile Image</a> |
                <a href="<?=ROOT?>change_profile_image/cover/<?php echo $user_data['userid']; ?>" style="color:#f0f; text-decoration:none;">Change Cover Image</a>
            <?php endif; ?>
            </span>
            <br>
            <div style="font-size:20px;color:black;">
                <a href="<?=ROOT?>profile/<?php echo $user_data['userid']; ?>">
                    <?php echo $user_data['first_name'] . " " . $user_data['last_name'];  ?>
                    <br> <span style="font-size:13px;">@    <?=$user_data['tag_name']?></span>
                </a>
                <?php
                $mylikes = "";
                if ($user_data['likes'] > 0) {
                    $mylikes = "(" . $user_data['likes'] . "Followers)";
                }
                ?>
                <br>
                <a href="<?=ROOT?>like/user/<?php echo $user_data['userid'] ?>">
                    <input type="button" id="post_button" value="Follow <?php echo $mylikes ?>" style="margin-right:10px; background-color:#9b409a;width:auto;">
                </a>

                <?php if ($user_data['userid'] == $_SESSION['mybook_userid']): ?>
                
                
                <a href="<?=ROOT?>messages">
                    <input type="button" id="post_button" value="Messages" style="margin-right:10px; background-color:#1b9186;width:auto;">
                </a>
                <?php else: ?>
                    <a href="<?=ROOT?>messages/new/<?=$user_data['userid']?>">
                        <input type="button" id="post_button" value="Message" style="margin-right:10px; background-color:#1b9186;width:auto;">
                    </a>
                <?php endif; ?>

            </div>
            <br>
            <br>



            <a href="<?=ROOT?>home">
                <div id="menu_buttons">Timeline</div>
            </a>
            <a href="<?=ROOT?>profile/<?php echo $user_data['userid'] ?>/about">
                <div id="menu_buttons">About</div>
            </a>
            <a href="<?=ROOT?>profile/<?php echo $user_data['userid'] ?>/following">
                <div id="menu_buttons">Following</div>
            </a>
            <a href="<?=ROOT?>profile/<?php echo $user_data['userid'] ?>/followers">
                <div id="menu_buttons">Followers</div>
            </a>
            <a href="<?=ROOT?>profile/<?php echo $user_data['userid'] ?>/photos">
                <div id="menu_buttons">Photos</div>
            </a>
            
            <?php if ($user_data['userid'] == $_SESSION['mybook_userid']) : ?> 
                <a href="<?=ROOT?>profile/<?php echo $user_data['userid'] ?>/groups">
                    <div id="menu_buttons">Groups</div>
                </a>
                <a href="<?=ROOT?>profile/<?php echo $user_data['userid'] ?>/settings">
                <div id="menu_buttons">Settings</div>
            </a>
            <?php endif; ?>
        </div>

        <!-- below cover area -->
        <?php
        $section = "default";
        if (isset($URL[2])) {
            $section = $URL[2];
        }
        if ($section == "default") {
            include("profile_content_default.php");
        } elseif ($section == "photos") {
            include("profile_content_photos.php");
        } elseif ($section == "followers") {
            include("profile_content_followers.php");
        } elseif ($section == "following") {
            include("profile_content_following.php");
        } elseif ($section == "about") {
            include("profile_content_about.php");
        } elseif ($section == "settings") {
            include("profile_content_settings.php");
        }
        elseif ($section == "groups") {
            include("profile_content_groups.php");
        }
        ?>

</body>

</html>

<script type="text/javascript">
    function show_change_profile_image(event) {
        // event.preventDefault();
        var profile_image = document.getElementById("change_profile_image");
        profile_image.style.display = "block";
    }

    function hide_change_profile_image() {
        var profile_image = document.getElementById("change_profile_image");
        profile_image.style.display = "none";
    }

    function show_change_cover_image(event) {
        // event.preventDefault();
        var profile_image = document.getElementById("change_cover_image");
        profile_image.style.display = "block";
    }

    function hide_change_cover_image() {
        var profile_image = document.getElementById("change_cover_image");
        profile_image.style.display = "none";
    }

    window.onkeydown = function(key) {
        if (key.keyCode == 27) {
            // esc key was pressed
            hide_change_profile_image();
            hide_change_cover_image();
        }
    }
</script>