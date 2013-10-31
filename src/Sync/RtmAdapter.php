<?php
/**
 * RtmAdapter.php
 * RememberTheMilk API Adapter
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
 * RememberTheMilk API Adapter
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
class RtmAdapter
{
    public $_rtm;

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
     * [getLists description]
     *
     * @return none
     */
    public function getLists()
    {
        if (!$this->_rtm) return;
        return $this->_rtm->getService(Rtm::SERVICE_LISTS)->getList();
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
        if (!$this->_rtm) return;
        return $this->_rtm->getService(Rtm::SERVICE_TASKS)->getList(null, $listId)->getTaskseries();
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
     * [setListName description]
     *
     * @param [type] $listId [description]
     * @param [type] $name   [description]
     *
     * @return [type]        [description]
     */
    public function setListName($listId, $name)
    {
        $list = $this->_rtm->getService(Rtm::SERVICE_LISTS)->setName($listId, $name);

        return $list;
    }

}
