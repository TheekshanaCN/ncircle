<?php
/**
 * views/emails/action.php  -  account email with one button. Sent by MailService::sendAction().
 * Email apps ignore stylesheets and CSS variables, so this uses tables, and the theme.css colours
 * are repeated here. Keep them in step with theme.css.
 */
$teal = "#1d3934";
$lime = "#c9f46a";
$offwhite = "#eeecea";
$muted = "#5f6b68";
$line = "#e4e2df";
$font = "font-family:'DM Sans',Inter,'Segoe UI',Helvetica,Arial,sans-serif;";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <title><?= e($title) ?></title>
</head>
<body style="margin:0;padding:0;background-color:<?= $offwhite ?>;">
<div style="display:none;max-height:0;overflow:hidden;mso-hide:all;"><?= e($intro) ?></div>
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="<?= $offwhite ?>" style="background-color:<?= $offwhite ?>;">
<tr><td align="center" style="padding:32px 16px;">
    <!--[if mso]><table role="presentation" width="560" cellpadding="0" cellspacing="0" border="0"><tr><td><![endif]-->
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width:560px;">
        <tr><td bgcolor="#ffffff" style="background-color:#ffffff;border:1px solid <?= $line ?>;border-radius:20px;overflow:hidden;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr><td bgcolor="<?= $lime ?>" style="background-color:<?= $lime ?>;height:6px;line-height:6px;font-size:0;">&nbsp;</td></tr>
                <tr><td align="center" style="padding:32px 32px 8px;">
                    <img src="<?= e($logo) ?>" height="48" width="<?= (int) $logoWidth ?>" alt="Ncircle" style="display:block;border:0;<?= $font ?>font-size:24px;font-weight:700;color:<?= $teal ?>;">
                </td></tr>
                <tr><td align="center" style="padding:16px 32px 0;<?= $font ?>font-size:26px;line-height:34px;font-weight:700;color:<?= $teal ?>;"><?= e($title) ?></td></tr>
                <tr><td align="center" style="padding:12px 32px 0;<?= $font ?>font-size:16px;line-height:25px;color:<?= $muted ?>;">Hi <?= e($name) ?>,<br><?= e($intro) ?></td></tr>
                <tr><td align="center" style="padding:28px 32px;">
                    <table role="presentation" cellpadding="0" cellspacing="0" border="0"><tr>
                        <td align="center" bgcolor="<?= $teal ?>" style="background-color:<?= $teal ?>;border-radius:999px;">
                            <a href="<?= e($link) ?>" style="display:inline-block;padding:14px 36px;<?= $font ?>font-size:16px;line-height:20px;font-weight:700;color:<?= $lime ?>;text-decoration:none;"><?= e($button) ?></a>
                        </td>
                    </tr></table>
                </td></tr>
                <tr><td align="center" style="padding:0 32px 28px;<?= $font ?>font-size:14px;line-height:22px;color:<?= $muted ?>;"><?= e($note) ?></td></tr>
                <tr><td style="padding:0 32px 32px;">
                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"><tr>
                        <td style="border-top:1px solid <?= $line ?>;padding-top:20px;<?= $font ?>font-size:13px;line-height:20px;color:<?= $muted ?>;">
                            Button not working? Copy this link into your browser:<br>
                            <a href="<?= e($link) ?>" style="color:<?= $teal ?>;word-break:break-all;"><?= e($link) ?></a>
                        </td>
                    </tr></table>
                </td></tr>
            </table>
        </td></tr>
        <tr><td align="center" style="padding:20px 16px 0;<?= $font ?>font-size:12px;line-height:18px;color:<?= $muted ?>;">
            Ncircle, the study community for NSBM students.<br>If you did not ask for this email, you can ignore it.
        </td></tr>
    </table>
    <!--[if mso]></td></tr></table><![endif]-->
</td></tr>
</table>
</body>
</html>
