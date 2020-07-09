# Phore Mail

A template wrapper around the famous *[PHPMailer](https://github.com/PHPMailer/PHPMailer)*
Mailer class and the *[text/template](https://github.com/dermatthes/text-template)* template system.

- Single Class
- Multipart Mime 
- Testing using [mailtrap.io](https://mailtrap.io)

## Demo template

```
{mail to="abc@abc.de" name="Some Name"}
{mail from="sender@address.de" name="Me"}
{mail cc="mail@email" name="Some Name"}
{mail bcc="mail@email" name="Some Name"}
{subject}Hello {=name} - You are the welcome{/subject}

{html}
    <body>
        <b>Hello {= name}</b>,
        <p>
            This HTML Mime Mail
        </p>
    </body>
{/html}

Hello {= name},

This is the alternative Text body
```

## Available mail parameters


| Parameter | Name | Default |
|-----------|------|---------|
| `charset` | |
| `to` ||
| `from` ||
| `cc` ||
| `bcc` ||

## Script for sending a mail

With auto-failover to second SMTP-Server.

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

## Configuring PHPMailer / text-template

```php
$mailer = new PhoreMailer();
$mailer->phpmailer->phpMailerFunction();
```

```php
$mailer = new PhoreMailer();
$mailer->textTemplate->textTemplateFunction();
```


## Sending mail without mailserver using SMTP

> This method is for testing only. Most Mailservers will
> reject mail transferred with this method.

```

```


## Demos

- [Basic/simple template sending mail](docs/simple-demo.php)
- [SMTP Auth](docs/smtp-auth-demo.php)
- [Setting charset](docs/setting-charset.php)

## Debugging

Instead of sending the mail, you can retrieve the PHPMailer
instance by calling `prepare()`. 

```
$phpmail = $phoreMailer->prepare($template,[]);
print_r ($phpmail);
$phpmail->Send();
```

## Intercepting outgoing mail

```php
$mailer->setSendMailFunction(function (PHPMailer $mail, PhoreMailer $phoreMailer) {
     $res["to"] = $mail->getAllRecipientAddresses();
     $res["subject"] = $mail->Subject;
     $res["html"] = $mail->Body;
     $res["text"] = $mail->AltBody;
});
```

