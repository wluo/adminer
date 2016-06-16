<?php
/**
 * @author     mfris
 * @copyright  Pixel federation
 * @license    Internal use only
 */

namespace Adminer\Vitess\PDOStatementMock;

/**
 * Description of class Keyspace
 *
 * @author  mfris
 * @package ${NAMESPACE}
 */
class Keyspace extends Dummy
{

    /**
     * @var string
     */
    private $keyspace;

    /**
     * Min_KeyspacePDOStatement constructor.
     *
     * @param string $keyspace
     */
    public function __construct($keyspace)
    {
        $this->keyspace = $keyspace;
    }


}
