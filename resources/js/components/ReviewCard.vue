<template>
    <li class="article-page__comments-item" :class="classes" v-if="type == 'article'" itemscope itemtype="http://schema.org/Review">
        <div itemprop="itemReviewed" itemscope itemtype="http://schema.org/Person">
            <meta itemprop="name" :content="review.reviewable_name">
        </div>
        <div class="article-page__comments-item__header">
            <p class="name" itemprop="author">{{ review.name }}</p>
            <meta itemprop="datePublished" :content="review.robots_date">
            <p class="date">{{ review.created_at }}</p>
        </div>
        <div class="article-page__comments-item__body">
            <div v-html="review.text" itemprop="reviewBody"></div>
        </div>
    </li>

    <li class="product-page__reviews-item" :class="classes" v-else-if="type == 'product'" itemscope itemtype="http://schema.org/Review">
        <div itemprop="itemReviewed" itemscope itemtype="http://schema.org/Person">
            <meta itemprop="name" :content="review.reviewable_name">
        </div>
        <div class="product-page__reviews-item__header">
            <div class="star__wrapper" v-if="review.rating">
                <meta itemprop="reviewRating" :content="review.rating">
                <ul class="star__list">
                    <li class="start__item">
                        <span class="icon-star"></span>
                    </li>
                    <li class="start__item">
                        <span class="icon-star"></span>
                    </li>
                    <li class="start__item">
                        <span class="icon-star"></span>
                    </li>
                    <li class="start__item">
                        <span class="icon-star"></span>
                    </li>
                    <li class="start__item">
                        <span class="icon-star"></span>
                    </li>
                </ul>
                <ul class="star__list star__list-atop" :style="{width: (review.rating * 10) + '%' }">
                    <li class="start__item">
                        <span class="icon-star"></span>
                    </li>
                    <li class="start__item">
                        <span class="icon-star"></span>
                    </li>
                    <li class="start__item">
                        <span class="icon-star"></span>
                    </li>
                    <li class="start__item">
                        <span class="icon-star"></span>
                    </li>
                    <li class="start__item">
                        <span class="icon-star"></span>
                    </li>
                </ul>
            </div>
            <meta itemprop="datePublished" :content="review.robots_date">
            <p class="date">{{ review.created_at }}</p>
        </div>
        <div class="product-page__reviews-item__body">
            <div v-html="review.text" itemprop="reviewBody"></div>
        </div>
        <div class="product-page__reviews-item__footer">
            <p itemprop="author">{{ review.name }}</p>
        </div>
    </li>

    <li class="product-page__reviews-item" :class="classes" v-else-if="type == 'product_image'" itemscope itemtype="http://schema.org/Review">
        <div itemprop="itemReviewed" itemscope itemtype="http://schema.org/Person">
            <meta itemprop="name" :content="review.reviewable_name">
        </div>
        <div class="product-page__reviews-item__img" :style="{backgroundImage:'url(' + review.reviewable_image + ')'}" v-if="review.reviewable_image"></div>
        <div class="product-page__reviews-item__header">
            <p class="caption" itemprop="name">{{ review.reviewable_name }}</p>
            <meta itemprop="datePublished" :content="review.robots_date">
            <p class="date">{{ review.created_at }}</p>
        </div>
        <div class="product-page__reviews-item__body">
            <div class="star__wrapper" v-if="review.rating">
                <meta itemprop="reviewRating" :content="review.rating">
                <ul class="star__list">
                    <li class="start__item">
                        <span class="icon-star"></span>
                    </li>
                    <li class="start__item">
                        <span class="icon-star"></span>
                    </li>
                    <li class="start__item">
                        <span class="icon-star"></span>
                    </li>
                    <li class="start__item">
                        <span class="icon-star"></span>
                    </li>
                    <li class="start__item">
                        <span class="icon-star"></span>
                    </li>
                </ul>
                <ul class="star__list star__list-atop" :style="{width: (review.rating * 10) + '%' }">
                    <li class="start__item">
                        <span class="icon-star"></span>
                    </li>
                    <li class="start__item">
                        <span class="icon-star"></span>
                    </li>
                    <li class="start__item">
                        <span class="icon-star"></span>
                    </li>
                    <li class="start__item">
                        <span class="icon-star"></span>
                    </li>
                    <li class="start__item">
                        <span class="icon-star"></span>
                    </li>
                </ul>
            </div>
            <div v-html="review.text" itemprop="reviewBody"></div>
        </div>
        <div class="product-page__reviews-item__footer">
            <p itemprop="author">{{ review.name }}</p>
        </div>
    </li>

    <li class="reviews__item js-item" v-else-if="type == 'zagorodna'" :class="classes" itemscope itemtype="http://schema.org/Review">
        <meta itemprop="name" content="https://www.zagorodna.com/">
        <div itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating">
            <meta itemprop="author" :content="review.name">
            <meta itemprop="ratingValue" :content="review.rating">
            <meta itemprop="bestRating" content="10">
            <meta itemprop="worstRating" content="1">
        </div>
        <div itemprop="itemReviewed" itemscope itemtype="http://schema.org/Thing">
            <meta itemprop="name" content="https://www.zagorodna.com/">
        </div>
        <div class="reviews__header">
            <div class="wrapper">
                <div class="start-construction__wrapper">
                    <div class="star__wrapper" v-if="review.rating">
                        <ul class="star__list">
                            <li class="start__item">
                                <span class="icon-star"></span>
                            </li>
                            <li class="start__item">
                                <span class="icon-star"></span>
                            </li>
                            <li class="start__item">
                                <span class="icon-star"></span>
                            </li>
                            <li class="start__item">
                                <span class="icon-star"></span>
                            </li>
                            <li class="start__item">
                                <span class="icon-star"></span>
                            </li>
                        </ul>
                        <ul class="star__list star__list-atop" :style="{width: (review.rating * 10) + '%' }">
                            <li class="start__item">
                                <span class="icon-star"></span>
                            </li>
                            <li class="start__item">
                                <span class="icon-star"></span>
                            </li>
                            <li class="start__item">
                                <span class="icon-star"></span>
                            </li>
                            <li class="start__item">
                                <span class="icon-star"></span>
                            </li>
                            <li class="start__item">
                                <span class="icon-star"></span>
                            </li>
                        </ul>
                    </div>
                </div>
                <!-- <p class="time-on-service">На сервисе 10 мес.</p> -->
            </div>
            <div class="reviews__img" v-if="review.file">
                <img :src="'/' + review.file" :alt="review.name + ' фото'" :title="review.name + ' картинка'">
            </div>
            <meta itemprop="datePublished" :content="review.robots_date">
            <p class="reviews__time-publication">{{ review.created_at }}</p>
        </div>
        <div class="reviews__caption">
            <h4 itemprop="author">{{ review.name }}</h4>
            <p v-if="review.profession">{{ review.profession }}</p>
        </div>
        <div class="reviews__text">
            <div class="js-content">
                <div v-html="review.text" itemprop="reviewBody"></div>
            </div>
        </div>
        <button class="read-more js-more js-button" data-target="reviews">
            <span>{{ read_more }}</span>
            <span class="icon-arrow-more"></span>
        </button>
    </li>

    <li class="reviews__item" v-else-if="type == 'precatalog'" :class="classes" itemscope itemtype="http://schema.org/Review">
        <div class="reviews__header">
            <div class="reviews__img" v-if="review.reviewable_image">
                <img :src="review.reviewable_image" :alt="review.reviewable_name + ' фото'" :title="review.reviewable_name + ' картинка'">
            </div>
            <meta itemprop="datePublished" :content="review.robots_date">
            <p class="reviews__time-publication">{{ review.created_at_number }}</p>
        </div>
        <div class="reviews__caption">
            <h4 itemprop="name">{{ review.reviewable_name }}</h4>
            <div class="start-construction__wrapper">
                <div class="star__wrapper" v-if="review.rating">
                    <meta itemprop="reviewRating" :content="review.rating">
                    <ul class="star__list">
                        <li class="start__item">
                            <span class="icon-star"></span>
                        </li>
                        <li class="start__item">
                            <span class="icon-star"></span>
                        </li>
                        <li class="start__item">
                            <span class="icon-star"></span>
                        </li>
                        <li class="start__item">
                            <span class="icon-star"></span>
                        </li>
                        <li class="start__item">
                            <span class="icon-star"></span>
                        </li>
                    </ul>
                    <ul class="star__list star__list-atop" :style="{width: (review.rating * 10) + '%' }">
                        <li class="start__item">
                            <span class="icon-star"></span>
                        </li>
                        <li class="start__item">
                            <span class="icon-star"></span>
                        </li>
                        <li class="start__item">
                            <span class="icon-star"></span>
                        </li>
                        <li class="start__item">
                            <span class="icon-star"></span>
                        </li>
                        <li class="start__item">
                            <span class="icon-star"></span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="reviews__text">
            <div v-html="review.text" itemprop="reviewBody"></div>
        </div>
        <p class="reviews__text__autor" itemprop="author">{{ review.name }}</p>
        <div class="reviews__footer">
            <p class="reviews__calc-coments">
                <span class="icon-comment-text-outline" v-if="review.reviewable_reviews_count"></span>
                <span class="number" v-if="review.reviewable_reviews_count">{{ review.reviewable_reviews_count }}</span>
            </p>
            <a :href="review.reviewable_link + '/reviews'" class="read-more">
                <span>{{ read_more_reviews }}</span>
                <span class="icon-arrow-more"></span>
            </a>
        </div>
    </li>
</template>

<script>
    export default {
        name: 'reviewcard',
        data: function(){
            return {
                review: this.dataReview,
                type: this.dataType,
                classes: this.dataClasses,
                read_more: read_more,
                read_more_reviews: read_more_reviews
            }
        },
        watch: {
            dataReview: {
                handler: function(value) {
                this.review = value;
                },
                deep: true
            },
            dataType: function(value) {
                this.type = value;
            },
        },
        props: ['dataReview', 'dataType', 'dataClasses'],
    }
</script>
