<?php

class dmTagPluginConfiguration extends sfPluginConfiguration
{
  public function configure()
  {
    $this->dispatcher->connect('dm.context.loaded', array($this,'listenToDmContextLoaded'));
  }

  public function listenToDmContextLoaded(sfEvent $e)
  {
    $cache = $e->getSubject()->get('cache_manager')->getCache('dm_tag');

    $taggableModels = $this->getTaggableModels($cache);

    $taggableModels = $this->dispatcher->filter(new sfEvent(
      $this, 'dm_tag.taggable_models.filter', array()
    ), $taggableModels)->getReturnValue();

    foreach($this->getTaggableModels($cache) as $model)
    {
      dmDb::table($model);
    }

    if($this->configuration instanceof dmAdminApplicationConfiguration)
    {
      $this->dispatcher->connect('form.post_configure', array($this, 'listenToFormPostConfigureEvent'));
    }

    $this->dispatcher->connect('dm.admin_generator_builder.config', array($this, 'listenToAdminGeneratorBuilderConfig'));

    $this->dispatcher->connect('dm.table.filter_seo_columns', array($this, 'listenToTableFilterSeoColumns'));
  }

  public function listenToAdminGeneratorBuilderConfig(sfEvent $event, array $config)
  {
    if($event['module']->getTable()->hasTemplate('DmTaggable'))
    {
      foreach($config['form']['display'] as $fieldset => $fields)
      {
        if(false !== ($tagsListPosition = array_search('tags_list', $fields)))
        {
          $config['form']['display'][$fieldset][$tagsListPosition] = 'tags';
        }
      }
    }

    return $config;
  }

  public function listenToTableFilterSeoColumns(sfEvent $event, array $seoColumns)
  {
    if($event->getSubject()->hasTemplate('DmTaggable'))
    {
      $seoColumns[] = 'tags_string';
    }

    return $seoColumns;
  }

  public function listenToFormPostConfigureEvent(sfEvent $event)
  {
    $form = $event->getSubject();

    if($form instanceof dmFormDoctrine && $form->getObject()->getTable()->hasTemplate('DmTaggable'))
    {
      $form->setWidget('tags', new sfWidgetFormDmTagsAutocomplete(
        array('choices' => $form->getObject()->getTagNames())
      ));

      $form->setValidator('tags', new sfValidatorDmTagsAutocomplete(array(
        'required' => false
      )));
    }
  }

  /**
   * @param sfCache $cache
   * @return array models that act as DmTaggable
   */
  public function getTaggableModels(sfCache $cache)
  {
    if($cache->has('taggable_models'))
    {
      return $cache->get('taggable_models');
    }

    $models = array();
    foreach(glob(dmOs::join(sfConfig::get('sf_lib_dir'), 'model/doctrine/base/Base*.class.php')) as $modelBaseFile)
    {
      if(strpos(file_get_contents($modelBaseFile), 'new Doctrine_Template_DmTaggable('))
      {
        $models[] = preg_replace('|^Base(\w+).class.php$|', '$1', basename($modelBaseFile));
      }
    }

    $cache->set('taggable_models', $models);

    return $models;
  }
}