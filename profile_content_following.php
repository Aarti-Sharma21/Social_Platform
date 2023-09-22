<div style="min-height:400px; width:100%; background-color:white;text-align:center;">
<div style = "padding:20px;">
<?php

$user_class = new User();
$image_class = new Image();
$post_class = new Post();
$following = $user_class->get_following($user_data['userid'],"user");
if(is_array($following))
{
    foreach($following as $follower){
        $FRIEND_ROW = $user_class->get_user($follower['userid']);
    include("user.php");}
}else {
    echo "This user is not following anyone";
}



?>
</div>
</div>