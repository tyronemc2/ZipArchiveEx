<?php

require('src/ZipArchiveEx.php');

class ZipArchiveExTest extends PHPUnit_Framework_TestCase {

	public function provideTestDirs() {
		return array(
			array('invalid dir', false, null),
			array('tests/zipme', true, null),
			array('tests/zipme', true, 'README'),
		);
	}


	/**
	 * @dataProvider provideTestDirs
	 */
	public function testAddDir($dirname, $expected_result, $manipulate) {
		# Create new archive
		$archive = '/tmp/archive.zip';
		$zip = new ZipArchiveEx();
		$zip->open($archive, ZIPARCHIVE::OVERWRITE);

		# Try to add directory:
		$result = $zip->addDir($dirname);
		$this->assertEquals($expected_result, $result);

		# Close archive:
		$zip->close();

		# If directory was added successfully
		if ($result) {
			# Extract directory
			$output = array();
			exec('unzip ' . $archive . ' -d /tmp', $output, $result);
			$this->assertEquals(0, $result); # 0 = successfull

			# $manipulate holds the file to manipulate,
			# so the following assertion fails.
			$extractionDir = '/tmp/' . basename($dirname);
			if ($manipulate) {
				file_put_contents(
					$extractionDir . '/' . $manipulate,
					'Lorem ipsum dolor sit amet.');
				$expected_result = 1;
			} else {
				$expected_result = 0;
			}

			# Compare extracted directory and original one
			exec('diff -arq ' . $dirname . ' ' . $extractionDir, $output, $result);
			$this->assertEquals($expected_result, $result);
		}
	}

};
