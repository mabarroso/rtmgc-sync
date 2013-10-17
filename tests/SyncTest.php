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

        $googleCalendar = $this->getMock('GoogleCalendar', array('getCalendarsFromAPI', 'getEventsFromAPI', 'insertEvent'));
        $googleCalendar->expects($this->any())
            ->method('getCalendarsFromAPI')
            ->will($this->returnValue($listCalendarList));
        $googleCalendar->expects($this->any())
            ->method('getEventsFromAPI')
            ->will($this->returnValue($listEvents));
        $googleCalendar->expects($this->any())
            ->method('insertEvent')
            ->will(
                $this->returnValue(
                    array(
                        'id' => 'event_id',
                        'updated' => 'updated_date',
                    )
                )
            );

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

        $listsIds = array_keys($this->subject->lists);
        $this->assertEquals('25392426', $listsIds[1]);
        $this->assertEquals('List1', $this->subject->lists[$listsIds[1]]->getName());

        $this->assertEquals('33786422', $listsIds[10]);
        $this->assertEquals('List9', $this->subject->lists[$listsIds[10]]->getName());
    }

    /**
     * [testGetCalendars description]
     *
     * @return none
     */
    public function testGetCalendars()
    {
        $this->subject->getCalendars();

        $this->assertEquals('8vu9s3macbikbva5r1r2jj75do@group.calendar.google.com', $this->subject->calendars[0]['id']);
        $this->assertEquals('User calendar', $this->subject->calendars[0]['summary']);

        $this->assertEquals('ppcemf16ugnpfspnmj9jjpde08@group.calendar.google.com', $this->subject->calendars[3]['id']);
        $this->assertEquals('RTM List 2', $this->subject->calendars[3]['summary']);
    }

    /**
     * [testSync description]
     *
     * @return none
     */
    public function testSync()
    {
    }

    /**
     * [testFillEventsByMatchId description]
     *
     * @return none
     */
    public function testFillEventsByMatchId()
    {
        $match = $this->subject->data['configuration']['Match'][0];

        $eventsRTM  = array();
        $eventsGC   = array();
        $this->subject->fillEventsByMatchId($match['id'], $eventsRTM, $eventsGC);

        $this->assertEquals('210833888', $eventsRTM['210833888']['id']);
        $this->assertEquals('c1tv9h466dm3ifd3olott04200', $eventsGC['c1tv9h466dm3ifd3olott04200']['id']);
    }

    /**
     * [testSyncMatch description]
     *
     * @return none
     */
    public function testSyncMatch()
    {
    }

    /**
     * [testSyncMatchRTM2GC description]
     *
     * @return none
     */
    public function testSyncMatchRTM2GC()
    {

        $match = $this->subject->data['configuration']['Match'][0];

        $eventsRTM  = array();
        $eventsGC   = array();
        $this->subject->fillEventsByMatchId($match['id'], $eventsRTM, $eventsGC);

        $eventsNew  = array();
        $tasks      = $this->subject->rtm->getTasks($match['rtm']['id']);
        $this->subject->syncMatchRTM2GC($match, $tasks, $events, $eventsNew, $eventsGC, $eventsRTM);

print_r($eventsNew);
//print_r($eventsRTM);
print_r($this->subject->results['log']);
    }


}
