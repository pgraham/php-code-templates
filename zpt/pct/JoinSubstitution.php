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
namespace zpt\pct;

/**
 * This class represents a join tag substitution in a line of a code template.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class JoinSubstitution extends Substitution
{

    private $key;
    private $name;
    private $glue;
    private $isPhp;

    public function __construct($key, $name, $glue, $isPhp, $lineNum)
    {
        parent::__construct($lineNum);

        $this->key = $key;
        $this->name = $name;
        $this->glue = $glue;
        $this->isPhp = $isPhp;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function getValue(TemplateValues $values)
    {
        $val = $values->getValue($this->name);
        if ($val === null) {
            return '';
        }

        if (!is_array($val)) {
            throw new InvalidTypeException(
                $this->name,
                'array',
                $val,
                $this->lineNum
            );
        }

        if ($this->isPhp) {
            $phpVals = array();
            foreach ($val as $v) {
                $phpVals[] = var_export($v, true);
            }
            $val = $phpVals;
        }

        return implode($this->glue, $val);
    }
}
