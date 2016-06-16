<?php
/**
 * @author     mfris
 * @copyright  Pixel federation
 * @license    Internal use only
 */

namespace Adminer\Vitess;

use Adminer\Vitess\PDOStatementMock\Keyspace;
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
     * @var string
     */
    private $keyspace;

    /**
     * @var string[]
     */
    private static $supportedSqlCommands = [
        'USE' => 'USE',
        'SHOW' => 'SHOW',
    ];

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
        $this->keyspace = $keyspace;
        $host = str_replace(":", ";unix_socket=", preg_replace('~:(\\d)~', ';port=\\1', $server));
        $this->dsn("vitess:host=" . $host . ';dbname=' . $keyspace, $username, $password);

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
        $parser = new \PHPSQLParser\PHPSQLParser($query);
        $parsedQuery = $parser->parsed;
        $command = false;

        foreach (self::$supportedSqlCommands as $supportedCommand) {
            if (isset($parsedQuery[$supportedCommand])) {
                $command = $supportedCommand;
                break;
            }
        }

        if (!$command) {
            throw new Exception("Unsupported query - " . $query);
        }

        if ($command === 'USE') {
            return new Keyspace($this->keyspace);
        }

        return parent::query($query, $unbuffered);
    }
}
