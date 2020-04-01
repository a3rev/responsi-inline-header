<?php
/**
 * Class SampleTest
 *
 * @package Sample_Plugin
 */

/**
 * Sample test case.
 */
class a3Rev_Tests_Sample extends WP_UnitTestCase {

	/**
	 * A single example test.
	 */

	function test_responsi_theme() {
		$output = 1;

		$this->assertTrue( defined( 'RESPONSI_FRAMEWORK_VERSION' ) );
	}

	function test_sample() {
		$output = 1;

		$this->assertEquals( 1 , $output );
	}

	function test_responsi_css() {

		global $responsi_ih;
		$output = (string)$responsi_ih->responsi_build_dynamic_css();
		$this->assertStringContainsString( '.ih-area-widget .widget-title h3' , $output );

	}

}
