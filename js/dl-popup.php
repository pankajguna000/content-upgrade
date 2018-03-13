<?php
// this file contains the contents of the popup window
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Content Upgrade</title>
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.js"></script>
        <script language="javascript" type="text/javascript"
        src="../../../../../wp-includes/js/tinymce/tiny_mce_popup.js"></script>
        <link rel="stylesheet" href="../css/tinymce-window.css"/>

        <script type="text/javascript">
            var DLButtonDialog = {
                local_ed: 'ed',
                init: function (ed) {
                    DLButtonDialog.local_ed = ed;
                    tinyMCEPopup.resizeToInnerSize();
                },
                insert: function insertButton(ed) {

                    // Try and remove existing style / blockquote
                    tinyMCEPopup.execCommand('mceRemoveNode', false, null);

                    // set up variables to contain our input values
                    var dl_url = jQuery('#button-dialog input#dl_url').val();
                    var dl_text = jQuery('#button-dialog input#dl_text').val();
                    var lv_url = jQuery('#button-dialog input#lv_url').val();
                    var lv_text = jQuery('#button-dialog input#lv_text').val();

                    var prev_domain = jQuery('#button-dialog select#dl_domain').val();
//                    var prev_first = jQuery('#button-dialog input#prv_heading1').val();
//                    var prev_second = jQuery('#button-dialog input#prv_heading2').val();
//                    var prv_btn_text = jQuery('#button-dialog input#prv_btn_text').val();
//                    var prv_btn_link = jQuery('#button-dialog input#prv_btn_link').val();

                    var output = '';

                    // setup the output of our shortcode
                    output = '[dlv ';
                    output += 'dl_url="' + dl_url + '" ';
                    output += 'dl_text="' + dl_text + '" ';
//                    output += 'prev_first="' + prev_first + '" ';
//                    output += 'prev_second="' + prev_second + '" ';
//                    output += 'prv_btn_text="' + prv_btn_text + '" ';
//                    output += 'prv_btn_link="' + prv_btn_link + '" ';
                    output += ']';
                    // only insert if the url field is not blank
                    //if (url)
                    //output += ' url=' + url;
                    // check to see if the TEXT field is blank
                    //if (text) {
                    //output += ']';
                    //}
                    // if it is blank, use the selected text, if present
                    //else {
                    //output += ']' + ButtonDialog.local_ed.selection.getContent() + '[/button]';
                    //}
                    tinyMCEPopup.execCommand('mceReplaceContent', false, output);

                    // Return
                    tinyMCEPopup.close();
                }
            };
            tinyMCEPopup.onInit.add(DLButtonDialog.init, DLButtonDialog);

        </script>
  

    </head>
    <body>
        <div id="button-dialog">
            <form action="/" method="get" accept-charset="utf-8" style="padding:5px;">
                <div style="display:none;">
                    <select name="dl_domain" id="dl_domain">
                        <option selected value="mailget">Mailget</option>
                        <option value="formget">FormGet</option>
                        <option value="inkthemes">InkThemes</option>
                    </select>
                </div>
                <!--                <div>
                                    <label for="prv_heading1">Preview First Heading</label>
                                    <input type="text" name="prv_heading1" id="prv_heading1"/>
                                </div>
                                <div>
                                    <label for="prv_heading2">Preview Second Heading</label>
                                    <input type="text" name="prv_heading2" id="prv_heading2"/>
                                </div>
                                <div>
                                    <label for="prv_btn_text">Preview Button Text</label>
                                    <input type="text" name="prv_btn_text" id="prv_btn_text"/>
                                </div>
                                <div>
                                    <label for="prv_btn_link">Preview Button Link</label>
                                    <input type="text" name="prv_btn_link" id="prv_btn_link"/>
                                </div>-->
                <div>
                    <label for="dl_text">Download Button Text</label>
                    <input type="text" name="dl_text" value="" id="dl_text"/>
                </div>
                <div>
                    <label for="dl_url">Download File URL</label>
                    <input type="text" name="dl_url" value="" id="dl_url"/>
                </div>
                <!--  <div>
                    <label for="lv_url">Demo Button URL</label>
                     <input type="text" name="lv_url" value="" id="lv_url"/>
                 </div>
                 <div>
                     <label for="lv_text">Demo Button Text</label>
                     <input type="text" name="lv_text" value="" id="lv_text"/>
                 </div>
                -->
                <div>
                    <a href="javascript:DLButtonDialog.insert(DLButtonDialog.local_ed)" id="insert"
                       style="background: #0a8dc1;border: 2px solid;border-color: #0085ba;-webkit-box-shadow: 0 1px 0 #006799;
                              box-shadow: 0 1px 0 #006799;color: #fff;padding: 2px 5px;text-decoration: none;border-radius: 3px;
                              text-shadow: 0 -1px 1px #006799, 1px 0 1px #006799, 0 1px 1px #006799, -1px 0 1px #006799;
    ">Insert</a>
                </div>
            </form>

        </div>
    </body>
</html>