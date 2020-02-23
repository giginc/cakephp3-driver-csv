<?php
namespace Giginc\Csv\ORM;

use BadMethodCallException;
use Cake\Datasource\EntityInterface;
use Cake\Datasource\Exception\InvalidPrimaryKeyException;
use Cake\ORM\Table as CakeTable;
use Exception;
use Giginc\Csv\ORM\Statement;
use Giginc\Csv\Database\Driver\Csv;

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

    /** {{{ _getConnection
     * return Csv file
     *
     * @return file
     * @throws Exception
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
    }// }}}
    /** {{{ disconnect
     * Closes the current datasource connection.
     */
    public function disconnect()
    {
        $this->_driver->disconnect();
    }// }}}
    /** {{{ setDelimiter
     * Set delimiter
     *
     * @param string $delimiter
     */
    public function setDelimiter($delimiter)
    {
        $this->_delimiter = $delimiter;
    }// }}}
    /** {{{ setEnclosure
     * Set enclosure
     *
     * @param string $enclosure
     */
    public function setEnclosure($enclosure)
    {
        $this->_enclosure = $enclosure;
    }// }}}
    /** {{{ setEscape
     * Set escape
     *
     * @param string $escape
     */
    public function setEscape($escape)
    {
        $this->_escape = $escape;
    }// }}}
    /** {{{ setSchemaRow
     * Set schema row
     *
     * @param integer $schemaRow
     */
    public function setSchemaRow($schemaRow)
    {
        $this->_schemaRow = $schemaRow;
    }// }}}
    /** {{{ query
     * Creates a new Query instance for a table.
     *
     * @return \Cake\ORM\Query
     */
    public function query()
    {
        return new Statement($this->_getConnection(), $this);
    }// }}}
    /** {{{ getSchema
     * Returns the schema table object describing this table's properties.
     *
     * @return \Cake\Database\Schema\TableSchema
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
        return $this->_schema;
    }// }}}
    /** {{{ setSchema
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
    }// }}}
    /** {{{ hasField
     * always return true because Csv is schemaless
     *
     * @param string $field
     * @return bool
     * @access public
     */
    public function hasField($field)
    {
        return true;
    }// }}}
    /** {{{ find
     * find documents
     *
     * @param string $type
     * @param array $options
     * @return Array
     * @access public
     * @throws \Exception
     */
    public function find($type = 'all', $options = [])
    {
        $query = $this->query();
        $query->select($options);
        return $query;
    }// }}}
    /** {{{ get
     * get the document by _id
     *
     * @param string $primaryKey
     * @param array $options
     * @return \Cake\ORM\Entity
     * @access public
     * @throws \Exception
     */
    public function get($primaryKey, $options = [])
    {
        $query = $this->query();
        return $query->get($primaryKey, $options);
    }// }}}
    /** {{{ delete
     * remove one document
     *
     * @param \Cake\Datasource\EntityInterface $entity
     * @param array $options
     * @return bool
     * @access public
     */
    public function delete(EntityInterface $entity, $options = [])
    {
        return false;
    }// }}}
    /** {{{ deleteAll
     * delete all rows matching $conditions
     * @param $conditions
     * @return int
     * @throws \Exception
     */
    public function deleteAll($conditions = null)
    {
        return false;
    }// }}}
    /** {{{ seve
     * save the document
     *
     * @param EntityInterface $entity
     * @param array $options
     * @return mixed $success
     * @access public
     * @throws \Exception
     */
    public function save(EntityInterface $entity, $options = [])
    {
        return false;
    }// }}}
    /** {{{ updateAll
     * {@inheritDoc}
     */
    public function updateAll($fields, $conditions)
    {
        return false;
    }// }}}
}
/* vim:set foldmethod=marker tabstop=4 shiftwidth=4 autoindent :*/
