var download = function (ar) {
    var prevfun = function () {
    };
    ar.forEach(function (address) {
        var pp = prevfun;
        var fun = function () {
            var iframe = jQuery('<iframe style="visibility: collapse;"></iframe>');
            jQuery('body').append(iframe);
            var content = iframe[0].contentDocument;
            var form = '<form action="' + address + '" method="POST"></form>';
            content.write(form);
            jQuery(form).submit();
            setTimeout(function () {
                jQuery(document).one('mousemove', function () { //<--slightly hacky!
                    iframe.remove();
                    pp();
                });
            }, 2000);
        }
        prevfun = fun;
    });
    prevfun();
}


/*jQuery(document).ready(function ($) {
 //    jQuery(document).on("click", ".dlv_download", function(e) {
 //        e.preventDefault();
 //        //var dl1 = $(this).attr('data-download1');
 //        var dl2 = $(this).attr('data-download2');
 //        //download([dl1, dl2]);
 //        download([dl2]);
 //    });
 
 jQuery(document).on("click", ".lv_demo", function (e) {
 e.preventDefault();
 var dl = $(this).attr('data-download');
 download([dl]);
 var redirect = jQuery(this).attr('href');
 
 setTimeout(function () {
 location.href = redirect;
 }, 6000);
 
 //        var data = {
 //            action: 'dlv_counter',
 //        };
 
 //        var url = dlv_script.url;
 //        jQuery.post(url, data, function (response) {
 //            if (response) {
 //                //alert(response);
 //                // download([response]);
 //                jQuery.fileDownload(response)
 //                        .done(function () {
 //                            setTimeout(function () {
 //                                location.href = redirect;
 //                            }, 6000);
 //                        })
 //                        .fail(function () {
 //                            setTimeout(function () {
 //                                location.href = redirect;
 //                            }, 6000);
 //                        });
 //            }
 //        });
 
 });
 
 });
 */

function validateEmail(email) {
    var reg = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return reg.test(email);
}
jQuery(document).ready(function () {
    jQuery("#send").click(function () {
        var emailval = jQuery("#email").val();
        var mailvalid = validateEmail(emailval);
        if (mailvalid == false) {
            jQuery("#email").addClass("error");
        } else if (mailvalid == true) {
            jQuery("#email").removeClass("error");
        }
    });

//To display contact form onclick of button
    jQuery(".dlv_container #popup").each(function () {
        jQuery(this).click(function () {
            var parent_div = jQuery(this).parent();
            console.log(jQuery(this).attr('class'));
            parent_div.find('#abc').fadeIn();
            parent_div.find('#popupContact').empty();
            parent_div.find('#popupContact').fadeIn();
            parent_div.find('#popupContact').append(dlv_close);
            var dl1 = parent_div.find('#popup').attr('data-download1');
            parent_div.find('#download').val(dl1);
            parent_div.find('#popupContact').append(create_popup());
            //jQuery(parent_div + "#abc").fadeIn();
            //jQuery("#popupContact").empty();
            //jQuery("#popupContact").fadeIn();
            //jQuery("#popupContact").append(dlv_close);
            //jQuery("#popupContact").append(dlv_social_icons);        
            //var dl1 = jQuery('#popup').attr('data-download1');
            //jQuery("#download").val(dl1);
            //jQuery("#popupContact").append(create_popup());
            //dlv_popup_share_setcount(crestaPermalink.thePermalink);
            //dlv_popup_share_setcount('http://goo.gl/zDqJuY');
            //dlv_show_form(crestaPermalink.thePermalink);
            //dlv_show_form('http://goo.gl/zDqJuY');
        });

    });

});

var dlv_email_validate = function (ref) {
    jQuery(ref).val("");
    jQuery(ref).css("color", "black");
};


var dlv_close_popup = function (ref) {
    var parent_div = jQuery(ref).parent().parent();
    parent_div.find("#abc").fadeOut();
    parent_div.find("#popupContact").fadeOut();
};

//===============================
jQuery(document).ready(function ($) {

    $(document).on('click', '#popup_submit', function (e) {
        e.preventDefault();
        var name = jQuery("#name").val();
        var email = jQuery("#email").val();
        var dl1 = $('#popup').attr('data-download1');
        var download = jQuery("#download").val();
        var url = dlv_script.url;

        var data = {
            action: 'dlv_ajax',
            name: name,
            email: email,
            download: download,
        };
        $.post(url, data, function (status) {
            if (status == 'Name Error') {
                jQuery("#name").css("border", "1px solid red");
            } else if (status == 'Email Error') {
                jQuery("#name").css("border", "1px solid #28393a");
                jQuery("#email").css("border", "1px solid red");
                jQuery("#email").val("please enter correct email");
                jQuery("#email").css("color", "red");
            } else if (status == 'success') {
                dlv_loading();
            } else {
                alert("Error in mail sending.");
            }
        });

    });
    //=========================================
    //jQuery(document).on('click', '.link', function(e)
    //{
    //  e.preventDefault();
    //  window.location.href = jQuery(".link").attr('href');

    //});
});
//===============================
//for loading
function dlv_loading() {
    jQuery("#form").css('display', 'none');
    jQuery("#loading_submit").css("textAlign", "center");
    jQuery("#loading_submit").append("<br/><span style='font-size:16px; font-weight:bold;'><br/>Please wait. Sending you email with your download.</span></br><span style='font-size:14px; font-weight:bold;'>If you haven't received the email yet, please check your spam folder once and mark the email as not spam.</span><br/><br/>");
    jQuery("#loading_submit").append(dl_script_content);
    jQuery("#loading_submit").css("fontSize", "11px");
    jQuery(".meter").show();
    jQuery(".meter > span").each(function () {
        jQuery(this)
                .data("origWidth", jQuery(this).width())
                .width(217)
                .animate({
                    width: jQuery(this).data("origWidth")
                }, 20000);
        setTimeout(function () {
            var subs_id = jQuery("#subs_id").val();
            var name = jQuery("#name").val();
            var email = jQuery("#email").val();
            var download = jQuery("#download").val();
            var url = dlv_script.url;
            var data = {
                action: 'dlv_email_ajax',
                name: name,
                email: email,
                email_content: dl_email_content,
                prev_domain: dl_domain,
                download: download,
            };
            jQuery.post(url, data, function (response) {
                alert(response);
            });
            var subs_data = {
                subs_name: name,
                subs_email: email,
                subs_custom: download
            };

//            -----------------------


//            -----------------------

            /**
             * mailget post
             */
            jQuery.post('https://www.formget.com/mailget/signups/subscribe/' + subs_id, subs_data, function (response) {
                if (response) {
                }

            });
        }, 20000);

    });
    setTimeout(function () {
        jQuery(".meter > span").fadeOut();
    }, 20000);
}


function dlv_show_form($URL) {


    var data = {
        action: 'dlv_socialcounter',
        url: $URL,
    };
    var loader = jQuery('.loaderwrap');
    var formshowed = false;

    //jQuery.post(dlv_script.url, data, function (count) {
    var clicked_count = Number(jQuery.cookie("dlv_clicked_count"));
    if (clicked_count && clicked_count !== undefined) {
        var share_count = jQuery.cookie("dlv_share_count");

        share_count = share_count + 2;
        if (2 == clicked_count || clicked_count > 2) {
            jQuery('#dlv_social').hide();
            jQuery("#popupContact").append(create_popup());
            formshowed = true;
            loader.hide();
        } else {
            loader.find('span').text('You have to share atleast two sites to download the script');
        }
    }
    //});

    jQuery("#abc").mouseover(function () {
        var data = {
            action: 'dlv_socialcounter',
            url: $URL,
        };
        loader.css('visibility', 'visible');
        if (formshowed == false) {
            console.log('Formwed: ' + formshowed);
            //jQuery.post(dlv_script.url, data, function (count) {

            var clicked_count = Number(jQuery.cookie("dlv_clicked_count"));
            if (clicked_count && clicked_count !== undefined) {
                var share_count = jQuery.cookie("dlv_share_count");

                share_count = Number(share_count) + Number(2);

                console.log('Total Shared ' + clicked_count + ' Cookie: ' + share_count);

                if (2 == clicked_count || clicked_count > 2) {
                    jQuery('#dlv_social').hide();
                    jQuery("#popupContact").append(create_popup());
                    //clearInterval(countInterval);
                    formshowed = true;
                    loader.hide();
                } else {
                    loader.find('span').text('You have to share atleast two sites to download the script');
                }

            }
            //});
        }
    });


}


function dlv_popup_share_setcount($URL) {
    var click_count = 0;
    jQuery('.sbutton a').each(function () {
        jQuery(this).on('click', function (e) {
            e.preventDefault();
            var targetWindow = window.open(jQuery(this).attr('data-href'), 'targetWindow', 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=700,height=450');
            click_count = Number(click_count) + 1;
            jQuery.cookie("dlv_clicked_count", click_count);
        });
    });


    var share_count = jQuery.cookie("dlv_share_count");
    if (1 > share_count || share_count == undefined) {
        var data = {
            action: 'dlv_socialcounter',
            url: $URL,
        };
        jQuery.post(dlv_script.url, data, function (count) {
            if (count) {
                jQuery.cookie("dlv_share_count", count);
            }
        });
    }
}
function objToString(obj) {
    var str = '';
    for (var p in obj) {
        if (obj.hasOwnProperty(p)) {
            str += p + '::' + obj[p] + '\n';
        }
    }
    return str;
}
jQuery(document).ready(function () {

});

function dlv_facebook_count($URL) {
    jQuery.getJSON('https://graph.facebook.com/?id=' + $URL, function (fbdata) {
        jQuery.cookie.json = true;
        var dlv_fbshare_count =
                [{
                        "id_1": '5',
                        "id_2": '4',
                    }]
                ;

        dlv_fbshare_count.push({"id_1": '9'});


        jQuery.cookie("dlv_share_count", {
            post_id: dlv_script.post_id,
            count: fbdata.shares || 0,
        });

        console.log(fbdata.shares || 0);
    });
}

function dlv_twitter_count($URL) {
    jQuery.getJSON('https://cdn.api.twitter.com/1/urls/count.json?url=' + $URL + '&callback=?', function (twitdata) {
        //return twitdata.count || 0;
        console.log(twitdata.count || 0);
    });
}

function dlv_google_count($URL) {
    //return crestaShare.GPlusCount || 0;
    console.log(crestaShare.GPlusCount || 0);
}

function dlv_linkedin_count($URL) {
    jQuery.getJSON('https://www.linkedin.com/countserv/count/share?url=' + $URL + '&callback=?', function (linkedindata) {
        //return linkedindata.count || 0;
        console.log(linkedindata.count || 0);
    });
}

