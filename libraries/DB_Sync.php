<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter DB_Sync!
 *
 * This is a wrapper for the very powerfull CLI tool mk-table-sync
 * <http://www.maatkit.org/doc/mk-table-sync.html> which is part of
 * the maatkit mysql toolkit <http://www.maatkit.org/>.
 *
 * With great power comes great responsibility! This tool changes data,
 * so it is a good idea to back up your data. It is also very powerful,
 * which means it is very complex and has the potential to ruin
 * your database when not used properly.
 *
 * This software is "as-is" without any guarantee or warranty.
 * The author is not responsible/liable for any damage resulting from the use of this software.
 *
 * @package		CodeIgniter
 * @author		Jeroen v.d. Gulik <http://isset.nl>
 * @license		DBAD License v1.0 <http://philsturgeon.co.uk/code/dbad-license>
 * @version		0.1
 */
class DB_Sync {

	protected $dbconfig;	// stores the database array for convienence
	protected $last_query;	// stores the last executes cli cmd for debugging
	protected $target_table;// stores the table name if it's different than the source

	function __construct()
	{
		include APPPATH .'config/database.php';

		$this->dbconfig = $db;
	}

	/**
	 * Returns the database array or a database group
	 *
	 * @param string optional $group name of the database group to target
	 * @return array
	 */
    function get_config($group = NULL)
	{
		if (is_null($group))
		{
			return $this->dbconfig;
		}

		return (isset($this->dbconfig[$group])) ? $this->dbconfig[$group] : FALSE;
	}

	/**
	 * Synchronizes a table with 1 or many other tables
	 * Returns the result in tree-view.
	 *
	 * @param string $table to synch
	 * @param array $targets to synch to
	 * @param string optional $source if 'default' is not the default
	 * @return string
	 * @todo change return to bool success
	 * @todo catch and parse the tree-view
	 */
	function table_sync($table, $targets, $source = 'default')
	{
		$targets = ( ! is_array($targets)) ? array($targets) : $targets;

		$source = $this->get_config($source);

		// Do we have a valid source DSN?
		if ( $source == FALSE )
		{
			show_error("database group '$source' does not exist");
		}

		// Is the target table name different than the source table name?
		$target_table = (isset($this->target_table)) ? $this->target_table : $table;

		// Prepare the source part of the cmd
		$shellcmd = "mk-table-sync --execute --verbose u={$source['username']},p={$source['password']},h={$source['hostname']},D={$source['database']},t={$table} ";

		foreach ($targets as $target)
		{
			$target = $this->get_config($target);

			// Do we have a valid target DSN?
			if ( $target == FALSE )
			{
				show_error("database group '$target' does not exist");
			}

			// Append 1 or many targets to the cmd
			$shellcmd .= "u={$target['username']},p={$target['password']},h={$target['hostname']},D={$target['database']},t={$target_table}";
		}

		// Save the cmd for debugging
		$this->last_query = $shellcmd;

		$output = shell_exec($shellcmd);

		return $output;
	}

	public function last_query()
	{
		return $this->last_query;
	}

	public function set_target($table)
	{
		$this->target_table = $table;
	}

	/**
	 * Parses log string to array
	 * Returns returns an array
	 *
	 * @param log string
	 * @return array
	 */
	function parse_log_to_array($logstring)
	{
		if ($logstring != '')
		{
			$result = explode('#', $logstring);
			$result = explode(' ', $result[3]);
			$temp_data = array();
			foreach ($result as $key=>$value)
			{
				if (strlen($value) > 0)
				{
					$temp_data[] = $value;
				}
			}

			$data['num_deleted']	= $temp_data[0];
			$data['num_replaced']	= $temp_data[1];
			$data['num_inserted']	= $temp_data[2];
			$data['num_updated']	= $temp_data[3];
			$data['str_algorithm']	= $temp_data[4];
			$data['num_exit']	= $temp_data[5];
			$data['str_table']	= $temp_data[6];
			$data['dat_date']	= date('Y-m-d H:i:s');
			$data['mem_log']	= $logstring;

			return $data;
		}
	}
}

/* End of file DB_Sync.php */
/* Location: ./application/libraries/DB_Sync.php */
