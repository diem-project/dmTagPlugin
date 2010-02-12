<?php
/**
 */
class PluginDmTagTable extends myDoctrineTable
{

  public function getTagNames()
  {
    return $this->createQuery('t')
    ->select('t.name')
    ->fetchFlat();
  }

  public function getPopularTagsQuery($relations = null, $limit = null, dmDoctrineQuery $q = null)
  {
    if (empty($relations))
    {
      $relations = array_keys($this->getRelationHolder()->getAssociations());
    }
    $relations = (array) $relations;

    if(empty($relations))
    {
      throw new dmException('There is no taggable model');
    }

    $q = $q ? $q : $this->createQuery('t')->select('t.*');

    $rootAlias = $q->getRootAlias();

    $counts = array();
    foreach ($relations as $relation)
    {
      $countAlias = 'num_' . Doctrine_Inflector::tableize($relation);

      $q->leftJoin($rootAlias.'.' . $relation . ' '.$relation);
      $q->addSelect('COUNT(DISTINCT ' . $relation . '.id) AS ' . $countAlias);
      $counts[] = 'COUNT(DISTINCT ' . $relation .'.id)';
    }

    $q->addSelect('(' . implode(' + ', $counts) . ') as total_num');
    $q->orderBy('total_num DESC');
    $q->groupBy($rootAlias.'.id');
    $q->addHaving('total_num > 0');

    if(null !== $limit)
    {
      $q->limit($limit);
    }
    
    return $q;
  }

  public function getPopularTags($relations = null, $limit = null, $hydrationMode = Doctrine::HYDRATE_RECORD)
  {
    $q = $this->getPopularTagsQuery($relations, $limit);

    return $q->execute(array(), $hydrationMode);
  }
}