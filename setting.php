<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

wp_enqueue_style('script_css', plugins_url('css/style.css', __FILE__));

if (isset($_POST['mailkey_btn'])) {

    global $wpdb;
    $table_name = $wpdb->prefix . 'dlv_subscriber';
    $my_key = sanitize_text_field($_POST["mailget_key"]);
    if (!wp_verify_nonce($_POST['mailget_key_nonce'], 'mailget_key_action')) {
        echo 'Sorry, your nonce did not verify.';
    } else {
        if ($my_key != '') {
            $wpdb->update($table_name, array('mailget_api_key' => $my_key), array('id' => 1));
        }
    }
}

if (isset($_POST['submit'])) {

    global $wpdb;
    $table_name = $wpdb->prefix . 'dlv_subscriber';
    $selected_list = sanitize_text_field($_POST["lists_name"]);
    if (!wp_verify_nonce($_POST['email_list_nonce'], 'email_list_action')) {
        echo "nonce dosen't varify";
    } else {
        $wpdb->update($table_name, array('subs_id' => $selected_list), array('id' => 1));
    }
}

//----------Users authentication [ Start ]------------
function con_upg_mailget_api_key() {

    global $wpdb;
    $table_name = $wpdb->prefix . 'dlv_subscriber';
    $query = "SELECT * FROM " . $table_name;
    $result = $wpdb->get_results($query);
    $data1 = array();
    foreach ($result as $value) {
        array_push($data1, $value->mailget_api_key);
    }
    ?>
    <h1>Content Upgrade Plugin</h1><p></p>
    <p>
        This plugin shows a button on the Add New Post and Add New Page section.
    </p>
    <p>
        You can offer content upgrades by adding a download button to your posts/pages and any leads captured will be sent to MailGet account instantly.
    </p>
    <div class="form_con">
        <form method="post">

            <?php wp_nonce_field('mailget_key_action', 'mailget_key_nonce'); ?>
            <table>
                <tr>
                    <td class="label">Enter MailGet API Key</td>
                    <td><input type="text" name="mailget_key" placeholder="Your Key" id="my_mailget_api_key" value="<?php echo wp_kses_post($data1[0]); ?>" /><br/>
                        <p><i>You can find your MailGet API key by visiting link below.</i><br/>
                            <a href="https://www.formget.com/mailget/mailget_api/api_setting" target="_blank">https://www.formget.com/mailget/mailget_api/api_setting</a>
                        </p>
                    </td>
                </tr>
                <tr>
                    <td class="mail_sub_btn"><p><input class="mailkey_btn" type="submit" name="mailkey_btn" value="Save API Key and Fetch Email Lists" /></p></td>
                </tr>
            </table>
        </form>
    </div>
    <?php
}

//----------Users authentication [ End ]------------

con_upg_mailget_api_key();

con_upg_setting_options_page();

function con_upg_setting_options_page() {

    global $wpdb;
    $table_name = $wpdb->prefix . 'dlv_subscriber';
    $query = "SELECT * FROM " . $table_name;
    $result = $wpdb->get_results($query);
    $data1 = array();
    foreach ($result as $value) {
        array_push($data1, wp_kses_post($value->subs_id));
        array_push($data1, wp_kses_post($value->mailget_api_key));
    }

    require_once('mailget_curl.php');
    $mailget_key = $data1[1];
    $send_val = 'multiple';/** For sending autoresponder use $send_val='single' * */
    $mailget_obj = new Con_Upg_Mailget_Curl($mailget_key);
    $list_arr = $mailget_obj->get_list_in_json($mailget_key);
    ?>
    <div class="form_con">
        <form method="post">
            <?php
            wp_nonce_field('email_list_action', 'email_list_nonce');
            settings_fields('myplugin_options_group');
            ?>
            <table>
                <tr>
                    <td class="label">Select Your Email List</td>
                    <td> <select name="lists_name">
                            <option disabled selected>Select Your List</option>
                            <?php
                            foreach ($list_arr as $list) {
                                ?>
                                <option value="<?php echo $list->list_id; ?>" <?php
                                if ($data1[0] == $list->list_id) {
                                    echo " selected ";
                                }
                                ?>
                                        ><?php echo $list->list_name; ?></option>
                                        <?php
                                    }
                                    ?>
                        </select><br/>
                        <p><i>Any Lead Captured through Content Upgrade button will be added to the list selected above.</i></p>
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
