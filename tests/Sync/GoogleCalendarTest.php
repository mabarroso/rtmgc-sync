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
 * AdTest
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
}
