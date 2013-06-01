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

use \Exception;

/**
 * Exception thrown when a non-existant template is attempted to be resolved by 
 * a TemplateResolver.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class TemplateNotFoundException extends Exception {

	private $templatePath;

	public function __construct($templatePath) {
		parent::__construct(
			"Unable to resolve template at $templatePath. File does not exist"
		);
		$this->templatePath = $templatePath;
	}

	public function getTemplatePath() {
		return $this->templatePath;
	}
}
