<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 24.05.19
 * Time: 15:03
 */

require __DIR__ . "/../vendor/autoload.php";


$template = <<<EOT
{mail to="matthes@leuffen2.de" name="Matthias Leuffen"}
{mail from="some@infracamp.org" name="My organisation"}
{subject}This is a cute subject for template Mail{/subject}

{html}
    <body>
        <h1>Hello</h1>
        <p>This is a Html Mail</p>
    </body>
{/html}

This is the alternative text E-Mail part.

EOT;




$mailer = new \Phore\Mail\PhoreMailer();
$mailer->setSmtpDirectConnect("infracamp.org");

$mailer->send($template);
