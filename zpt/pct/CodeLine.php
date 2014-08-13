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
 * This class encapsulates a line of code to be output when performing value
 * substitution on a template.  Actual output and number of times it is output
 * depends on the given substitution values and where in the template block
 * structure it appears.
 *
 * @author Philip Graham <philip@zeptech.ca>
 */
class CodeLine
{

    const TAG_OPEN = '/\*#\s*';

    const TAG_CLOSE = '\s*#?\*/';

    /**
     * Regular expression for detecting a `join` substitution tag.
     */
    // TODO Switch the order of the glue and the variable so that it is
    //      possible specify whitespace in the glue string without it being
    //      ambiguous with whitespace at the end of the substitution expression
    const JOIN_RE = 'join(-php)?:([\w\-]+(?:\[[\w\-]+\])?):(.+?)';

    /**
     * Regular expression for detecting a `json` substitution tag.
     */
    const JSON_RE = 'json:([\w\-]+(?:\[[\w\-]+\])?)';

    /**
     * Regular expression for detecting a `php` substitution tag.
     */
    const PHP_RE = 'php:([\w\-]+(?:\[[\w\-]+\])?)';

    /**
     * Regular expression for detecting a substitution.
     */
    const TAG_RE = '([\w\-]+(?:\[[\w\-]+\])?)';

    /**
     * Regular expression for detecting a `xml` substitution tag.
     */
    const XML_RE = 'xml:([\w\-]+(?:\[[\w\-]+\])?)';

    private static $JOIN_RE = null;
    private static $JSON_RE = null;
    private static $PHP_RE = null;
    private static $TAG_RE = null;
    private static $XML_RE = null;

    /* Build full regexs from regex fragment constants */
    private static function initRegexs() {
        if (self::$JOIN_RE !== null) {
            // Already initialized
            return;
        }

        $buildRe = function ($re) {
            return '~' . CodeLine::TAG_OPEN . $re . CodeLine::TAG_CLOSE . '~';
        };

        self::$JOIN_RE = $buildRe(self::JOIN_RE);
        self::$JSON_RE = $buildRe(self::JSON_RE);
        self::$PHP_RE = $buildRe(self::PHP_RE);
        self::$TAG_RE = $buildRe(self::TAG_RE);
        self::$XML_RE = $buildRe(self::XML_RE);
    }

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
        self::initRegexs();

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
            $this->parseTags();
        }

        $search = array();
        $replace = array();
        foreach ($this->tags as $tag) {
            $search[] = $tag->getKey();
            $replace[] = $tag->getValue($values);
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

    private function findTags($re)
    {
        $tags = array();
        preg_match_all($re, $this->line, $tags, PREG_SET_ORDER);
        return $tags;
    }

    private function getIndent()
    {
        $indent = '';
        for ($i = 0; $i < $this->indent; $i++) {
            $indent .= '  ';
        }
        return $indent;
    }

    private function parseTags()
    {
        $this->tags = array();

        // Parse joins
        $joins = $this->findTags(self::$JOIN_RE);
        foreach ($joins as $join) {
            $tag = new JoinSubstitution(
                $join[0],
                $join[2],
                $join[3],
                $join[1] === '-php',
                $this->lineNum
            );
            $this->tags[] = $tag;
        }

        // Parse JSON outputs
        $jsons = $this->findTags(self::$JSON_RE);
        foreach ($jsons as $json) {
            $tag = new JsonSubstitution($json[0], $json[1], $this->lineNum);
            $this->tags[] = $tag;
        }

        // Parse XML encoded outputs
        $xmls = $this->findTags(self::$XML_RE);
        foreach ($xmls as $xml) {
            $tag = new XmlSubstitution($xml[0], $xml[1], $this->lineNum);
            $this->tags[] = $tag;
        }

        // Parse PHP output
        $phps = $this->findTags(self::$PHP_RE);
        foreach ($phps as $php) {
            $tag = new PhpSubstitution(
                $php[0],
                $php[1],
                $this->lineNum,
                $this->indent
            );
            $this->tags[] = $tag;
        }

        // Parse normal substitutions
        $tags = $this->findTags(self::$TAG_RE);
        foreach ($tags as $tag) {
            $tag = new TagSubstitution($tag[0], $tag[1], $this->lineNum);
            $this->tags[] = $tag;
        }
    }
}
