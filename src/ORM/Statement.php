<?php
declare(strict_types=1);

namespace Giginc\Csv\ORM;

use League\Csv\Statement as LeagueCsvStatement;

/**
 * Statement
 *
 * @uses LeagueCsvStatement
 * @copyright Copyright (c) 2020,GIG inc.
 * @author Shota KAGAWA <kagawa@giginc.co.jp>
 */
class Statement extends LeagueCsvStatement
{
    protected $_connection;
    protected $_table;
    protected $_response = [];

    /**
     * Constructor
     *
     * @param \Cake\Database\Connection $connection The connection object
     * @param \Cake\ORM\Table $table The table this query is starting on
     */
    public function __construct($connection, $table)
    {
        $this->_connection = $connection;
        $this->_table = $table;
    }

    /**
     * Closes the current datasource connection.
     *
     * @access private
     * @return void
     */
    private function _disconnect()
    {
        $this->_table
            ->disconnect();
        unset($this->_response);
    }

    /**
     * select
     *
     * @param array $options Option.
     * @access public
     * @return void
     */
    public function select(array $options = [])
    {
        $schema = $this->_table->getSchema();
        $entity = $this->_table->getEntityClass();
        $records = $this->process($this->_connection);
        foreach ($records->getRecords($schema) as $record) {
            if (isset($options['conditions']) && !empty($options['conditions'])) {
                foreach ($options['conditions'] as $key => $condition) {
                    $keys = explode('.', $key);
                    $key = count($keys) == 1 ? $keys[0] : $keys[1];
                    if (isset($record[$key]) && $record[$key] == $condition) {
                        $this->_response[] = new $entity($record);
                    }
                }
            } else {
                $this->_response[] = new $entity($record);
            }
        }
    }

    /**
     * get
     *
     * @param string $primaryKey Primary key.
     * @param array $options Option.
     * @access public
     * @return \Cake\ORM\Entity
     */
    public function get($primaryKey, array $options = [])
    {
        $primaryKeyField = $this->_table->getPrimaryKey();
        $this->select(['conditions' => [
            $primaryKeyField => $primaryKey,
        ]]);

        return $this->first();
    }

    /**
     * first
     *
     * @access public
     * @return \Cake\ORM\Entity
     */
    public function first()
    {
        $response = current($this->_response);
        $this->_disconnect();

        return $response;
    }

    /**
     * all
     *
     * @access public
     * @return \Cake\ORM\Entity
     */
    public function all()
    {
        $response = $this->_response;
        $this->_disconnect();

        return $response;
    }
}
