# PHP Code Templates

PHP Code templates (pct) is a PHP library that parses and performs value
substitution for code templates.

## Creating Templates

Defining a template involves creating a file that encapsulates a common
structure marked up with areas where values should be substituted in order to
create a concrete file that can perform (in the case of code) or provide
something useful. PCT supports simple tag substitution as well as conditional
and repeating sections.

### Substitution Tags

To specify a spot in a template where a value is to be substituted, add a tag
with the following syntax: `${name[idx]}`.

The index portion is optional. Indexes are described below in the section on
Value Substitution.

Array substitution values can be substituted inline using a join substitution
tag: `${join:<var>:<glue>}`.

### Conditional Sections

Templates support conditional sections. These sections will only be output
durring value substitution if the set of substitution values matches the
conditional expression. Conditional sections have to following syntax:

```
${if:<var> <op> <value>}
  <section>
${elseif:<var> <op> <value>}
  <section>
${else}
  <section>
${fi}
```

In the condition, `<var>` has the same syntax as a substitution tag and must be
the name of a provided substitution value, `<op>` is one of `=, >, >=, <, <=,
!=, ISSET, ISNOTSET` and `<value>` is any value. For boolean substitution values
`<op>` and `<value>` can be ommitted.

`${elseif: ...}` and `${else}` sections are optional.

### Repeating Sections

Repeating sections can be specified as follows:

```
${each:<var> as <name>}
  <section>
${done}
```

`<var>` must refer to an array substitution value.  Within the repeated section,
the current value of the array substitution value will be available for use by
using the substitution value with name `<name>`

### Example

```php
<?php
/**
 * Generated persister class.
 */
class ${class} {

  private $_cache = array();

  private $_pdo;

  private $_create;

  private $_update;

  private $_delete;

  public function __construct() {
    $this->_pdo = PdoWrapper::get();

    $this->_create = $this->_pdo->prepare("INSERT INTO ${table} (${join:colNames:,}) VALUES (${join:colParams:,})");

    // ...
  }

  public function create(\${model} $model) {
    if ($model->get${idProperty}() !== null) {
      return $model->get${idProperty}();
    }

    try {
      $startedTransaction = $this->_pdo->begin();

      $params = array(); // Create statement parameter values

      ${each:properties as prop}
        ${if:prop[type] = boolean}
          $params[':${prop[col]}'] = $model->get${prop[name]}() ? 1 : 0;
        ${else}
          $params[':${prop[col]}'] = $model->get${prop[name]}();
        ${fi}
      ${done}

      ${each:relationships as rel}
        ${if:rel[type] = many-to-one}
          // Ensure that if the 'one' side of the relationship is not null that
          // it has id
          
          // ...
        ${fi}
      ${done}

      $this->_create->execute($params);
      $id = $this->_pdo->lastInsertId();
      $model->set${idProperty}($id);
      $this->_cache[$id] = $model;

      ${each:relationships as rel}
        ${if:rel[type] = many-to-many}
          // Update link table to match list of related entities in the model

          // ...

        ${elseif:rel[type] = one-to-many}
          // Update many side of the relationship to match list of related
          // entities in the model

          // ...

        ${fi}
      ${done}

      if ($startedTransaction) {
        $this->_pdo->commit();
      }
    } catch (PDOException $e) {
      $this->_pdo->rollback();
      $model->set${idProperty}(null);
    }
  }
}
```

## Value Substitution

Value substitution is the process of replacing all template tags with values so
that a code template becomes an actual useful piece of code.
