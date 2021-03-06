<?php
/**
 * Глобальные опции проекта
 * @author GreyMag
 *
 */
class LPMGlobals extends Globals
{
    
    //public static $success = true;
    protected static $_instance = null;
    /**
     * @return DefaultGlobals
     */
    /*public static function getInstance() {
        if (self::$_instance === null) self::$_instance = new JGlobals();
        return self::$_instance;
    }*/
    
    /**
     * Установить информацию авторизации
     * @param $authInfo
     */
    /*public static function setAuthInfo( $authInfo )
    {
        $session = Session::getInstance();

        // записываем в сессию
        if ($authInfo === null) $session->unsetVar( self::SESSION_NAME );
        else $session->set( self::SESSION_NAME, serialize( $authInfo ) );
    }*/

    /**
     * Получить параметры от соц. сети
     * @return JAuthInfo
     */
    /*public static function getAuthInfo()
    {
       //if( !session_start() ) throw new Exception( 'Session not started' );
       $session = Session::getInstance();
       $authInfo = unserialize( $session->get( self::SESSION_NAME ) );

       return $authInfo;
    }*/

    /*public static function getImgUrl( $fileName, $subdir = '', $assetsMode = true )
    {
        if (empty( $fileName )) return '';
        if (substr( $fileName, 0, 7 ) != 'http://') {
        	if ($assetsMode)
        	   $subdir = JAIL_ASSETS_DIR . $subdir;

            return JAIL_URL . str_replace( '//', '/', $subdir . $fileName );
        }
        else return $fileName;
    }*/

    private $_db;

    public function __construct()
    {
        parent::__construct();
    }

    protected function createOptions()
    {
        return new LPMOptions();
    }

    // public function getDBConnect() {
    //     if ($this->_db == null) {
    //         $this->_db = new \GMFramework\DBConnect(
    //             MYSQL_SERVER, MYSQL_USER, MYSQL_PASS, DB_NAME, PREFIX);
    //     }
    //     return $this->_db;
    // }

    protected function createDBConnect()
    {
        $db = parent::createDBConnect();
        $db->set_charset('utf8mb4');
        return $db;
    }
}
