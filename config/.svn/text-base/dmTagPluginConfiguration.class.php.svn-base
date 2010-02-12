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

    foreach($this->getTaggableModels($cache) as $model)
    {
      dmDb::table($model);
    }

    if($this->configuration instanceof dmAdminApplicationConfiguration)
    {
      $this->dispatcher->connect('form.post_configure', array($this, 'listenToFormPostConfigureEvent'));
    }

    $this->dispatcher->connect('dm.admin_generator_builder.config', array($this, 'listenToAdminGeneratorBuilderConfig'));
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

  public function listenToFormPostConfigureEvent(sfEvent $event)
  {
    $form = $event->getSubject();

    if($form instanceof dmFormDoctrine && $form->getObject()->getTable()->hasTemplate('DmTaggable'))
    {
      $form->setWidget('tags', new sfWidgetFormDmTagsAutocomplete(
        array('choices' => $form->getObject()->getTagNames())
      ));

      $form->setValidator('tags', new sfValidatorDmTagsAutocomplete());
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
    foreach(dmProject::getAllModels() as $model)
    {
      if(dmDb::table($model)->hasTemplate('DmTaggable'))
      {
        $models[] = $model;
      }
    }

    $cache->set('taggable_models', $models);

    return $models;
  }
}