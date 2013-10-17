<?php
/**
 * SyncTest
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

require_once 'src/Sync.php';

use Rtm\Rtm;

/**
 * SyncTest
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
class SyncTest extends PHPUnit_Framework_TestCase
{
    protected $subject;

    const SYNC_FILE = 'tmp/testuser.json';

    /**
     * Constructor
     *
     * @return none
     */
    protected function setUp()
    {
        copy('tests/_files/testuser.json', self::SYNC_FILE);
        $this->subject = new Sync(self::SYNC_FILE);

        $rtm = new Rtm;
        $rtmClient = $rtm->getClient();

        $rememberTheMilk = $this->getMock('RememberTheMilk', array('getListsFromAPI', 'getTasksFromAPI'));
        $rememberTheMilk->expects($this->any())
            ->method('getListsFromAPI')
            ->will($this->returnValue($rtmClient->createResponse(file_get_contents('tests/_files/rtm_service_lists.json'))->getResponse()->getLists()->getList()));
        $rememberTheMilk->expects($this->any())
            ->method('getTasksFromAPI')
            ->will($this->returnValue($rtmClient->createResponse(file_get_contents('tests/_files/rtm_service_tasks.json'))->getResponse()->getTasks()->getList()->getTaskseries()));

        include 'tests/_files/google_listCalendarList.php';
        include 'tests/_files/google_listEvents.php';

        $googleCalendar = $this->getMock('GoogleCalendar', array('getCalendarsFromAPI', 'getEventsFromAPI'));
        $googleCalendar->expects($this->any())
            ->method('getCalendarsFromAPI')
            ->will($this->returnValue($listCalendarList));
        $googleCalendar->expects($this->any())
            ->method('getEventsFromAPI')
            ->will($this->returnValue($listEvents));

        $this->subject->setMocks($rememberTheMilk, $googleCalendar);

        $this->subject->load();
    }

    /**
     * [testInstanceType description]
     *
     * @return none
     */
    public function testInstanceType()
    {
        $this->assertTrue($this->subject instanceof Sync);
    }

    /**
     * [testClear description]
     *
     * @return none
     */
    public function testClear()
    {
        $this->subject->clear();
        $this->assertCount(4, $this->subject->results);
        $this->assertCount(0, $this->subject->results['ok']);
        $this->assertCount(0, $this->subject->results['error']);
        $this->assertCount(0, $this->subject->results['warning']);
        $this->assertCount(0, $this->subject->results['log']);
    }

    /**
     * [testOk description]
     *
     * @return none
     */
    public function testOk()
    {
        //$this->subject->clear();
        //$this->subject->ok('test');
        //$this->assertCount(1, $this->subject->results['ok']);
        //$this->assertCount(1, $this->subject->results['log']);
    }

    /**
     * [testError description]
     *
     * @return none
     */
    public function testError()
    {
        //$this->subject->clear();
        //$this->subject->error('test');
        //$this->assertCount(1, $this->subject->results['error']);
        //$this->assertCount(1, $this->subject->results['log']);
    }

    /**
     * [testWarning description]
     *
     * @return none
     */
    public function testWarning()
    {
        //$this->subject->clear();
        //$this->subject->warning('test');
        //$this->assertCount(1, $this->subject->results['warning']);
        //$this->assertCount(1, $this->subject->results['log']);
    }

    /**
     * [testLog description]
     *
     * @return none
     */
    public function testLog()
    {
        //$this->subject->clear();
        //$this->subject->log('test');
        //$this->assertCount(1, $this->subject->results['log']);
    }

    /**
     * [testLoad description]
     *
     * @return none
     */
    public function testLoad()
    {
    }

    /**
     * [testSave description]
     *
     * @return none
     */
    public function testSave()
    {
    }

    /**
     * [testGetData description]
     *
     * @return none
     */
    public function testGetData()
    {
    }

    /**
     * [testConnect description]
     *
     * @return none
     */
    public function testConnect()
    {
    }

    /**
     * [testGetLists description]
     *
     * @return none
     */
    public function testGetLists()
    {
        $this->subject->getLists();
//        print_r ($this->subject->lists);
    }

}
