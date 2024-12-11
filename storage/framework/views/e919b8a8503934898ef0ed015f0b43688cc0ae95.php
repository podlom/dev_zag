

<?php $__env->startSection('content'); ?>
<main style="padding-bottom:60px">
    <div class="decor-background" style="background-image:url(<?php echo e(url('img/background-img-2.png')); ?>)"></div>
    <section class="breadcrumbs">
        <div class="breadcrumbs__wrapper">
            <?php echo e(Breadcrumbs::render('events', $article->category->parent, $article->category, $article->title)); ?>

        </div>
    </section>
    <section class="article-page" itemscope itemtype="http://schema.org/Article">
        <meta itemprop="mainEntityOfPage" content="<?php echo e($article->link); ?>">
        <div itemprop="publisher" itemscope itemtype="http://schema.org/Organization">
            <meta itemprop="name" content="Zagorodna.com">
            <div itemprop="logo" itemscope itemtype="http://schema.org/ImageObject">
                <meta itemprop="url" content="<?php echo e(url('img/logo.png')); ?>">
            </div>
        </div>
        <meta itemprop="author" content="Zagorodna.com">
        <meta itemprop="dateModified" content="<?php echo e($article->updated_at->format('Y-m-d')); ?>">
        <div class="article-page__wrapper container">
            <div class="general-heading">
                <h1 class="main-caption-l main-caption-l--transform" itemprop="headline"><?php echo e($article->title); ?></h1>
                <!-- <div class="general-noty__buttons-container">
                    <button class="general-noty__button general-noty__button-favorite">
                        <span class="icon-heart-outline"></span>
                    </button>
                    <button class="general-noty__button general-noty__button-sing-up">
                        <span class="icon-bell-outline"></span>
                    </button>
                </div> -->
            </div>
            <?php if($lang != 'ru'): ?>
            <a href="<?php echo e($article->translation_link); ?>" class="translate-article">Читать статью на русском</a>
            <?php elseif($article->translation_link !== url('uk')): ?>
            <a href="<?php echo e($article->translation_link); ?>" class="translate-article">Читати статтю українською</a>
            <?php endif; ?>
            <article class="article-main">
                <div class="article-main__header">
                    <p class="name" itemprop="about"><?php echo e($article->category->name); ?></p>
                </div>
                <div class="article-main__body" itemprop="articleBody">
                    <?php echo $article->short_desc; ?>

                    <?php echo $article->filtered_content; ?>

                    <!-- <div class="article-main__recommendation">
                        Читайте также: <a href="#">“Коронавирус наступает: несколько слов о профилактике и лечении”</a>
                    </div> -->
                </div>
                <div class="article-main__footer">
                    <div class="popular__statistics">
                        <p class="popular__views">
                            <span class="icon-eyes"></span>
                            <span><?php echo e($article->views); ?></span>
                        </p>
                        <p class="popular__comments" itemprop="interactionStatistic" itemscope itemtype="http://schema.org/InteractionCounter">
                            <meta itemprop="interactionType" content="http://schema.org/CommentAction"/>
                            <span class="icon-comment-text-outline"></span>
                            <span itemprop="userInteractionCount"><?php echo e($article->reviews->count()); ?></span>
                        </p>
                    </div>
                    <div class="general-social__wrapper">
                        <h5><?php echo e(__('main.Поделиться ссылкой')); ?>:</h5>
                        <ul class="general__socila-list">
                            <li class="general__social-item">
                                <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo e(request()->url()); ?>" target="_blank" class="general__social-link social-facebook" title="Facebook" rel="nofollow">
                                    <span class="icon-facebook"></span>
                                </a>
                            </li>
                            <li class="general__social-item">
                                <a href="https://twitter.com/intent/tweet?text=<?php echo e($article->title); ?>&url=<?php echo e(request()->url()); ?>" target="_blank" class="general__social-link social-twitter" title="Twitter" rel="nofollow">
                                    <span class="icon-twitter"></span>
                                </a>
                            </li>
                            <li class="general__social-item">
                                <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo e(request()->url()); ?>" target="_blank" class="general__social-link social-linkedin" title="Linkedin" rel="nofollow">
                                    <span class="icon-linkedin"></span>
                                </a>
                            </li>
                            <li class="general__social-item">
                                <a href="https://telegram.me/share/url?url=<?php echo e(request()->url()); ?>&text=<?php echo e($article->title); ?>" target="_blank" class="general__social-link social-telegram" title="Telegram" rel="nofollow">
                                    <span class="icon-telegram"></span>
                                </a>
                            </li>
                            <li class="general__social-item copy">
                                <input type="text" value="<?php echo e(request()->url()); ?>">
                                <a href="#" title="<?php echo e(__('main.Скопировать ссылку')); ?>" class="general__social-link social-link" rel="nofollow">
                                    <span class="icon-link-variant"></span>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <?php if($article->tags->count()): ?>
                    <div class="article-main__tags">
                        <p>Теги:</p>
                          <?php $__currentLoopData = $article->tags; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e(url($lang . '/tags?id=' . $tag->id)); ?>">
                          <?php echo e($key == 0 ? $tag->name : ', ' . $tag->name); ?>

                        </a>
                          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <?php endif; ?>
                </div>
            </article>
        </div>
    </section>
</main>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
  var article = <?php echo json_encode($article, 15, 512) ?>;
  var reviews = <?php echo json_encode($reviews, 15, 512) ?>;
  var otherArticles = <?php echo json_encode($otherArticles, 15, 512) ?>;
</script>
<script src="<?php echo e(url('js/news/article.js?v=' . $version)); ?>"></script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', [
  'meta_title' => $article->meta_title? $article->meta_title : $article->title,
  'meta_desc' => $article->meta_desc? $article->meta_desc : $article->title,
  'hide_from_index' => $article->hide_from_index,
  'og_type' => 'article',
], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/zagorodnaz/dev.zagorodna.com/dev_app/resources/views/business/show.blade.php ENDPATH**/ ?>