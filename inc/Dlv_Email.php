<?php

/**
 * Created by PhpStorm.
 * User: imac
 * Date: 17/09/15
 * Time: 3:30 PM
 */
class Con_Upg_Dlv_Email
{
    function smtp_email($from, $to, $name, $subject, $body)
    {
        $mail = new PHPMailer();
        $mail->IsSMTP(true); // SMTP
        $mail->SMTPAuth = true; // SMTP authentication
        $mail->Mailer = "smtp";
        $mail->Host = $this->smtp_host; // Amazon SES server, note "tls://" protocol
        $mail->Port = $this->smtp_port; // set the SMTP port
        $mail->Username = $this->smtp_username; // SES SMTP username
        $mail->Password = $this->smtp_password; // SES SMTP password
        $mail->SetFrom($from, 'FormGet');
        $mail->AddReplyTo($from);
        $mail->Subject = $subject;
        $custom_body = '<p>Hello ' . $name . '</p>';
        $custom_body .= '<p>We have sent your download code in a separate email with Subject "Download Your ZIP file."<br/>';
        $custom_body .= 'If you have not received it yet. Check your spam folder to find the email.<br/>';
        $custom_body .= 'Also, mark the email as <strong>Not Spam so</strong> that you can receive emails without any issues.</p>';

        $custom_body .= '<p>Thanks<br/>';
        $custom_body .= 'FormGet Team</p>';


        $mail->MsgHTML($custom_body);
        $mail->AddAddress($to);
        if (!$mail->Send()) {
            echo $mail->ErrorInfo;
        } else {
            echo 'Email sent to : ' . $to;
        }
    }

    function mailget_mail($from, $to, $name, $subject, $body)
    {
        $mail = new PHPMailer();
        $mail->IsSMTP(true); // SMTP
        $mail->Host = "smtp1-1.ga.formgetmail.com"; // Connect to this GreenArrow server
        $mail->SMTPAuth = true;
        // enables SMTP authentication. Set to false for IP-based authentication
        $mail->Port = 587; // SMTP submission port to inject mail into. Usually port 587 or 25
        $mail->Username = "jitu@ga.formgetmail.com"; // SMTP username
        $mail->Password = "251251"; // SMTP password
        // Campaign Settings
        $mail_class = "default"; // Mail Class to use
        $mail->SetFrom($from, 'FormGet');
        $mail->AddReplyTo($from);
        $mail->Subject = $subject;
        $mail->MsgHTML($body);
        $mail->AddAddress($to);
        if (!$mail->Send()) {
            // echo $mail->ErrorInfo;
        } else {
            //echo 'Email sent to : ' . $to;
        }
    }
}