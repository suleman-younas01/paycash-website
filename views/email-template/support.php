<?php
$message='<!doctype html>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>'.SITE_NAME.'</title>
</head>
<body>
<div style="background-color:#f4f4f4;margin:0!important;padding:0!important">


    <table border="0" cellpadding="0" cellspacing="0" width="100%">

        <tbody>
        <tr>
            <td bgcolor="#f4f4f4" align="center" style="padding:0px 10px 0px 10px">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width:600px">
                    <tbody><tr>
                        <td bgcolor="#ffffff" align="center" valign="top" style="padding:40px 20px 20px 20px;border-radius:4px 4px 0px 0px;color:#111111;font-family:"Lato",Helvetica,Arial,sans-serif;font-size:48px;font-weight:400;letter-spacing:4px;line-height:48px">
                          <img src="https://res.cloudinary.com/dw26bztu2/image/upload/v1707821290/image_jhbyfh.png" width="50%" class="CToWUd" data-bit="iit">
                        </td>
                        </tr>
                        <tr>
                         <td bgcolor="#ffffff" align="center" valign="top" style="padding:40px 20px 20px 20px;border-radius:4px 4px 0px 0px;color:#111111;font-family:"Lato",Helvetica,Arial,sans-serif;font-size:48px;font-weight:400;letter-spacing:4px;line-height:48px">
                            <h1 style="font-size:32px;font-weight:400;margin:2">New Ticket has been created.</h1>
                        </td>
                    </tr>
                    </tbody></table>
            </td>
        </tr>
        <tr>
            <td bgcolor="#f4f4f4" align="center" style="padding:0px 0px 0px 10px">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width:600px">
                    <tbody><tr>
                        <td bgcolor="#ffffff" align="center" style="padding:20px 30px 40px 30px;color:#666666;font-family:"Lato",Helvetica,Arial,sans-serif;font-size:18px;font-weight:400;line-height:25px">
                            <h3 style="font-size:30px;font-weight:400;margin:2">Dear Admin</h3>
                            
                            <p>'.$_SESSION['users']['fname'].' '.$_SESSION['users']['lname'].' created a new ticket. And ticket id is '.@$ticket_id.'. please check your portal.</p>
                       <br><br>
                     
                        Thank you,
                        '.SITE_NAME.' Team</p>
                           <br><br>
                        </td>
                    </tr>
                    </tbody></table>
            </td>
        </tr>
        <tr>
            <td bgcolor="#f4f4f4" align="center" >
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width:600px">
                    <tbody><tr>
                        <td bgcolor="#FFECD1" align="center" style="padding:30px 30px 30px 30px;border-radius:4px 4px 4px 4px;color:#666666;font-family:"Lato",Helvetica,Arial,sans-serif;font-size:18px;font-weight:400;line-height:25px">
                            <p style="margin:0">You are receiving this email because your email address was used to sign up for an account at <a href="#" target="_blank" data-saferedirecturl="https://www.google.com/url?q=https://paywithmoon.com&amp;source=gmail&amp;ust=1666436123784000&amp;usg=AOvVaw1gmBJ5oK0QGzrUsrMq9kkE">'.SITE_NAME.'</a> or someone requested a password reset on your BSA account. If this wasn"t you, you can safely ignore this email.</p>
                        </td>
                    </tr>
                    </tbody></table>
            </td>
        </tr>

        </tbody></table><div class="yj6qo"></div><div class="adL">
</div></div>
</body>
</html>';