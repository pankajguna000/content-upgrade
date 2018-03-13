<?php

/**
 * Created by PhpStorm.
 * User: imac
 * Date: 17/09/15
 * Time: 5:24 PM
 */
class Dlv_1Point_Zero_One
{

    var $social_icons = '';

    static function Instance()
    {
        $obj = new self();
        $obj->Init();
    }

    function Init()
    {
        add_action('dlv_social_shortcode', array($this, 'social_filter'), 30);
        add_action('wp_ajax_dlv_socialcounter', array($this, 'dlv_socialcounter'));
        add_action('wp_ajax_nopriv_dlv_socialcounter', array($this, 'dlv_socialcounter'));
    }

    function con_upg_popup_script($scripts)
    {

    }

    function social_filter($socialIcons)
    {
        $heading = '<div id="dlv_social"><h6 style="text-align: center;margin-top: 0; margin-bottom: 15px;">Share our best article on FormGet on any two social network to download the code.</h6>';
        $script = '<script type="text/javascript">var dlv_social_icons = ' . wp_json_encode(
                $heading . $socialIcons
            ) . ';</script></div>';

        return $script;
    }

    function dlv_socialcounter()
    {
        ob_clean();
        include('share_count.php');
        $url = wp_kses_post($_POST['url']);
        $obj = new Dlv_ShareCount($url);  //Use your website or URL
        $count = 0;
        $count += $obj->get_tweets(); //to get tweets
        $count += $obj->get_fb(); //to get facebook total count (likes+shares+comments)
        $count += $obj->get_linkedin(); //to get linkedin shares
        $count += $obj->get_plusones(); //to get google plusones
        echo $count;
        //$obj->get_delicious(); //to get delicious bookmarks  count
        //$obj->get_stumble(); //to get Stumbleupon views
        //$obj->get_pinterest(); //to get pinterest pins
        die();
    }

}

Dlv_1Point_Zero_One::Instance();
