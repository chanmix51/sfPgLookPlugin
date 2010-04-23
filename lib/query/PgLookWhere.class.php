<?php

class PgLookWhere
{
  public $stack = array();
  public $element;
  public $values = array();
  public $operator;

  public static function create($element = null, $values = array())
  {
    return new self($element, $values);
  }

  public function __construct($element = null, $values = array())
  {
    if (!is_null($element))
    {
      $this->element = $element;
      $this->values = $values;

    }
  }

  public function setOperator($operator)
  {
    $this->operator = $operator;

    return $this;
  }

  public function isEmpty()
  {
    return (is_null($this->element) and count($this->stack) == 0);
  }

  public function transmute(PgLookWhere $where)
  {
    $this->stack = $where->stack;
    $this->element = $where->element;
    $this->operator = $where->operator;
    $this->values = $where->values;
  }

  public function addWhere($element, $values, $operator)
  {
    if (!$element instanceof PgLookWhere)
    {
      $element = new self($element, $values);
    }

    if ($element->isEmpty()) return $this;
    if ($this->isEmpty())
    {
      $this->transmute($element);

      return $this;
    }

    if ($this->hasElement())
    {
      $this->stack = array(new self($this->getElement(), $this->values), $element);
      $this->element = NULL;
      $this->values = array();
    }
    else
    {
      if ($this->operator == $operator)
      {
        $this->stack[] = $element;
      }
      else
      {
        $this->stack = array(self::create()->setStack($this->stack)->setOperator($this->operator), $element);
      }
    }

    $this->operator = $operator;

    return $this;
  }

  public function andWhere($element, $values = array())
  {
     return $this->addWhere($element, $values, 'AND');
  }

  public function orWhere($element, $values = array())
  {
    return $this->addWhere($element, $values, 'OR');
  }

  public function setStack(Array $stack)
  {
    $this->stack = $stack;

    return $this;
  }

  public function __toString()
  {
    if ($this->isEmpty())
    {
      return 'true';
    }
    else
    {
      return $this->parse();
    }
  }

  public function hasElement()
  {
    return ! is_null($this->element);
  }

  public function getElement()
  {
    return $this->element;
  }

  protected function parse()
  {
    if ($this->hasElement())
    {
      return $this->getElement();
    }

    $stack = array();
    foreach ($this->stack as $offset => $where)
    {
      $stack[$offset] = $where->parse();
    }

    return sprintf('(%s)', join(sprintf(' %s ', $this->operator), $stack));
  }

  public function getValues()
  {
    if ($this->isEmpty())
    {
      return array();
    }
    if ($this->hasElement())
    {
      return $this->values;
    }

    $values = array();
    foreach($this->stack as $offset => $where)
    {
      $values = array_merge($values, $where->getValues());
    }

    return $values;
  }
}
