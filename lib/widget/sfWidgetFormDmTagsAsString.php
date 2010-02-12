<?php

class sfWidgetFormDmTagsAutocomplete extends sfWidgetFormSelect
{
  
  protected function configure($options = array(), $attributes = array())
  {
    parent::configure($options, $attributes);

    $this->setOption('multiple', true);
  }

  protected function getOptionsForSelect($value, $choices)
  {
    $choices = dmArray::valueToKey($choices);
    $value = $choices;

    $html = parent::getOptionsForSelect($choices, $choices);

    // fcbkcomplete wants a class selected
    $html = str_replace('selected="selected"', 'class="selected"', $html);

    return $html;
  }

  public function getJavascripts()
  {
    return array_merge(parent::getJavaScripts(), array(
      'dmTagPlugin.fcbkcomplete',
      'dmTagPlugin.launcher'
    ));
  }

  public function getStylesheets()
  {
    return array_merge(parent::getStylesheets(), array(
      'dmTagPlugin.fcbkcomplete' => array()
    ));
  }
}