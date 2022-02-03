<?php
namespace App\Http\Components\Thyssen\Schneider;

use App\Http\Components\Thyssen\Schneider\Connect;

class Schneider {

    private static $connection;

    static function connect()
    {
        self::$connection = Connect::connect();
    }

    static function disconnect()
    {
        self::$connection = Connect::disconnect();
    }

    // Reading in remote folder
    static function readFolder($path)
    {
        return ftp_nlist(Connect::$connection, $path);
    }

    static function read($path)
    {
        $content = self::_readFile($path);

        return $content;
    }

    static function readFile($path)
    {
        $xmlSchema = self::_readFile($path);
        $simpleXml = self::xmlLoad($xmlSchema);

        return $simpleXml;
    }

    /**
     * Opens the current file with a given $mode
     *
     * @param string $paymentId integer - payment ID
     * @return string $contents
     */
    static function _readFile($path)
    {
        $streamData = fopen('php://temp', 'r+');

        ftp_fget(Connect::$connection, $streamData, $path, FTP_ASCII, 0);
        $fstats = fstat($streamData);
        fseek($streamData, 0);
        $contents = fread($streamData, $fstats['size']);
        fclose($streamData);

        return $contents;
    }

    static function deleteFile($path)
    {
        if (!ftp_delete(Connect::$connection, $path)) {
            $this->logs[] = "Не удалось удалить $file\n";
        }
    }

    static function xmlLoad($xmlSchema)
    {
        return \simplexml_load_string($xmlSchema);
    }

    /**
     * Creates and puts  file to remote server
     *
     * @param string $remotePath path for remoted file
     * @param string $content content of file
     * @return bool True on success, false on failure
     */
    static function putToRemote($remotePath, $content, $mode = 'FTP_ASCII')
    {
        $success = false;
        $tmpfname = tempnam(sys_get_temp_dir(), '_schneider_');
        $tmpFile = fopen($tmpfname, "w");
        fwrite($tmpFile, $content);
        fseek($tmpFile, 0);

        if (ftp_put(Connect::$connection, $remotePath, $tmpfname, FTP_BINARY)) $success = true;

        fclose($tmpFile);

        return $success;
    }

}