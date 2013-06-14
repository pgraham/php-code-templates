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

use \SplFileObject;

/**
 * This class will resolve a template at a specified path location with given 
 * values and output the result at the specified path location.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class TemplateResolver {

	private $templateParser;

	public function __construct(CodeTemplateParser $templateParser = null) {
		$this->templateParser = $templateParser;
	}

	public function resolve($templatePath, $resolvedPath, $values = array()) {
		$this->ensureDependencies();

		if (!file_exists($templatePath)) {
			throw new TemplateNotFoundException($templatePath);
		}

		$template = $this->templateParser->parse($templatePath);
		try {
			$resolved = $template->forValues($values);
		} catch (SubstitutionException $e) {
			throw new ResolutionException(
				$templatePath,
				$e->getLineNum(),
				$values,
				$e
			);
		}

		$outDir = dirname($resolvedPath);
		if (!file_exists($outDir)) {
			mkdir($outDir, 0755, true);
		}

		$file = new SplFileObject($resolvedPath, 'w');
		$file->fwrite($resolved);
	}

	private function ensureDependencies() {
		if ($this->templateParser === null) {
			$this->templateParser = new CodeTemplateParser();
		}
	}
}
