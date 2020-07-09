<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 25.07.18
 * Time: 11:25
 */

namespace Test;
use Phore\Mail\PhoreMailer;
use PHPMailer\PHPMailer\PHPMailer;
use Tester\Assert;
use Tester\Environment;
require __DIR__ . "/../vendor/autoload.php";

Environment::setup();


$template = <<<EOT
{mail to=recipient_email name=recipient_name}
{mail from=" 58c9d9dbca-ce7762@inbox.mailtrap.io" name="My organisation"}
{subject}This is a cute subject for {=name}{/subject}

{html}
    <body>
        <h1>Hello {= name}</h1>
        <p>This is a Html Mail</p>
    </body>
{/html}

Hello {=name},

This is the alternative text E-Mail part.
EOT;


$res = [];
$mailer = new PhoreMailer();
$mailer->setSendMailFunction(function (PHPMailer $mail) use (&$res) {
     $res["to"] = $mail->getAllRecipientAddresses();
     $res["subject"] = $mail->Subject;
     $res["html"] = $mail->Body;
     $res["text"] = $mail->AltBody;
});

$mailer->send($template, [
    "name" => "John Doe",
    "recipient_email"   => "58c9d9dbca-ce7762@inbox.mailtrap.io",
    "recipient_name"    => "Some Name"
]);


print_r ($res);

Assert::equal(true, true);