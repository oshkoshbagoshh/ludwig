<?php

class FtpUploader {
    private $ftpConn;
    private $host;
    private $username;
    private $password;
    
    public function __construct($host, $username, $password) {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
    }
    
    public function connect() {
        $this->ftpConn = ftp_connect($this->host);
        if ($this->ftpConn === false) {
            throw new Exception("Could not connect to FTP server.");
        }
            
        $login = ftp_login($this->ftpConn, $this->username, $this->password);
        if (!$login) {
            throw new Exception("FTP login failed.");
        }
        
        // Turn on passive mode
        ftp_pasv($this->ftpConn, true);
    }
    
    public function uploadFile($localFile, $remoteFile) {
        if (!file_exists($localFile)) {
            throw new Exception("Local file does not exist.");
        }
        
        $upload = ftp_put($this->ftpConn, $remoteFile, $localFile, FTP_BINARY);
        if (!$upload) {
            throw new Exception("FTP upload failed.");
        }
        return true;
    }
    
    public function disconnect() {
        if ($this->ftpConn) {
            ftp_close($this->ftpConn);
        }
    }
}