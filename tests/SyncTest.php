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

        $rememberTheMilk = $this->getMock('RememberTheMilk', array('getListsFromAPI', 'getTasksFromAPI', 'addTask', 'updateTask', 'deleteTask'));
        $rtmLists        = $rtmClient->createResponse(file_get_contents('tests/_files/rtm_service_lists.json'))->getResponse()->getLists()->getList();
        $rtmTasks        = $rtmClient->createResponse(file_get_contents('tests/_files/rtm_service_tasks.json'))->getResponse()->getTasks()->getList()->getTaskseries();
        $rtmTasksNew     = $rtmClient->createResponse(file_get_contents('tests/_files/rtm_service_tasks_new.json'))->getResponse()->getTasks()->getList()->getTaskseries();
        $rtmTaskIterator = $rtmTasksNew->getIterator();
        $rtmTaskIterator->valid();
        $rtmTask = $rtmTaskIterator->current();
        $rememberTheMilk->expects($this->any())
            ->method('getListsFromAPI')
            ->will($this->returnValue($rtmLists));
        $rememberTheMilk->expects($this->any())
            ->method('getTasksFromAPI')
            ->will($this->returnValue($rtmTasks));
        $rememberTheMilk->expects($this->any())
            ->method('addTask')
            ->will($this->returnValue($rtmTask));
        $rememberTheMilk->expects($this->any())
            ->method('updateTask')
            ->will($this->returnValue($rtmTask));
        $rememberTheMilk->expects($this->any())
            ->method('deleteTask')
            ->will($this->returnValue($rtmTask));

        $rtmAdapter = $this->getMock('Object', array('setListName'));
        $tmpLists = $rtmClient->createResponse(file_get_contents('tests/_files/rtm_service_lists.json'))->getResponse()->getLists()->getList();
        $rtmAdapter->expects($this->any())
            ->method('update')
            ->will($this->returnValue($tmpLists));

        $rememberTheMilk->_rtm = $rtmAdapter;

        include 'tests/_files/google_listCalendarList.php';
        include 'tests/_files/google_listEvents.php';

        $googleCalendar = $this->getMock('GoogleCalendar', array('getCalendarsFromAPI', 'getEventsFromAPI', 'event', 'insertEvent', 'updateEvent', 'deleteEvent'));
        $googleCalendar->expects($this->any())
            ->method('getCalendarsFromAPI')
            ->will($this->returnValue($listCalendarList));
        $googleCalendar->expects($this->any())
            ->method('getEventsFromAPI')
            ->will($this->returnValue($listEvents));
        $googleCalendar->expects($this->any())
            ->method('event')
            ->will($this->returnValue(""));
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
        $googleCalendar->expects($this->any())
            ->method('updateEvent')
            ->will(
                $this->returnValue(
                    array(
                        'id' => 'event_id',
                        'updated' => 'updated_date',
                    )
                )
            );

        $googleCalendar->expects($this->any())
            ->method('deleteEvent')
            ->will($this->returnValue(""));

        $google_CalendarService = $this->getMock('Object', array('update'));
        $google_CalendarService->expects($this->any())
            ->method('update')
            ->will($this->returnValue(1));
        $googleCalendar->_calendar = $google_CalendarService;


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


        // Skip RTM task 210835293. Not due date ne1 event completed'
        foreach ($eventsNew as $eventCouple) {
            $this->assertNotEquals('210835293', $eventCouple['rtm']['id'], 'RTM 210835293 event must be ignored');
        }
        // Skip RTM task 210835257. Not due date ne0 event without date'
        foreach ($eventsNew as $eventCouple) {
            $this->assertNotEquals('210835257', $eventCouple['rtm']['id'], 'RTM 210835257 event must be ignored');
        }

        // Add RTM task 110834062A to GC: 2013-09-01T22:00:00Z 'e01 event created rtm all day'
        $this->assertEquals('110834062A', $eventsNew[0]['rtm']['id'], 'RTM 110834062A event must be sync');
        $this->assertEquals('event_id', $eventsNew[0]['google']['id'], 'RTM 110834062A event must be added to GC');
        $this->assertEquals('updated_date', $eventsNew[0]['google']['last'], 'RTM 110834062A event must be added to GC');
        $this->assertFalse($eventsNew[0]['conflict'], 'RTM 110834062A event must not be conflicted');

        // Add RTM task 110834264B to GC: 2013-09-02T08:00:00Z 'e02 event created rtm appointment'
        $this->assertEquals('110834264B', $eventsNew[1]['rtm']['id'], 'RTM 110834264B event must be sync');
        $this->assertEquals('event_id', $eventsNew[1]['google']['id'], 'RTM 110834264B event must be added to GC');
        $this->assertEquals('updated_date', $eventsNew[1]['google']['last'], 'RTM 110834264B event must be added to GC');
        $this->assertFalse($eventsNew[1]['conflict'], 'RTM 110834264B event must not be conflicted');

        // Preserve RTM task 210834211 in GC (halftrue) 2013-09-01T08:00:00Z 'e4 event changed google appointment'
        $this->assertEquals('210834211', $eventsNew[2]['rtm']['id'], 'RTM 210834211 event must be sync');
        $this->assertEquals('c1tv9h466dm3ifd3olott04204', $eventsNew[2]['google']['id'], 'RTM 210834211 event must be preserved to next check');
        $this->assertEquals('2013-09-02T20:20:30.000Z', $eventsNew[2]['google']['last'], 'RTM 210834211 event must be preserved to next check');
        $this->assertEquals(1, $eventsNew[2]['halftrue'], 'RTM 210834211 event must be marked as half check');
        $this->assertFalse($eventsNew[2]['conflict'], 'RTM 210834211 event must not be conflicted');

        // Update RTM task 210834264 in GC: (2013-09-01T20:20:30Z != 2013-09-01T10:20:30Z) 2013-09-02T08:00:00Z 'e5 event changed rtm appointment'
        $this->assertEquals('210834264', $eventsNew[3]['rtm']['id'], 'RTM 210834264 event must be sync');
        $this->assertEquals('c1tv9h466dm3ifd3olott04205', $eventsNew[3]['google']['id'], 'RTM 210834264 event must be the correct id in GC');
        $this->assertEquals('updated_date', $eventsNew[3]['google']['last'], 'RTM 210834264 event must be updated in GC');
        $this->assertFalse($eventsNew[3]['conflict'], 'RTM 210834264 event must not be conflicted');

        // Preserve RTM task 210833961 in GC (halftrue) 2013-09-02T22:00:00Z 'e1 event changed google all day'
        $this->assertEquals('210833961', $eventsNew[4]['rtm']['id'], 'RTM 210833961 event must be sync');
        $this->assertEquals('c1tv9h466dm3ifd3olott04201', $eventsNew[4]['google']['id'], 'RTM 210833961 event must be preserved to next check');
        $this->assertEquals('2013-09-02T20:20:30.000Z', $eventsNew[4]['google']['last'], 'RTM 210833961 event must be preserved to next check');
        $this->assertEquals(1, $eventsNew[4]['halftrue'], 'RTM 210833961 event must be marked as half check');
        $this->assertFalse($eventsNew[4]['conflict'], 'RTM 210833961 event must not be conflicted');

        // Preserve RTM task 210833888 in GC (halftrue) 2013-08-31T22:00:00Z 'e0 event unchanged all day'
        $this->assertEquals('210833888', $eventsNew[5]['rtm']['id'], 'RTM 210833888 event must be sync');
        $this->assertEquals('c1tv9h466dm3ifd3olott04200', $eventsNew[5]['google']['id'], 'RTM 210833888 event must be preserved to next check');
        $this->assertEquals('2013-09-01T10:20:30.000Z', $eventsNew[5]['google']['last'], 'RTM 210833888 event must be preserved to next check');
        $this->assertEquals(1, $eventsNew[5]['halftrue'], 'RTM 210833888 event must be marked as half check');
        $this->assertFalse($eventsNew[5]['conflict'], 'RTM 210833888 event must not be conflicted');

        // Update RTM task 210834062 in GC: (2013-09-03T20:20:30Z != 2013-09-03T10:20:30Z) 2013-09-01T22:00:00Z 'e2 event changed rtm all day'
        $this->assertEquals('210834062', $eventsNew[6]['rtm']['id'], 'RTM 210834062 event must be sync');
        $this->assertEquals('c1tv9h466dm3ifd3olott04202', $eventsNew[6]['google']['id'], 'RTM 210834062 event must be the correct id in GC');
        $this->assertEquals('updated_date', $eventsNew[6]['google']['last'], 'RTM 210834062 event must be updated in GC');
        $this->assertFalse($eventsNew[6]['conflict'], 'RTM 210834062 event must not be conflicted');

        // Preserve RTM task 210834146 in GC (halftrue) 2013-09-03T08:00:00Z 'e3 event unchanged appointment'
        $this->assertEquals('210834146', $eventsNew[7]['rtm']['id'], 'RTM 210834146 event must be sync');
        $this->assertEquals('c1tv9h466dm3ifd3olott04203', $eventsNew[7]['google']['id'], 'RTM 210834146 event must be preserved to next check');
        $this->assertEquals('2013-09-03T10:20:30.000Z', $eventsNew[7]['google']['last'], 'RTM 210834146 event must be preserved to next check');
        $this->assertEquals(1, $eventsNew[7]['halftrue'], 'RTM 210834146 event must be marked as half check');
        $this->assertFalse($eventsNew[7]['conflict'], 'RTM 210834146 event must not be conflicted');

        // Delete RTM task id_deleted in GC
        $this->assertEquals('id_deleted', $eventsNew[8]['rtm']['id'], 'RTM id_deleted event must be sync');
        $this->assertEquals('id_to_delete', $eventsNew[8]['google']['id'], 'RTM id_deleted event must be preserved to next check');
        $this->assertEquals(1, $eventsNew[8]['deleted'], 'RTM id_deleted event must be marked as deleted');

        // Remove deleted RTM task id_deleted2
        $this->assertEquals(9, count($eventsNew), 'RTM id_deleted2 event must be removed');
    }

    /**
     * [testSyncMatchGC2RTM description]
     *
     * @return none
     */
    public function testSyncMatchGC2RTM()
    {

        $match = $this->subject->data['configuration']['Match'][1];

        $eventsRTM  = array();
        $eventsGC   = array();
        $this->subject->fillEventsByMatchId($match['id'], $eventsRTM, $eventsGC);

        $eventsNew  = array();
        $events     = $this->subject->gc->getEvents($match['google']['id']);
        $this->subject->syncMatchGC2RTM($match, $tasks, $events, $eventsNew, $eventsGC, $eventsRTM);

        // Add GC task id_e01 to GC: 2013-09-01T10:20:30.000Z 'e01 event created all day'
        $this->assertEquals('id_e01', $eventsNew[0]['google']['id'], 'GC id_e01 event must be sync');
        $this->assertEquals('taskseries_id', $eventsNew[0]['rtm']['id'], 'GC id_e01 event must be added to RTM');
        $this->assertEquals('taskserie_modified_date', $eventsNew[0]['rtm']['last'], 'GC id_e01 event must be added to RTM');
        $this->assertFalse($eventsNew[0]['conflict'], 'GC id_e01 event must not be conflicted');

        // Add GC task id_e02 to RTM: 2013-09-01T10:20:30.000Z 'e02 event created appointment'
        $this->assertEquals('id_e02', $eventsNew[1]['google']['id'], 'GC id_e02 event must be sync');
        $this->assertEquals('taskseries_id', $eventsNew[1]['rtm']['id'], 'GC id_e02 event must be added to RTM');
        $this->assertEquals('taskserie_modified_date', $eventsNew[1]['rtm']['last'], 'GC id_e02 event must be added to RTM');
        $this->assertFalse($eventsNew[1]['conflict'], 'GC id_e01 event must not be conflicted');

        // Preserve GC task id_e03 in RTM (halftrue) 2013-09-01T08:00:00Z 'e03 event not changed'
        $this->assertEquals('id_e03', $eventsNew[2]['google']['id'], 'GC id_e03 event must be sync');
        $this->assertEquals('210833888', $eventsNew[2]['rtm']['id'], 'GC id_e03 event must be preserved to next check');
        $this->assertEquals('2013-09-01T10:20:30Z', $eventsNew[2]['rtm']['last'], 'GC id_e03 event must be preserved to next check');
        $this->assertEquals(1, $eventsNew[2]['halftrue'], 'GC id_e03 event must be marked as half check');
        $this->assertFalse($eventsNew[2]['conflict'], 'GC id_e03 event must not be conflicted');

        // Update GC task id_e04 in RTM:  2013-09-02T22:00:00Z 'e04 event changed all day'
        $this->assertEquals('id_e04', $eventsNew[3]['google']['id'], 'GC id_e04 event must be sync');
        $this->assertEquals('210833888', $eventsNew[3]['rtm']['id'], 'GC id_e04 event must be the correct id in RTM');
        $this->assertEquals('taskserie_modified_date', $eventsNew[3]['rtm']['last'], 'GC id_e04 event must be updated in RTM');
        $this->assertFalse($eventsNew[3]['conflict'], 'GC id_e04 event must not be conflicted');

        // Update GC task id_e05 in RTM 'e05 event changed appointment'
        $this->assertEquals('id_e05', $eventsNew[4]['google']['id'], 'GC id_e05 event must be sync');
        $this->assertEquals('210833888', $eventsNew[4]['rtm']['id'], 'GC id_e05 event must be the correct id in RTM');
        $this->assertEquals('taskserie_modified_date', $eventsNew[4]['rtm']['last'], 'GC id_e05 event must be updated in RTM');
        $this->assertFalse($eventsNew[4]['conflict'], 'GC id_e05 event must not be conflicted');

        // Delete GC task id_deleted in RTM
        $this->assertEquals('id_deleted', $eventsNew[5]['google']['id'], 'GC id_deleted event must be sync');
        $this->assertEquals('id_to_delete', $eventsNew[5]['rtm']['id'], 'GC id_deleted event must be preserved to next check');
        $this->assertEquals(1, $eventsNew[5]['deleted'], 'GC id_deleted event must be marked as deleted');

        // Remove deleted GC task id_deleted2
        $this->assertEquals(6, count($eventsNew), 'GC id_deleted2 event must be removed');
    }

    /**
     * [testSyncMatchNames description]
     *
     * @return none
     */
    public function testSyncMatchNames()
    {
        $match = $this->subject->data['configuration']['Match'][0];
        $this->subject->getLists();
        $this->subject->getCalendars();

        // No changes
        $match['rtm']['name'] = 'THE_NAME';
        $this->subject->lists[$match['rtm']['id']]->setName('THE_NAME');

        $match['google']['name'] = 'THE_NAME';
        $this->subject->gc->updateCalendarName($match['google']['id'], 'THE_NAME');
        $calendar = $this->subject->gc->getCalendarById($match['google']['id']);
        $calendar['summary'] = 'THE_NAME';

        $this->subject->syncMatchNames($match);

        $this->assertEquals('THE_NAME', $match['rtm']['name']);
        $this->assertEquals('THE_NAME', $match['google']['name']);
        $this->assertEquals($match['rtm']['name'], $this->subject->lists[$match['rtm']['id']]->getName());
        $calendar = $this->subject->gc->getCalendarById($match['google']['id']);
        $this->assertEquals($match['google']['name'],  $calendar['summary']);

        // RTM Changed
        $match['rtm']['name'] = 'THE_NAME';
        $this->subject->lists[$match['rtm']['id']]->setName('NEW_NAME');

        $match['google']['name'] = 'THE_NAME';
        $this->subject->gc->updateCalendarName($match['google']['id'], 'THE_NAME');
        $calendar = $this->subject->gc->getCalendarById($match['google']['id']);
        $calendar['summary'] = 'THE_NAME';

        $this->subject->syncMatchNames($match);

        $this->assertEquals('NEW_NAME', $match['rtm']['name']);
        $this->assertEquals('NEW_NAME', $match['google']['name']);
        $this->assertEquals($match['rtm']['name'], $this->subject->lists[$match['rtm']['id']]->getName());
        $calendar = $this->subject->gc->getCalendarById($match['google']['id']);
        $this->assertEquals($match['google']['name'],  $calendar['summary']);

        // Google Changed
        $match['rtm']['name'] = 'THE_NAME';
        $this->subject->lists[$match['rtm']['id']]->setName('THE_NAME');

        $match['google']['name'] = 'THE_NAME';
        $this->subject->gc->updateCalendarName($match['google']['id'], 'NEW_NAME');
        $calendar = $this->subject->gc->getCalendarById($match['google']['id']);
        $calendar['summary'] = 'NEW_NAME';

        $this->subject->syncMatchNames($match);

        $this->assertEquals('NEW_NAME', $match['google']['name']);
        $this->assertEquals('NEW_NAME', $match['rtm']['name']);
        //TODO factorizar para hacer mock apropiado para el test
        //$this->assertEquals($match['rtm']['name'], $this->subject->lists[$match['rtm']['id']]->getName());
        $calendar = $this->subject->gc->getCalendarById($match['google']['id']);
        $this->assertEquals($match['google']['name'],  $calendar['summary']);

    }
}
