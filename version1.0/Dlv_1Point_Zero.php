<?php

/**
 * Created by Krishna.
 * User: imac
 * Date: 17/09/15
 * Time: 3:28 PM
 */
class Dlv_1Point_Zero
{
    static function Init()
    {
        $obj = new self();
        add_action('dlv_ajax_callback', array($obj, 'ajax_callback'));
        add_action('dlv_js', array($obj, '_js'));
        add_action('dlv_ajax', array($obj, '_ajax'));
    }

    function _ajax()
    {
        add_action('wp_ajax_dlv_counter', array($this, 'ajax_dlcounter'));
        add_action('wp_ajax_nopriv_dlv_counter', array($this, 'ajax_dlcounter'));
    }

    /**
     * Madmimi api submit
     * @param type $name
     * @param type $email
     */
    function add_madmimi_subscriber($name, $email)
    {
        $name = wp_kses_post($_POST['name']);
        $email = wp_kses_post($_POST['email']);
        $mailer = new MadMimi('admin@inkthemes.com', 'a156acc46dfbe585d2cf6d29b5ea89d1');
        $response = $mailer->AddMembership("formget-blog-script-download", $email, array("first_name" => $name, "Blogger" => "Technical Blog Post"));
        if (strpos($response, 'Member') !== false)
            echo "not";
        else
            //echo "added";
            //echo $response;
            die();
    }

    function _js()
    {

    }

    function _include()
    {
        require_once $this->dir_path . 'inc/api/madmimi/MadMimi.class.php';
    }

    /**
     * Ajax download counter
     * This provide the different download link on every fourth click
     */
    function ajax_dlcounter()
    {
        ob_clean();
        die();
    }

    function ajax_callback()
    {
        ob_clean();
        $name = wp_kses_post($_POST['name']);
        $email = wp_kses_post($_POST['email']);
        $download = wp_kses_post($_POST['download']);
        if ($name != '') {
            if ($email != '' && preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/", $email)) {
                $to = $email;
                $subject = 'write something for user.';
                /* Let's prepare the message for the e-mail */
                $message = 'Here is the download link for code.' . $download;
                $headers = 'From: Admin <' . $to . '>' . "\r\n";
                /* Send the message using mail() function */
                echo "success";
            } else {
                echo "Email Error";
            }
        } else {
            echo "Name Error";
        }
        $this->add_madmimi_subscriber($name, $email);
        die();
    }
}