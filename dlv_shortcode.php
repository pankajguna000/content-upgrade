<?php

/**
 * Plugin Name: Content Upgrade
 * Plugin URI: http://inkthemes.com/content-upgrade-plugin/
 * Description: This plugin is used for make a like with icon for downloadable files.
 * Version: 1.0.3
 * Author: InkThemes (Krish)
 * Author URI: http://inkthemes.com
 * License: GPL2
 */
class Con_Upg_Shortcode {

    /**
     * Plugin path url
     * @var type string
     */
    var $plugin_path = '';

    /**
     * Plugin path dir
     * @var type string
     */
    var $dir_path = '';

    /**
     * Smtp host
     * @var type string
     */
    var $smtp_host = 'tls://email-smtp.us-east-1.amazonaws.com';
    //var $smtp_host = '69.58.4.66';
    /**
     * Smtp post number
     * @var type int
     */
    var $smtp_port = 465;
    // var $smtp_port = 587;
    /**
     * Smtp user name
     * @var type string
     */
    var $smtp_username = 'AKIAIW5KON7FDF5YH5WA';
    //var $smtp_username = 'neeraj';

    /**
     * Smtp password
     * @var type string
     */
    var $smtp_password = 'AjLUXFev4qh+yxngsfrfMcmTFV785nKBfJXQlbX8rcg5';
    //var $smtp_password = 'vip946cacfbeea5';

    /**
     * Server domain name
     * @var type string
     */
    var $domain = '';

    function __construct() {
        $this->plugin_path = plugin_dir_url(__FILE__);
        $this->dir_path = plugin_dir_path(__FILE__);
        $this->domain = $_SERVER['SERVER_NAME'];
        if ($this->domain == 'localhost') {
            $this->domain = 'www.formget.com';
        } else {
            $this->domain = $_SERVER['SERVER_NAME'];
        }
    }

    static function init() {
        $obj = new Con_Upg_Shortcode();
        //$obj->inc();
        add_shortcode('dlv', array($obj, 'con_upg_shorcode_dlv'));
        add_action('admin_menu', array($obj, 'setting_page'));
        add_action('wp_enqueue_scripts', array($obj, '_css'));
        add_action('wp_enqueue_scripts', array($obj, 'js'));
        add_action('admin_init', array($obj, 'con_upg_dlvshortcode_buttons'));
        add_action('admin_init', array($obj, '_ajax'));
    }

    /**
     * Include libraries
     */
    function inc() {
        require_once $this->dir_path . 'version1.0.0/Dlv_1Point_ZeroOne.php';
        do_action('dlv_api_include');
    }

    /**
     * Registers the buttons for use
     */
    function dlregister_buttons($buttons) {
        /**
         * inserts a separator between existing buttons and our new one
         * "inkbtn_button" is the ID of our button
         */
        array_push($buttons, "|", "dlv_button");
        return $buttons;
    }

    /**
     * Ajax
     */
    function _ajax() {
        add_action('wp_ajax_dlv_ajax', array($this, 'ajax_callback'));
        add_action('wp_ajax_nopriv_dlv_ajax', array($this, 'ajax_callback'));

        add_action('wp_ajax_dlv_email_ajax', array($this, 'ajax_email_callback'));
        add_action('wp_ajax_nopriv_dlv_email_ajax', array($this, 'ajax_email_callback'));

        add_action('wp_ajax_dlv_counter', array($this, 'ajax_dlcounter'));
        add_action('wp_ajax_nopriv_dlv_counter', array($this, 'ajax_dlcounter'));
    }

    function ajax_callback() {
        ob_clean();
        $name = sanitize_text_field($_POST['name']);
        $email = sanitize_email($_POST['email']);
        $download = esc_url($_POST['download']);
        if ($name != '') {
            if ($email != '' && preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/", $email)) {
                $res = $this->con_upg_check_email_add($email);
                if ($res) {
                    /* $email_verify_res = $this->email_telnet($email);
                      if(isset($email_verify_res) && $email_verify_res != "" && $email_verify_res == "pass"){
                     */
                    $to = $email;
                    $subject = 'write something for user.';
                    /* Let's prepare the message for the e-mail */
                    $message = 'Here is the download link for code.' . $download;
                    $headers = 'From: Admin <' . $to . '>' . "\r\n";
                    /* Send the message using mail() function */
                    echo "success";
                    /*
                      }else{
                      echo "Email Error";
                      } */
                } else {
                    echo "Email Error";
                }
            } else {
                echo "Email Error";
            }
        } else {
            echo "Name Error";
        }
        do_action('dlv_ajax_callback');
        //$this->add_madmimi_subscriber($name, $email);
        die();
    }

    function ajax_email_callback() {
        ob_clean();
        $name = sanitize_text_field($_POST['name']);
        $email = sanitize_email($_POST['email']);
        $download = esc_url($_POST['download']);
        $domain = esc_attr($_POST['prev_domain']);
        if ($name != '') {
            if ($email != '' && preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/", $email)) {
                $to = $email;
                $my_data = array();
                global $wpdb;
                $table_name = $wpdb->prefix . 'dlv_subscriber';
                $query = "SELECT * FROM " . $table_name;
                $result = $wpdb->get_results($query);
                foreach ($result as $value) {
                    array_push($my_data, wp_kses_post($value->email_head));
                    array_push($my_data, wp_kses_post($value->email_body));
                    array_push($my_data, wp_kses_post($value->email_sign));
                    array_push($my_data, wp_kses_post($value->email_footer));
                }

                if ($domain == 'formget') {
                    $subject = 'Hi ' . $name . ', Download Your Code Here';
                    //$subject = 'Download your Zip File - FormGet Online Form Builder';
                } elseif ($domain == 'inkthemes') {
                    $subject = 'Download your Zip File - InkThemes Responsive WordPress Themes';
                } else {
                    $subject = 'Hi ' . $name . ', ' . $my_data[0] . ' ';
                    //$subject = 'Download your Zip File - MailGet Email Marketing Tool';
                }
                $message = '';
                if ($this->domain == 'www.inkthemes.com') {
                    $email_content = wp_kses_post($_POST['email_content']);
                    $message = $email_content;
                } else {


                    /* Let's prepare the message for the e-mail */
                    /* $message = 'Hello ' . $name . ',' . "<br/><br/>";
                      $message .= 'FormGet is a free online form builder - <a href="http://www.formget.com/app">Try it for free</a>, <br/>';
                      $message .= 'Premium WordPress Themes and Plugins - <a href="http://www.formget.com/mailget/">Try InkThemes</a><br/>';
                      $message .= 'And email marketing solution <a href="http://www.formget.com/mailget/">Try MailGet</a><br/><br/>';
                      $message .= 'Here is your download code <br/>';
                      $message .= $download . "<br/>";
                      $message .= '<p>Did your problem solved ?</p>';
                      $message .= '<p>If not, write your complete issue in detail and send email at <a href="mailto:pankajguna000@gmail.com">pankajguna000@gmail.com</a></p>';
                      $message .= '<p>We receive thousands of query everyday. We try to solve your problem as soon as possible.</p>';
                      $message .= '<p>We will be happy if you can also give solution to any other users to help the community.</p>';
                      $message .= '<p>Or</p>';
                      $message .= '<p>You can also submit your tutorial here to help community.</p>';
                      $message .= '<br/>';

                      $message .= 'You can also submit your form tutorials on FormGet or you request a new tutorial,<br/> '
                      . 'So other users can be benefited. <br/><br/>';
                      $message .= '<p>Thanks</p>';
                      $message .= '<p>Pankaj Agarwal</p>';
                      $headers = 'From: Admin <' . $to . '>' . "\r\n";
                      /* $message = 'Hello ' . $name . ',' . "<br/><br/>";
                      $message .= 'Thanks for requesting the coding script.<br/>';
                      $message .= '<p>Please do let reply as <b>Send me this script</b> to this email and you will get the Script within 15 minutes on the same email address for download.</p><p><b>Note:</b>Please use the same line as Send me this script and also do not use any quotations in your message.</p>';
                      $message .= '<p>Thanks,</p>';
                      $message .= '<p>FormGet Team</p>';

                      $headers = 'From: Admin <' . $to . '>' . "\r\n"; */
                    $message = '<html xmlns="http://www.w3.org/1999/xhtml" xmlns="http://www.w3.org/1999/xhtml" style="box-sizing: border-box; font-family: Helvetica Neue,  Helvetica, Arial, sans-serif; margin: 0; padding: 0">
  <head><meta name="viewport" content="width=device-width" /><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><!--fgmgMediaReplacePlaceholder-->
<meta name="robots" content="noindex, nofollow" /><meta charset="UTF-8" /><title>Preview</title></head>
  <body align="center" style="-webkit-font-smoothing: antialiased; -webkit-text-size-adjust: none; background: #e0e0e0; box-sizing: border-box; font-family: Helvetica Neue,  Helvetica, Arial, sans-serif; height: 100%; line-height: 1.7; margin: 0; padding: 0; width: 100% !important" bgcolor="#e0e0e0">
    <style type="text/css">
img {
max-width: 100%; display: block;
}
body {
-webkit-font-smoothing: antialiased; -webkit-text-size-adjust: none; width: 100% !important; height: 100%; line-height: 1.7;
}
body {
background-color: #01191D;
}
.ExternalClass {
width: 100%;
}
body {
background-color: #e0e0e0;
}
</style>
    <table align="center" class="body-wrap" style="background: #e0e0e0; box-sizing: border-box; font-family: Helvetica 
Neue, Helvetica, Helvetica, Arial, sans-serif; margin: 0; padding: 0; width: 100%; word-break: break-word" 
bgcolor="#e0e0e0">
      <tr style="box-sizing: border-box; font-family: Helvetica Neue, Helvetica, Helvetica, Arial, sans-serif; margin: 0; padding: 0">
        <td align="center" style="box-sizing: border-box; font-family: Helvetica Neue, Helvetica, Helvetica, Arial, sans-serif; margin: 0 auto; padding: 0; vertical-align: top" valign="top">
          <table style="box-sizing: border-box; font-family: Helvetica Neue, Helvetica, Helvetica, Arial, sans-serif; margin: 0; padding: 0">
            <tr style="box-sizing: border-box; font-family: Helvetica Neue, Helvetica, Helvetica, Arial, sans-serif; margin: 0; padding: 0">
              <td class="container" width="600" style="box-sizing: border-box; clear: both !important; display: block !important; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; margin: 0 auto; max-width: 600px !important; padding: 0; vertical-align: top" valign="top">
                <div class="content" style="box-sizing: border-box; display: block; font-family: Helvetica Neue, Helvetica, Helvetica, Arial, sans-serif; margin: 0 auto; max-width: 600px; padding: 20px"><table class="main" width="100%" cellpadding="0" cellspacing="0" style="background: #FFFFFF; box-sizing: border-box; font-family: Helvetica Neue, Helvetica, Helvetica, Arial, sans-serif; margin: 0; padding: 0" bgcolor="#FFFFFF"><tr style="box-sizing: border-box; font-family: Helvetica Neue,  Helvetica, Arial, sans-serif; margin: 0; padding: 0"><td class="content-wrap" style="box-sizing: border-box; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; margin: 0 auto; padding: 0 20px 20px; vertical-align: top" valign="top"><table width="100%" cellpadding="0" cellspacing="0" style="box-sizing: border-box; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; margin: 0; padding: 0"><tr style="box-sizing: border-box; font-family: Helvetica Neue,  Helvetica, Arial, sans-serif; margin: 0; padding: 0"><td class="content-block" style="box-sizing: border-box; color: #2e2e2e; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 16px; line-height: 1.7; margin: 0 auto; padding: 20px 0; vertical-align: top" valign="top">';
                    $message .= "Hello " . $name . ",";
                    $message .= '<br/><br/><div style="box-sizing: border-box; font-family: Helvetica Neue, Helvetica, Helvetica, Arial, sans-serif; margin: 0; padding: 0">Thanks for signing up to download the file. If you have any questions you can email us by replying to this email.<br style="box-sizing: border-box; font-family: Helvetica Neue, Helvetica, Helvetica, Arial, sans-serif; margin: 0; padding: 0" /></div><div style="box-sizing: border-box; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; margin: 0; padding: 0"><br style="box-sizing: border-box; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; margin: 0; padding: 0" /></div><div style="box-sizing: border-box; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; margin: 0; padding: 0"><b style="box-sizing: border-box; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; margin: 0; padding: 0">Click link below to download:</b></div><div style="box-sizing: border-box; font-family: Helvetica Neue,Helvetica, Arial, sans-serif; margin: 0; padding: 0"><a rel="nofollow" target="_blank" href="' . $download . '" id="laKOsD2" style="box-sizing: border-box; color: #348eda; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; margin: 0; padding: 0; text-decoration: none"><span class="wysiwyg-color-blue" style="box-sizing: border-box; color: blue !important; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; margin: 0; padding: 0">' . $download . '</span></a></div> </td></tr>
                        <tr style="box-sizing: border-box; font-family:Helvetica Neue, Helvetica, Arial, sans-serif; margin: 0; padding: 0"><td class="content-block" style="box-sizing: border-box; color: #2e2e2e; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 16px; line-height: 1.7; margin: 0 auto; padding: 20px 0; vertical-align: top" valign="top"><center style="box-sizing: border-box; font-family: Helvetica Neue, Helvetica, Helvetica, Arial, sans-serif; margin: 0; padding: 0"><table width="300" style="box-sizing: border-box; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; margin: 0 auto; padding: 0"><tr style="box-sizing: border-box; font-family: Helvetica Neue, Helvetica, Helvetica, Arial, sans-serif; margin: 0; padding: 0"><td class="divider" style="background: none; border-color: #dbdada; border-style: solid; border-width: 2px 0 0; box-sizing: border-box; font-family: Helvetica Neue,  Helvetica, Arial, sans-serif; font-size: 1px; height: 1px; line-height: 1; margin: 0px; padding: 0; vertical-align: top; width: 100%" valign="top"> </td></tr></table></center></td></tr>
<tr style="box-sizing: border-box; font-family: Helvetica Neue, Helvetica, Helvetica, Arial, sans-serif; margin: 0; padding: 0"><td class="content-block" style="box-sizing: border-box; color: #2e2e2e; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 16px; line-height: 1.7; margin: 0 auto; padding: 20px 0; vertical-align: top" valign="top">

<div class="wysiwyg-text-align-center" style="box-sizing: border-box; font-family: Helvetica Neue, Helvetica, Helvetica, Arial, sans-serif; margin: 0; padding: 0; text-align: center !important" align="center"><p style="box-sizing: border-box; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; margin: 0; padding: 0">' . $my_data[1] . '</p></div>

</td></tr>

<tr style="box-sizing: border-box; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; margin: 0; padding: 0"><td class="content-block" style="box-sizing: border-box; color: #2e2e2e; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 16px; line-height: 1.7; margin: 0 auto; padding: 20px 0; vertical-align: top" valign="top">

<div style="box-sizing: border-box; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; margin: 0; padding: 0">' . $my_data[2] . '</div>

</td></tr></table></td></tr><tr style="box-sizing: border-box; font-family: Helvetica Neue, Helvetica, Helvetica, Arial, sans-serif; margin: 0; padding: 0"><td class="aligncenter mailer-info" style="background: #F5F5F5; border-top-color: #DBDADA; border-top-style: solid; border-top-width: 1px; box-sizing: border-box; color: #575454; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 12px; font-weight: normal; line-height: 1.7; margin: 0 auto; padding: 15px 20px 20px; text-align: center; vertical-align: top" align="center" bgcolor="#F5F5F5" valign="top"><p class="wysiwyg-text-align-center" style="box-sizing: border-box; color: rgb(46, 46, 46); font-family: Helvetica Neue, Helvetica, Arial, Lucida Grande, sans-serif; font-size: 14px; line-height: 1.7; margin: 0; padding: 0; text-align: center !important" align="center">'. $my_data[3] .'</p></td></tr> </table> <div class="footer dark" style="box-sizing: border-box; clear: both; color: #999; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; margin: 0; padding: 20px 0; width: 100%"><table width="100%" style="box-sizing: border-box; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; margin: 0; padding: 0"><tr style="box-sizing: border-box; font-family: Helvetica Neue, Arial, sans-serif; margin: 0; padding: 0"><td class="aligncenter footer-td" style="box-sizing: border-box; color: #FFFFFF; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; margin: 0 auto; padding: 0 20px; text-align: center; vertical-align: top" align="center" valign="top"><p class="unsubscribe" style="box-sizing: border-box; color: #111111; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 12px; font-weight: normal; line-height: 1.7; margin: 0; padding: 0"><img src="" width="1" height="1" border="0" style="box-sizing: border-box; display: block; font-family: Helvetica Neue,  Helvetica, Arial, sans-serif; margin: 0; max-width: 100%; opacity: 0; padding: 0" alt="" /></p><p class="powered" style="box-sizing: border-box; color: #111111; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 12px; font-weight: bold; line-height: 1.7; margin: 0; padding: 0; text-transform: uppercase"> Email via <a href="http://www.formget.com/mailget-app" style="box-sizing: border-box; color: #111111; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 12px; font-weight: bold; margin: 0; padding: 0; text-decoration: none; text-transform: uppercase">MailGet</a></p></td></tr></table></div></div>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
  </body>
</html>';
                }
                $from = 'neeraj@formget.com';
                // $from = 'neeraj@formgetmail4.com';

                $this->con_upg_send_mail($from, $to, $name, $subject, $message);
            }
        }
        die();
    }

    /*
      function email_telnet($email){
      $url = "http://45.76.27.27/telnet/email_verifier.php";
      $data = array(
      'email' => $email
      );

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_URL, $url);
      $res = curl_exec($ch);
      return $res;
      }
     */

    function con_upg_send_mail($from, $to, $toEmailName, $subject, $message) {
        if ($this->domain == 'www.inkthemes.com') {
            $this->con_upg_wp_email($from, $to, $toEmailName, $subject, $message);
        } else {
            $this->con_upg_wp_email($from, $to, $toEmailName, $subject, $message);
            //require_once ABSPATH . 'app/aws_sdk/phpmailer/PHPmailer/class.phpmailer.php';
            //$this->smtp_email($from, $to, $toEmailName, $subject, $message);
            //$this->mailget_mail($from, $to, $toEmailName, $subject, $message);
        }
    }

    /**
     * Send email function
     * @param type $from [from email comming]
     * @param type $to [where to deliver]
     * @param type $toEmailName [name of recepient name]
     * @param type $subject [email subject]
     * @param type $message [content of email]
     */
    function con_upg_wp_email($from, $to, $toEmailName, $subject, $message) {
        //$res = $this->check_email_add($to);
        //   if($res){
        $from = apply_filters('cc_send_from_emailid', $from);
        $to = apply_filters('cc_send_to_emailid', $to);
        $toEmailName = apply_filters('cc_send_to_emailname', $toEmailName);

        add_filter('wp_mail_content_type', array($this, 'set_html_content_type'));
        /**
         * Additional headers
         */
        $headers = '';
        $headers .= 'To: ' . $toEmailName . ' <' . $to . '>' . "\r\n";
        $headers .= 'From: ' . get_bloginfo('name') . ' <' . $from . '>' . "\r\n";
        //$headers .= 'Reply-to: formgetscript@gmail.com'. "\r\n";

        $subject = apply_filters('cc_send_email_subject', $subject);
        $message = apply_filters('cc_send_email_content', stripslashes($message));
        $headers = apply_filters('cc_send_email_headers', $headers);
        /**
         * Send mail
         */
        //@mail( $to, $subject, $message, $headers );
        //$message .= '<a href="http://www.inkthemes.com">inkthemes</a>';
        if (wp_mail($to, $subject, $message, $headers)) {
            echo 'Email sent to : ' . $to;
        }
        remove_filter('wp_mail_content_type', array($this, 'set_html_content_type'));
        /* }
          else{
          echo 'Please enter valid email address';
          } */
    }

    function set_html_content_type() {
        return 'text/html';
    }

    function con_upg_check_email_add($signup_email) {
        $result = false;
        # BASIC CHECK FOR EMAIL PATTERN WITH REGULAR EXPRESSION
        if (!preg_match('/^[_A-z0-9-]+((\.|\+)[_A-z0-9-]+)*@[A-z0-9-]+(\.[A-z0-9-]+)*(\.[A-z]{2,4})$/', $signup_email))
            return $result;
        # MX RECORD CHECK
        list($name, $domain) = explode('@', $signup_email);
        $user_length = strlen(trim($name));
        $first_char = $name[0];
        if (trim($domain == 'yahoo.com') || trim($domain == 'yahoo.co.in')) {
            if ($user_length < 4)
                return $result;
            if (!preg_match('/[a-z]/i', $first_char))
                return $result;
        } elseif (trim($domain == 'aol.com')) {
            if ($user_length < 3)
                return $result;
            if (!preg_match('/[a-z]/i', $first_char))
                return $result;
        } elseif (trim($domain == 'gmail.com')) {
            if ($user_length < 6 || $user_length > 30)
                return $result;
            if (!preg_match('/[0-9a-z]/i', $first_char))
                return $result;
        }
        if (checkdnsrr($domain, 'MX'))
            return true;
    }

    function js() {
        if (is_single() && $this->qaplus_has_shortcode('dlv')) {
            global $post;
            if ($this->domain == 'www.inkthemes.com') {
                wp_enqueue_script('dlv_scripts', $this->plugin_path . 'js/ink-popup.js', array('jquery'));
            } else {
                wp_enqueue_script('dlv_scripts-download', $this->plugin_path . 'js/jquery.fileDownload.js', array('jquery'));
                wp_enqueue_script('dlv_scripts-cookie', $this->plugin_path . 'js/jquery.cookie.js', array('jquery'));
                wp_enqueue_script('dlv_scripts', $this->plugin_path . 'js/custom.js', array('jquery'));
            }
            wp_localize_script('dlv_scripts', 'dlv_script', array(
                'url' => admin_url('admin-ajax.php'),
                'plugin_path' => $this->plugin_path,
                'link_url' => esc_url(site_url('/app/home/?blog')),
                'post_id' => $post->ID,
                    )
            );

            do_action('dlv_js');
        }
    }

    function qaplus_has_shortcode($shortcode = '') {
        global $wp_query;
        foreach ($wp_query->posts as $post) {
            if (!empty($shortcode) && stripos($post->post_content, '[' . $shortcode) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Filters the tinyMCE buttons and adds our custom buttons
     */
    function con_upg_dlvshortcode_buttons() {
        /**
         *  Don't bother doing this stuff if the current user lacks permissions
         */
        if (!current_user_can('edit_posts') && !current_user_can('edit_pages'))
            return;

        /**
         *  Add only in Rich Editor mode
         */
        if (get_user_option('rich_editing') == 'true') {
            /**
             *  Filter the tinyMCE buttons and add our own
             */
            add_filter("mce_external_plugins", array($this, "add_dltinymce_plugin"));
            add_filter('mce_buttons', array($this, 'dlregister_buttons'));
        }
    }

    /**
     * Add the button to the tinyMCE bar
     */
    function add_dltinymce_plugin($plugin_array) {
        $plugin_array['dlv_button'] = $this->plugin_path . '/js/shortcode-plugin.js';
        return $plugin_array;
    }

    function setting_page() {
        $handle = ($this->domain == 'www.formget.com') ? 'formget' : 'inkthemes';
        $page = add_submenu_page($handle, 'Download Button', 'Download Button', 'manage_options', 'ink_dlv', array($this, 'con_upg_setting_interface'));
        add_action("admin_print_scripts-$page", array($this, '_css'));
    }

    function _css() {
        wp_enqueue_style('dlv-latofont', $this->plugin_path . 'css/fonts.css');
        wp_enqueue_style('dlv-css', $this->plugin_path . 'css/style.css');
    }

    /**
     * Ajax download counter
     * This provide the different download link on every fourth click
     */
    function ajax_dlcounter() {
        ob_clean();
        die();
    }

    function con_upg_shorcode_dlv($atts) {

        /**
         * Defining variables
         */
//        $dl_text = '';
//        $dl_url = '';
//        $lv_text = '';
//        $lv_url = '';

        /**
         * Assigning urls
         */
        $dl_url = isset($atts['dl_url']) ? str_replace('https://www.formget.com', '', $atts['dl_url']) : '';
      
              /**
         * Check if exists
         */
        if (isset($atts['dl_text']) && $atts['dl_text'] != '') {
            $dl_text = __($atts['dl_text'], 'dlv');
        } else {
            $dl_text = __('Download script', 'dlv');
        }

        $prev_domain = isset($atts['prev_domain']) ? $atts['prev_domain'] : 'mailget';
        $prev_first = isset($atts['prev_first']) ? $atts['prev_first'] : '';
        $prev_second = isset($atts['prev_second']) ? $atts['prev_second'] : '';
        $prv_btn_text = isset($atts['prv_btn_text']) ? $atts['prv_btn_text'] : '';
        $prv_btn_link = isset($atts['prv_btn_link']) ? $atts['prv_btn_link'] : '';


      
        $dlv = '<div class="dlv_container">';
        if ($this->domain != 'www.inkthemes.com') {
            if ($dl_url != ''):
                if (isset($atts['ext_url']) && $atts['ext_url'] != '') {
                    $dlv .= '<a class="dlv_download" href="' . $atts['ext_url'] . '"   >' . $dl_text . '</a>';
                    $dlv .= '&nbsp;&nbsp;&nbsp;<script type="text/javascript" src="https://gumroad.com/js/gumroad.js"></script>';
                } else {
                    $dlv .= '<a class="dlv_download" id="popup"  data-download1="' . $dl_url . '" href="javascript:void(0)" id="popup"  >' . $dl_text . '</a>';
                    $dlv .= '&nbsp;&nbsp;&nbsp;';
                }
            endif;
        }
        $dlv .= '<div id="abc"></div><div id="popupContact"></div>';
        $dlv .= '</div>';


        /**
         * Download preview script content
         */
        $script_content = "";
        if ($prev_domain == 'formget') {
            if ($prev_first == '') {
                $prev_first = "Free and Easiest Form Builder for collecting payments and leads Online";
            }
            if ($prev_second == '') {
                $prev_second = 'Powerful extensions <br/>to increase your form features<br/>FormBuilder + all extensions at $ 147';
            }
            if ($prv_btn_text == '') {
                $prv_btn_text = 'Sign up for FormGet Today';
            }
            if ($prv_btn_link == '') {
                $prv_btn_link = 'http://www.formget.com/';
            }
          //  $script_content .= "<a target='new' href='http://www.formget.com/'><img style='margin-bottom:12px;width: 195px;margin-left: auto;margin-right: auto;' src='http://www.formget.com/wp-content/uploads/2014/12/dl-formget.png' ></a><hr/>";
            $script_content .= $this->con_upg_prev_content($prev_domain, $prev_first, $prev_second, $prv_btn_text, $prv_btn_link);
        } elseif ($prev_domain == 'inkthemes') {
            if ($prev_first == '') {
                $prev_first = "Record 25000 Happy Customers <br/>You too Join InkThemes and create your websites";
            }
            if ($prev_second == '') {
                $prev_second = 'Join InkThemes membership today at $147<br/> and avail extra benefits of logos, ad banners, facebook covers, all free with membership.';
            }
            if ($prv_btn_text == '') {
                $prv_btn_text = 'Join InkThemes';
            }
            if ($prv_btn_link == '') {
                $prv_btn_link = 'http://www.inkthemes.com/';
            }
            //$script_content .= "<a target='new' href='http://www.inkthemes.com/'><img style='margin-bottom:12px;width: 195px;margin-left: auto;margin-right: auto;' src='http://www.formget.com/wp-content/uploads/2014/12/dl-inkthemes.png' ></a><hr/>";
            $script_content .= $this->con_upg_prev_content($prev_domain, $prev_first, $prev_second, $prv_btn_text, $prv_btn_link);
        } else {
            if ($prev_first == '') {
                $prev_first = "Send Email Via Amazon SES<br/>$29 Special Offer";
            }
            if ($prev_second == '') {
                $prev_second = '10k Emails for just $29 - Special Offer<br/>Limited Time, High Delivery Rate, No Download Required.';
            }
            if ($prv_btn_text == '') {
                $prv_btn_text = 'Start Email Marketing';
            }
            if ($prv_btn_link == '') {
                $prv_btn_link = 'http://www.formget.com/mailget/';
            }
            // $script_content .= "<a target='new' href='http://www.formget.com/mailget/'><img style='margin-bottom:12px;width: 195px;margin-left: auto;margin-right: auto;' src='http://www.formget.com/mailget/images/logo.png' ></a><hr/>";
            $script_content .= $this->con_upg_prev_content($prev_domain, $prev_first, $prev_second, $prv_btn_text, $prv_btn_link);
        }

        /**
         * Popup form
         */
        if ($this->domain == 'www.inkthemes.com') {
            $dlv .= $this->con_upg_popup_for_inkthemes($script_content, $prev_domain, $dl_url, $prev_first, $prev_second);
        } else {
            $script = $this->con_upg_popup_script($script_content, $prev_domain, $dl_url, $prev_first, $prev_second);
            $dlv .= $script;
        }

        return $dlv;
    }

    function email_content($prev_first, $prev_second, $dl_url) {
        $email_content = '<p>Thanks for choosing InkThemes.</p>';
        $email_content .= '<p>' . $prev_first . '</p>';
        $email_content .= '<p>' . $prev_second . '</p>';
        $email_content .= '<p>You just visited this particular Blog - <a href="' . get_permalink() . '">' . get_the_title() . '</a></p>';
        $email_content .= '<p>Here is the download link that you requested <a href="' . esc_url(site_url() . $dl_url) . '">' . $dl_url . '</a></p>';
        $email_content .= '<br/>';
        $email_content .= '<p>Thanks & Regards<br/>';
        $email_content .= 'InkThemes Team</p>';
        return apply_filters('dlv_email_content', $email_content);
    }

    function con_upg_popup_script($script_content, $prev_domain, $dl_url, $prev_first, $prev_second) {
//        ------  Getting Subscriptions id  ------

        $subs_id = '';
        global $wpdb;
        $table_name = $wpdb->prefix . 'dlv_subscriber';
        $query = "select subs_id from wp_dlv_subscriber WHERE id =1";
        $subs_id = $wpdb->get_results($query);
        foreach ($subs_id as $value)
            $subs_id = $value->subs_id;


//        ------  Getting Subscriptions id  ------


        $script = "";
        $script .= '<script type="text/javascript">';
        $script .= 'var dl_script_content = "' . $script_content . '";';
        if ($this->domain == 'www.inkthemes.com') {
            $script .= "var dl_email_content = '" . $this->email_content($prev_first, $prev_second, $dl_url) . "';";
        } else {
            $script .= "var dl_email_content = '';";
        }
        $script .= 'var dlv_loadeer = \'<p style="visibility: hidden;margin-top: 20px;" class="loaderwrap"><img class="loader" src="' . $this->plugin_path . 'images/loader-small.gif"><span></span></p>\';';
        $script .= 'var dl_domain = "' . $prev_domain . '";';
        $script .= 'var dlv_close = \'<img src = "' . $this->plugin_path . 'images/close.png\" onclick="dlv_close_popup(jQuery(this));" id ="close" / >\';';
        $script .= 'function create_popup() {';
        $script .= 'output = "";';
        $script .= 'output += \'<div class="meter">\';';
        $script .= 'output += \'<span style = "width: 100%"></span></div><div id="loading_submit"></div><form method="post" id="form">\';';
        $script .= 'output += \'<label for="name">Your Name</label><span class="input_wrapper"><input type="text" name="name" id="name"/></span>\';';
        $script .= 'output += \'<input type ="hidden" name="subs_id" id="subs_id" value="' . $subs_id . '"/>\';';
        $script .= 'output += \'<input type ="hidden" name="download" id="download" value="' . $dl_url . '"/>\';';
        $script .= 'output += \'<label for="email">Your Email Address</label><span class="input_wrapper"><input onclick="dlv_email_validate(jQuery(this));" placeholder="joie@example.com" type ="text" required="" value="" name="email" id="email"/></span>\';';
        $script .= 'output += \'<input type ="button" id="popup_submit" name="submit" value="Download File" />\';';
        $script .= 'output += \'<p> Download link will be send to your Email.\';';
        $script .= 'output += \'</form>\';';
        $script .= "return output;";
        $script .= "}";

        $script_vars = array(
            'script' => $script,
            'script_content' => $script_content,
            'prev_domain' => $prev_domain,
            'dl_url' => $dl_url,
            'prev_first' => $prev_first,
            'prev_second' => $prev_second,
        );
        $script_var = apply_filters('dlv_script_vars', $script_vars);
        //$script .= $script_var['script'];
        $script .= "</script>";


        return apply_filters('dlv_popup_scripts', $script);
    }

    function con_upg_popup_for_inkthemes($script_content, $prev_domain, $dl_url, $prev_first, $prev_second) {
        $script = "";
        $script .= '<script type="text/javascript">';
        $script .= 'var dl_inkthemes = "true";';
        $script .= 'var dl_script_content = "' . $script_content . '";';
        if ($this->domain == 'www.inkthemes.com') {
            $script .= "var dl_email_content = '" . $this->email_content($prev_first, $prev_second, $dl_url) . "';";
        } else {
            $script .= "var dl_email_content = '';";
        }
        $script .= 'var dl_domain = "' . $prev_domain . '";';
        $script .= 'function create_popup() {';
        $script .= 'var output = \'<img src = "' . $this->plugin_path . 'images/close.png\" onclick="dlv_close_popup(jQuery(this));" id ="close" / >\';';
        $script .= 'output += \'<div class="meter">\';';
        $script .= 'output += \'<span style = "width: 100%"></span></div><div id="loading_submit"></div><form method="post" id="form">\';';
        $script .= 'output += \'<label for="name">Your Name</label><span class="input_wrapper"><input type="text" name="name" id="name"/></span>\';';
        $script .= 'output += \'<input type ="hidden" name="download" id="download" value="' . $dl_url . '"/>\';';
        $script .= 'output += \'<label for="email">Your Email Address</label><span class="input_wrapper"><input onclick="dlv_email_validate(jQuery(this));" placeholder="joie@example.com" type ="text" required="" value="" name="email" id="email"/></span>\';';
        $script .= 'output += \'<input type ="button" id="popup_submit" name="submit" value="Download File" />\';';
        $script .= 'output += \'<p> Download link will be send to your Email.\';';
        $script .= 'output += \'</form>\';';
        $script .= "return output;";
        $script .= "}";
        $script .= "</script>";
        return $script;
    }

    function con_upg_prev_content($domain, $prev_first, $prev_second, $prv_btn_text, $prv_btn_link) {
        $script_content = '';
        if ($domain != '') {
            $script_content .= "<div class='dl-info'>";
            //   $script_content .= "<p class='prev-first'><img class='loader-small' src='" . $this->plugin_path . "images/loader-small.gif' > " . $prev_first . "</p>";
            $script_content .= "<p class='pinfo'>This usually takes about 20 seconds for collecting your files and to send email.</p>";
            //$script_content .= "<p class='prev-second'>$prev_second</p>";
            //  $script_content .= "<p class='note'><a class='link' href='$prv_btn_link' target='_tab'>$prv_btn_text</a> (opens in a new window)</p>";
            if ($this->domain == 'www.inkthemes.com') {
                $script_content .= "<input type='button' id='show_download_form' value='Click here to download'/>";
            }
            $script_content .= "</div>";
        }
        return $script_content;
    }

    function con_upg_setting_interface() {
        // Check that the user is allowed to update options
        if (!current_user_can('manage_options')) {
            wp_die('You do not have sufficient permissions to access this page.');
        }
        ?>
        <div class="wrap">
            <h2><?php echo __('Ink dlv-shortcode Help', 'dlv'); ?></h2>
            <hr/>
            <h2><strong>Shortcode format</strong></h2>

            <p>Shortcode Parameters:</p>
            <ol>
                <li><strong>dl_url</strong>&nbsp;: For download file source link url</li>
                <li><strong>dl_text</strong>&nbsp;: For download button text</li>
                <li><strong>lv_url</strong>&nbsp;: For live demo link link url</li>
                <li><strong>lv_text</strong>&nbsp;: Live demo button text</li>
            </ol>
            <p><strong>Complete format:</strong> [dlv dl_url="http://www.example.com" dl_text ="Download script"
                lv_url="http://www.exampledemo.com" lv_text="Live Demo"]</p>
        </div>
        <?php
    }

}

Con_Upg_Shortcode::init();



//---------------------Creating Setting page Menu--------------------------

add_action('admin_menu', 'con_upg_add_setting_page');

function con_upg_add_setting_page() {
    if (function_exists('add_menu_page')) {
        /** add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position ); 
         * add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function);
         * $page_title=write on page title
         * $menu_title= Menu name on display Dashboard
         * $capability= user Level Capability in access point top capability is 10 and low is 0.
         * $menu_slug=The slug name to refer to this menu by (should be unique for this menu)
         * $function= new function creat on dashboard.
         * $icon_url=icon url
         * $position=position menu on dashboard.
         * 2 Dashboard,4 Separator, 5 Posts, 10 Media, 15 Links, 20 Pages, 25 Comments, 59 Separator, 60 Appearance, 65 Plugins, 70 Users,
         * 75 Tools, 80 Settings, 99 Separator
         */
        add_menu_page('Setting', 'Content Upgrade', 'manage_options', 'setting_page', 'con_upg_api_setting', plugin_dir_url(__FILE__) . 'images/my_icon.png', 66);
        add_submenu_page('setting_page', 'Email Customization', 'Email Customization', 'manage_options', 'customization', 'con_upg_customize_option');
    }
}

function con_upg_api_setting() {
    include(plugin_dir_path(__FILE__) . "setting.php");
}

function con_upg_customize_option() {
    include(plugin_dir_path(__FILE__) . "customize.php");
}

//-----------------------------Create table -----------------------------------


function con_upg_plugin_options_install() {

    global $wpdb;
    $your_tb_name = $wpdb->prefix . 'dlv_subscriber';
    $your_data = $wpdb->prefix . 'data';
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    // create the ECPT metabox database table
    if ($wpdb->get_var("show tables like '$your_tb_name'") != $your_tb_name) {
        $sql = "CREATE TABLE " . $your_tb_name . " (
		`id` int NOT NULL AUTO_INCREMENT,
		`subs_id` varchar(255) NOT NULL,
		`mailget_api_key` varchar(255) NOT NULL,
		`email_head` varchar(255) NOT NULL,
		`email_body` varchar(255) NOT NULL,
		`email_sign` varchar(255) NOT NULL,
		`email_footer` varchar(255) NOT NULL,
                 UNIQUE KEY id (id)
		);";


        dbDelta($sql);

        $wpdb->insert($your_tb_name, array(
            'subs_id' => '',
            'mailget_api_key' => '',
            'email_head' => 'Download Your File Here',
            'email_body' => 'This email is sent using MailGet Bolt Email Marketing Service.
If you wish to send emails to your clients, you can also get MailGet Bolt and send emails in bulk.',
            'email_sign' => 'Thanks & Warm Regards,<br/>
Pankaj Agarwal<br/>
Founder, FormGet.com',
            'email_footer' => '<b>FormGet Team</b>
E3/49, 3rd Floor, Arera Colony, Bhopal, MP, India 462016',
        ));
    }
}

// run the install scripts upon plugin activation
register_activation_hook(__FILE__, 'con_upg_plugin_options_install');
