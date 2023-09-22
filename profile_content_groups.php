<div style="min-height:400px; width:100%; background-color:white;text-align:center;">

    <br>
    <a href="<?= ROOT ?>create_group">
        <input type="button" id="post_button" value="Create Group" style="float:none;margin-right:10px; background-color:#1b9186;width:auto;">
    </a>
    <div style="padding:20px;">
        <?php

        $user_class = new User();
        $image_class = new Image();
        $group_class = new Group();
        $groups = $group_class->get_my_groups($user_data['userid']);
        if (is_array($groups)) {
            foreach ($groups as $follower) {
                $FRIEND_ROW = $user_class->get_user($follower['userid']);
                include("group.inc.php");
            }
        } else {
            echo "No followers were found";
        }



        ?>
    </div>
</div>