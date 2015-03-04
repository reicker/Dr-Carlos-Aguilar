<?php
/**
 * WP Job Manager - Tags
 */

class Listify_WP_Job_Manager_Tags extends listify_Integration {

	public function __construct() {
		$this->includes = array();

		$this->integration = 'wp-job-manager-tags';

		parent::__construct();
	}

	public function setup_actions() {
	}


}

$GLOBALS[ 'listify_job_manager_tags' ] = new Listify_WP_Job_Manager_Tags();
