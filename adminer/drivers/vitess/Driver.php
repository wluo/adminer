<?php
/**
 * @author     mfris
 * @copyright  Pixel federation
 * @license    Internal use only
 */

namespace Adminer\Vitess;

use Min_SQL;

/**
 * Description of class Driver
 *
 * @author  mfris
 * @package ${NAMESPACE}
 */
class Driver extends Min_SQL
{

    function insert($table, $set)
    {
        return ($set ? parent::insert($table, $set) : queries("INSERT INTO " . table($table) . " ()\nVALUES ()"));
    }

    function insertUpdate($table, $rows, $primary)
    {
        $columns = array_keys(reset($rows));
        $prefix = "INSERT INTO " . table($table) . " (" . implode(", ", $columns) . ") VALUES\n";
        $values = array();
        foreach ($columns as $key) {
            $values[$key] = "$key = VALUES($key)";
        }
        $suffix = "\nON DUPLICATE KEY UPDATE " . implode(", ", $values);
        $values = array();
        $length = 0;
        foreach ($rows as $set) {
            $value = "(" . implode(", ", $set) . ")";
            if ($values && (strlen($prefix) + $length + strlen($value) + strlen($suffix) > 1e6)) { // 1e6 - default max_allowed_packet
                if (!queries($prefix . implode(",\n", $values) . $suffix)) {
                    return false;
                }
                $values = array();
                $length = 0;
            }
            $values[] = $value;
            $length += strlen($value) + 2; // 2 - strlen(",\n")
        }
        return queries($prefix . implode(",\n", $values) . $suffix);
    }

}
