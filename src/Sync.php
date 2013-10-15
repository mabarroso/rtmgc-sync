<?php
/**
 * Sync.php
 * Sync application
 *
 * PHP version 5.2
 *
 * @category  RTMGC
 * @package   Sync
 * @author    mabarroso <mabarroso@mabarroso.com>
 * @copyright 2013 mabarroso.com
 * @license   Apache 2 License http://www.apache.org/licenses/LICENSE-2.0.html
 * @version   GIT: $Id$
 * @link      http://www.mabarroso.com
 * @since     File available since Release 0.1
 */

require_once 'bootstrap.php';
require "Sync/RememberTheMilk.php";
require "Sync/GoogleCalendar.php";

/**
 * Sync
 *
 * @category  RTMGC
 * @package   Sync
 * @author    mabarroso <mabarroso@mabarroso.com>
 * @copyright 2013 mabarroso.com
 * @license   Apache 2 License http://www.apache.org/licenses/LICENSE-2.0.html
 * @version   GIT: $Id$
 * @link      http://www.mabarroso.com
 * @since     File available since Release 0.1
 */
class Sync
{
    private $_filePath;
    private $_data;
    private $_rtm;
    private $_gc;

    /**
     * [__construct description]
     *
     * @param [type] $filePath [description]
     */
    public function __construct($filePath)
    {
        $this->_filePath = $filePath;
        $this->_rtm      = new RememberTheMilk;
        $this->_gc       = new GoogleCalendar;
    }

    /**
     * [load description]
     *
     * @return [type] [description]
     */
    public function load()
    {
        $data = file_get_contents($this->_filePath);
        $this->_data = json_decode($data, true);
    }

    /**
     * [save description]
     *
     * @return [type] [description]
     */
    public function save()
    {
        file_put_contents($this->_filePath, json_encode($this->_data));
    }

    /**
     * [getData description]
     *
     * @return [type] [description]
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * [connect description]
     *
     * @return [type] [description]
     */
    public function connect()
    {
        $this->_rtm->connect(RTM_APIKEY, RTM_SECRET, $this->_data["auth"]["rtm_token"]);
        $this->_gc->connect(GOOGLE_CLIENTID, GOOGLE_CLIENTSECRET, $this->_data["auth"]["google_code"]);


        $lists = $this->_rtm->getLists();
        $calendars = $this->_gc-> getCalendars();

echo var_export($lists, true)."\n";
echo var_export($calendars, true)."\n";
    }


}
