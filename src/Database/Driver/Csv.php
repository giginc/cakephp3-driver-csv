<?php
namespace Giginc\Csv\Database\Driver;

use League\Csv\Reader;
use Exception;

class Csv
{
    /**
     * Config
     *
     * @var array
     * @access private
     */
    private $_config;

    /**
     * Are we connected to the DataSource?
     *
     * true - yes
     * false - nope, and we can't connect
     *
     * @var boolean
     * @access public
     */
    public $connected = false;

    /**
     * File Instance
     *
     * @var File
     * @access protected
     */
    protected $_csv = null;


    /**
     * Base Config
     *
     * set_string_id:
     *        true: In read() method, convert Csv\BSON\ObjectId object to string and set it to array 'id'.
     *        false: not convert and set.
     *
     * @var array
     * @access public
     *
     */
    protected $_baseConfig = [
        'baseDir' => CONFIG,
    ];

    /**
     * Direct connection with database
     *
     * @var mixed null | Mongo
     * @access private
     */
    private $connection = null;

    /**
     * @param array $config configuration
     */
    public function __construct($config)
    {
        $this->_config = array_merge($this->_baseConfig, $config);
    }

    /**
     * return configuration
     *
     * @return array
     * @access public
     */
    public function getConfig()
    {
        return $this->_config;
    }

    /**
     * connect to the database
     *
     * @return bool
     * @access public
     */
    public function connect($name)
    {
        try {
            $path = realpath($this->_config['baseDir']). DIRECTORY_SEPARATOR. $name. ".csv";

            if (file_exists($path)) {
                if (($this->_csv = Reader::createFromPath($path, 'r')) === false) {
                    trigger_error("Could not open file.{$path}");

                    return false;
                }
            } else {
                trigger_error("The specified file was not found.{$path}");

                return false;
            }

            $this->connected = true;
        } catch (Exception $e) {
            trigger_error($e->getMessage());
        }

        return $this->connected;
    }

    /**
     * return Csv file
     *
     * @access public
     */
    public function getConnection($name)
    {
        if (!$this->isConnected()) {
            $this->connect($name);
        }

        return $this->_csv;
    }

    /**
     * disconnect from the database
     *
     * @return bool
     * @access public
     */
    public function disconnect()
    {
        if ($this->connected) {
            unset($this->_csv, $this->connection);

            return $this->connected = false;
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
        return $this->connected;
    }

    /**
     * @return bool
     */
    public function enabled()
    {
        return true;
    }
}
