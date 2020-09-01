<?php
declare(strict_types=1);

namespace Giginc\Csv\ORM;

use Cake\Datasource\EntityInterface;
use Cake\ORM\Table as CakeTable;
use Exception;
use Giginc\Csv\Database\Driver\Csv;
use League\Csv\Statement;

class Table extends CakeTable
{
    protected $_driver;

    protected $_csv;

    protected $_primaryKey = 0;

    protected $_delimiter = ',';

    protected $_enclosure = '"';

    protected $_escape = "\\";

    protected $_schemaRow = 0;

    /**
     * The schema object containing a description of this table fields
     *
     * @var \Cake\Database\Schema\TableSchema
     */
    protected $_schema;

    /**
     * return Csv file
     *
     * @return \Giginc\Csv\ORM\file
     * @throws \Exception
     */
    private function _getConnection()
    {
        $this->_driver = $this->getConnection()->getDriver();
        if (!$this->_driver instanceof Csv) {
            throw new Exception("Driver must be an instance of 'Giginc\Csv\Database\Driver\Csv'");
        }
        $this->_csv = $this->_driver->getConnection($this->getTable());
        $this->_csv->setDelimiter($this->_delimiter);
        $this->_csv->setEnclosure($this->_enclosure);
        $this->_csv->setEscape($this->_escape);

        // 1 row is 0
        $this->_schemaRow = $this->_schemaRow - 1;

        // get schema
        if ($this->_schemaRow >= 0) {
            $this->_csv->setHeaderOffset($this->_schemaRow);
        }
        $this->getSchema();

        return $this->_csv;
    }

    /**
     * Closes the current datasource connection.
     *
     * @access private
     * @return void
     */
    private function _disconnect()
    {
        $this->_driver->disconnect();
    }

    /**
     * Set delimiter
     *
     * @param string $delimiter Delimiter.
     * @access public
     * @return void
     */
    public function setDelimiter($delimiter)
    {
        $this->_delimiter = $delimiter;
    }

    /**
     * Set enclosure
     *
     * @param string $enclosure Enclosure.
     * @access public
     * @return void
     */
    public function setEnclosure($enclosure)
    {
        $this->_enclosure = $enclosure;
    }

    /**
     * Set escape
     *
     * @param string $escape Escape.
     * @access public
     * @return void
     */
    public function setEscape($escape)
    {
        $this->_escape = $escape;
    }

    /**
     * Set schema row
     *
     * @param int $schemaRow Schema row.
     * @access public
     * @return void
     */
    public function setSchemaRow($schemaRow)
    {
        $this->_schemaRow = $schemaRow;
    }

    /**
     * Creates a new Query instance for a table.
     *
     * @access public
     * @return \League\Csv\Statement
     */
    public function query()
    {
        return new Statement();
    }

    /**
     * Returns the schema table object describing this table's properties.
     *
     * @access public
     * @return void
     */
    public function getSchema()
    {
        if ($this->_schema === null) {
            $schema = $this->_csv->getHeader();

            if ($this->_schemaRow >= 0) {
                $this->_schema = $schema;
            } else {
                $this->_schema = range(0, count($schema));
            }
        }
    }

    /**
     * Sets the schema table object describing this table's properties.
     *
     * If an array is passed, a new TableSchema will be constructed
     * out of it and used as the schema for this table.
     *
     * @param array|\Cake\Database\Schema\TableSchema $schema Schema to be used for this table
     * @return $this
     */
    public function setSchema($schema)
    {
        if (is_array($schema)) {
            $this->_schema = $schema;
        }

        return $this;
    }

    /**
     * always return true because Csv is schemaless
     *
     * @param string $field Field name.
     * @access public
     * @return bool
     */
    public function hasField($field)
    {
        return true;
    }

    /**
     * find documents
     *
     * @param string $type Type.
     * @param array $options Option.
     * @access public
     * @return array
     * @throws \Exception
     */
    public function find($type = 'all', $options = [])
    {
        $entity = $this->getEntityClass();
        $csv = $this->_getConnection();
        $query = $this->query();
        $records = $query->process($csv);
        foreach ($records->getRecords($this->_schema) as $record) {
            $response[] = new $entity($record);
        }
        $this->_disconnect();

        return $response;
    }

    /**
     * get the document by _id
     *
     * @param string $primaryKey Primary key.
     * @param array $options Option.
     * @access public
     * @return \Cake\ORM\Entity
     * @throws \Exception
     */
    public function get($primaryKey, $options = [])
    {
        $entity = $this->getEntityClass();
        $csv = $this->_getConnection();
        $primaryColumNumber = array_search($this->_primaryKey, $this->_schema);
        $primaryKeyField = $this->_primaryKey;
        $query = $this->query();
        $records = $query->process($csv);
        foreach ($records->getRecords($this->_schema) as $record) {
            if (isset($record[$this->_primaryKey])) {
                $recordPrimaryKey = ctype_digit($record[$this->_primaryKey])
                    ? (int)$record[$this->_primaryKey] : $record[$this->_primaryKey];
                $primaryKey = ctype_digit($primaryKey) ? (int)$primaryKey : $primaryKey;

                if ($recordPrimaryKey === $primaryKey) {
                    $this->_disconnect();

                    return new $entity($record);
                }
            }
        }
        $this->_disconnect();

        return false;
    }

    /**
     * remove one document
     *
     * @param \Cake\Datasource\EntityInterface $entity Entity.
     * @param array $options Option.
     * @access public
     * @return bool
     */
    public function delete(EntityInterface $entity, $options = [])
    {
        return false;
    }

    /**
     * delete all rows matching $conditions
     *
     * @param array|null $conditions Condition.
     * @access public
     * @return bool
     */
    public function deleteAll($conditions = null)
    {
        return false;
    }

    /**
     * save the document
     *
     * @param \Cake\Datasource\EntityInterface $entity Entity.
     * @param array $options Option.
     * @return bool
     * @access public
     * @throws \Exception
     */
    public function save(EntityInterface $entity, $options = [])
    {
        return false;
    }

    /**
     * update all document
     *
     * @param array $fields Field.
     * @param array $conditions Condition.
     * @access public
     * @return bool
     */
    public function updateAll($fields, $conditions)
    {
        return false;
    }
}
