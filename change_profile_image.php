<?php

include("classes/autoload.php");

$login = new Login();
$image_class = new Image();
$user_data = $login->check_login($_SESSION['mybook_userid']);

##############################################################################
if (isset($URL[2]))
{
    $profile = new Profile();
    $profile_data = $profile->get_profile($URL[2]);
    
    if(is_array($profile_data)){
    $user_data = $profile_data[0];
    }
    
}

#################################################################################

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    if (isset($_FILES['file']['name']) && $_FILES['file']['name'] != "") {
        if ($_FILES['file']['type'] == "image/jpeg") {
            $allowed_size = 3 * 1024 * 1024;
            if ($_FILES['file']['size'] <= $allowed_size) {

                #everything is fine
                $folder = "uploads/" . $user_data['userid'] . "/";

                #create folder
                if (!file_exists($folder)) {
                    mkdir($folder, 0777, true);
                }

                $image = new Image();
                $filename = $folder .  $image->generate_filename(15) . ".jpg";
                move_uploaded_file($_FILES['file']['tmp_name'], $filename);

                $change = "profile";

                # check for mode
                if (isset($URL[1])) {
                    $change = $URL[1];
                }
                if ($change == "cover") {
                    if (file_exists($user_data['cover_image']))
                        unlink($user_data['cover_image']);
                    $image->resize_image($filename, $filename, 1500, 1500);
                } else {
                    if (file_exists($user_data['profile_image']))
                        unlink($user_data['profile_image']);
                    $image->resize_image($filename, $filename, 1500, 1500);
                }
                if (file_exists($filename)) {
                    $userid = $user_data['userid'];

                    if ($change == "cover") {
                        $query = "update users set cover_image = '$filename' where userid = '$userid' limit 1";
                        $_POST['is_cover_image'] = 1;
                    } else {
                        $_POST['is_profile_image'] = 1;
                        $query = "update users set profile_image = '$filename' where userid = '$userid' limit 1";
                    }
                    $DB = new Database();
                    $DB->save($query);

                    # create a post
                    $post = new Post();
                    $post->create_post($userid, $_POST, $filename);

                    header("Location: " . ROOT . "profile");
                    die;
                }
            } 
            else {
                echo "<div style ='text-align:center; font-size:12px; color:white; background-color:grey;'>";
                echo "Only images of size 3MB or lower are alowwed";
                echo "</div>";
            }
        } else {
            echo "<div style ='text-align:center; font-size:12px; color:white; background-color:grey;'>";
            echo "Only images of jpeg type are alowwed";
            echo "</div>";
        }
    } else {
        echo "<div style ='text-align:center; font-size:12px; color:white; background-color:grey;'>";
        echo "Please add a valid image!";
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
    <title>Change Profile Image | Mybook</title>
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

        #post_button {
            float: right;
            background-color: #405d9b;
            border: none;
            color: white;
            font-size: 14px;
            padding: 4px;
            border-radius: 2px;
            width: 100px;
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
                <form method="post" enctype="multipart/form-data">
                    <div style="border:solid thin #aaa;padding:10px; background-color:white;">
                        <input type="file" name="file">
                        <input type="submit" id="post_button" value="Change" style="cursor:pointer;">
                        <br>
                        <div style="text-align:center;">
                            <br><br>
                            <?php
                            if (isset($URL[1]) && $URL[1] == "cover") {
                                
                                echo "<img src='".ROOT.$user_data['cover_image']."' style = 'max-width:500px;' >";
                            } else {
                                echo "<img src='".ROOT.$user_data['profile_image']."' style = 'max-width:500px;' >";
                            }
                            ?>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>