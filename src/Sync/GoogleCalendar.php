<?php
/**
 * GoogleCalendar
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

set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . '/../../vendor/');
require_once 'google-api/src/Google_Client.php';
require_once 'google-api/src/contrib/Google_CalendarService.php';

/**
 * AdTest
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
class GoogleCalendar
{
    private $_client;
    private $_calendar;
    private $_calendars;
    private $_events;

    /**
     * [__construct description]
     *
     * @throws Exception if token not valid
     */
    public function __construct()
    {
        $this->_client = false;
        $this->_calendar = false;
        $this->clear();
    }

    /**
     * [clear description]
     *
     * @return none
     */
    public function clear()
    {
        $this->_calendars = false;
        $this->_events = false;

    }
    /**
     * [connect description]
     *
     * @param [type] $clientId     [description]
     * @param [type] $clientSecret [description]
     * @param [type] $token        [description]
     *
     * @throws Exception if token not valid
     *
     * @return none
     */
    public function connect($clientId, $clientSecret, $token)
    {

        $this->_client = new Google_Client();
        $this->_client->setApplicationName("Sync remember The Milk to Google Calendar");
        $this->_client->setClientId($clientId);
        $this->_client->setClientSecret($clientSecret);

        $this->_calendar = new Google_CalendarService($this->_client);

        try {
            // Check authentication token
            $this->_client->setAccessToken($token);
        } catch(Exception $e) {
            $this->_client = false;
            $this->_calendar = false;
            throw new Exception($e->message);
        }
    }

    /**
     * [getCalendarsFromAPI description]
     *
     * @return none
     */
    public function getCalendarsFromAPI()
    {
        if (!$this->_calendar) return array();
        return $this->_calendar->calendarList->listCalendarList();

    }

    /**
     * [getEventsFromAPI description]
     *
     * @param [type] $calendarId [description]
     *
     * @return none
     */
    public function getEventsFromAPI($calendarId)
    {
        if (!$this->_calendar) return array();
        return $this->_calendar->events->listEvents($calendarId);
    }

    /**
     * [loadCalendars description]
     *
     * @return none
     */
    private function _loadCalendars()
    {
        $this->_calendars = $this->getCalendarsFromAPI();
    }

    /**
     * [loadEvents description]
     *
     * @param [type] $calendarId [description]
     *
     * @return none
     */
    private function _loadEvents($calendarId)
    {
        $this->_events = $this->getEventsFromAPI($calendarId);
    }

    /**
     * [getCalendars description]
     *
     * @return Array [description]
     */
    public function getCalendars()
    {
        if (!$this->_calendars) $this->_loadCalendars();
        if (count($this->_calendars) > 0)
            return $this->_calendars['items'];
        else
            return array();
    }

    /**
     * [getCalendarById description]
     *
     * @param [type] $id [description]
     *
     * @return none
     */
    public function getCalendarById($id)
    {
        if (!$this->_calendars) $this->_loadCalendars();
        if (count($this->_calendars) > 0) {
            foreach ($this->_calendars as $calendar) {
                if ($calendar['id'] == $id) return $calendar;
            }
        } else
            return false;
    }

    /**
     * [getEvents description]
     *
     * @param [type] $calendarId [description]
     *
     * @return none
     */
    public function getEvents($calendarId)
    {
        if (!$this->_events) $this->_loadEvents();
        if (count($this->_events) > 0)
            return $this->_events['items'];
        else
            return array();
    }

    /**
     * [getEventById description]
     *
     * @param [type] $calendarId [description]
     * @param [type] $id         [description]
     *
     * @return none
     */
    public function getEventById($calendarId, $id)
    {
        if (!$this->_events) $this->_loadEvents();
        if (count($this->_events) > 0) {
            foreach ($this->_events as $event) {
                if ($event['id'] == $id) return $event;
            }
        } else
            return false;
    }
}
