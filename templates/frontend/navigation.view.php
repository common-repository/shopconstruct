<?php
/**
 * @var $args array
 */
$total = $args['count'];
$limit = $args['per_page'];
$pages = ceil( $total / $limit );
$page = $args['paged'];
$offset = ($page - 1)  * $limit;
$start = $offset + 1;
$end = min(($offset + $limit), $total);
$prevlink = ($page > 1)
    ? '<a href="'.add_query_arg( 'paged', '1').'" title="First page">&laquo;</a> <a href="'.add_query_arg('paged',$page - 1).'" title="Previous page">'.($page - 1).'</a>'
    : '<span class="disabled">&laquo;</span> <span class="disabled">&lsaquo;</span>';
$nextlink = ($page < $pages)
    ? '<a href="'.add_query_arg( 'paged', $page + 1).'" title="Next page">'.($page + 1).'</a> <a href="'.add_query_arg('paged',$pages).'" title="Last page">&raquo;</a>'
    : '<span class="disabled">'.($page + 1).'</span> <span class="disabled">&raquo;</span>';

echo '<div id="paging"><p>', $prevlink, ' Page <span>', $page, '</span> of ', $pages, ' pages, displaying ', $start, '-', $end, ' of ', $total, ' results ', $nextlink, ' </p></div>';