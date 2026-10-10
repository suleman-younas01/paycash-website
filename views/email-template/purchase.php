<?php
$message='<!doctype html>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>'.$env['SITE_NAME'].'</title>
    <style>
       .button {
  background-color: #04AA6D; /* Green */
  border: none;
  color: white !important;
  padding: 15px 32px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: 16px;
}
</style>
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
                          <img src="https://res.cloudinary.com/dw26bztu2/image/upload/v1707821290/image_jhbyfh.png" width="50%" class="CToWUd" alt="image" data-bit="iit">
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
                            <h3 style="font-size:30px;font-weight:400;margin:2">Thank You For Purchase</h3>
                           <p>
                           <h4>'.@$emailTitle.'</h4>
                           <h4>Transaction ID: '.@$balance_transaction.'</h4>
                           <h4>Amount: $'.@$finalPrice.'</h4>
                           </p>   
                        <br><br>
                        Thank you,
                        '.$env['SITE_NAME'].' Team</p>
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
                            <p style="margin:0">If you did not request this verification, please ignore this email.</p>
                        </td>
                    </tr>
                    </tbody></table>
            </td>
        </tr>
        <tr>
            <td align="center" >
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width:600px">
                    <tbody><tr>
                        <td  align="center" style="padding:30px 30px 30px 30px;border-radius:4px 4px 4px 4px;color:#666666;font-family:"Lato",Helvetica,Arial,sans-serif;font-size:18px;font-weight:400;line-height:25px">
                           <a href="https://www.biafrasecurityadministration.org/unsubsribe">Unsubsribe</a>
                            </td>
                    </tr>
                    </tbody></table>
            </td>
        </tr>
        
     

        </tbody></table><div class="yj6qo"></div><div class="adL">
</div></div>
</body>
</html>';