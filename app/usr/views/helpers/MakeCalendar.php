<?php
/*---------------------------------------------------------------------
 * @project:	ChurchPoint
 * 
 * @Project		ChurchPoint Application
 * 
 * --------------------------------------------------------------------
 * Created by ashon on Aug 15, 2012
 * 
 * (c) 2010 - 2012 Copyright Ashon Associates Inc. Web Solutions 
 * 
 * This program is free software: you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *   (at your option) any later version.
 *
 *   This program is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with this program.  If not, see <http://www.gnu.org/licenses/>.
 * 
 * --------------------------------------------------------------------
 */
class Point_View_Helper_MakeCalendar 
{
	protected	$_local_options	= array(
	
				'container'	=> array(
								'element'	=> 	'table',
								'class'		=>	'calendar',
								'others'	=> array()
								),
				'row_head'	=> array(
								'element'	=> 	'tr',
								'class'		=>	'cal-row-head',
								'others'	=> array()
								),
				'row' 	=> array(
								'element'	=> 	'tr',
								'class'		=>	'cal-row',
								'others'	=> array()
								),
				'cell_head'	=> array(
								'element'	=> 	'th',
								'class'		=>	'cal-cell-head',
								'others'	=> array()
								),
				'cell'	=> array(
								'element'	=> 	'td',
								'class'		=>	'cal-cell',
								'others'	=> array()
								),
				'week'	=> array('class'		=> 'cal-week'),
				'day'	=> array('class'		=> 'cal-day')
					);
	
	public function makeCalendar(array $contArray, $options = null)
	{
		$content		= null;
		
		/* Get the calendar options */ 
		if (!empty($options) && is_array($options))
		{
			$this->_local_options	= array_merge($this->_local_options, $options);
		}	
		
		$settings		= $this->_local_options;
		$s_container	= $settings['container'];
		$s_rows_head	= $settings['row_head'];
		$s_rows			= $settings['row'];
		$s_cells_head	= $settings['cell_head'];
		$s_cells		= $settings['cell'];
		$s_week			= $settings['week'];
		$s_day			= $settings['day'];

		$calendar_obj	= Point_Object_Calendar::getInstance();
		
		/* Get the desired month*/
		$current_time	= isset($contArray['time']) ? $contArray['time'] : time();		// Assumes current month if not passed
		$date_array		= getDateArray( $current_time ); 				// Retrieve the various date components (Month, year, week etc) 
		
		$first_day_of_month	= date('w', mktime(0, 0, 0, $date_array['month'], 1, $date_array['year']))   +   1; // Adding 1 to make it more human!!
																												// 1 => Sunday
																												// 2 => Monday
																												// 7 => Saturday
		$last_day_of_month 	= $calendar_obj->getLastDayOfMonth	($date_array['month'], $date_array['year']);
		$weeks_in_month		= $calendar_obj->getWeeksInMonth	($date_array['month'], $date_array['year']);

		$content			= $content_rows = $content_days = '';
				
		/* create the loop of weeks */
		
		
		
		// Create top rows first
		$days_of_week		= array('Sunday', 'Monday','Tuesday', 'Wednesday','Thursday','Friday', 'Saturday');
		
		$content_rows		= '<'. $s_rows_head['element'] . ' class="'. $s_rows_head['class'].'"><'. $s_cells_head['element'] . '>&nbsp;</'. $s_cells_head['element'] . '>';
		
			foreach($days_of_week as $day_of_week)
			{ $content_rows .= wrapHtml($day_of_week, $s_cells_head['element'], array_merge(array('class' => $s_cells_head['class']), $s_cells_head['others']));}
		
		$content_rows		.= '</'. $s_rows_head['element'] . '>';
		
		// Create the other weeks
		foreach ($weeks_in_month as $this_week)
		{
			$content_days	= wrapHtml('days', $s_cells['element'],
							array_merge(array('class' => $s_container['class']), $s_container['others']) // Join the other attributes if available
							);
							
			$content_rows	.= wrapHtml($content_days, $s_rows['element'],
							array_merge(array('class' => $s_container['class']), $s_container['others']) // Join the other attributes if available
							);
						
		}

		
		$content		= wrapHtml( // Parent tag
		
				$content_rows,
				$s_container['element'],
				array_merge(array('class' => $s_container['class']), $s_container['others']) // Join the other attributes if available
		
		);// End of parent tag
		
		pr($contArray);
		return $content;

	}
} 