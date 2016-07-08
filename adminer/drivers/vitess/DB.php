<?php
/**
 * @author     mfris
 * @copyright  Pixel federation
 * @license    Internal use only
 */

namespace Adminer\Vitess;

use Min_VitessPDO;

/**
 * Description of class DB
 *
 * @author  mfris
 * @package Adminer\Vitess
 */
class DB extends Min_VitessPDO
{

    /**
     * @var string
     */
    public $extension = "VitessPDO";

    /**
     * @param string $server
     * @param string $username
     * @param string $password
     * @param string $keyspace
     *
     * @return bool
     */
    public function connect($server, $username, $password, $keyspace)
    {
        $hosts = explode("|", $server);
        $host = str_replace(":", ";unix_socket=", preg_replace('~:(\\d)~', ';port=\\1', $hosts[0]));
        $dsnString = "vitess:host=" . $host;

        if ($keyspace) {
            $dsnString .= ';keyspace=' . $keyspace;
        }

        if (!isset($hosts[1], $hosts[2])) {
            return false;
        }

        $dsnString .= ';vtctld_host=' . preg_replace('~:(\\d)~', ';vtctld_port=\\1', $hosts[1]);
        $dsnString .= ';cell=' . trim($hosts[2]);

        $this->dsn($dsnString, $username, $password);

        return true;
    }

    /**
     * @param string $charset
     *
     * @throws Exception
     */
    public function set_charset($charset)
    {
        $this->query("SET NAMES $charset"); // charset in DSN is ignored before PHP 5.3.6
    }

    /**
     * @param string $database
     *
     * @return bool|false|\VitessPdo\PDO\PDOStatement
     * @throws Exception
     */
    public function select_db($database)
    {
        // database selection is separated from the connection so dbname in DSN can't be used
        return $this->query("USE " . idf_escape($database));
    }

    /**
     * @param string $query
     * @param bool   $unbuffered
     *
     * @return bool|false|\VitessPdo\PDO\PDOStatement
     * @throws Exception
     */
    public function query($query, $unbuffered = false)
    {
        //$this->setAttribute(1000, !$unbuffered); // 1000 - PDO::MYSQL_ATTR_USE_BUFFERED_QUERY

        $result =  parent::query($query, $unbuffered);
        \PhpConsole\Connector::getInstance()->getDebugDispatcher()->dispatchDebug($query, 'query');
        \PhpConsole\Connector::getInstance()->getDebugDispatcher()->dispatchDebug($result, 'result');

        return $result;
    }
}
