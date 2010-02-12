<?php // Vars: $dmTagPager

echo _open('ul.elements');

foreach ($dmTags as $dmTag)
{
  echo _open('li.element');

    echo _link($dmTag)->text($dmTag->name.' ('.$dmTag->total_num.')');

  echo _close('li');
}

echo _close('ul');