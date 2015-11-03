<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 *
 * @version    1.4
 *
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */
namespace GSD;

class email
{
    private $text,
        $html,
        $template,
        $output,
        $eol,
        $vars,
        $debug,
        $swift,
        $message;

    public function __construct()
    {
        global $GLOBALS, $_email;

        require_once CLASSPATH.'swift/swift_required'.PHPEXT;

        // Create the Transport
        $this->swift = \Swift_SmtpTransport::newInstance($_email['host'], $_email['port'])
            ->setUsername($_email['user'])
            ->setPassword($_email['pass']);

        // Create the Mailer using your created Transport
        $this->swift = \Swift_Mailer::newInstance($this->swift);

        // Create a message
        $this->message = \Swift_Message::newInstance();

        $this->reset();

        $this->debug = defined('DEBUG') ? DEBUG : 0;
        $this->eol = "\r\n";

        return 0;
    }

    public function reset()
    {
        $this->output = $this->template = $this->html = $this->text = '';
        $this->vars = array();
    }

    public function setto($email, $name = null)
    {
        $stored = $this->message->getTo() ? $this->message->getTo() : array();
        $this->message->setTo(array_merge($stored, array($email => $name)));
    }

    public function setcc($email, $name = null)
    {
        $stored = $this->message->getCc() ? $this->message->getCc() : array();
        $this->message->setCc(array_merge($stored, array($email => $name)));
    }

    public function setbcc($email, $name = null)
    {
        $stored = $this->message->getBcc() ? $this->message->getBcc() : array();
        $this->message->setBcc(array_merge($stored, array($email => $name)));
    }

    public function setfrom($email, $name = null)
    {
        $this->message->setFrom(array($email => $name));
    }

    public function setreplyto($email, $name = null)
    {
        $this->message->setReplyTo(array($email => $name));
    }

    public function addattachment($file, $name = null)
    {
        if (file_exists($file)) {
            array_push($this->attachment, $file);
            array_push($this->attachment_name, $name);
        }
    }

    public function setsubject($text)
    {
        $this->message->setSubject($text);
    }

    public function setvar($name, $value)
    {
        $this->vars[$name] = $value;
    }

    public function setbody($text)
    {
        $this->text .= $text;
    }

    private function parsehtml($content)
    {
        $pattern = '/\$(?P<name>\w+)/i';

        $result = preg_match_all($pattern, $content, $matches);
        foreach ($matches['name'] as $match) {
            if (isset($this->vars[$match])) {
                $content = str_replace(sprintf('$%s', $match), $this->vars[$match], $content);
            } else {
                $content = str_replace(sprintf('$%s', $match), sprintf('@$%s', $match), $content);
            }
        }
        $this->html .= html_entity_decode(htmlentities($content, ENT_QUOTES, 'UTF-8'), ENT_QUOTES);
    }

    public function sethtml($text)
    {
        $this->parsehtml($text);
    }

    public function settemplate($file)
    {
        global $mysql;

        $content = file_get_contents($file);

        $this->parsehtml($content);

        $file = explode('/', $file);
        $file = explode('.', $file[sizeof($file) - 1]);
        $mysql->reset()
            ->select('subject, from, to, cc, bcc, attachment')
            ->from('emails')
            ->where('template = ?')
            ->values($file[0])
            ->exec();

        $info = $mysql->singleline();

        if ($mysql->total) {
            $subject = $info->subject;
            $from = $info->from;
            $to = $info->to;
            $cc = $info->cc;
            $bcc = $info->bcc;
            $attachment = $info->attachment;
            if ($subject != '') {
                $result = preg_match_all($pattern, $subject, $matches);
                foreach ($matches['name'] as $match) {
                    if (isset($this->vars[$match])) {
                        $subject = str_replace(sprintf('$%s', $match), $this->vars[$match], $subject);
                    } else {
                        $subject = str_replace(sprintf('$%s', $match), '', $subject);
                    }
                }
                $this->subject = html_entity_decode($subject);
            }

            if ($from != '') {
                $this->setfrom($from);
                $this->setreplyto($from);
            }
            if ($to != '') {
                $this->to = array();
                $tos = explode(',', str_replace(array(';', ' '), array(',', ''), $to));
                foreach ($tos as $to) {
                    $this->setto($to);
                }
            }
            if ($cc != '') {
                $this->cc = array();
                $ccs = explode(',', str_replace(array(';', ' '), array(',', ''), $cc));
                foreach ($ccs as $cc) {
                    $this->setcc($cc);
                }
            }
            if ($bcc != '') {
                $this->bcc = array();
                $bccs = explode(',', str_replace(array(';', ' '), array(',', ''), $bcc));
                foreach ($bccs as $bcc) {
                    $this->setbcc($bcc);
                }
            }
            if ($attachment != '[]') {
                $attachments = json_decode($attachment, true);
                foreach ($attachments as $attachment) {
                    $attachment = sprintf('../attachments/', $attachment);
                    if (file_exists($attachment)) {
                        array_push($this->attachment, $attachment);
                        array_push($this->attachment_name, null);
                    }
                }
            }
        }
    }

    public function sendmail()
    {
        $message = '';
        
        if (strlen($this->html)) {
            ob_start();
            eval(' ?>'.$this->html.'<?php ');
            $message = ob_get_contents();
            ob_end_clean();
        }

          $this->message->setBody($message, 'text/html');

        // Send the message
        $result = $this->swift->send($this->message);

        return $result;
    }
}
