<?php

namespace Core;

class Mail {

    protected $to;
    protected $from;
    protected $sender;
    protected $subject;
    protected $reply_to;
    protected $reply_to_email;
    protected $text;
    protected $html;
    protected $attachments = array();
    protected $mandrill;
    public $protocol = 'mail';
    public $hostname;
    public $username;
    public $password;
    public $port = 25;
    public $timeout = 5;
    public $newline = "\n";
    public $crlf = "\r\n";
    public $verp = false;
    public $parameter = '';
    public $MessageId = 'omniCoreFramework';
    public $mandrill_key;
    public $tags = array();

    public function setTo($to) {
        $this->to = $to;
    }

    public function setFrom($from) {
        $this->from = $from;
    }

    public function setSender($sender) {
        $this->sender = $sender;
    }

    public function setReplyTo($reply_to, $reply_to_name = '') {
        $this->reply_to = $this->reply_to_email = $reply_to;
        if ($reply_to_name) {
            $this->reply_to = $reply_to_name;
        }
    }

    public function setSubject($subject) {
        $this->subject = $subject;
    }

    public function setText($text) {
        $this->text = $text;
    }

    public function setHtml($html) {
        $this->html = $html;
    }

    public function addAttachment($path, $filename = false) {
        if (!$filename) {
            $filename = $path;
        }
        $filename = basename($filename);
        $this->attachments[$filename] = $path;
    }

    public function getSpamData() {
        $this->protocol = 'spamtest';
        return $this->send();
    }

    public function send() {
        if (!$this->to) {
            throw new \Core\Exception('Error: E-Mail to required!');
            return false;
        }

        if (!$this->from) {
            throw new \Core\Exception('Error: E-Mail from required!');
            return false;
        }

        if (!$this->sender) {
            throw new \Core\Exception('Error: E-Mail sender required!');
            return false;
        }

        if (!$this->subject) {
            throw new \Core\Exception('Error: E-Mail subject required!');
            return false;
        }

        if ((!$this->text) && (!$this->html)) {
            throw new \Core\Exception('Error: E-Mail message required!');
            return false;
        }

        if (is_array($this->to)) {
            $to = implode(',', $this->to);
        } else {
            $to = $this->to;
        }

        $boundary = '----=_NextPart_' . md5(time());

        $header = '';

        $header .= 'MIME-Version: 1.0' . $this->newline;

        if ($this->protocol == 'mandrill') {
            $this->sendMandrillSingle();
        } else {

            if ($this->protocol != 'mail') {
                $header .= 'To: ' . $to . $this->newline;
                $header .= 'Subject: ' . $this->subject . $this->newline;
            }

            $header .= 'Date: ' . date('D, d M Y H:i:s O') . $this->newline;
            $header .= 'From: ' . '=?UTF-8?B?' . base64_encode($this->sender) . '?=' . '<' . $this->from . '>' . $this->newline;
            if ($this->reply_to) {
                $header .= 'Reply-To: ' . '=?UTF-8?B?' . base64_encode($this->reply_to) . '?=' . '<' . $this->reply_to_email . '>' . $this->newline;
            } else {
                $header .= 'Reply-To: ' . '=?UTF-8?B?' . base64_encode($this->sender) . '?=' . '<' . $this->from . '>' . $this->newline;
            }
            $header .= 'Return-Path: ' . $this->from . $this->newline;
            $header .= 'X-Mailer: PHP/' . phpversion() . $this->newline;
            if ($this->MessageId) {
                $header .= "Message-Id: " . $this->MessageId . $this->newline;
            }

            $header .= 'Content-Type: multipart/related; boundary="' . $boundary . '"' . $this->newline . $this->newline;

            if (!$this->html) {
                $message = '--' . $boundary . $this->newline;
                $message .= 'Content-Type: text/plain; charset="utf-8"' . $this->newline;
                $message .= 'Content-Transfer-Encoding: 8bit' . $this->newline . $this->newline;
                $message .= $this->text . $this->newline;
            } else {
                $message = '--' . $boundary . $this->newline;
                $message .= 'Content-Type: multipart/alternative; boundary="' . $boundary . '_alt"' . $this->newline . $this->newline;
                $message .= '--' . $boundary . '_alt' . $this->newline;
                $message .= 'Content-Type: text/plain; charset="utf-8"' . $this->newline;
                $message .= 'Content-Transfer-Encoding: 8bit' . $this->newline . $this->newline;

                if ($this->text) {
                    $message .= $this->text . $this->newline;
                } else {
                    $message .= 'This is a HTML email and your email client software does not support HTML email!' . $this->newline;
                }

                $message .= '--' . $boundary . '_alt' . $this->newline;
                $message .= 'Content-Type: text/html; charset="utf-8"' . $this->newline;
                $message .= 'Content-Transfer-Encoding: 8bit' . $this->newline . $this->newline;
                $message .= $this->html . $this->newline;
                $message .= '--' . $boundary . '_alt--' . $this->newline;
            }

            foreach ($this->attachments as $afn => $attachment) {
                if (file_exists($attachment)) {
                    $handle = fopen($attachment, 'r');

                    $content = fread($handle, filesize($attachment));

                    fclose($handle);

                    $message .= '--' . $boundary . $this->newline;
                    $message .= 'Content-Type: application/octet-stream; name="' . basename($afn) . '"' . $this->newline;
                    $message .= 'Content-Transfer-Encoding: base64' . $this->newline;
                    $message .= 'Content-Disposition: attachment; filename="' . basename($afn) . '"' . $this->newline;
                    $message .= 'Content-ID: <' . basename(urlencode($afn)) . '>' . $this->newline;
                    $message .= 'X-Attachment-Id: ' . basename(urlencode($afn)) . $this->newline . $this->newline;
                    $message .= chunk_split(base64_encode($content));
                }
            }

            $message .= '--' . $boundary . '--' . $this->newline;

            if ($this->protocol == 'smtp') {
                $handle = fsockopen($this->hostname, $this->port, $errno, $errstr, $this->timeout);

                if (!$handle) {
                    throw new \Core\Exception('Error: ' . $errstr . ' (' . $errno . ')');
                    return false;
                } else {
                    if (substr(PHP_OS, 0, 3) != 'WIN') {
                        socket_set_timeout($handle, $this->timeout, 0);
                    }

                    while ($line = fgets($handle, 515)) {
                        if (substr($line, 3, 1) == ' ') {
                            break;
                        }
                    }

                    if (substr($this->hostname, 0, 3) == 'tls') {
                        fputs($handle, 'STARTTLS' . $this->crlf);

                        while ($line = fgets($handle, 515)) {
                            $reply .= $line;

                            if (substr($line, 3, 1) == ' ') {
                                break;
                            }
                        }

                        if (substr($reply, 0, 3) != 220) {
                            throw new \Core\Exception('Error: STARTTLS not accepted from server!');
                            return false;
                        }
                    }

                    if (!empty($this->username) && !empty($this->password)) {
                        fputs($handle, 'EHLO [' . getenv('SERVER_NAME') . ']' . $this->crlf);

                        $reply = '';

                        while ($line = fgets($handle, 515)) {
                            $reply .= $line;

                            if (substr($line, 3, 1) == ' ') {
                                break;
                            }
                        }

                        if (substr($reply, 0, 3) != 250) {
                            throw new \Core\Exception('Error: EHLO not accepted from server!');
                            return false;
                        }

                        fputs($handle, 'AUTH LOGIN' . $this->crlf);

                        $reply = '';

                        while ($line = fgets($handle, 515)) {
                            $reply .= $line;

                            if (substr($line, 3, 1) == ' ') {
                                break;
                            }
                        }

                        if (substr($reply, 0, 3) != 334) {
                            throw new \Core\Exception('Error: AUTH LOGIN not accepted from server!');
                            return false;
                        }

                        fputs($handle, base64_encode($this->username) . $this->crlf);

                        $reply = '';

                        while ($line = fgets($handle, 515)) {
                            $reply .= $line;

                            if (substr($line, 3, 1) == ' ') {
                                break;
                            }
                        }

                        if (substr($reply, 0, 3) != 334) {
                            throw new \Core\Exception('Error: Username not accepted from server!');
                            return false;
                        }

                        fputs($handle, base64_encode($this->password) . $this->crlf);

                        $reply = '';

                        while ($line = fgets($handle, 515)) {
                            $reply .= $line;

                            if (substr($line, 3, 1) == ' ') {
                                break;
                            }
                        }

                        if (substr($reply, 0, 3) != 235) {
                            throw new \Core\Exception('Error: Password not accepted from server!');
                            return false;
                        }
                    } else {
                        fputs($handle, 'HELO ' . getenv('SERVER_NAME') . $this->crlf);

                        $reply = '';

                        while ($line = fgets($handle, 515)) {
                            $reply .= $line;

                            if (substr($line, 3, 1) == ' ') {
                                break;
                            }
                        }

                        if (substr($reply, 0, 3) != 250) {
                            throw new \Core\Exception('Error: HELO not accepted from server!');
                            return false;
                        }
                    }

                    if ($this->verp) {
                        fputs($handle, 'MAIL FROM: <' . $this->from . '>XVERP' . $this->crlf);
                    } else {
                        fputs($handle, 'MAIL FROM: <' . $this->from . '>' . $this->crlf);
                    }

                    $reply = '';

                    while ($line = fgets($handle, 515)) {
                        $reply .= $line;

                        if (substr($line, 3, 1) == ' ') {
                            break;
                        }
                    }

                    if (substr($reply, 0, 3) != 250) {
                        throw new \Core\Exception('Error: MAIL FROM not accepted from server!');
                        return false;
                    }

                    if (!is_array($this->to)) {
                        fputs($handle, 'RCPT TO: <' . $this->to . '>' . $this->crlf);

                        $reply = '';

                        while ($line = fgets($handle, 515)) {
                            $reply .= $line;

                            if (substr($line, 3, 1) == ' ') {
                                break;
                            }
                        }

                        if ((substr($reply, 0, 3) != 250) && (substr($reply, 0, 3) != 251)) {
                            throw new \Core\Exception('Error: RCPT TO not accepted from server!');
                            return false;
                        }
                    } else {
                        foreach ($this->to as $recipient) {
                            fputs($handle, 'RCPT TO: <' . $recipient . '>' . $this->crlf);

                            $reply = '';

                            while ($line = fgets($handle, 515)) {
                                $reply .= $line;

                                if (substr($line, 3, 1) == ' ') {
                                    break;
                                }
                            }

                            if ((substr($reply, 0, 3) != 250) && (substr($reply, 0, 3) != 251)) {
                                throw new \Core\Exception('Error: RCPT TO not accepted from server!');
                                return false;
                            }
                        }
                    }

                    fputs($handle, 'DATA' . $this->crlf);

                    $reply = '';

                    while ($line = fgets($handle, 515)) {
                        $reply .= $line;

                        if (substr($line, 3, 1) == ' ') {
                            break;
                        }
                    }

                    if (substr($reply, 0, 3) != 354) {
                        throw new \Core\Exception('Error: DATA not accepted from server!');
                        return false;
                    }

                    // According to rfc 821 we should not send more than 1000 including the CRLF
                    $message = str_replace("\r\n", "\n", $header . $message);
                    $message = str_replace("\r", "\n", $message);

                    $lines = explode("\n", $message);

                    foreach ($lines as $line) {
                        $results = str_split($line, 998);

                        foreach ($results as $result) {
                            if (substr(PHP_OS, 0, 3) != 'WIN') {
                                fputs($handle, $result . $this->crlf);
                            } else {
                                fputs($handle, str_replace("\n", "\r\n", $result) . $this->crlf);
                            }
                        }
                    }

                    fputs($handle, '.' . $this->crlf);

                    $reply = '';

                    while ($line = fgets($handle, 515)) {
                        $reply .= $line;

                        if (substr($line, 3, 1) == ' ') {
                            break;
                        }
                    }

                    if (substr($reply, 0, 3) != 250) {
                        throw new \Core\Exception('Error: DATA not accepted from server!');
                        return false;
                    }

                    fputs($handle, 'QUIT' . $this->crlf);

                    $reply = '';

                    while ($line = fgets($handle, 515)) {
                        $reply .= $line;

                        if (substr($line, 3, 1) == ' ') {
                            break;
                        }
                    }

                    if (substr($reply, 0, 3) != 221) {
                        throw new \Core\Exception('Error: QUIT not accepted from server!');
                        return false;
                    }

                    fclose($handle);
                }
            }
        }
    }

    protected function initMandrill() {
        $this->mandrill = new \Mandrill($this->mandrill_key);

        $message = array(
            'html' => $this->html,
            'text' => $this->text,
            'subject' => $this->subject,
            'from_email' => $this->from,
            'from_name' => $this->sender,
            'to' => array(),
            'headers' => array(),
            'important' => false,
            'track_opens' => false,
            'track_clicks' => false,
            'auto_text' => true,
            'auto_html' => true,
            'inline_css' => true,
            'url_strip_qs' => false,
            'preserve_recipients' => false,
            'view_content_link' => true,
            'merge_language' => 'mailchimp',
            'global_merge_vars' => array(),
            'merge_vars' => array(),
            'tags' => $this->tags,
            'attachments' => array(),
            'async' => true,
            'send_at' => gmdate("Y-m-d H:i:s", time())
        );

        if (!is_array($this->to)) {
            $to = array($this->to);
        } else {
            $to = $this->to;
        }
        foreach ($to as $t) {
            $message['to'][] = array('email' => $t);
        }

        if ($this->reply_to) {
            $message['headers']['Reply-To'] = $this->reply_to_email;
        }

        if ($this->MessageId) {
            $message['headers']["Message-Id"] = $this->MessageId;
        }

        foreach ($this->attachments as $afn => $attachment) {
            if (file_exists($attachment)) {
                $handle = fopen($attachment, 'r');

                $content = fread($handle, filesize($attachment));

                fclose($handle);

                $message['attachments'][] = array(
                    'type' => 'application/octet-stream',
                    'name' => basename($afn),
                    'content' => chunk_split(base64_encode($content))
                );
            }
        }


        return $message;
    }

    public function sendMandrillSingle() {
        $message = $this->initMandrill();
        try {
            $result = $this->mandrill->messages->send($message, false);
            return true;
        } catch (Mandrill_Error $ex) {
            throw new \Core\Exception($ex->getMessage());
            return false;
        }
    }

    public function sendMandrillBatch() {
        
    }

}
