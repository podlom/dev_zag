<?php
  $original = isset($entry)? $entry->original : null;
  $translations = isset($entry)? $entry->translations : null;
?>

<?php if($original): ?>
<?php 
if($field['model'] != 'meta')
  $title = $original->title? $original->title : ($original->name? $original->name : ($original->question? $original->question : 'Без названия'));
else
  $title = $original->extras['cottage_h1']? $original->extras['cottage_h1'] : ($original->extras['newbuild_h1']? $original->extras['newbuild_h1'] : 'Без названия');
  
?>
<div class="form-group col-sm-12">
  <label>Оригинал</label>
  <br>
  <a href="<?php echo e(url('admin/' .  $field['model'] . '/' . $original->id . '/edit')); ?>"><?php echo e($title); ?></a>
</div>
<?php elseif($translations && $translations->count()): ?>
<div class="form-group col-sm-12">
  <label>Переводы</label>
  
  <?php $__currentLoopData = $translations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
  <?php 
  if($field['model'] != 'meta')
    $title = $item->title? $item->title : ($item->name? $item->name : ($item->question && $item->getTable() != 'poll_options'? $item->question : 'Без названия'));
  else
    $title = $item->extras['cottage_h1']? $item->extras['cottage_h1'] : ($item->extras['newbuild_h1']? $item->extras['newbuild_h1'] : 'Без названия');
    
  ?>
  <br>
  <a href="<?php echo e(url('admin/' .  $field['model'] . '/' . $item->id . '/edit')); ?>"><?php echo e($title); ?> (<?php echo e($item->language_abbr); ?>)</a>
  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php endif; ?><?php /**PATH /var/www/zagorodnaz/dev.zagorodna.com/dev_app/resources/views/vendor/backpack/crud/fields/translation.blade.php ENDPATH**/ ?>