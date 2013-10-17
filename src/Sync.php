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
require 'Sync/RememberTheMilk.php';
require 'Sync/GoogleCalendar.php';

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
    public $lists;
    public $calendars;
    public $results;

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
        $this->clear();
    }

    /**
     * [setMocks description]
     *
     * @param RememberTheMilk $rememberTheMilk Mocked class for tests
     * @param GoogleCalendar  $googleCalendar  Mocked class for tests
     *
     * @return none
     */
    public function setMocks($rememberTheMilk, $googleCalendar)
    {
        $this->_rtm = $rememberTheMilk;
        $this->_gc  = $googleCalendar;
    }

    /**
     * Clear previous results
     *
     * @return none
     */
    public function clear()
    {
        $this->results = array(
            'ok'        => array(),
            'error'     => array(),
            'warning'   => array(),
            'log'       => array(),
            );
    }

    /**
     * [ok description]
     *
     * @param String $info Message description
     *
     * @return Boolean
     */
    protected function ok($info)
    {
        $this->log('ok', $info);
        return true;
    }

    /**
     * [error description]
     *
     * @param String $info Message description
     *
     * @return Boolean
     */
    protected function error($info)
    {
        $this->log('error', $info);
        return false;
    }

    /**
     * [warning description]
     *
     * @param String $info Message description
     *
     * @return Boolean
     */

    protected function warning($info)
    {
        $this->log('warning', $info);
        return true;
    }

    /**
     * [log description]
     *
     * @param String $level Message description
     * @param String $info  Message description
     *
     * @return Boolean
     */

    protected function log($level, $info)
    {
        $this->results[$level][] = $info;
        $this->results['log'][] = date('Y-m-d H:i:s').' '.$info;
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
        $this->ok("sync data loaded");
    }

    /**
     * [save description]
     *
     * @return [type] [description]
     */
    public function save()
    {
        file_put_contents($this->_filePath, json_encode($this->_data));
        $this->ok("sync data saved");
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
        try {
            $this->_rtm->connect(RTM_APIKEY, RTM_SECRET, $this->_data['auth']['rtm_token']);
            $this->ok("Connected to RTM");
        } catch(Exception $e) {
            $this->warning("Error connecting to RTM {$e->getMessage()}");
            throw new Exception($e->getMessage());
        }
        try {
            $this->_gc->connect(GOOGLE_CLIENTID, GOOGLE_CLIENTSECRET, $this->_data['auth']['google_code']);
            $this->ok("Connected to Google");
        } catch(Exception $e) {
            $this->warning("Error connecting to Google {$e->getMessage()}");
            throw new Exception($e->getMessage());
        }
    }

    /**
     * [getLists description]
     *
     * @return [type] [description]
     */
    public function getLists()
    {
        $this->lists = $this->_rtm->getLists();
    }

    /**
     * [getCalendars description]
     *
     * @return [type] [description]
     */
    public function getCalendars()
    {
        $this->calendars = $this->_gc->getCalendars();
    }

    /**
     * [sync description]
     *
     * @return [type] [description]
     */
    public function sync()
    {
        $this->clear();
        $this->load();

        try {
            $this->connect();
        } catch(Exception $e) {
            $this->error("Error connecting to service provider");
            return false;
        }

        $this->getLists();
        //TODO: *** $this->getCalendars();

        $newSync = array();
        foreach ( $this->_data['configuration']['Match'] as $match) {
            $newSync[$match['id']] = $this->_syncMatch($match);
        }
        $this->_data['sync'] = $newSync;
        $this->save();
    }


    /**
     * [_fillEventsByMathId description]
     *
     * @param [type] $mathId     [description]
     * @param [type] &$eventsRTM [description]
     * @param [type] &$eventsGC  [description]
     *
     * @return [type]             [description]
     */
    private function _fillEventsByMathId($mathId, &$eventsRTM, &$eventsGC)
    {
        if (!isset($this->_data['sync'][$mathId]))
            return;

        foreach ($this->_data['sync'][$mathId] as $index => $eventCouple) {
            $eventsRTM[$eventCouple['rtm']['id']] = $eventCouple['rtm'];
            $eventsGC[$eventCouple['google']['id']] = $eventCouple['google'];

            $eventsRTM[$eventCouple['rtm']['id']]['index'] = $index;
            $eventsGC[$eventCouple['google']['id']]['index'] = $index;
        }
    }

    /**
     * [_syncMatch description]
     *
     * @param [type] $match [description]
     *
     * @return [type]        [description]
     */
    private function _syncMatch($match)
    {
        $eventsRTM = array();
        $eventsGC  = array();
        $eventsNew = array();

        $this->_fillEventsByMathId($match['id'], $eventsRTM, $eventsGC);

        $tasks      = $this->_rtm->getTasks($match['rtm']['id']);
        //TODO: *** $events     = $this->_gc->getEvents($match['google']['id']);

        $this->_syncMatchRTM2GC($match, $tasks, $events, $eventsNew, $eventsGC, $eventsRTM);
        $this->_syncMatchGC2RTM($events, $eventsGC, $eventsRTM);
        return $eventsNew;
    }

    /**
     * [_syncMatchRTM2GC description]
     *
     * @param [type] &$match     [description]
     * @param [type] &$tasks     [description]
     * @param [type] &$events    [description]
     * @param [type] &$eventsNew [description]
     * @param [type] &$eventsGC  [description]
     * @param [type] &$eventsRTM [description]
     *
     * @return [type]            [description]
     */
    private function _syncMatchRTM2GC(&$match, &$tasks, &$events, &$eventsNew, &$eventsGC, &$eventsRTM)
    {
        $listId     = $match['rtm']['id'];
        $calendarId = $match['google']['id'];

        // TODO: check for list name
        // TODO: check for calendar name

        // check new or modified
        foreach ($tasks as $taskId => $task) {
            $date = $task->getTask()->get('due');
            if (strlen($date) < 2) {
                // skip, no due date
                $this->ok("Skip RTM task $taskId. Not due date {$task->getName()}'");
            } else {
                if (!isset($eventsRTM[$taskId])) {
                    // New: Not in RTM and GC
                    // TODO: Location
                    $createdEvent = $this->_gc->insertEvent(
                        $calendarId, $task->getName(), $date, $date,
                        false, $match['google']['backgroundColor'], $match['google']['foregroundColor']
                    );
                    $eventsNew[] = array(
                        'rtm' => array(
                            'list_id' => $listId,
                            'id' => $taskId,
                            'last' => $task->getModified()
                        ),
                        'google' => array(
                            'id' => $createdEvent['id'],
                            'last' => $createdEvent['updated']
                        ),
                        'conflict' => false,
                    );
                    $this->ok("Add RTM task $taskId to GC: $date '{$task->getName()}'");
                } else if ($task->getModified() != $eventsRTM[$taskId]['last']) {
                    // updated in RTM
                    $this->ok("Update RTM task $taskId in GC: ({$task->getModified()} != {$eventsRTM[$taskId]['last']}) $date '{$task->getName()}'");
                } else {
                    // no changes
                    $eventsNew[] = $this->_data['sync'][$match['id']][$eventsRTM[$taskId]['index']];
                    $eventsNew[count($eventsNew)-1]['halftrue'] = true;
                    $this->ok("Preserve RTM task $taskId in GC (halftrue) $date '{$task->getName()}'");
                }
            }

        }

        // check deleted
        echo "  _syncMatchRTM2GC\n";
    }

    /**
     * [_syncMatchGC2RTM description]
     *
     * @param [type] &$events    [description]
     * @param [type] &$eventsGC  [description]
     * @param [type] &$eventsRTM [description]
     *
     * @return [type]            [description]
     */
    private function _syncMatchGC2RTM(&$events, &$eventsGC, &$eventsRTM)
    {
        // check new or modified
        // check deleted
        echo "  _syncMatchGC2RTM\n";
    }
}
