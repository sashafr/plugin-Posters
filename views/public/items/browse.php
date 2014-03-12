<?php
echo pagination_links(
    array(
        'url' => url(
            array(
                'controller' => 'posters', 
                'action' => 'items', 
                'page' => null
            )
        )
    )
);
?>

<div id="item-list">
    <?php echo item_search_filters()); ?>
    <?php if (!has_loop_records('items')): ?>
        <p><?php echo __('There are no items to choose from. Please refine your search'); ?></p>
    <?php endif; ?>
    <?php foreach (loop('items') as $item): ?>
        <?php echo $this->posterItemListing($item); ?>
    <?php endforeach; ?>
</div>