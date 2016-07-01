<?php
/**
 * @author     mfris
 * @copyright  Pixel federation
 * @license    Internal use only
 */

namespace Adminer\Vitess;

use VitessPdo\PDO;

/**
 * Description of class VitessPDO
 *
 * @author  mfris
 * @package Adminer\Vitess
 */
abstract class VitessPDO extends PDO
{
    private $_result;
    private $server_info;
    private $affected_rows;
    private $errno;
    private $error;

    /**
     * Min_VitessPDO constructor.
     */
    public function __construct()
    {
        global $adminer;
        $pos = array_search("SQL", $adminer->operators);
        if ($pos !== false) {
            unset($adminer->operators[$pos]);
        }
    }

    /**
     * @param string $dsn
     * @param string $username
     * @param string $password
     */
    public function dsn($dsn, $username, $password)
    {
        try {
            parent::__construct($dsn, $username, $password);
        } catch (Exception $ex) {
            auth_error($ex->getMessage());
        }
        $this->setAttribute(\PDO::ATTR_STATEMENT_CLASS, array('Min_VitessPDOStatement'));
        $this->server_info = $this->getAttribute(\PDO::ATTR_SERVER_VERSION);
    }

    /**
     * @param string $query
     * @param bool   $unbuffered
     *
     * @return bool|false|\VitessPdo\PDO\PDOStatement
     * @throws \VitessPdo\PDO\Exception
     */
    public function query($query, $unbuffered = false)
    {
        $result = parent::query($query);
        $this->error = "";
        if (!$result) {
            list(, $this->errno, $this->error) = $this->errorInfo();
            return false;
        }
        $this->store_result($result);
        return $result;
    }

    public function multi_query($query)
    {
        return $this->_result = $this->query($query);
    }

    public function store_result($result = null)
    {
        if (!$result) {
            $result = $this->_result;
            if (!$result) {
                return false;
            }
        }
        if ($result->columnCount()) {
            $result->num_rows = $result->rowCount(); // is not guaranteed to work with all drivers
            return $result;
        }
        $this->affected_rows = $result->rowCount();
        return true;
    }

    public function next_result()
    {
        if (!$this->_result) {
            return false;
        }
        $this->_result->_offset = 0;
        return @$this->_result->nextRowset(); // @ - PDO_PgSQL doesn't support it
    }

    public function result($query, $field = 0)
    {
        $result = $this->query($query);
        if (!$result) {
            return false;
        }
        $row = $result->fetch();
        return $row[$field];
    }
}