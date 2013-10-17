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

    /**
     * Constructor
     *
     * @return none
     */
    protected function setUp()
    {
        $this->subject = new Sync('tests/_files/testuser.json');

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
}
