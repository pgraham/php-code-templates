# Upgrading guide

## Upgrading from 2.x to 3.x

 -  Filters now accept parameters in brackets, not after the variable name. So
    `join:var:,` will become `join(,):var`.
 -  join-php filter has been eliminated. The same effect can now be acheived
    with `each(php)-join(,):var`
 -  Substitution tags must now end with '#*/', '*/' is no longer a valid end
    tag.

### What's new
 -  If block elseif and else statements can now be optionally prefixed with
    a closing brace to not break brace matching in editors. E.g:
    
        #{ if ...
        #}{ elseif ...
        #}{ else ...
        #}

 -  Filters can now be layered. Layered filters are evaluated from left to
    right. E.g: `each(php)-join(,):var` will encode each value of the array
    `var` as php and join the resulting array using a comma as the glue string.

## Upgrading from 1.x to 2.x

Erm, just rewrite it.
