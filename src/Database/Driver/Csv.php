<?php
declare(strict_types=1);

namespace Giginc\Csv\Database\Driver;

use Exception;
use League\Csv\Reader;

/**
 * Csv
 *
 * @copyright Copyright (c) 2020,GIG inc.
 * @author Shota KAGAWA <kagawa@giginc.co.jp>
 */
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
     * @var bool
     * @access public
     */
    public $connected = false;

    /**
     * File Instance
     *
     * @var \Giginc\Csv\Database\Driver\File
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
     * @var mixed null | Csv
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
     * @param string $name Csv file name.
     * @access public
     * @return bool
     */
    public function connect(string $name)
    {
        try {
            $path = realpath($this->_config['baseDir']) . DIRECTORY_SEPARATOR . $name . ".csv";

            if (file_exists($path)) {
                $this->_csv = Reader::createFromPath($path, 'r');
                if ($this->_csv === false) {
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
     * @param string $name Csv file name.
     * @access public
     * @return \Giginc\Csv\Database\Driver\File
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
