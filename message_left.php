<div id="message_left" style="background-color:#e6e0de;">

    <div>
        <?php
        $image_class = new Image();
        $image = "images/user_male.jpg";
        if ($ROW_USER['gender'] == "Female") {
            $image = "images/user_female.jpg";
        }

        if (file_exists($ROW_USER['profile_image'])) {
            $image =  $image_class->get_thumb_profile($ROW_USER['profile_image']);
        }


        ?>
        <img src="<?php echo ROOT . $image; ?>" style="width:75px;margin-right:4px; border-radius:50%;">
    </div>
    <div style="width:100%;">
        <div style="font-weight:bold;color:#405d9b;width:100%;">
            <?php
            echo "<a href='" . ROOT . "profile/$MESSAGE[msgid]'> ";
            echo htmlspecialchars($ROW_USER['first_name']) . " " . htmlspecialchars($ROW_USER['last_name']);
            echo "</a>";


            ?>
        </div>
        <?php echo check_tags($MESSAGE['message']); ?>

        <?php

        if (file_exists($MESSAGE['file'])) {
            $post_image = ROOT . $image_class->get_thumb_post($MESSAGE['file']);
            echo "<img src = '$post_image' style = 'width:80%'>";
        }
        ?>
        <br><br>

        <span style="color:#999;">
            <?php echo $MESSAGE['date']; ?>
        </span>
        <?php

        if (file_exists($MESSAGE['file'])) {
            echo "<a href = '" . ROOT . "image_view/msg/$MESSAGE[id]'>";
            echo ".View Full Image.";
            echo "</a>";
        }
        ?>
        <span style="color:#999; float:right;">
            <?php

            $post = new Post();


        
                echo "<a href='" . ROOT . "delete/msg/$MESSAGE[id];'>
    Delete
</a>";
            

            ?>
        </span>
            
    </div>
</div>