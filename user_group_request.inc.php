<div id="friends" style="display:inline-block;vertical-align:top;width:200px;background-color:#eee;">

    <?php
    $image_class = new Image();
    $image = "images/user_male.jpg";
    if ($FRIEND_ROW['gender'] == "Female") {
        $image = "images/user_female.jpg";
    }

    if (file_exists($FRIEND_ROW['profile_image'])) {
        $image =  $image_class->get_thumb_profile($FRIEND_ROW['profile_image']);
    }
    ?>

    <a href="<?= ROOT ?>profile/<?php echo $FRIEND_ROW['userid']; ?>">
        <img id="friends_img" src="<?php echo ROOT .  $image ?>">
        <br>
        <?php echo $FRIEND_ROW['first_name'] . " " . $FRIEND_ROW['last_name']; ?>
        <br>
        <?php
        $online = "Last seen: <br> Unknown";
        if (isset($FRIEND_ROW['userid']) && $FRIEND_ROW['online'] > 0) {
            $online = $FRIEND_ROW['online'];

            $current_time = time();
            $threshold = 60 * 2; // 2 minutes

            if (($current_time - $online) < $threshold) {
                $online = "<span style = 'color:green;'>Online</span>";
            } else {

                $online = "Last seen: <br>" . Time::get_time(date("Y-m-d H:i:s", $online));
            }
        }


        ?>
        <span style="color:grey;font-size:11px;font-weight:normal;"><?php echo $online ?></span>
        <br style="clear:both;">
        <?php

        $invited = isset($URL[2]) && $URL[2] == "invited" ? true : false;
        if ($invited && isset($INVITER_ROW)) {
            echo "You were invited by " . $INVITER_ROW['first_name'] . " " . $INVITER_ROW['last_name'] . "<br><br>";
        }

        ?>
        <a href="<?= ROOT ?>group_request_accept/<?= $group_data['userid'] ?>/<?= $FRIEND_ROW['userid'] ?>/decline">
            <input type="button" id="post_button" value="Decline" style="float:right;margin-right:10px; background-color:#916e1b;width:auto;">
        </a>
        <a href="<?= ROOT ?>group_request_accept/<?= $group_data['userid'] ?>/<?= $FRIEND_ROW['userid'] ?>/accept">
            <input type="button" id="post_button" value="Accept" style="float:left;margin-right:10px; background-color:#1b9186;width:auto;">
        </a>
    </a>
</div>