<?php
/**
 * basic email template for plain text messages
 * @var $message
 */
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
</head>
<body style="width: 600px;margin: 0 auto;">
<div class="wrapper" style="border: 1px solid #dcdcdc;border-radius: 5px 5px 0 0">
    <p style="font-size:18px;font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif"><?php echo $message ?></p>
    <div style="padding: 48px;border:0;color:#99b1c7;font-family:Arial;font-size:12px;line-height:125%;text-align:center"><?php
        echo get_bloginfo('name') . ' - ';
        _e('Powered by ShopConstruct', 'shop_ct');
        ?></div>
</div>