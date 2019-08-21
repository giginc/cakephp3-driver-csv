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
    protected $_fileLength = 1000;

    protected $_delimiter = ',';

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
     * @return file
     * @throws Exception
     */
    private function _getConnection()
    {
        $driver = $this->getConnection()->getDriver();
        if (!$driver instanceof Csv) {
            throw new Exception("Driver must be an instance of 'Giginc\Csv\Database\Driver\Csv'");
        }
        $file = $driver->getConnection();

        return $file;
    }

    /**
     * Returns the schema table object describing this table's properties.
     *
     * @return \Cake\Database\Schema\TableSchema
     */
    public function getSchema()
    {   
        if ($this->_schema === null) {
            $file = $this->_getConnection();

            $row = 0;
            while (($data = fgetcsv($file, $this->_fileLength, $this->_delimiter)) !== FALSE) {
                if ($row == $this->_schemaRow) {
                    $this->_schema = $data;
                    return $this->_schema;
                }
                $row++;
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
     * @param string $field
     * @return bool
     * @access public
     */
    public function hasField($field)
    {
        return true;
    }

    /**
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
    }

    /**
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
         $file = $this->_getConnection();
         $schema = $this->getSchema();
         $row = 0;
         $response = [];
         while (($data = fgetcsv($file, $this->_fileLength, $this->_delimiter)) !== FALSE) {
             if (isset($data[$this->_primaryKey]) && $data[$this->_primaryKey] == $primaryKey) {
                 foreach ($schema as $key => $value) {
                     $response[$value] = $data[$key];
                 }
                 return $response;
             }
             $row++;
         }

        return false;
    }

    /**
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
    }

    /**
     * delete all rows matching $conditions
     * @param $conditions
     * @return int
     * @throws \Exception
     */
    public function deleteAll($conditions = null)
    {
        return false;
    }

    /**
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
    }

    public function updateAll($fields, $conditions)
    {
        return false;
    }
}
