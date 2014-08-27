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
 * High level exception for exceptions that occur while resolving a template.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class ResolutionException extends Exception {

	private $lineNum;
	private $templatePath;
	private $values;

	public function __construct(
		$templatePath,
		$lineNum,
		$values,
		Exception $e = null
	) {
		$this->templatePath = $templatePath;
		$this->lineNum = $lineNum;
		$this->values = $values;

		$msg = "An exception occured while resolving $templatePath at line $lineNum";
		if ($e !== null) {
			$msg .= ": {$e->getMessage()}";
		}
		parent::__construct($msg, $e !== null ? $e->getCode() : 0, $e);
	}

}
