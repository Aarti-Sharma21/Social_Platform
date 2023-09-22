<div style="min-height:400px; width:100%; background-color:white;text-align:center;">
    <div style="padding:20px; max-width:350px;display:inline-block;">


        <form method="post" enctype="multipart/form-data">



            <?php
            $settings_class  = new Settings();

            $settings = $settings_class->get_settings($group_data['userid']);

            if (is_array($settings)) {

                echo "<input type = 'text' id = 'textbox' name ='first_name' value = '" . htmlspecialchars($settings['first_name']) . "'placeholder = 'Group Name'>";

                echo "<select id = 'textbox' name='group_type' style = 'height:30px;width:104%;'  >
            <option>" . htmlspecialchars($settings['group_type']) . "</option>
            <option>public</option>
            <option>private</option>
            </select>";

                echo "<br><br>About the Group:<br>
                    <textarea name = 'about' id = 'textbox' style = 'height:200px;'>" . htmlspecialchars($settings['about']) . "</textarea>
                    ";
                echo '<input type="submit" id="post_button" value="Save" style="cursor:pointer;">';
            }

            ?>
        </form>
    </div>
</div>