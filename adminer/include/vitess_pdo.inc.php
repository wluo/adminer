<?php
// PDO can be used in several database drivers
if (class_exists('VitessPdo\PDO')) {
    class Min_VitessPDO extends \Adminer\Vitess\VitessPDO
    {}

    class Min_VitessPDOStatement extends \Adminer\Vitess\VitessPDOStatement
    {}
}

$drivers = array();
