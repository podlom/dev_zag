

<?php $__env->startSection('content'); ?>
<main>
    <div class="decor-background" style="background-image:url(<?php echo e(url('img/background-img-2.png')); ?>)"></div>
    <section class="breadcrumbs">
        <div class="breadcrumbs__wrapper">
            <?php echo e(Breadcrumbs::render('article', $article->category->parent, $article->category, $article->title)); ?>

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
                <div class="general-noty__buttons-container">
                    <button class="general-noty__button general-noty__button-favorite" @click="addToFavorites(article, 'articles')" :class="{active: favorites['articles'].includes(article.id) || favorites['articles'].includes(article.original_id)}" title="<?php echo e(__('main.Добавить в избранное')); ?>">
                        <span class="icon-heart-outline"></span>
                    </button>
                    <!-- <button class="general-noty__button general-noty__button-sing-up">
                        <span class="icon-bell-outline"></span>
                    </button> -->
                </div>
            </div>
            <?php if($lang != 'ru'): ?>
            <a href="<?php echo e($article->translation_link); ?>" class="translate-article">Читать статью на русском</a>
            <?php elseif($article->translation_link !== url('uk')): ?>
            <a href="<?php echo e($article->translation_link); ?>" class="translate-article">Читати статтю українською</a>
            <?php endif; ?>
            <article class="article-main">
                <div class="article-main__header">
                    <p class="name" itemprop="about"><?php echo e($article->category->name); ?></p>
                    <p class="area" itemprop="contentLocation"><?php echo e($article->region_name); ?></p>
                    <meta itemprop="datePublished" content="<?php echo e($article->date->format('Y-m-d')); ?>">
                    <p class="date"><?php echo e($article->date->format('d.m.Y')); ?></p>
                </div>
                <div class="article-main__img">
                    <img src="" v-lazy="'<?php echo e($article->bigImg); ?>'" alt="<?php echo e($article->title); ?> фото" title="<?php echo e($article->title); ?> картинка">
                    <meta itemprop="image" content="<?php echo e($article->bigImg); ?>">
                </div>
                <div class="article-main__body" itemprop="articleBody">
                    <?php echo $article->short_desc; ?>

                    <?php if($sameCategoryArticle): ?>
                    <div class="article-main__recommendation">
                        <?php echo e(__('main.Читайте также')); ?>:
                        <a href="<?php echo e($sameCategoryArticle->link); ?>"><?php echo e($sameCategoryArticle->title); ?></a>
                    </div>
                    <?php endif; ?>
                    <?php echo $article->filtered_content; ?>


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
            <div class="article-page__links-wrapper">
                <?php if($prev): ?>
                <a href="<?php echo e($prev->link); ?>" class="article-page__link prev">
                    <span class="icon-arrow-small"></span>
                    <span class="text"><?php echo e($prev->title); ?></span>
                </a>
                <?php endif; ?>
                <?php if($next): ?>
                <a href="<?php echo e($next->link); ?>" class="article-page__link next">
                    <span class="text"><?php echo e($next->title); ?></span>
                    <span class="icon-arrow-small"></span>
                </a>
                <?php endif; ?>
            </div>
            <div class="subscribe-block subscribe-block-alone">
                <h5 class="subscribe-block__text"><?php echo e(__('main.Нашли_полезную_информацию?')); ?><br><?php echo e(__('main.Подписывайтесь на актуальные публикации')); ?>:</h5>
                <?php echo $__env->make('modules.subscription', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>
            <div class="article-page__comments ts-news-show-blade__ln-139__2024-08-12">
                <div class="general-heading">
                    <h2 class="main-caption-l main-caption-l--transform"><?php echo e(__('main.Комментарии')); ?></h2>
                </div>
                <ul class="article-page__comments-list" v-if="reviews.total">
                    <reviewCard v-for="(review, key) in reviews.data" :data-review="review" data-type="article" :key="key"></reviewCard>
                    <div class="pagination__wrapper">
                        <a href="#" class="main-button-more" @click.prevent="loadmore()" v-if="reviews.to != reviews.total">
                            <span class="text"><?php echo e(__('main.Показать больше')); ?></span>
                        </a>
                    </div>
                </ul>
                <div class="article-page__comments-list" v-else><?php echo e(__('main.К этой новости нет комментариев')); ?></div>
                <form action="<?php echo e(url('reviews/create/article')); ?>" method="post" class="article-page__comments-form" id="review_article">
                  <?php echo csrf_field(); ?>
                    <input type="hidden" name="reviewable_type" value="Backpack\NewsCRUD\app\Models\Article">
                    <input type="hidden" name="reviewable_id" value="<?php echo e($article->id); ?>">
                    <input type="hidden" name="lang" value="<?php echo e($article->language_abbr); ?>">
                    <div class="wrapper">
                        <label class="textarea__wrapper <?php $__errorArgs = ['article_review_text'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                            <h5 class="input__caption"><?php echo e(__('main.Комментарий')); ?></h5>
                            <textarea class="main-textarea" name="article_review_text" placeholder="Текст" value="<?php echo e(old('article_review_text')); ?>"></textarea>
                            <?php $__errorArgs = ['article_review_text'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="error-text" role="alert">
                                    <?php echo e($message); ?>

                                </span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </label>
                        <div class="container-form">
                            <label class="input__wrapper <?php $__errorArgs = ['article_review_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                <h5 class="input__caption"><?php echo e(__('main.Имя')); ?></h5>
                                <input type="text" class="main-input" name="article_review_name" placeholder="<?php echo e(__('forms.placeholders.Введите имя')); ?>" value="<?php echo e(old('article_review_name')); ?>" autocomplete="name">
                                <?php $__errorArgs = ['article_review_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span class="error-text" role="alert">
                                        <?php echo e($message); ?>

                                    </span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </label>
                            <label class="input__wrapper <?php $__errorArgs = ['article_review_email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                <h5 class="input__caption">Email</h5>
                                <input type="email" class="main-input" name="article_review_email" placeholder="<?php echo e(__('forms.placeholders.Ваш электронный адрес')); ?>" value="<?php echo e(old('article_review_email')); ?>" autocomplete="email">
                                <?php $__errorArgs = ['article_review_email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span class="error-text" role="alert">
                                        <?php echo e($message); ?>

                                    </span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </label>
                        </div>
                        <button class="subscribe-block__button"><?php echo e(__('main.Комментировать')); ?></button>
                    </div>
                </form>
            </div>
            <div class="article-page__more-news" v-if="otherArticles.length">
                <div class="general-heading">
                    <h2 class="main-caption-l main-caption-l--transform"><?php echo e(__('main.Читайте также')); ?></h2>
                </div>
                <ul class="popular__block__list popular__block__list-more">
                    <articlecard v-for="(article, key) in otherArticles" :key="key" :data-article="article" @add-to-favorites="addToFavorites"></articlecard>
                </ul>
            </div>
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
  'og_image' => $article->img? url($article->img) : '',
  'og_type' => 'article'
], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/zagorodnaz/dev.zagorodna.com/dev_app/resources/views/news/show.blade.php ENDPATH**/ ?>