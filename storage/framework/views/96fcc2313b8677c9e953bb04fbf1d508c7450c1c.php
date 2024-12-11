<?=
/* Using an echo tag here so the `<? ... ?>` won't get parsed as short tags */
'<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL
?>
<?php

?>
<rss version="2.0">
    <channel>
        <title><![CDATA[<?php echo e($meta['title']); ?>]]></title>
        <link><![CDATA[<?php echo e($meta['language'] === 'uk-UA'? url('feed?lang=uk') : url('feed')); ?>]]></link>
        <description><![CDATA[<?php echo e($meta['description']); ?>]]></description>
        <language><?php echo e($meta['language']); ?></language>
        <pubDate><?php echo e($meta['updated']); ?></pubDate>

        <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <item>
                <title><![CDATA[<?php echo e($item->title); ?>]]></title>
                <link><?php echo e(url($item->link)); ?></link>
                <enclosure url="<?php echo e($item->image); ?>" type="image/jpeg"/>
                <description><![CDATA[<?php echo $item->summary; ?>]]></description>
                <full-text><![CDATA[<?php echo $item->theContent; ?>]]></full-text>
                <author><![CDATA[<?php echo e($item->author); ?>]]></author>
                <guid isPermaLink="false"><?php echo e($item->id); ?></guid>
                <pubDate><?php echo e($item->updated->toRssString()); ?></pubDate>
                <?php $__currentLoopData = $item->category; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <category><?php echo e($category); ?></category>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </item>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </channel>
</rss>
<?php /**PATH /var/www/zagorodnaz/dev.zagorodna.com/dev_app/resources/views/feed/rss.blade.php ENDPATH**/ ?>