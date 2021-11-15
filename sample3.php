<?php

/*
 * Prevent Direct Access
 */
defined( 'ABSPATH' ) or die( "Sorry, Can not access page directly." );

/*
 * Class for the Scheduler
 */
if ( ! class_exists( 'Sample_Schedular' ) ) {
    class Sample_Schedular {

	private $plugin = null;
	private $prefix = null;
	private $dir = null;
	protected static $instance = null;

	public function __construct() { }

	public static function getInstance() {
		if ( !isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/*
	 * Register actions and filters when object created
	 */
	public function init( $params = array() ) {
		$this->plugin = $params;
		add_filter( 'cron_schedules', array( $this, 'set_schedule' ), 99 );
	}

	/*
	 * Wrapper to get the plugin params used to create this class object
	 */
	public function get_my_plugin( $key ) {
		return isset( $this->plugin[$key] ) ? $this->plugin[$key] : null;
	}

	/*
	 * Format schedule time to a readable format
	 */
	private static function format_scheduled_time ( $scheduledtime, $which ) {
		$readable = 'Not set.';
		if ( $scheduledtime ) {
			$readable = date( "Y-m-d H:i:s", $scheduledtime + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) ) . " " . get_option( 'timezone_string' );
		}
		else if ( get_option( $which ) != 0 ) {
			$readable = $which . ' hook not found, likely another plugin misuse of cron_schedules. See FAQ.';
		}
		return $readable;
	}

	/*
	 * Load the scheduler template to show current status
	 */
	public function show_scheduler() {
		$this->check_for_scheduler_change();
		$current_server_time = date( 'Y-m-d H:i:s', current_time( "timestamp" ) ) . " " . get_option( 'timezone_string' );
		if ( get_option( $this->get_my_plugin('prefix') . 'sch_enabled' ) == TRUE ) {
			$running = get_option( $this->get_my_plugin( 'prefix' ) . 'running' );
			$scheduledtime = wp_next_scheduled( $this->get_my_plugin('prefix') . 'Schedule' );
			$next_schedule = $this->format_scheduled_time( $scheduledtime, $this->get_my_plugin( 'prefix' ) . 'sch_freq' );
			$scheduledtime = wp_next_scheduled( $this->get_my_plugin('prefix') . 'Check' );
			$next_checker = $this->format_scheduled_time( $scheduledtime, $this->get_my_plugin( 'prefix' ) . 'sch_checker' );
		}
		include( sprintf( "%s/templates/scheduler.php", $this->get_my_plugin('dir') ) );
	}

	/*
	 * Checks if a status button was pressed
	 * Enable - calculates next schedule hook times
	 * Disable - clears the schedule hooks
	 * Save - saves Scheduler options
	 */
	public function check_for_scheduler_change() {
                if ( isset( $_POST['enable_scheduler'] ) ) {
			wp_clear_scheduled_hook( $this->get_my_plugin('prefix') . 'Schedule' );
			wp_clear_scheduled_hook( $this->get_my_plugin('prefix') . 'Check' );
                        update_option( $this->get_my_plugin('prefix') . 'sch_enabled', TRUE );
			$start = get_option( $this->get_my_plugin('prefix') . 'sch_start' );
			if ( preg_match( "/(\d+)\s*-\s*(\d+)/", $start, $matches ) ) {
				$start = mt_rand( $matches[1] * 3600, $matches[2] * 3600 );
			}
			else {
   	 			$start = $start * 3600;
			}
			$freq = get_option( $this->get_my_plugin('prefix') . 'sch_freq' );
			if ( ! empty( $freq ) ) {
                        	wp_schedule_event( time() + $start, $this->get_my_plugin( 'prefix' ) . 'scheduler', $this->get_my_plugin('prefix') . 'Schedule' );
			}
			$checker = get_option( $this->get_my_plugin('prefix') . 'sch_checker' );
			if ( ! empty( $checker ) ) {
                        	wp_schedule_event( time() + $start, $this->get_my_plugin( 'prefix' ) . 'checker', $this->get_my_plugin('prefix') . 'Check' );
			}
			echo "<div id='message' class='updated'><p>" . __( 'Scheduler Enabled.', 'sample' ) . "</p></div>";
			if ( defined( 'DISABLE_WP_CRON' ) ) {
				echo "<div id='message' class='updated'><p>" . __( 'DISABLE_WP_CRON detected - Scheduler will only run from a cron job.', 'sample' ) . "</p></div>";
			}
                }
                else if ( isset( $_POST['disable_scheduler'] ) ) {
			wp_clear_scheduled_hook( $this->get_my_plugin('prefix') . 'Schedule' );
			wp_clear_scheduled_hook( $this->get_my_plugin('prefix') . 'Check' );
                        update_option( $this->get_my_plugin('prefix') . 'sch_enabled', FALSE );
			$running = get_option( $this->get_my_plugin( 'prefix' ) . 'running' );
                        update_option( $this->get_my_plugin('prefix') . 'running', '' );
			if ( empty( $running ) ) {
				echo "<div id='message' class='updated'><p>" . __( 'Scheduler Disabled.', 'sample' ) . "</p></div>";
			}
			else {
				echo "<div id='message' class='updated'><p>" . __( 'Scheduler will be Disabled after current import is finished.', 'sample' ) . "</p></div>";
			}
                }
                else if ( isset( $_POST['save_scheduler'] ) ) {
			$delay = isset( $_POST['sample_sch_start'] ) ? $_POST['sample_sch_start'] : 0;
			$freq = isset( $_POST['sample_sch_freq'] ) ? $_POST['sample_sch_freq'] : 0;
			$checker = isset( $_POST['sample_sch_checker'] ) ? $_POST['sample_sch_checker'] : 0;
			update_option( $this->get_my_plugin('prefix') . 'sch_start', $delay );
			update_option( $this->get_my_plugin('prefix') . 'sch_freq', $freq);
			update_option( $this->get_my_plugin('prefix') . 'sch_checker', $checker);
			echo "<div id='message' class='updated'><p>" . __( 'Settings saved.', 'sample' ) . "</p></div>";
		}
	}

	/*
	 * Called by WordPress to get global schedules
	 * If scheduler Enabled, calculate and add our custom hook times
	 */
	public function set_schedule( $schedules )  {
		if ( get_option( $this->get_my_plugin('prefix') . 'sch_enabled' ) === FALSE ) {
			return $schedules;
		}

		// add schedule entry for video setting
		$hours = get_option( $this->get_my_plugin('prefix') . 'sch_freq' );
    		$scheduleName = $this->get_my_plugin( 'prefix' ) . 'scheduler';
		if ( $hours && ! in_array( $scheduleName, $schedules ) ) {
			if ( preg_match( "/(\d+)\s*-\s*(\d+)/", $hours, $matches ) ) {
				$timesecs = mt_rand( $matches[1] * 3600, $matches[2] * 3600 );
			}
			else {
   	 			$timesecs = $hours * 3600;
			}
    			$schedules[$scheduleName] = array(
       	 			'interval' => $timesecs, 'display' => $this->get_my_plugin('short_name') . ' Scheduler'
    			);
		}

		// add schedule entry for video checker
		$hours = get_option( $this->get_my_plugin('prefix') . 'sch_checker' );
    		$scheduleName = $this->get_my_plugin( 'prefix' ) . 'checker';
		if ( $hours && ! in_array( $scheduleName, $schedules ) ) {
			if ( preg_match( "/(\d+)\s*-\s*(\d+)/", $hours, $matches ) ) {
				$timesecs = mt_rand( $matches[1] * 3600, $matches[2] * 3600 );
			}
			else {
   	 			$timesecs = $hours * 3600;
			}
    			$schedules[$scheduleName] = array(
       	 			'interval' => $timesecs, 'display' => $this->get_my_plugin('short_name') . ' Utility'
    			);
		}

    		return $schedules;
	}

    } // END class Sample_Schedular
} // END if ( ! class_exists( 'Sample_Schedular' ) )
?>