<?php
/*echo $paginator->prev(
  ' << ' . __('previous'),
  array(),
  null,
  array('class' => 'prev disabled')
);*/
//echo $paginator->first(3);
echo $paginator->numbers(array(
    'before' => '<ul class="pagination">',
    'separator' => '',
    'currentClass' => 'active',
    'currentTag' => 'a',
    'tag' => 'li',
    //'first' => 2,
    'after' => '</ul><br />'
        ));
