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


    protected $smtpDirectConnectHeloHostname = null;

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
     * Set the auth by string:
     * 
     * smtp://user:passwd@mailserver:port
     * 
     * 
     * @param string $relay
     */
    public function setRelay(string $relay)
    {
        $url = parse_url($relay);
        $this->phpmailer->Host = $url["host"];
        $this->phpmailer->Username = isset($url["user"]) ? $url["user"] : '';
        $this->phpmailer->Password = isset($url["pass"]) ? $url["pass"] : '';
        $this->phpmailer->Port = isset ($url["port"]) ? $url["port"] : 25;
        $this->phpmailer->isSMTP();
    }
    
    
    /**
     * It this is set and no SMTP relay host is
     * set, it will load the MX Record of the
     * to Address and try connecting this service
     * directly (without any SMTP server in between)
     *
     * @param string $heloHotname
     */
    public function setSmtpDirectConnect(string $heloHotname)
    {
        $this->smtpDirectConnectHeloHostname = $heloHotname;
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

        if ($this->curMail->Host == "localhost" && $this->smtpDirectConnectHeloHostname !== null) {
            $this->curMail->Hostname = $this->smtpDirectConnectHeloHostname;
            $this->curMail->isSMTP();

            $addr = array_keys($this->curMail->getAllRecipientAddresses());
            if (count ($addr) !== 1)
                throw new \InvalidArgumentException("Cannot send to more than one recipient using direct-smtp-connect mode");
            $hostname = explode('@', $addr[0]);
            $hostname = array_pop($hostname);
            if (getmxrr($hostname, $mxRR)) {

                $this->curMail->Host = $mxRR[0];
            } else {
                $this->curMail->Host = $hostname;
            }
            $this->curMail->isSMTP();
            $this->curMail->Hostname = $this->smtpDirectConnectHeloHostname;
        }


        return $this->curMail;
    }


    public function send(string $template, array $data = [])
    {
        $this->prepare($template, $data)->send();
    }


}
