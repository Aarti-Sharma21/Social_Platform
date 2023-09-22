<div id="posts">
    <div>
        <?php
        $image_class =  new Image();
        $image = "images/user_male.jpg";
        if ($ROW_USER['gender'] = "Female") {
            $image = "images/user_female.jpg";
        }

        if (file_exists($ROW_USER['profile_image'])) {
            $image = $image_class->get_thumb_profile($ROW_USER['profile_image']);
        }


        ?>
        <img src="<?php echo ROOT . $image; ?>" style="width:75px;margin-right:4px; border-radius:50%;">
    </div>
    <div style="width:100%;">
        <div style="font-weight:bold;color:#405d9b;width:100%;">
            <?php echo htmlspecialchars($ROW_USER['first_name']) . " " . htmlspecialchars($ROW_USER['last_name']);

            if ($ROW['is_profile_image']) {

                $pronoun = "his";
                if ($ROW_USER['gender'] == "Female")
                    $pronoun = "her";
                echo "<span style= ' font-weigth:normal;color:#aaa;'> updated $pronoun profile image</span>";
            }


            if ($ROW['is_cover_image']) {

                $pronoun = "his";
                if ($ROW_USER['gender'] == "Female")
                    $pronoun = "her";
                echo "<span style= ' font-weigth:normal;color:#aaa;'> updated $pronoun cover image</span>";
            }
            ?>
        </div>
        <?php echo htmlspecialchars($ROW['post']); ?>
        <br>
        <br>
        <?php

        if (file_exists($ROW['image'])) {
            $ext = pathinfo($ROW['image'], PATHINFO_EXTENSION);
            $ext = strtolower($ext);

            if ($ext == "jpg" || $ext == "jpeg") {
                $post_image = $image_class->get_thumb_post($ROW['image']);
                echo "<img src = '" . ROOT . "$post_image' style = 'width:80%'>";
                
            } elseif ($ext == "mp4") {
             
                echo "<video controls style = 'width:100%;' autoplay>
                
                <source src = '". ROOT . "$ROW[image]'>

                </video>";
            
            }
        }
        ?>
        <br><br>

    </div>
</div>