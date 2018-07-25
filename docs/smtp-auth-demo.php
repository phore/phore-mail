<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 25.07.18
 * Time: 14:36
 */

namespace Docs;
use Phore\Mail\PhoreMailer;

require __DIR__ . "/../vendor/autoload.php";


$template = <<<EOT
{mail to=recipient_email name=recipient_name}
{mail from="from@me.org" name="My organisation"}

{html}
    <body>
        <h1>Hello {= name}</h1>
        <p>This is a Html Mail</p>
    </body>
{/html}

Hello {=name},

This is the alternative text E-Mail part.
EOT;



$mailer = new PhoreMailer();
$mailer->config(
    [
        "Host" => "smtp.example.de",
        "Username" => "smtp@host.de",
        "Password" => "secret",
        "SMTPAuth" => true,
        "SMTPSecure" => "tls",
        "Port" => 857
    ]
);

$mailer->send($template, [
    "name" => "John Doe",
    "recipient_email"   =>"to@email.org",
    "recipient_name"    => "Some Name"
]);
