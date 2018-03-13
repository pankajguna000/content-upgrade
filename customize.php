<?php
wp_enqueue_style('script_css', plugins_url('css/style.css', __FILE__));

if (isset($_POST['submit'])) {
    if (!wp_verify_nonce($_POST['email_customize_nonce'], 'email_customize_action')) {
        echo "nonce dosen't varify";
    } else {
        global $wpdb;
        $table_name = $wpdb->prefix . 'dlv_subscriber';
        $email_head = wp_kses_post($_POST["email_head"]);
        $email_body = wp_kses_post($_POST["email_body"]);
        $email_sign = wp_kses_post($_POST["email_sign"]);
        $email_footer = wp_kses_post($_POST["email_footer"]);
        $wpdb->update($table_name, array('email_head' => $email_head, 'email_body' => $email_body, 'email_sign' => $email_sign, 'email_footer' => $email_footer), array('id' => 1));
    }
}

con_upg_email_customize();

function con_upg_email_customize() {
    $check = '';
    $data1 = array();
    $data1 = con_upg_get_email_data();
    ?>

    <div class="form_con">
        <h1>Customize Your Email</h1>   
        <form method="post">
    <?php
    settings_fields('myplugin_options_group');
    wp_nonce_field('email_customize_action', 'email_customize_nonce');
    ?>
            <table>
                <tr>
                    <td class="label">Enter Email Heading</td>
                    <td>
                        <input class="email_head" type="text" name="email_head" value="<?php echo $data1[0]; ?>" />
                    </td>
                </tr>
                <tr>
                    <td class="label">Enter Email Body</td>
                    <td>
                        <textarea rows="5" cols="70" name="email_body"><?php echo $data1[1]; ?></textarea>
                    </td>
                </tr>
                <tr>
                    <td class="label">Enter Email Signature</td>
                    <td>
                        <textarea rows="5" cols="70" name="email_sign"><?php echo $data1[2]; ?></textarea>
                    </td>
                </tr>
                <tr>
                    <td class="label">Enter Email Footer</td>
                    <td>
                        <textarea rows="5" cols="70" name="email_footer"><?php echo $data1[3]; ?></textarea>
                    </td>
                </tr>

            </table>
            <div class="sub_btn">
    <?php submit_button(); ?>
            </div>
        </form>
    </div>

    <?php
}

function con_upg_get_email_data() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'dlv_subscriber';
    $query = "SELECT * FROM " . $table_name;
    $result = $wpdb->get_results($query);
    $data = array();
    foreach ($result as $value) {
        array_push($data, wp_kses_post($value->email_head));
        array_push($data, wp_kses_post($value->email_body));
        array_push($data, wp_kses_post($value->email_sign));
        array_push($data, wp_kses_post($value->email_footer));
    }
    return $data;
}
