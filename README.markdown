CodeIgniter-dbsync
================

CodeIgniter-dbsync is a wrapper for the very powerfull CLI tool mk-table-sync 
 <http://www.maatkit.org/doc/mk-table-sync.html> which is part of 
 the maatkit mysql toolkit <http://www.maatkit.org/>.
 
 It allows you to easily sync database tables over 1 or many different databases.
 This could be usefull when implementing the DTAP model, where you want to keep
 the development, test, acceptance or live database all in sync.

Requirements
-----
You will need the maatkit toolkit to use mk-table-sync. Under any debian based system
you can install it via apt:

	$ (sudo) apt-get install maatkit

For installing it on any other platform I'd like to point you to the maatkit website.

You will also need CodeIgniter

Installation
-------------
After you made sure mk-table-sync works from the CLI, drop the libraries/DB_Sync.php file 
into your application/libraries folder. Load the library like any other CodeIgniter Library.

	$this->load->library('DB_Sync');


Basic Functions
-----
Let's assume we have 4 different db groups in the database.php

	default - the dev group and also the default
	test	- your test group
	accept	- your client accept group
	live	- the production database
	
Sync the table 'users' on your developer database to the test database. We don't need to specify
the default group since this is implied

	$this->db_sync->table_sync('users','test');

Sync the table 'content' on your developer database to the test and accept database. We don't need to specify
the default group since this is implied

	$data = array(
		 		'test',
		  		'accept'
 			);

	$this->db_sync->table_sync('content',$data);

Sync the table 'invoices' on your test database to the accept database to the table 'invoices_test'
Note: this has not been tested with serveral databases at once

	$this->db_sync->set_target('invoices_test');
	$this->db_sync->table_sync('invoices', 'test', 'accept');


Debug functions
-----
Show the last executed CLI command

	$this->db_sync->last_query();


Utility functions
-----
Return the database array

	$this->db_sync->get_config();
	
Return a group from the database array

	$this->db_sync->get_config('default');


Contact
-----
If you'd like to request changes, report bug fixes, or contact
the developer of this library, email <email.n0xie@gmail.com>

