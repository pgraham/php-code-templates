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
 * This class represents a php tag substitution in a line of a code template.
 * A php substitution inserts the identified as value as valid php code using
 * var_export
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class PhpSubstitution extends Substitution
{

    private $key;
    private $name;
    private $lineIndent;

    public function __construct($key, $name, $lineNum, $lineIndent)
    {
        parent::__construct($lineNum);

        $this->key = $key;
        $this->name = $name;
        $this->lineIndent = $lineIndent;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function getValue(TemplateValues $values)
    {
        $php = $this->varExport($values->getValue($this->name));
        if (preg_match('/^array\((.*)\)$/s', $php, $matches)) {
            $indent = $this->getIndent();

            $arCtnt = $matches[1];
            $arCtnt = str_replace("\n", "\n$indent", $arCtnt);

            $php = "array ($arCtnt)";
        }
        return $php;
    }

    private function getIndent()
    {
        $indent = '';
        for ($i = 0; $i < $this->lineIndent; $i++) {
            $indent .= '  ';
        }
        return $indent;
    }

    /*
     * Private function to create a PHP representation of a given variable avoid
     * a known issue with var_export and instances of StdClass.
     */
    private function varExport($val)
    {
        if (is_array($val)) {
            $vals = array();
            foreach ($val as $k => $v) {
                $vals[] = $this->varExport($k, true)
                        . ' => '
                        . $this->varExport($v);
            }
            return 'array(' . implode(',', $vals) . ')';

        } elseif (is_object($val) && get_class($val) === 'stdClass') {
            return '(object) ' . $this->varExport((array) $val);

        } else {
            return var_export($val, true);
        }
    }

}
