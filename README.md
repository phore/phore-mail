# Phore Mail

Sends mail using phpmailer and text-template.


## Demo template

```
{mail to="abc@abc.de" name="Some Name"}
{mail from="sender@address.de" name="Me"}
{mail cc="mail@email" name="Some Name"}
{subject}Hello {=name} - You are the welcome{/subject}

{html}
    <body>
        <b>This is Html</b> E-Mail
    </body>
{/html}

And This is text mail
```

## Script for sending a mail

```php
$mailer = new PhoreMailer();
$mailer->config([
    "Host"      => "smtp1.example.org;smtp2.example.org",
    "Username"  => "user@example.org",
    "Password"  => "secret", 
    "SMTPAuth"  => true
]);
$mailer->send($templateText, ["name"=>"Joe Doe"]);
```

## Installation

```
composer require phore/mail
```

