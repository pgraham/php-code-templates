<?php
/**
 * =============================================================================
 * Copyright (c) 2012, Philip Graham
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
namespace zpt\pct\exception;

use LogicException;

/**
 * Exception thrown by composite blocks when an invalid nesting structure is
 * detected.  These Exceptions should be caught by the parser and translated
 * into a ParseException.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class StructureException extends LogicException {}
