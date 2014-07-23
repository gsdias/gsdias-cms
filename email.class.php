<?php
/*************************************
	* File with email class information *
	*************************************/

class email {
    private $to, $to_name, $from, $from_name, $reply_to, $reply_to_name, $cc, $cc_name, $bcc, $bcc_name, $attachment, $attachment_name, $text, $html, $template, $subject, $output, $eol, $vars, $debug;
    
    public function __construct () { global $GLOBALS;
        $this->reset();
        
        $this->debug = defined('DEBUG') ? DEBUG : 0;

        return 0;
    }
    
    public function reset () {		 
        $this->to = array();
        $this->to_name = array();
        $this->from = "";
        $this->from_name = "";
        $this->reply_to = "";
        $this->reply_to_name = "";
        $this->cc = array();
        $this->cc_name = array();
        $this->bcc = array();
        $this->bcc_name = array();
        $this->attachment = array();
        $this->attachment_name = array();
        $this->text = "";
        $this->html = "";
        $this->template = "";
        $this->subject = "";
        $this->output = "";
        $this->eol = "\r\n";
        $this->vars = array();
        
    }
    
    public function setTo ($email, $name = null) {
        array_push($this->to, $email);
        array_push($this->to_name, $name);
                
    }
    public function getTo () {
        return $this->to;
    }
    public function setFrom ($email, $name = null) {
        $this->from = $email;
        $this->from_name = $name;
    }
    
    public function getFrom () {
        return $this->from;
    }
    public function setReplyTo ($email, $name = null) {
        $this->reply_to = $email;
        $this->reply_to_name = $name;
    }
    
    public function setSubject ($text) {
        $this->subject .= $text;
    }
    
    public function getSubject () {
        return $this->subject;
    }
    public function setVar ($name, $value) {
        $this->vars[$name] = $value;
    }
    
    public function setBody ($text) {
        $this->text .= $text;
    }
    
    public function setHtml ($text) {
        
        $content = $text;
        
        $pattern = '/\$(?P<name>\w+)/i';

        $result = preg_match_all($pattern, $content, $matches);
        foreach ($matches['name'] as $match) {
            if (isset($this->vars[$match])) {
                $content = str_replace('$' . $match, $this->vars[$match], $content);
            } else {
                $content = str_replace('$' . $match, '@$' . $match, $content);
            }
        }
        $this->html = html_entity_decode(htmlentities($content, ENT_QUOTES, "UTF-8"), ENT_QUOTES);
    }
    
    public function setTemplate ($file) {
        global $mysql;
        $content = file_get_contents($file);
        
        $pattern = '/\$(?P<name>\w+)/i';

        $result = preg_match_all($pattern, $content, $matches);
        
        foreach ($matches['name'] as $match) {
            if (isset($this->vars[$match])) {
                $content = str_replace('$' . $match, $this->vars[$match], $content);
            } else {
                $content = str_replace('$' . $match, '@$' . $match, $content);
            }
        }
        $this->html = html_entity_decode(htmlentities($content, ENT_QUOTES, "UTF-8"), ENT_QUOTES);
        $file = explode('/', $file);
        $file = explode('.', $file[sizeof($file) - 1]);
        $mysql->statement('UPDATE emails SET sent = 1 WHERE template = :template;', array(':template' => $file[0]));
        $mysql->statement('SELECT subject, `from`, cc, bcc, attachment FROM emails WHERE template = :template;', array(':template' => $file[0]));
        $info = $mysql->singleline();
        if ($mysql->total) {
            $subject = $info['subject'];
            $from = $info['from'];
            $cc = $info['cc'];
            $bcc = $info['bcc'];
            $attachment = $info['attachment'];
            if ($subject != '') {
                $result = preg_match_all($pattern, $subject, $matches);
                foreach ($matches['name'] as $match) {
                    if (isset($this->vars[$match])) {
                        $subject = str_replace('$' . $match, $this->vars[$match], $subject);
                    } else {
                        $subject = str_replace('$' . $match, '', $subject);
                    }
                }
                $this->subject = html_entity_decode($subject);
            }
            if ($from != '') {
                $this->from = $from;
                $this->reply_to = $from;
            }
            if ($cc != '') {
                $this->cc = array();
                array_push($this->cc, $cc);
            }
            if ($bcc != '') {
                $this->bcc = array();
                array_push($this->bcc, $bcc);
            }
            if ($attachment != '[]') {
                $attachments = json_decode($attachment, true);
                foreach ($attachments as $attachment) {
                    $attachment = '../attachments/' . $attachment;
                    if (file_exists($attachment)) {
                        array_push($this->attachment, $attachment);
                        array_push($this->attachment_name, null);
                    }
                }
            }
        }
    }
    
    public function setCC ($email, $name = null) {
        array_push($this->cc, $email);
        array_push($this->cc_name, $name);
    }
    
    public function setBCC ($email, $name = null) {
        array_push($this->bcc, $email);
        array_push($this->bcc_name, $name);
    }
    
    public function addAttachment ($file, $name = null) {
        if (file_exists($file)) {
            array_push($this->attachment, $file);
            array_push($this->attachment_name, $name);
        }
    }
    
    public function getAttachment () {
        return $this->attachment_name;
    }
    public function sendMail () {
        global $tpl, $user;
        $body = "";
        $header = "";
        
        $uid = md5(uniqid(time()));
        $subject = sprintf("=?utf-8?B?%s?=", base64_encode($this->subject));
        
        $header .= sprintf("From: %s <%s>%s", $this->from_name, $this->from, $this->eol);
        $header .= sprintf("Reply-To: %s <%s>%s", $this->reply_to_name, $this->reply_to, $this->eol);
        $header .= sprintf("X-Sender: %s <%s>%s", $this->from_name, $this->from, $this->eol);
        $header .= sprintf("X-Mailer: PHP/%s%s", phpversion(), $this->eol);
        $header .= sprintf("X-Priority: 3%s", $this->eol);
        $header .= sprintf("Return-Path: %s <%s>%s" , $this->from_name, $this->from, $this->eol);
        $header .= sprintf("Envelope-from: %s <%s>%s", $this->from_name, $this->from, $this->eol);
        
        $teste = $this->to;
        
        if ($this->debug) {
            if (isset($_SESSION['user'])) {
                $to = sprintf("%s <%s>, ", mb_encode_mimeheader ($_SESSION['user']->name), $_SESSION['user']->email);
            } else {
                $to = $this->to[0];
            }
        }
        
        if (sizeof($this->to) && !$this->debug) {
            $temp = null;
            while ($to = array_pop($this->to)) {
                $to_name = array_pop($this->to_name);
                
                $temp .= $to_name ? sprintf("%s <%s>, ", $to_name, $to) : sprintf("%s, ", $to);
            }
            $to = substr($temp, 0, -2);
            unset($temp);
            
        }
        if (sizeof($this->cc) && !$this->debug) {
            $temp = null;
            while ($cc = array_pop($this->cc)) {
                $cc_name = array_pop($this->cc_name);
                
                $temp .= $cc_name ? $cc_name . " <" . $cc . ">, " : $cc . ", " ;
            }
            
            $header .= sprintf("cc: %s%s", substr($temp, 0, -2), $this->eol);
            
            unset($temp);
        }
        if (sizeof($this->bcc) && !$this->debug) {
            $temp = null;
            while ($bcc = array_pop($this->bcc)) {
                $bcc_name = array_pop($this->bcc_name);
                
                $temp .= $bcc_name ? $bcc_name . " <" . $bcc . ">, " : $bcc . ", ";
            }
            
            $header .= sprintf("bcc: %s%s",substr($temp, 0, -2), $this->eol);
            
            unset($temp);
        }
        $header .= sprintf("MIME-Version: 1.0%s", $this->eol);
        $header .= sprintf("Content-Type: multipart/mixed; boundary=\"%s\"%s%s", $uid, $this->eol, $this->eol);
        $header .= sprintf("This is a multi-part message in MIME format.%s", $this->eol);
        $body = "";
        if (strlen($this->text)) {
            $body .= sprintf("--%s%s", $uid, $this->eol);
            $body .= sprintf("Content-type:text/plain; charset=\"UTF-8\"", $this->eol);
            $body .= sprintf("Content-Transfer-Encoding: 8bit%s%s", $this->eol, $this->eol);
            $body .= sprintf($this->text . "%s", $this->eol);
        }
        if (strlen($this->html)) {
            
            ob_start();
            eval(" ?>" . $this->html . "<?php ");
            $message = ob_get_contents();
            ob_end_clean();
            
            $body .= sprintf("--%s%s", $uid, $this->eol);
            $body .= sprintf("Content-type:text/html; charset=\"UTF-8\"", $this->eol);
            $body .= sprintf("Content-Transfer-Encoding: 8bit%s%s", $this->eol, $this->eol);
            $body .= $message . $this->eol;
        }
        if (sizeof($this->attachment)) {
            while ($attachment = array_pop($this->attachment)) {
                $file_name = array_pop($this->attachment_name);
                $ext = explode(".", $attachment);
                $ext = $ext[sizeof($ext) - 1];
                $file = explode("/", $attachment);
                $file_name = strlen($file_name) ? sprintf("%s.%s", $file_name, $ext) : $file[sizeof($file) - 1] ;
                $file_size = filesize($attachment);
                if ($file_size) {
                    $handle = fopen($attachment, "r");
                    $content = fread($handle, $file_size);
                    fclose($handle);
                }
                $body .= sprintf("--%s%s", $uid, $this->eol);
                    $body .= sprintf("Content-Type: %s; name=\"%s\"%s", mime_content_type($attachment), $file_name, $this->eol);
                    $body .= sprintf("Content-Transfer-Encoding: base64%s", $this->eol);
                    $body .= sprintf("Content-Disposition: attachment; filename=\"%s\"%s%s", $file_name, $this->eol, $this->eol);
                    $body .= sprintf("%s%s%s", chunk_split(base64_encode($content)), $this->eol, $this->eol);
                }
            }
        $body .= sprintf("--%s--", $uid);
        if ($this->debug) {
            $tpl->adderror(sprintf('System tried to send an email to %s from %s with title %s', implode($teste, ', '), $this->from, $this->subject));
            $tpl->adderror($body);
        }
        return strlen($to) > 0 ? mail($to, $subject, $body, $header) : 0;
    }
}
