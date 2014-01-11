<?php
/*********************************************

	LightBlog 0.9
	SQLite blogging platform

	Sources/Processors/Processor.ErrorLog.php

	©2008-2012 The LightBlog Team. All
	rights reserved. Released under the
	GNU General Public License 3. For
	all licensing information, please
	see the LICENSE.txt document
	included in this distribution.

*********************************************/

class ErrorLog
{
	private $dbh;

	public function __construct()
	{
		$this->dbh = $GLOBALS['dbh'];
	}

	public function processor($data)
	{
		$where = array();
		if(isset($data['prev']))
		{
			$data['page'] -= 1;
		}
		if(isset($data['next']))
		{
			$data['page'] += 1;
		}
		if($data['page'] != 0)
		{
			((!isset($data['count'])) ? $data['count'] = 10 : '');
			$data['page'] = (int)(($data['page'] - 1) * $data['count']);
		}
		elseif($data['before'] != 0)
		{
			array_push($where, "error_id < ".(int)$data['before']);
			$data['start'] = 0;
		}
		((!empty($where)) ? $where = implode(' AND ', $where) : $where = '');
		$result = @$this->dbh->query("SELECT error_id, error_type, error_time, error_message FROM error_log {$where} ORDER BY error_id desc LIMIT ".(int)$data['page'].", ".(int)$data['count']) or die(json_encode(array("result" => "error", "response" => sqlite_error_string($this->dbh->lastError()))));
		$query_c = "SELECT error_id, error_type, error_time, error_message FROM error_log {$where} ORDER BY error_id desc LIMIT ".(int)$data['page'].", ".(int)$data['count'];
		$query_c_rows = count_rows($query_c);
		$return = '';
		$i = 0;
		while($row = $result->fetchObject())
		{
			$i++;
			if($i == $query_c_rows)
			{
				$return .= '<tr id="'.$row->error_id.'" class="last">';
			}
			elseif($i == 1)
			{
				$return .= '<tr id="'.$row->error_id.'" class="first">';
			}
			else
			{
				$return .= '<tr id="'.$row->error_id.'">';
			}
			$return .= '<td><input type="checkbox" name="checked[]" value="'.$row->error_id.'" class="bf table" /></td>';
			$return .= '<td>'.errorsMapType($row->error_type).'</td>';
			$return .= '<td>
				<a href="'.get_bloginfo('url').'admin/error.php?id='.$row->error_id.'">'.(strlen($row->error_message) > 50 ? substr($row->error_message, 0, 47).'...' : $row->error_message).'</a>';
			$return .= '</td>';
			$return .= '<td>'.date('n/j/Y g:i:sA', $row->error_time).'</td>';
			$return .= '<td class="c"><img src="style/delete.png" alt="Delete" onclick="deleteItem('.$row->error_id.');" style="cursor:pointer;" /></td>';
			$return .= '</tr>';
		}
		return array("result" => "success", "response" => $return);
	}
}

?>