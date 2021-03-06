<?php
/**
 *  Copyright (c) Enalean, 2012 - 2017. All Rights Reserved.
 *
 *   This file is a part of Tuleap.
 *
 *   Tuleap is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *   Tuleap is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with Tuleap. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Tuleap\GraphOnTrackersV5;

use GraphOnTrackersV5_Burndown_Data;
use TimePeriodWithoutWeekEnd;
use Tracker_Chart_Burndown;
use TuleapTestCase;

require_once(dirname(__FILE__) . '/../include/autoload.php');
require_once(dirname(__FILE__) . '/../../tracker/include/autoload.php');

class GraphOnTrackersV5BurndownDataTest extends TuleapTestCase
{
    /**
     * @var GraphOnTrackersV5_Burndown_Data
     */
    private $burndown_data;
    /**
     * @var array
     */
    private $artifacts_ids;

    public function setUp()
    {
        $this->artifacts_ids = array(5215, 5217, 5239, 5241);
        $this->burndown_data = mock('GraphOnTrackersV5_Burndown_Data');
    }

    public function itNormalizeDataDayByDayStartingAtStartDate()
    {
        $remaining_efforts = array(
            20170901 => array(
                5215 => null,
                5217 => null
            ),
            20170902 => array(
                5215 => '1.0000',
                5217 => null,
                5239 => null,
                5241 => '13.0000'
            ),
            20170904 => array(
                5215 => '2.0000',
                5217 => '1.0000',
                5239 => '0.5000',
                5241 => '14.0000'
            )
        );
        $start_date        = mktime(8, 0, 0, 9, 1, 2017);
        $duration          = 5;
        $time_period       = new TimePeriodWithoutWeekEnd($start_date, $duration);
        stub($this->burndown_data)->getTimePeriod()->returns($time_period);
        stub($this->burndown_data)->getRemainingEffort()->returns($remaining_efforts);

        $burndown = new Tracker_Chart_Burndown($this->burndown_data);

        $expected_values = array(
            'Fri 01' => array(null),
            'Sat 02' => array(14),
            'Sun 03' => array(14),
            'Mon 04' => array(17.5),
            'Tue 05' => array(17.5),
            'Wed 06' => array(17.5)
        );
        $burndown_values = $burndown->getComputedData();

        $this->assertEqual($expected_values, $burndown_values);
    }

    public function itDisplaysNothingWhenRemainingEffortAreNotSet()
    {
        $remaining_efforts = array();
        $start_date        = mktime(8, 0, 0, 9, 1, 2017);
        $duration          = 5;
        $time_period       = new TimePeriodWithoutWeekEnd($start_date, $duration);
        stub($this->burndown_data)->getTimePeriod()->returns($time_period);
        stub($this->burndown_data)->getRemainingEffort()->returns($remaining_efforts);

        $burndown = new Tracker_Chart_Burndown($this->burndown_data);

        $expected_values = array(
            'Fri 01' => null,
            'Sat 02' => null,
            'Sun 03' => null,
            'Mon 04' => null,
            'Tue 05' => null,
            'Wed 06' => null
        );
        $burndown_values = $burndown->getComputedData();

        $this->assertEqual($expected_values, $burndown_values);
    }

    public function itDontDisplayFuture()
    {
        $remaining_efforts = array();
        $start_date        = time();
        $duration          = 5;
        $time_period       = new TimePeriodWithoutWeekEnd($start_date, $duration);
        stub($this->burndown_data)->getTimePeriod()->returns($time_period);
        stub($this->burndown_data)->getRemainingEffort()->returns($remaining_efforts);

        $burndown = new Tracker_Chart_Burndown($this->burndown_data);


        $burndown_values = $burndown->getComputedData();
        $expected_values = array(
            date('D d', time()) => null,
            date('D d', strtotime("+1 day", time())) => null,
            date('D d', strtotime("+2 day", time())) => null,
            date('D d', strtotime("+3 day", time())) => null,
            date('D d', strtotime("+4 day", time())) => null,
            date('D d', strtotime("+5 day", time())) => null,
        );

        $this->assertEqual($expected_values, $burndown_values);
    }

    public function itDisplaysNothingWhenEndBurndownDateIsBeforeStartDateAskedByUser()
    {
        $remaining_efforts = array(
            20170901 => array(
                5215 => null,
                5217 => null
            ),
            20170902 => array(
                5215 => '1.0000',
                5217 => null,
                5239 => null,
                5241 => '13.0000'
            ),
            20170904 => array(
                5215 => '2.0000',
                5217 => '1.0000',
                5239 => '0.5000',
                5241 => '14.0000'
            )
        );
        $start_date        = mktime(8, 0, 0, 9, 1, 2016);
        $duration          = 5;
        $time_period       = new TimePeriodWithoutWeekEnd($start_date, $duration);
        stub($this->burndown_data)->getTimePeriod()->returns($time_period);
        stub($this->burndown_data)->getRemainingEffort()->returns($remaining_efforts);

        $burndown = new Tracker_Chart_Burndown($this->burndown_data);

        $expected_values = array(
            'Thu 01' => null,
            'Fri 02' => null,
            'Sat 03' => null,
            'Sun 04' => null,
            'Mon 05' => null,
            'Tue 06' => null
        );
        $burndown_values = $burndown->getComputedData();

        $this->assertEqual($expected_values, $burndown_values);
    }

    public function itShouldTakeIntoAccountWhenValueFallToZero()
    {
        $remaining_efforts = array(
            20170901 => array(
                5215 => null,
                5217 => null
            ),
            20170902 => array(
                5215 => '1.0000',
                5217 => null,
                5239 => null,
                5241 => '13.0000'
            ),
            20170904 => array(
                5215 => '0',
                5217 => '0',
                5239 => '0',
                5241 => '0'
            )
        );
        $start_date        = mktime(8, 0, 0, 9, 1, 2017);
        $duration          = 5;
        $time_period       = new TimePeriodWithoutWeekEnd($start_date, $duration);
        stub($this->burndown_data)->getTimePeriod()->returns($time_period);
        stub($this->burndown_data)->getRemainingEffort()->returns($remaining_efforts);

        $burndown = new Tracker_Chart_Burndown($this->burndown_data);

        $expected_values = array(
            'Fri 01' => array(null),
            'Sat 02' => array(14),
            'Sun 03' => array(14),
            'Mon 04' => array(0),
            'Tue 05' => array(0),
            'Wed 06' => array(0)
        );
        $burndown_values = $burndown->getComputedData();

        $this->assertEqual($expected_values, $burndown_values);
    }

    public function itShouldComputeIdealBurndownForDisplay()
    {
        $remaining_efforts = array(
            20170901 => array(
                5215 => null,
                5217 => null
            ),
            20170902 => array(
                5215 => 500,
                5241 => 40
            ),
            20170903 => array(
                5215 => 0,
                5217 => 0,
                5239 => 0,
                5241 => 0
            )
        );
        $start_date        = mktime(8, 0, 0, 9, 1, 2017);
        $duration          = 20;
        $time_period       = new TimePeriodWithoutWeekEnd($start_date, $duration);
        stub($this->burndown_data)->getTimePeriod()->returns($time_period);
        stub($this->burndown_data)->getRemainingEffort()->returns($remaining_efforts);

        $burndown = new Tracker_Chart_Burndown($this->burndown_data);
        $burndown->prepareDataForGraph($remaining_efforts);

        $ideal_burndown_by_day = $burndown->getGraphDataIdealBurndown();

        $this->assertIdentical($ideal_burndown_by_day[0], 540.0);
        $this->assertIdentical($ideal_burndown_by_day[1], 486.0);
        $this->assertIdentical($ideal_burndown_by_day[2], 432.0);
    }

    public function itShouldAddPreviousValuesWhenStartDateIsToday()
    {
        $start_date = time();
        $yesterday  = date("Ymd", strtotime("-1 day", time()));

        $remaining_efforts = array(
            $yesterday => array(
                5215 => 10,
                5217 => 20
            )
        );
        $duration          = 5;
        $time_period       = new TimePeriodWithoutWeekEnd($start_date, $duration);
        stub($this->burndown_data)->getTimePeriod()->returns($time_period);
        stub($this->burndown_data)->getRemainingEffort()->returns($remaining_efforts);

        $burndown = new Tracker_Chart_Burndown($this->burndown_data);


        $burndown_values = $burndown->getComputedData();
        $expected_values = array(
            date('D d', time()) => array(30),
            date('D d', strtotime("+1 day", time())) => null,
            date('D d', strtotime("+2 day", time())) => null,
            date('D d', strtotime("+3 day", time())) => null,
            date('D d', strtotime("+4 day", time())) => null,
            date('D d', strtotime("+5 day", time())) => null,
        );

        $this->assertEqual($expected_values, $burndown_values);
    }
}
