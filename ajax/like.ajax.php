<?php

$URL = split_url_from_string($data->link);
$_GET['type'] = isset($URL[5])?$URL[5]:null;
$_GET['id'] = isset($URL[6])?$URL[6]:null;
// print_r($URL);

$_SESSION['mybook_userid'] = isset($_SESSION['mybook_userid']) ? $_SESSION['mybook_userid'] : 0;
$login = new Login();
$user_data = $login->check_login($_SESSION['mybook_userid'], false);
// check if not logged in 
if ($_SESSION['mybook_userid'] == 0) {
    $obj = (object)[];
    $obj->action = "like_post";

    echo json_encode($obj);
}

// $query_string = explode("?", $data->link);
// $query_string = end($query_string);

// $str = explode("&", $query_string);

// foreach ($str as $value) {
//     $value = explode("=", $value);
//     $_GET[$value[0]] = $value[1];
// }




$_GET['id'] = addslashes($_GET['id']);
$_GET['type'] = addslashes($_GET['type']);

if (isset($_GET['type']) && isset($_GET['id'])) {
    $post = new Post();
    if (is_numeric($_GET['id'])) {
        $allowed[] = 'post';
        $allowed[] = 'user';
        $allowed[] = 'comment';

        if (in_array($_GET['type'], $allowed)) {
            $user_class = new User();


            $post->like_post($_GET['id'], $_GET['type'], $_SESSION['mybook_userid']);


            
            if ($_GET['type'] == "user") {
                $user_class->follow_user($_GET['id'], $_GET['type'], $_SESSION['mybook_userid']);
            }

            
        }
    }
    // read likes

    $likes = $post->get_likes($_GET['id'], $_GET['type']);

    //create likes 
    $likes = array();
    $info = "";

    $i_liked = false;
    if (isset($_SESSION['mybook_userid'])) {
        $DB = new Database();
        $sql = "select likes from likes where type = 'post' && contentid = '$_GET[id]' limit 1";
        $result = $DB->read($sql);
        if (is_array($result)) {
            $likes = json_decode($result[0]['likes'], true);

            $user_ids = array_column($likes, "userid");


            if (in_array($_SESSION['mybook_userid'], $user_ids)) {
                $i_liked = true;
            }
        }
    }
    $like_count = count($likes);
    if ($like_count > 0) {
        $info .= "<br>";
        if ($like_count == 1) {
            if ($i_liked) {
                $info .=  "<span style='float:left;'> You liked this post </span>";
            } else {

                $info .=  "<span style='float:left;'>1 person liked this post </span>";
            }
        } else {
            if ($i_liked) {

                if ($like_count - 1 == 1) {

                    $info .=  "<span style='float:left;'>You and 1 other liked this post </span>";
                } else {
                    $info .=  "<span style='float:left;'>You and " . $like_count . " others liked this post </span>";
                }
            } else {
                $info .=  "<span style='float:left;'>" . $like_count . " people liked this post </span>";
            }
        }
    }

    $obj = (object)[];
    $obj->likes = count($likes);
    $obj->action = "like_post";
    $obj->info = $info;
    $obj->id = "info_$_GET[id]";




    echo json_encode($obj);
}
