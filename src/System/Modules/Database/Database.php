<?php

namespace System\Modules\Database;
use System\Modules\Database\Exceptions\DatabaseInvalidParamException;
use System\Modules\Database\Exceptions\DatabaseParamNotFoundException;
use System\System;

/**
 * Class Database
 * Database module
 * Permit to get data frm database
 * @package System\Modules\Database
 * @author Romain Bourré
 */
class Database extends \PDO {

    /**
     * Instance of PDO from configurations file parameters
     * @var
     */
    private static $_instance;

    /**
     * Get instance of Database from parameters of app configuration file
     * @return Database
     * @throws DatabaseParamNotFoundException
     * @throws DatabaseInvalidParamException
     */
    public static function getDB(): Database {
        if(is_null(self::$_instance)) {
            $conf = System::get()->getAppConf();
            if(isset($conf['database']['type'])) $type = $conf['database']['type']; else throw new DatabaseParamNotFoundException("type", System::APP_CONF_FILE);
            if(isset($conf['database']['host'])) $host = $conf['database']['host']; else throw new DatabaseParamNotFoundException("host", System::APP_CONF_FILE);
            if(isset($conf['database']['dbname'])) $dbname = $conf['database']['dbname']; else throw new DatabaseParamNotFoundException("dbname", System::APP_CONF_FILE);
            if(isset($conf['database']['user'])) $user = $conf['database']['user']; else throw new DatabaseParamNotFoundException("user", System::APP_CONF_FILE);
            if(isset($conf['database']['password'])) $pwd = $conf['database']['password']; else throw new DatabaseParamNotFoundException("password", System::APP_CONF_FILE);
            if(isset($conf['database']['options'])) {
                if(is_array($conf['database']['options'])) $options = $conf['database']['options']; else throw new DatabaseInvalidParamException("options", System::APP_CONF_FILE);
            } else $options = null;
            self::$_instance = new self("$type:dbname=$dbname;host=$host", $user, $pwd, $options);
        }
        return self::$_instance;
    }

}