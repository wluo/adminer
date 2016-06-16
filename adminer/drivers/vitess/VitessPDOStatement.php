<?php
/**
 * @author     mfris
 * @copyright  Pixel federation
 * @license    Internal use only
 */

namespace Adminer\Vitess;

use VitessPdo\PDO\PDOStatement;

/**
 * Description of class VitessPDOStatement
 *
 * @author  mfris
 * @package ${NAMESPACE}
 */
class VitessPDOStatement extends PDOStatement
{
    /**
     * @var int
     */
    private $_offset = 0;

    /**
     * @var int
     */
    private $num_rows;

    /**
     * @return mixed
     * @throws \VitessPdo\PDO\Exception
     */
    public function fetch_assoc()
    {
        return $this->fetch(2); // PDO::FETCH_ASSOC
    }

    /**
     * @return mixed
     * @throws \VitessPdo\PDO\Exception
     */
    public function fetch_row()
    {
        return $this->fetch(3); // PDO::FETCH_NUM
    }

    /**
     * @return object
     */
    public function fetch_field()
    {
        $row = (object) $this->getColumnMeta($this->_offset++);
        $row->orgtable = $row->table;
        $row->orgname = $row->name;
        $row->charsetnr = (in_array("blob", (array) $row->flags) ? 63 : 0);
        return $row;
    }
}
