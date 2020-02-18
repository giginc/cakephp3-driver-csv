<?php

namespace Giginc\Csv\ORM;

use BadMethodCallException;
use Cake\Datasource\EntityInterface;
use Cake\Datasource\Exception\InvalidPrimaryKeyException;
use Cake\ORM\Table as CakeTable;
use Exception;
use Giginc\Csv\Database\Driver\Csv;

class Table extends CakeTable
{
    // csv driver
    protected $_driver = null;

    protected $_primaryKey = 0;

    protected $_fileLength = 0;

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
        $file = $this->_driver->getConnection($this->getTable());

        return $file;
    }// }}}

    /** {{{ _disconnect
     * Closes the current datasource connection.
     */
    private function _disconnect()
    {
        $this->_driver->disconnect();
    }// }}}

    /** {{{ setFileLength
     * Set file length
     *
     * @param integer $fileLength
     */
    public function setFileLength($fileLength)
    {
        $this->_fileLength = $fileLength;
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

    /** {{{ getSchema
     * Returns the schema table object describing this table's properties.
     *
     * @return \Cake\Database\Schema\TableSchema
     */
    public function getSchema()
    {
        if ($this->_schema === null) {
            $file = $this->_getConnection();

            $row = 1;
            while (($data = fgetcsv($file, $this->_fileLength, $this->_delimiter)) !== FALSE) {
                // if $this->_schemaRow is 0 then the column number become this schema.
                if ($this->_schemaRow === 0) {
                    $res = [];
                    $count = count($data);
                    for ($i=0;$i<$count;$i++) {
                        $res[$i] = $i;
                    }

                    $this->_disconnect();
                    return $res;
                }

                if ($row == $this->_schemaRow) {
                    $this->_schema = $data;

                    $this->_disconnect();
                    return $this->_schema;
                }
                $row++;
            }
        }
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
        return false;
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
        $schema = $this->getSchema();
        $file = $this->_getConnection();
        $primaryColumNumber = array_search($this->_primaryKey, $schema);
        $row = 1;
        $response = [];
        while (($data = fgetcsv($file, $this->_fileLength, $this->_delimiter)) !== FALSE) {
            if ($row === $this->_schemaRow) {
                $row++;
                continue; // skip schema row
            }
            if (isset($data[$primaryColumNumber]) && $data[$primaryColumNumber] == $primaryKey) {
                foreach ($schema as $key => $value) {
                    $response[$value] = $data[$key];
                }

                $this->_disconnect();
                return $response;
            }
            $row++;
        }
        $this->_disconnect();

        return false;
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

    /**  {{{ updateAll
     * {@inheritDoc}
     */
    public function updateAll($fields, $conditions)
    {
        return false;
    }// }}}
}
/* vim:set foldmethod=marker tabstop=4 shiftwidth=4 autoindent :*/
