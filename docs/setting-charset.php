<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 25.07.18
 * Time: 16:53
 */

namespace Docs;
use Phore\Mail\PhoreMailer;

require __DIR__ . "/../vendor/autoload.php";


$template = <<<EOT
{mail to="58c9d9dbca-ce7762@inbox.mailtrap.io"}
{mail from="58c9d9dbca-ce7762@inbox.mailtrap.io"}
{mail charset="latin1"}
{subject}This is a cute subject for{/subject}

Hello {=name},

This is the alternative text E-Mail part.
EOT;



$mailer = new PhoreMailer();
$mailer->config(
    [
        "Host" => "smtp.mailtrap.io",
        "Username" => "c0973a3d3f2666",
        "Password" => "b671dd37eb3ac1",
        "SMTPAuth" => true,
        "Port" => 2525
    ]
);

$mailer->send($template);
