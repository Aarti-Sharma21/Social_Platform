<?php


include("classes/autoload.php");


$login = new Login();
$user_data = $login->check_login($_SESSION['mybook_userid']);

$USER = $user_data;
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $profile = new Profile();
    $profile_data = $profile->get_profile($_GET['id']);

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
        header("Location:" . ROOT. "home");
        die;
    } else {
        echo "<div style ='text-align:center; font-size:12px; color:white; background-color:grey;'>";
        echo "<br>The following occured<br><br>";
        echo $result;
        echo "</div>";
    }
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Timeline | Mybook</title>
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

            <!-- freinds area-->
            <div style="min-height:200px; flex:1;">
                <div id="friends_bar">
                    <?php
                    $image = "images/user_male.jpg";
                    if ($user_data['gender'] == "Female") {
                        $image = "images/user_female.jpg";
                    }
                    if (file_exists($user_data['profile_image'])) {
                        $image =  $image_class->get_thumb_profile($user_data['profile_image']);
                    } ?>
                    <img src="<?php echo ROOT . $image ?>" id="profile_pic"><br>
                    <a href="<?=ROOT?>profile" style="text-decoration:none;"><?php echo $user_data['first_name'] . "<br>" . $user_data['last_name']; ?></a>
                </div>
            </div>

            <!-- posts area-->
            <div style="min-height:400px; flex:2.5;padding:20px;padding-right:0px;">
                <div style="border:solid thin #aaa;padding:10px; background-color:white;">
                    <form method="post" enctype="multipart/form-data">
                        <textarea name="post" placeholder="Whats on your mind?"></textarea>
                        <input type="file" name="file">
                        <input type="submit" id="post_button" value="Post" style="cursor:pointer;">
                        <br>
                    </form>
                </div>
                <!-- posts-->
                <div id="post_bar">
                    
                    <?php
                    $page_number = 1;
                    if (isset($_GET['page'])) {
                        $page_number = (int)$_GET['page'];
                    }
                    if ($page_number < 1) {
                        $page_number = 1;
                    }

                    
                    $limit = 10;
                    $offset = ($page_number - 1) * $limit;

                    $DB = new Database();
                    $user_class = new User();
                    $image_class = new Image();
                    $followers = $user_class->get_following($_SESSION['mybook_userid'], "user");
                    $follower_ids = false;
                    if (is_array($followers)) {
                        $follower_ids = array_column($followers, "userid");
                        $follower_ids = implode("','", $follower_ids);
                    }
                    if ($follower_ids) {
                        $myuserid = $_SESSION['mybook_userid'];
                        $sql = "select * from posts where parent = 0 and owner = 0 and (userid = '$myuserid' || userid in('" . $follower_ids . "')) order by id desc limit $limit offset $offset ";
                        $posts = $DB->read($sql);
                    } else {
                        $myuserid = $_SESSION['mybook_userid'];
                        $sql = "select * from posts where userid = '$myuserid' limit 30 ";
                        $posts = $DB->read($sql);
                    }
                    if (isset($posts) && $posts) {
                        foreach ($posts as $ROW) {
                            $user = new User();
                            $ROW_USER = $user->get_user($ROW['userid']);
                            include("post.php");
                        }
                    }
                    // get current url
                    $pg = pagination_link();
                    ?>
                    <a href="<?= $pg['next_page'] ?>">
                        <input type="button" id="post_button" value="Next Page" style="cursor:pointer; float:right;width:150px;">
                    </a>
                    <a href="<?= $pg['prev_page'] ?>">
                        <input type="button" id="post_button" value="Previous Page" style="cursor:pointer;float:left;width:150px;">
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>