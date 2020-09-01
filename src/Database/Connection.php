<?php
declare(strict_types=1);

namespace Giginc\Csv\Database;

use Cake\Database\Exception\MissingConnectionException;
use Giginc\Csv\Database\Driver\Csv;

class Connection extends \Cake\Database\Connection
{
    /**
     * Contains the configuration param for this connection
     *
     * @var array
     */
    protected $_config;

    /**
     * Database Driver object
     *
     * @var \Giginc\Csv\Database\Driver\Csv ;
     */
    protected $_driver = null;

    /**
     * disconnect existent connection
     *
     * @access public
     * @return void
     */
    public function __destruct()
    {
        if ($this->_driver->connected) {
            $this->_driver->disconnect();
            unset($this->_driver);
        }
    }

    /**
     * return configuration
     *
     * @return array $_config
     * @access public
     */
    public function config()
    {
        return $this->_config;
    }

    /**
     * return configuration name
     *
     * @return string
     * @access public
     */
    public function configName()
    {
        return 'csv';
    }

    /**
     * @param null $driver driver
     * @param array $config configuration
     * @return \Giginc\Csv\Database\Driver\Csv|resource
     */
    public function driver($driver = null, $config = [])
    {
        if ($driver === null) {
            return $this->_driver;
        }
        $this->_driver = new Csv($config);

        return $this->_driver;
    }

    /**
     * connect to the database
     *
     * @return bool
     * @access public
     */
    public function connect()
    {
        try {
            $this->_driver->connect();

            return true;
        } catch (\Exception $e) {
            throw new MissingConnectionException(['reason' => $e->getMessage()]);
        }
    }

    /**
     * disconnect from the database
     *
     * @return \Giginc\Csv\Database\boole
     * @access public
     */
    public function disconnect()
    {
        if ($this->_driver->isConnected()) {
            return $this->_driver->disconnect();
        }

        return true;
    }

    /**
     * database connection status
     *
     * @return bool
     * @access public
     */
    public function isConnected()
    {
        return $this->_driver->isConnected();
    }

    /**
     * Csv doesn't support transaction
     *
     * @param callable $transaction Transaction.
     * @access public
     * @return false
     */
    public function transactional(callable $transaction)
    {
        return false;
    }

    /**
     * Csv doesn't support foreign keys
     *
     * @param callable $operation Operation.
     * @return false
     * @access public
     */
    public function disableConstraints(callable $operation)
    {
        return false;
    }

    /**
     * lastInsertId
     *
     * @param null $table Table.
     * @param null $column Column.
     * @access public
     * @return int|string|void
     */
    public function lastInsertId($table = null, $column = null)
    {
        // TODO: Implement lastInsertId() method.
    }
}
