<?php
namespace App\Http\Components\Thyssen\Schneider;

class Connect {

    public static $connection;

    static private $host = 'ftp.schneider-group.com';
    static private $user = 'thyssen24';
    static private $psw = 'wDnV86W6';
    static private $port = '21';

    static function connect()
    {
        try {
            self::$connection = ftp_ssl_connect(self::$host, self::$port, 10);
            $loginResult = ftp_login(self::$connection, self::$user, self::$psw);
            ftp_pasv(self::$connection, true);

            if (!$loginResult) {
                die("Не удалось подключиться к серверу");
            }

        } catch (Exception\ExceptionFtpsConnect $e) {
            return $e->connectError();
        }

    }

    static function disconnect()
    {
        ftp_close(self::$connection);
    }
    
}