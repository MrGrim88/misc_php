<?php
/*

	Copyright 2018 Richard McQuiston
	Free for personal, non-commercial use.
	
	
	Donations Gladly Accepted via PayPal to bigrpromotions@gmail.com
*/
	class Globals {
		//protected static & constants only.
		private static $sql_hosts = array('localhost');
		private static $sql_dbs = array('casv2_CAS_2_5_16');
		private static $sql_accounts = array(
			array(
				'username'=>'casv2',
				'password'=>'qg9386V5ygY3wlO'
			)
		);
		protected static $SQL_CONNECTION = array();
		function __construct(){
			self::$SQL_CONNECTION = array(
				array(
					'host'=>self::$sql_hosts[0x0],
					'db'=>self::$sql_dbs[0x0],
					'username'=>self::$sql_accounts[0x0]['username'],
					'password'=>self::$sql_accounts[0x0]['password']
				)
			);
		}
	}