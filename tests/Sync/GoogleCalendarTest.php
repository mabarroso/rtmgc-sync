<?php
/**
 * GoogleCalendar
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

require_once 'src/Sync/GoogleCalendar.php';

/**
 * GoogleCalendar
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
class GoogleCalendarTest extends PHPUnit_Framework_TestCase
{
    protected $subject;

    /**
     * Constructor
     *
     * @return none
     */
    protected function setUp()
    {
        $this->subject = new GoogleCalendar();

        include 'tests/_files/google_listCalendarList.php';
        include 'tests/_files/google_listEvents.php';

        $this->subject = $this->getMock('GoogleCalendar', array('getCalendarsFromAPI', 'getEventsFromAPI'));
        $this->subject->expects($this->any())
            ->method('getCalendarsFromAPI')
            ->will($this->returnValue($listCalendarList));
        $this->subject->expects($this->any())
            ->method('getEventsFromAPI')
            ->will($this->returnValue($listEvents));
    }

    /**
     * [testInstanceType description]
     *
     * @return none
     */
    public function testInstanceType()
    {
        $this->assertTrue($this->subject instanceof GoogleCalendar);
    }

    /**
     * [testGetCalendar description]
     *
     * @return none
     */
    public function testGetCalendar()
    {
        $calendars = $this->subject->getCalendars();
        $this->assertEquals('8vu9s3macbikbva5r1r2jj75do@group.calendar.google.com', $calendars[0]['id']);
        $this->assertEquals('User calendar', $calendars[0]['summary']);

        $this->assertEquals('ppcemf16ugnpfspnmj9jjpde08@group.calendar.google.com', $calendars[3]['id']);
        $this->assertEquals('RTM List 2', $calendars[3]['summary']);
    }

    /**
     * [testGetCalendarById description]
     *
     * @return none
     */
    public function testGetCalendarById()
    {
        $calendar = $this->subject->getCalendarById('8vu9s3macbikbva5r1r2jj75do@group.calendar.google.com');
        $this->assertEquals('User calendar', $calendar['summary']);
        $calendar = $this->subject->getCalendarById('ppcemf16ugnpfspnmj9jjpde08@group.calendar.google.com');
        $this->assertEquals('RTM List 2', $calendar['summary']);
    }

    /**
     * [testGetEvents description]
     *
     * @return none
     */
    public function testGetEvents()
    {
        $events = $this->subject->getEvents('ppcemf16ugnpefspnmj9jjpd08@group.calendar.google.com');

        $this->assertEquals('id_e01', $events[0]['id']);
        $this->assertEquals('e01', $events[0]['summary']);
        $this->assertEquals('e01 event created all day', $events[0]['description']);

        $this->assertEquals('id_e05', $events[4]['id']);
        $this->assertEquals('e05', $events[4]['summary']);
        $this->assertEquals('e05 event changed appointment', $events[4]['description']);
    }

    /**
     * [testGetEventById description]
     *
     * @return none
     */
    public function testGetEventById()
    {
        $event = $this->subject->getEventById('ppcemf16ugnpefspnmj9jjpd08@group.calendar.google.com', 'id_e01');
        $this->assertEquals('e01', $event['summary']);

        $event = $this->subject->getEventById('ppcemf16ugnpefspnmj9jjpd08@group.calendar.google.com', 'id_e05');
        $this->assertEquals('e05', $event['summary']);
    }
}
