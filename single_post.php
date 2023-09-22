<?php


include("classes/autoload.php");


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
# posting starts here
if ($_SERVER['REQUEST_METHOD'] == "POST") {


    $post = new Post();
    $id = $_SESSION['mybook_userid'];
    $result = $post->create_post($id, $_POST, $_FILES);

    if ($result == "") {
        header("Location: " . ROOT . "single_post/$URL[1]");
        die;
    } else {
        echo "<div style ='text-align:center; font-size:12px; color:white; background-color:grey;'>";
        echo "<br>The following occured<br><br>";
        echo $result;
        echo "</div>";
    }
}

$Post = new Post();
$ROW = false;

$is_group_post = false;
$ERROR = "";
if (isset($URL[1])) {
    $ROW = $Post->get_one_post($URL[1]);
    if ($ROW['owner'] > 0) {
        $user_class = new User();
        $group_data = $user_class->get_user($ROW['owner']);
        if ($group_data['type'] = "group") {
            $is_group_post = true;
        }
    }
} else {
    $ERROR = "No post was found!";
}





?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Single Post | Mybook</title>
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

    <!--post area-->
    <?php if (!($is_group_post && $group_data['group_type'] == 'private' && !group_access($_SESSION['mybook_userid'], $group_data, 'member'))) : ?>

        <div style="width:800px; margin:auto;min-height:400px;">

            <!-- below cover area -->
            <div style="display:flex;">


                <!-- posts area-->
                <div style="min-height:400px; flex:2.5;padding:20px;padding-right:0px;">

                    <div style="border:solid thin #aaa;padding:10px; background-color:white;">


                        <?php

                        // check if this is from a notification
                        if (isset(
                            $URL[2]
                        )) {
                            notification_seen($URL[2]);
                        }
                        $user = new User();
                        $image_class = new Image();

                        if (is_array($ROW)) {
                            $ROW_USER = $user->get_user($ROW['userid']);
                            if ($ROW['parent'] == 0)
                                include("post.php");
                            else {
                                $COMMENT = $ROW;
                                include("comment.php");
                            }
                        }

                        ?>
                        <?php if (isset($ROW['parent']) && $ROW['parent'] == 0) :  ?>

                            <br style="clear:both;">
                            <?php if (!($is_group_post && $group_data['group_type'] == 'public' && !group_access($_SESSION['mybook_userid'], $group_data, 'member'))) : ?>

                                <div style="border:solid thin #aaa;padding:10px; background-color:white;">

                                    <form method="post" enctype="multipart/form-data">
                                        <textarea name="post" placeholder="Post a comment"></textarea>
                                        <input type="hidden" name="parent" value="<?php echo $ROW['postid']; ?>">
                                        <input type="file" name="file">
                                        <input type="submit" id="post_button" value="Post" style="cursor:pointer;">
                                        <br>
                                    </form>
                                </div>
                            <?php endif; ?>

                        <?php else: ?>
                            <a href="<?= ROOT ?>single_post/<?= $ROW['parent']; ?>">
                                <input id="post_button" style="width:auto;float:left;cursor:pointer;" type="button" value="Back to main post">
                            </a>
                        <?php endif; ?>
                        <?php


                        if (isset($ROW['postid'])) {
                            $comments = $Post->get_comments($ROW['postid']);

                            if (is_array($comments)) {
                                foreach ($comments as $COMMENT) {
                                    $ROW_USER = $user->get_user($COMMENT['userid']);
                                    include("comment.php");
                                }
                            }
                        }
                        $pg = pagination_link();
                        ?>

                        <?php if (isset($ROW['parent']) && $ROW['parent'] == 0) : ?>
                            <a href="<?= $pg['next_page'] ?>">
                                <input type="button" id="post_button" value="Next Page" style="cursor:pointer; float:right;width:150px;">
                            </a>
                            <a href="<?= $pg['prev_page'] ?>">
                                <input type="button" id="post_button" value="Previous Page" style="cursor:pointer;float:left;width:150px;">
                            </a>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
        </div>

        <!-- end post -->
    <?php else : ?>
        <div style="padding:1em;text-align:center;background-color:white;">Sorry, you do not have access to this content</div>
    <?php endif; ?>
</body>

</html>