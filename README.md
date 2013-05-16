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
with the following syntax: `/*# name[idx] */`.

The index portion is optional. Indexes are described below in the section on
Value Substitution.

Array substitution values can be substituted inline using a join substitution
tag: `/*# join:<var>:<glue> */`.

### Conditional Sections

Templates support conditional sections. These sections will only be output
durring value substitution if the set of substitution values matches the
conditional expression. Conditional sections have to following syntax:

```
#{ if <var> <op> <value>
  <section>
#{ elseif <var> <op> <value>
  <section>
#{ else
  <section>
#}
```

In the condition, `<var>` has the same syntax as a substitution tag and must be
the name of a provided substitution value, `<op>` is one of `=, >, >=, <, <=,
!=, ISSET, ISNOTSET` and `<value>` is any value. For boolean substitution values
`<op>` and `<value>` can be ommitted.

`#{ elseif ...` and `#{ else` sections are optional.

### Repeating Sections

Repeating sections can be specified as follows:

```
#{ each <var> as <name>
  <section>
#}
```

`<var>` must refer to an array substitution value.  Within the repeated section,
the current value of the array substitution value will be available for use by
using the substitution value with name `<name>`

### Example

This example is portion of the template used to create a persister object for
model classes in the [Clarinet ORM Project](https://github.com/pgraham/Clarinet). The complete template can be found at
<https://github.com/pgraham/Clarinet/blob/master/src/persister/persister.php>

```php
class /*# actor */ {

    // ...

    public function create(\/*# class */ $model) {

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
            $params['/*# prop[col] */'] = $model->get/*# prop[name] */() ? 1 : 0;
          #{ else
            $params['/*# prop[col] */'] = $model->get/*# prop[name] */();
          #}
        #}

        #{ each relationships as rel
          #{ if rel[type] = many-to-one
            // Populate /*# rel[rhs] */ parameter --------------------------------
            // ...
            // -------------------------------------------------------------------
          #}
        #}

        $this->_create->execute($params);

        $id = $this->transformer->idFromDb($this->_pdo->lastInsertId());
        $model->set/*# id_property */($id);
        $this->_cache[$id] = $model;

        #{ each collections as col
          $this->insertCollection_/*# col[property] */(
            $id,
            $model->get/*# col[property] */()
          );
        #}

        #{ each relationships as rel
          $related = $model->get/*# rel[lhsProperty] */();
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

        $e = new PdoExceptionWrapper($e, '/*# class */');
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
