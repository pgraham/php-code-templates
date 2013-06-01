<?php
/**
 * =============================================================================
 * Copyright (c) 2013, Philip Graham
 * All rights reserved.
 *
 * This file is part of php-code-templates and is licensed by the Copyright
 * holder under the 3-clause BSD License.  The full text of the license can be
 * found in the LICENSE.txt file included in the root directory of this
 * distribution or at the link below.
 * =============================================================================
 *
 * @license http://www.opensource.org/licenses/bsd-license.php
 */
namespace zpt\pct;

use \PHPUnit_Framework_TestCase as TestCase;

require_once __DIR__ . '/test-common.php';

/**
 * This class tests the TemplateResolver class.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class TemplateResolverTest extends TestCase {

	protected function setUp() {
		$targetPath = __DIR__ . '/target';
		if (file_exists($targetPath)) {
			exec("rm -r $targetPath");
		}
		mkdir($targetPath);
	}

	public function testResolveSimpleTemplate() {
		$this->assertFileNotExists(
			__DIR__ . '/target/sample.php',
			"Target not clean"
		);

		$templateResolver = new TemplateResolver();
		$templateResolver->resolve(
			__DIR__ . '/mock/sample.tmpl.php',
			__DIR__ . '/target/sample.php',
			array('tag' => 'value')
		);

		$this->assertFileExists(__DIR__ . '/target/sample.php');
	}

	public function testResolveTemplateNotFound() {
		try {
			$templateResolver = new TemplateResolver();
			$templateResolver->resolve(
				__DIR__ . '/mock/no-sample.tmpl.php',
				__DIR__ . '/target/no-sample.php',
				array()
			);

			$this->fail('Expected TemplateNotFoundException');
		} catch (TemplateNotFoundException $e) {
			$this->assertEquals(
				__DIR__ . '/mock/no-sample.tmpl.php',
				$e->getTemplatePath()
			);
		}
	}
}
