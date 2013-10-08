<?php
/**
 * RememberTheMilk
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
            throw new Exception($e->message);
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
                $lists[$list->getId()] = $list->getName();
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
        if (!$this->_lists) $this->_loadTasks($listId);

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
        if (!$this->_lists) $this->_loadTasks($listId);

    }
}
