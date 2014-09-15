# PHP Code Templates

PHP Code templates (pct) is a PHP library that parses and performs value
substitution for PHP class templates.

## Creating Templates

Defining a template involves creating a file that encapsulates a common
structure marked up with areas where values should be substituted in order to
create a concrete file that can perform (in the case of code) or provide
something useful. PCT supports simple tag substitution as well as conditional
and repeating sections.

### Substitution Tags

To specify a spot in a template where a value is to be substituted, add a tag
with the following grammar:

    SUBSTITUTION_TAG        <- '/*#' SUBSTITUTION_EXPRESSION '#*/'
    SUBSTITUTION_EXPRESSION <- (FILTER_EXPRESSION ('-' FILTER_EXPRESSION)* ':')?VARIABLE_NAME
    FILTER_EXPRESSION       <- [a-zA-Z]+ ( '(' FILTER_PARAMETER (',' FILTER_PARAMETER)* ')' )?
    FILTER_PARAMETER        <- .+
    VARIABLE_NAME           <- [a-zA-Z]+ ('[' VARIABLE_INDEX ']')*
    VARIABLE_INDEX          <- [a-zA-Z]+

Note that whitespace included between elements is for clarity only and should
not be included when writing tags. The only exception to this is that any amount
of whitespace can appear after the opening `/*#` and before the closing `#*/`.

Examples:

 -  `/*# var #*/`  -- Output the substitution value `var`
 -  `/*# php:var #*/` -- Output the substitution value `var` using the php
    filter
 -  `/*# join(,):var #*/` -- Output the substitution value `var` using the join
    filter with the ',' character as glue
 -  `/*# each(php)-join(,):var #*/` -- Pass each value of `var` through the php
    filter and then join the resulting values using a comma
 -  `/*# var[idx] #*/` -- Output the `idx` value of the array value `var`
 -  `/*# var[idx][subidx] #*/` -- Nested indexes are supported too

#### Filters

There are a number of predefined filters for outputting substitution values.

 -  _each(filter)_ Apply a filter to each value of an array substitution value.
 -  _json_ Output the substitution value encoded as JSON.
 -  _join(glue)_ Join all of the values of an array using `glue` and output the
    result.
 -  _php_ Output the substitution value using
    [var_export](http://php.net/manual/en/function.var-export.php).
 -  _xml_ Output the substitution with encoded XML entities. Note that this will
    not serialize an array or object as XML.

Filters can be layered by joining multiple filter expressions with the '-'
character. Filters defined in this manner are evaluated left to right with each
filter receiving the result of the previous filter as input.

### Conditional Sections

Templates support conditional sections. These sections will only be output
durring value substitution if the set of substitution values matches the
conditional expression. Conditional sections have to following grammar:

    IF_EXPRESSION   <- '#{ if' CONDITIONAL '\n' BLOCK
                       (('#{' / '#}{') ' elseif' CONDITIONAL '\n' BLOCK)*
                       (('#{' / '#}{') ' else' '\n' BLOCK)?
                       '#}'
    CONDITIONAL     <- COMPARISON (('and' / 'or') COMPARISON)*
    COMPARISON      <- OPERAND (UNARY_OPERATOR / BINARY_OPERATOR OPERAND)?
    OPERAND         <- VARIABLE_NAME / NUMBER / STRING
    VARIABLE_NAME   <- [a-zA-Z]+ ('[' VARIABLE_INDEX ']')*
    VARIABLE_INDEX  <- [a-zA-Z]+
    NUMBER          <- Any numeric string*
    STRING          <- ('\'' / '"') .* ('\'' / '"')
    BINARY_OPERATOR <- '=' / '!=' / '<' / '>' / '<=' / '>=' /
    UNARY_OPERATOR  <- 'ISSET' / 'ISNOTSET'
    BLOCK           <- PHP code with addition template syntax

When a comparision is defined as a single `OPERAND` without an operator, the
boolean value of the resolved operand will be used to resolve the
`IF_EXPRESSION`.

\* as defined by [is_numeric](http://php.net/manual/en/function.is-numeric.php).

Example:

    #{ if var[type] = 'list'
        // Handle a list
    #}{ elseif var[type] = 'map'
       // Handle a map
    #}{ else
       // Handle non-collection type
    #}

### Switch Block

In addition to if style conditional sections, a switch section can be used to
substitute different content for different values of the same substitution
variable.

Example:

    #{ switch var
    #| case 0
        // Handle case when var = 0
    #| case > 0
        // Handle case when var > 0
    #| case < 0
        // Handle case when var < 0
    #}

The example for the if block could be rewritten as:

    #{ switch var[type]
    #| case 'list'
        // Handle a list
    #| case 'map'
        // Handle a map
    #| default
        / Handle non-collection type
    #}

Switch cases do __NOT__ fall through.

### Repeating Sections

Repeating sections can be specified as follows:

```
#{ each <var> as <name> [<status>]
  <section>
#}
```

`<var>` must refer to an array substitution value.  Within the repeated section,
the current value of the array substitution value will be available for use by
using the substitution value with name `<name>`

If a name is provided for the `<status>` variable, it will be populated as an
array containing the following information:

 -  index: The current index of the iteration.
 -  first: Whether or not the iteration is on the first element.
 -  last: Whether or not the iteration is on the last element.
 -  has_next: Whether or not the iteration has another element after the current
    element.

### Example

This example is a portion of the template used to create a persister object for
model classes in the [Clarinet ORM Project](https://github.com/pgraham/Clarinet). The complete template can be found at
<https://github.com/pgraham/Clarinet/blob/master/src/persister/persister.tmlp.php>

```php
class /*# actor #*/ {

    // ...

    public function create(\/*# class #*/ $model) {

      if (!$this->validator->validate($model, $e)) {
        throw $e;
      }

      if ($model->get/*# id_property */() !== null) {
        return $model->get/*# id_property */();
      }

      #{ if beforeCreate
        $model->beforeCreate();
      #}

      try {
        $startTransaction = $this->_pdo->beginTransaction();

        $model->set/*# id_property */(self::CREATE_MARKER);

        $params = Array();
        #{ each properties as prop
          #{ if prop[type] = boolean
            $params['/*# prop[col] #*/'] = $model->get/*# prop[name] #*/() ? 1 : 0;
          #{ else
            $params['/*# prop[col] #*/'] = $model->get/*# prop[name] #*/();
          #}
        #}

        #{ each relationships as rel
          #{ if rel[type] = many-to-one
            // Populate /*# rel[rhs] #*/ parameter --------------------------------
            // ...
            // -------------------------------------------------------------------
          #}
        #}

        $this->_create->execute($params);

        $id = $this->transformer->idFromDb($this->_pdo->lastInsertId());
        $model->set/*# id_property */($id);
        $this->_cache[$id] = $model;

        #{ each collections as col
          $this->insertCollection_/*# col[property] #*/(
            $id,
            $model->get/*# col[property] #*/()
          );
        #}

        #{ each relationships as rel
          $related = $model->get/*# rel[lhsProperty] #*/();
          #{ if rel[type] = many-to-many
            // ...

          #{ elseif rel[type] = one-to-many
            // ...
          #}
        #}

        if ($startTransaction) {
          $this->_pdo->commit();
        }

        #{ if onCreate
          $model->onCreate();
        #} 

        return $id;
      } catch (PDOException $e) {
        $this->_pdo->rollback();
        $model->set/*# id_property */(null);

        $e = new PdoExceptionWrapper($e, '/*# class #*/');
        $e->setSql($sql, $params);
        throw $e;
      }
    }

    // ...

}
```

## Value Substitution

Value substitution, referred to here after as template resolution (or more
simply resolution) is the process of replacing all template tags with values so
that a code template becomes an actual useful piece of code.

Templates can be resolved using a `zpt\pct\TemplateResolver` instance and
invoking it's `resolve($templatePath, $outputpath, $values);` method.
