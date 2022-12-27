<?php
/*
PHPTelnet 1.1
by Antone Roundy
adapted from code found on the PHP website
public domain
*/


class telnet_lib
{
    var $use_usleep=1;  // change to 1 for faster execution
    // don't change to 1 on Windows servers unless you have PHP 5
    var $sleeptime=125000;
    var $loginsleeptime=1000000;

    var $fp=NULL;
    var $loginprompt;

    var $conn1;
    var $conn2;

    /*
        0 = success
        1 = couldn't open network connection
        2 = unknown host
        3 = login failed
        4 = PHP version too low
    */

    function Connect($server, $port, $user, $pass)
    {
        $rv = 0;
        $vers = explode('.',PHP_VERSION);
        $needvers = [4,3,0];
        $j = count($vers);
        $k = count($needvers);
    
        if ($k < $j)
            $j = $k;
    
        for ($i=0; $i<$j; ++$i)
        {
            if ( ($vers[$i]+0) > $needvers[$i] )
                break;
            if ( ($vers[$i]+0) < $needvers[$i] )
                return 4;
        }

        $this->Disconnect();

        if ( strlen($server) )
        {
            if (preg_match('/[^0-9.]/', $server))
            {
                $ip = gethostbyname($server);
                if ($ip == $server)
                {
                    $ip = '';
                    $rv = 2;
                }
            }
            else
                $ip = $server;
        }
        else
            $ip = '127.0.0.1';

        if (strlen($ip))
        {
            if ($this->fp=fsockopen($ip, $port))
            {
                fputs($this->fp,$this->conn1);
                $this->Sleep();

                fputs($this->fp,$this->conn2);
                $this->Sleep();
                $this->GetResponse($r);
                $r = explode("\n",$r);
                $this->loginprompt = $r[count($r)-1];

                fputs($this->fp, "USER $user\r");
                $this->Sleep();

                fputs($this->fp, "PASS $pass\r");
                if ($this->use_usleep)
                    usleep($this->loginsleeptime);
                else
                    sleep(1);
                $this->GetResponse($r);
                $r = explode("\n", $r);
                if (($r[count($r)-1] == '')||($this->loginprompt == $r[count($r)-1]))
                {
                    $rv = 3;
                    $this->Disconnect();
                }
            }
            else
                $rv=1;
        }
        return $rv;
    }


    function Disconnect($exit = 1)
    {
        if ($this->fp)
        {
            if ($exit)
                $this->DoCommand('exit', $junk);
            fclose($this->fp);
            $this->fp=NULL;
        }
    }


    function DoCommand($c, &$r)
    {
        if ($this->fp)
        {
            fputs($this->fp, "$c\r");
            $this->Sleep();
            $this->GetResponse($r);
            $r=preg_replace('/^.*?\n(.*)\n[^\n]*$/', '$1', $r);
        }
        return $this->fp?1:0;
    }


    function GetResponse(&$r)
    {
        $r = '';
        do
        {
            $r .= fread($this->fp,1000);
            $s = socket_get_status($this->fp);
        }
        while ($s['unread_bytes']);
    }


    function Sleep()
    {
        if ($this->use_usleep)
            usleep($this->sleeptime);
        else
            sleep(1);
    }


    function PHPTelnet()
    {
        $this->conn1=chr(0xFF).chr(0xFB).chr(0x1F).chr(0xFF).chr(0xFB).chr(0x20).
                    chr(0xFF).chr(0xFB).chr(0x18).chr(0xFF).chr(0xFB).chr(0x27).chr(0xFF).
                    chr(0xFD).chr(0x01).chr(0xFF).chr(0xFB).chr(0x03).chr(0xFF).chr(0xFD).
                    chr(0x03).chr(0xFF).chr(0xFC).chr(0x23).chr(0xFF).chr(0xFC).chr(0x24).
                    chr(0xFF).chr(0xFA).chr(0x1F).chr(0x00).chr(0x50).chr(0x00).chr(0x18).
                    chr(0xFF).chr(0xF0).chr(0xFF).chr(0xFA).chr(0x20).chr(0x00).chr(0x33).
                    chr(0x38).chr(0x34).chr(0x30).chr(0x30).chr(0x2C).chr(0x33).chr(0x38).
                    chr(0x34).chr(0x30).chr(0x30).chr(0xFF).chr(0xF0).chr(0xFF).chr(0xFA).
                    chr(0x27).chr(0x00).chr(0xFF).chr(0xF0).chr(0xFF).chr(0xFA).chr(0x18).
                    chr(0x00).chr(0x58).chr(0x54).chr(0x45).chr(0x52).chr(0x4D).chr(0xFF).
                    chr(0xF0);

        $this->conn2=chr(0xFF).chr(0xFC).chr(0x01).chr(0xFF).chr(0xFC).chr(0x22).
                    chr(0xFF).chr(0xFE).chr(0x05).chr(0xFF).chr(0xFC).chr(0x21);
    }
}


?>