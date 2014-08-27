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

use zpt\pct\tags\TagParser;

/**
 * This class encapsulates a line of code to be output when performing value
 * substitution on a template.  Actual output and number of times it is output
 * depends on the given substitution values and where in the template block
 * structure it appears.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class CodeLine
{

    private $indent = 0;
    private $line;
    private $lineNum;

    private $tags;

    /**
     * Constructor.
     *
     * @param string $line The ensupsulated line of code.
     * @param int $lineNum The line number on which this line of code appears
     *        in it's source file.
     */
    public function __construct($line, $lineNum)
    {
        $this->line = trim($line);
        $this->lineNum = $lineNum;
    }

    /**
     * Substitute the given values into the template.
     *
     * @param TemplateValues $values
     * @return string
     */
    public function forValues(TemplateValues $values)
    {
        // Only parse tags once and only if the line is actually output
        if ($this->tags === null) {
            $tagParser = new TagParser();
            $this->tags = $tagParser->parse($this->line);
        }

        $search = array();
        $replace = array();
        foreach ($this->tags as $key => $tag) {
            try {
                $search[] = $key;
                $replace[] = $tag->getValue($values);
            } catch (UndefinedValueException $e) {
                throw new SubstitutionException($this->lineNum, $e);
            }
        }

        $r = $this->getIndent() . str_replace($search, $replace, $this->line);
        return $r;
    }

    public function getLineNum()
    {
        return $this->lineNum;
    }

    public function setIndent($indent)
    {
        $this->indent = $indent;
    }

    private function getIndent()
    {
        $indent = '';
        for ($i = 0; $i < $this->indent; $i++) {
            $indent .= '  ';
        }
        return $indent;
    }
}
