<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 25.07.18
 * Time: 11:25
 */

namespace Test;
use Phore\Mail\PhoreMailer;
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



$mailer = new PhoreMailer();
$mailer->config(
    [
        "Host"      => "smtp.mailtrap.io",
        "Username"  => "c0973a3d3f2666",
        "Password"  => "b671dd37eb3ac1",
        "SMTPAuth"  => true,
        "Port"      => 2525
    ]
);

$mailer->send($template, [
    "name" => "John Doe",
    "recipient_email"   => "58c9d9dbca-ce7762@inbox.mailtrap.io",
    "recipient_name"    => "Some Name"
]);


$mailer->send("{mail to='58c9d9dbca-ce7762@inbox.mailtrap.io' from='58c9d9dbca-ce7762@inbox.mailtrap.io' subject='hello'}Hello guys");

Assert::true(true);