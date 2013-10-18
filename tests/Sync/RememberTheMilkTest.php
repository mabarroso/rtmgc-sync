<?php
/**
 * RememberTheMilk
 *
 * PHP version 5.2
 *
 * @category   RTMGC
 * @package    Sync
 * @subpackage Tests
 * @author     mabarroso <mabarroso@mabarroso.com>
 * @copyright  2013 mabarroso.com
 * @license    Apache 2 License http://www.apache.org/licenses/LICENSE-2.0.html
 * @version    GIT: $Id$
 * @link       http://www.mabarroso.com
 * @since      File available since Release 0.1
 */

require_once 'src/Sync/RememberTheMilk.php';

use Rtm\Rtm;

/**
 * RememberTheMilk
 *
 * @category   RTMGC
 * @package    Sync
 * @subpackage Tests
 * @author     mabarroso <mabarroso@mabarroso.com>
 * @copyright  2013 mabarroso.com
 * @license    Apache 2 License http://www.apache.org/licenses/LICENSE-2.0.html
 * @version    GIT: $Id$
 * @link       http://www.mabarroso.com
 * @since      File available since Release 0.1
 */
class RememberTheMilkTest extends PHPUnit_Framework_TestCase
{
    protected $subject;

    /**
     * Constructor
     *
     * @return none
     */
    protected function setUp()
    {
        $rtm = new Rtm;
        $rtmClient = $rtm->getClient();

        $this->subject = $this->getMock('RememberTheMilk', array('getListsFromAPI', 'getTasksFromAPI'));
        $this->subject->expects($this->any())
            ->method('getListsFromAPI')
            ->will($this->returnValue($rtmClient->createResponse(file_get_contents('tests/_files/rtm_service_lists.json'))->getResponse()->getLists()->getList()));
        $this->subject->expects($this->any())
            ->method('getTasksFromAPI')
            ->will($this->returnValue($rtmClient->createResponse(file_get_contents('tests/_files/rtm_service_tasks.json'))->getResponse()->getTasks()->getList()->getTaskseries()));
    }

    /**
     * [testInstanceType description]
     *
     * @return none
     */
    public function testInstanceType()
    {
        $this->assertTrue($this->subject instanceof RememberTheMilk);
    }

    /**
     * [testGetLists description]
     *
     * @return none
     */
    public function testGetLists()
    {
        $lists = $this->subject->getLists();
        $listsIds = array_keys($lists);
        $this->assertEquals('25392426', $listsIds[1]);
        $this->assertEquals('List1', $lists[$listsIds[1]]->getName());

        $this->assertEquals('33786422', $listsIds[10]);
        $this->assertEquals('List9', $lists[$listsIds[10]]->getName());
    }

    /**
     * [testGetListById description]
     *
     * @return none
     */
    public function testGetListById()
    {
        $this->assertEquals('List1', $this->subject->getListById('25392426')->getName());
        $this->assertEquals('List9', $this->subject->getListById('33786422')->getName());
    }

    /**
     * [testGetTasks description]
     *
     * @return none
     */
    public function testGetTasks()
    {
        $tasks = $this->subject->getTasks('25392426');
        $tasksIds = array_keys($tasks);

        $this->assertEquals('210835293', $tasksIds[0]);
        $this->assertEquals('ne1 event completed', $tasks[$tasksIds[0]]->getName());

        $this->assertEquals('210834146', $tasksIds[9]);
        $this->assertEquals('e3 event unchanged appointment', $tasks[$tasksIds[9]]->getName());
    }

    /**
     * [testGetListById description]
     *
     * @return none
     */
    public function testGetTaskById()
    {
        $task = $this->subject->getTaskById('25392426', '210835293');
        $this->assertEquals('ne1 event completed', $task->getName());

        $task = $this->subject->getTaskById('25392426', '210834146');
        $this->assertEquals('e3 event unchanged appointment', $task->getName());
    }

}
