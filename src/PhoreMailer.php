<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 25.07.18
 * Time: 11:26
 */

namespace Phore\Mail;


use Leuffen\TextTemplate\TextTemplate;
use PHPMailer\PHPMailer\PHPMailer;

class PhoreMailer
{

    /**
     * @var TextTemplate
     */
    public $textTemplate;

    /**
     * @var PHPMailer
     */
    public $phpmailer;

    /**
     * @var PHPMailer
     */
    public $curMail;

    public $curMeta = [];

    public function __construct(PHPMailer $phpmailer=null, TextTemplate $textTemplate=null)
    {
        if ($textTemplate === null)
            $textTemplate = new TextTemplate();
        $this->textTemplate = $textTemplate;

        if ($phpmailer === null)
            $phpmailer = new PHPMailer(true);
        $this->phpmailer = $phpmailer;
        $this->_registerTemplateFunctions();
    }

    /**
     * Set PhpMailers config variables by array
     *
     * @param array $config
     *
     * @return PhoreMailer
     */
    public function config(array $config) : self
    {
        if (isset($config["Host"]) && $config["Host"] != null) {
            // Switch to SMTP mode
            $this->phpmailer->isSMTP();
        }
        foreach ($config as $key => $value)
            $this->phpmailer->$key = $value;
        return $this;
    }

    private function _registerTemplateFunctions ()
    {
        $this->textTemplate->addFunction("mail", function($params) {
            if (isset ($params["to"])) {
                $this->curMail->addAddress($params["to"], isset($params["name"]) ? $params["name"] : '');
            }
            if (isset ($params["cc"])) {
                $this->curMail->addCC($params["cc"], isset($params["name"]) ? $params["name"] : '');
            }
            if (isset ($params["bcc"])) {
                $this->curMail->addBCC($params["bcc"], isset($params["name"]) ? $params["name"] : '');
            }
            if (isset ($params["subject"])) {
                $this->curMail->Subject = trim($params["subject"]);
            }
            if (isset($params["charset"])) {
                $this->curMail->CharSet = $params["charset"];
            }
            if (isset ($params["from"])) {
                $this->curMail->setFrom($params["from"], isset($params["name"]) ? $params["name"] : '');
            }
            if (isset ($params["replyto"])) {
                $this->curMail->addReplyTo($params["replyto"], isset($params["name"]) ? $params["name"] : '');
            }
            if (isset ($params["header"])) {
                $this->curMail->addCustomHeader($params["header"], isset($params["value"]) ? $params["value"] : '');
            }
            return "";
        });

        $this->textTemplate->addSection("subject", function($content, array $paramsArr) {
            $this->curMail->Subject = trim($content);
        });

        $this->textTemplate->addSection("html", function ($content, array $paramArr)  {
            $this->curMail->Body = $content;
            $this->curMeta["html"] = true;
            return "";
        });


    }


    public function prepare(string $template, array $data = []) : PHPMailer
    {
        $this->curMail = clone $this->phpmailer;
        $this->curMeta = ["html" => false];

        $textData = $this->textTemplate->loadTemplate($template)->apply($data);

        if ($this->curMeta["html"]) {
            $this->curMail->AltBody = $textData;
        } else {
            $this->curMail->Body = $textData;
        }

        return $this->curMail;
    }


    public function send(string $template, array $data = [])
    {
        $this->prepare($template, $data)->send();
    }


}