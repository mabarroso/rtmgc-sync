<?php
/**
 * RememberTheMilk.php
 * RememberTheMilk API
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

use Rtm\Rtm;

/**
 * RememberTheMilk
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
class RememberTheMilk
{
    private $_rtm;
    private $_lists;
    private $_tasks;

    /**
     * [__construct description]
     *
     * @throws Exception if token not valid
     */
    public function __construct()
    {
        $this->clear();
    }

    /**
     * [clear description]
     *
     * @return none
     */
    public function clear()
    {
        $this->_lists = false;
        $this->_tasks = array();
    }
    /**
     * [connect description]
     *
     * @param [type] $apiKey [description]
     * @param [type] $secret [description]
     * @param [type] $token  [description]
     *
     * @throws Exception if token not valid
     *
     * @return none
     */
    public function connect($apiKey, $secret, $token)
    {
        $this->_rtm = new Rtm;
        $this->_rtm->setApiKey($apiKey);
        $this->_rtm->setSecret($secret);
        $this->_rtm->setAuthToken($token);

        try {
            // Check authentication token
            $this->_rtm->getService(Rtm::SERVICE_AUTH)->checkToken();
        } catch(Exception $e) {
            $this->_rtm = false;
            throw new Exception($e->getMessage());
        }
    }

    /**
     * [getListsFromAPI description]
     *
     * @return none
     */
    public function getListsFromAPI()
    {
        if (!$this->_rtm) return;
        return $this->_rtm->getService(Rtm::SERVICE_LISTS)->getList();

    }

    /**
     * [getTasksFromAPI description]
     *
     * @param [type] $listId [description]
     *
     * @return none
     */
    public function getTasksFromAPI($listId)
    {
        if (!$this->_rtm) return;
        return $this->_rtm->getService(Rtm::SERVICE_TASKS)->getList(null, $listId)->getTaskseries();
    }

    /**
     * [loadLists description]
     *
     * @return none
     */
    private function _loadLists()
    {
        $this->_lists = $this->getListsFromAPI();
    }

    /**
     * [loadTasks description]
     *
     * @param [type] $listId [description]
     *
     * @return none
     */
    private function _loadTasks($listId)
    {
        $this->_tasks = $this->getTasksFromAPI($listId);
    }

    /**
     * [getLists description]
     *
     * @return Array [description]
     */
    public function getLists()
    {
        $lists = array();
        if (!$this->_lists) $this->_loadLists();
        foreach ($this->_lists as $list) {
            if ($list->getSmart() == 0 && $list->getDeleted() == 0 && $list->getArchived() == 0) {
                $lists[$list->getId()] = $list;
            }
        }
        return $lists;
    }

    /**
     * [getListById description]
     *
     * @param [type] $id [description]
     *
     * @return none
     */
    public function getListById($id)
    {
        if (!$this->_lists) $this->_loadLists();
        foreach ($this->_lists as $list) {
            if ($id == $list->getId()) {
                return $list;
            }
        }
        return false;
    }

    /**
     * [getTasks description]
     *
     * @param [type] $listId [description]
     *
     * @return none
     */
    public function getTasks($listId)
    {
        $tasks = array();
        if (!$this->_tasks) $this->_loadTasks($listId);
        foreach ($this->_tasks as $task) {
            if ($task->getCompleted() == '' && $task->getDeleted() == '') {
                $tasks[$task->getId()] = $task;
            }
        }
        return $tasks;
    }

    /**
     * [getTaskById description]
     *
     * @param [type] $listId [description]
     * @param [type] $id     [description]
     *
     * @return none
     */
    public function getTaskById($listId, $id)
    {
        if (!$this->_tasks) $this->_loadTasks($listId);
        foreach ($this->_tasks as $task) {
            if ($id == $task->getId()) {
                return $task;
            }
        }
        return false;
    }

    /**
     * [task description]
     *
     * @param [type]  $name      [description]
     * @param [type]  $startDate Format '2012-10-31T10:25:00.000-05:00'
     * @param [type]  $endDate   Format '2012-10-31T10:25:00.000-05:00'
     * @param boolean $location  [description]
     *
     * @return String            RTM task string to parse
     */
    public function task($name, $startDate, $endDate = false, $location = false)
    {
        $task = "$name ^$startDate";

        if ($endDate) {
            //TODO: Duration
        }

        if ($location) {
            //TODO: Location
        }

        return $task;
    }

    /**
     * [addTask description]
     *
     * @param String $listId     [description]
     * @param String $taskString [description]
     *
     * @return DataContainer [description]
     */
    public function addTask($listId, $taskString)
    {
        return $this->_rtm->getService(Rtm::SERVICE_TASKS)->add($taskString, $listId);
    }

    /**
     * [updateTask description]
     *
     * @param [type]  $taskId    [description]
     * @param String  $listId    [description]
     * @param [type]  $name      [description]
     * @param [type]  $startDate Format '2012-10-31T10:25:00.000-05:00'
     * @param [type]  $endDate   Format '2012-10-31T10:25:00.000-05:00'
     * @param boolean $location  [description]
     *
     * @return DataContainer      [description]
     */
    public function updateTask($taskId, $listId, $name = false, $startDate = false, $endDate = false, $location = false)
    {
        $taskSeriesId = $taskId;
        $realTaskId = $this->getTaskById($listId, $taskId)->get('task')->get('id');

        if ($name) {
            //$updatedTask = $this->_calendar->events->setName($realTaskId, $listId, $taskSeriesId, $name);
            //TODO
        }

        if ($startDate) {
            //$updatedTask = $this->_calendar->events->setDueDate($realTaskId, $listId, $taskSeriesId, $startDate, true, true);
            //TODO
        }

        if ($endDate) {
            //TODO: Duration
        }

        if ($location) {
            //TODO: Location
        }


        return $updatedTask;
    }

    /**
     * [deleteEvent description]
     *
     * @param [type] $taskId [description]
     * @param String $listId [description]
     *
     * @return DataContainer [description]
     */
    public function deleteTask($taskId, $listId)
    {
        $taskSeriesId = $taskId;
        $realTaskId = $this->getTaskById($listId, $taskId)->get('task')->get('id');

        $deletedTask = $this->_calendar->events->delete($realTaskId, $listId, $taskSeriesId);

        return $deletedTask;
    }

    /**
     * [updateListName description]
     *
     * @param [type] $listId [description]
     * @param [type] $name   [description]
     *
     * @return [type]        [description]
     */
    public function updateListName($listId, $name)
    {
        $list = $this->getListById($listId);

        $list->setName($listId, $name);

        return $list;
    }
}
