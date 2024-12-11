<?php

namespace App;
use Backpack\LangFileManager\app\Models\Language;

trait PageTemplates
{
    /*
    |--------------------------------------------------------------------------
    | Page Templates for Backpack\PageManager
    |--------------------------------------------------------------------------
    |
    | Each page template has its own method, that define what fields should show up using the Backpack\CRUD API.
    | Use snake_case for naming and PageManager will make sure it looks pretty in the create/update form
    | template dropdown.
    |
    | Any fields defined here will show up after the standard page fields:
    | - select template
    | - page name (only seen by admins)
    | - page title
    | - page slug
    */

    // private function services()
    // {
    //     $this->crud->addField([   // CustomHTML
    //                     'name' => 'metas_separator',
    //                     'type' => 'custom_html',
    //                     'value' => '<br><h2>'.trans('backpack::pagemanager.metas').'</h2><hr>',
    //                 ]);
    //     $this->crud->addField([
    //                     'name' => 'meta_title',
    //                     'label' => trans('backpack::pagemanager.meta_title'),
    //                     'fake' => true,
    //                     'store_in' => 'extras',
    //                 ]);
    //     $this->crud->addField([
    //                     'name' => 'meta_description',
    //                     'label' => trans('backpack::pagemanager.meta_description'),
    //                     'fake' => true,
    //                     'store_in' => 'extras',
    //                 ]);
    //     $this->crud->addField([
    //                     'name' => 'meta_keywords',
    //                     'type' => 'textarea',
    //                     'label' => trans('backpack::pagemanager.meta_keywords'),
    //                     'fake' => true,
    //                     'store_in' => 'extras',
    //                 ]);
    //     $this->crud->addField([   // CustomHTML
    //                     'name' => 'content_separator',
    //                     'type' => 'custom_html',
    //                     'value' => '<br><h2>'.trans('backpack::pagemanager.content').'</h2><hr>',
    //                 ]);
    //     $this->crud->addField([
    //                     'name' => 'content',
    //                     'label' => trans('backpack::pagemanager.content'),
    //                     'type' => 'wysiwyg',
    //                     'placeholder' => trans('backpack::pagemanager.content_placeholder'),
    //                 ]);
    // }

    // private function about_us()
    // {
    //     $this->crud->addField([
    //                     'name' => 'content',
    //                     'label' => trans('backpack::pagemanager.content'),
    //                     'type' => 'wysiwyg',
    //                     'placeholder' => trans('backpack::pagemanager.content_placeholder'),
    //                 ]);
    // }

    private function common()
    {
        $this->languages = Language::getActiveLanguagesNames();
        
        if($this->crud->getCurrentEntry()) {
            $this->crud->addField([
                      'name' => 'language_abbr',
                      'label' => 'Language',
                      'attributes' => [
                          'readonly' => 'radonly'
                      ]
                    ]);
        } else {
            $this->crud->addField([
                'name' => 'language_abbr',
                'label' => 'Language',
                'type' => 'select2_from_array',
                'options' => $this->languages,
              ]);
        }

        $this->crud->addField([
            'name' => 'slug',
            'label' => 'Slug',
        ]);

        $this->crud->addField([
            'name' => 'content',
            'label' => 'Контент',
            'type' => 'ckeditor',
        ]);

        $this->crud->addField([
            'name' => 'meta_title',
            'label' => 'Meta title',
            'fake' => true,
            'store_in' => 'extras',
        ]);

        $this->crud->addField([
                    'name' => 'meta_desc',
                    'label' => 'Meta description',
                    'fake' => true,
                    'store_in' => 'extras',
        ]);

        $this->crud->addField([
            'name' => 'seo_text',
            'label' => 'Seo-текст',
            'type' => 'ckeditor',
            'fake' => true,
            'store_in' => 'extras',
        ]);
    }

    private function researches()
    {
        $this->languages = Language::getActiveLanguagesNames();
        
        if($this->crud->getCurrentEntry()) {
            $this->crud->addField([
                      'name' => 'language_abbr',
                      'label' => 'Language',
                      'attributes' => [
                          'readonly' => 'radonly'
                      ]
                    ]);
        } else {
            $this->crud->addField([
                'name' => 'language_abbr',
                'label' => 'Language',
                'type' => 'select2_from_array',
                'options' => $this->languages,
              ]);
        }

        $this->crud->addField([
                        'name' => 'methods',
                        'label' => 'Методики исследования',
                        'fake' => true,
                        'store_in' => 'extras',
                        'type' => 'table',
                        'entity_singular' => 'метод',
                        'columns' => [
                            'name' => 'Название',
                        ],
                    ]);

        $this->crud->addField([   // CustomHTML
                        'name' => 'separator_1',
                        'type' => 'custom_html',
                        'value' => '<br><h2>Индивидуальное исследование</h2><hr>',
                    ]);
        $this->crud->addField([
                        'name' => 'main_title',
                        'label' => 'Заголовок',
                        'fake' => true,
                        'store_in' => 'extras',
                    ]);
        $this->crud->addField([
                        'name' => 'main_description',
                        'type' => 'wysiwyg',
                        'label' => 'Описание',
                        'fake' => true,
                        'store_in' => 'extras',
                    ]);

        $this->crud->addField([
                        'name' => 'main_meta_title',
                        'label' => 'Meta title',
                        'fake' => true,
                        'store_in' => 'extras',
        ]);

        $this->crud->addField([
                        'name' => 'main_meta_desc',
                        'label' => 'Meta description',
                        'fake' => true,
                        'store_in' => 'extras',
        ]);

        $this->crud->addField([
            'name' => 'main_seo_text',
            'type' => 'wysiwyg',
            'label' => 'Seo-текст',
            'fake' => true,
            'store_in' => 'extras',
        ]);

        $this->crud->addField([   // CustomHTML
                        'name' => 'separator_2',
                        'type' => 'custom_html',
                        'value' => '<br><h2>Исследование всех рынков</h2><hr>',
        ]);
        $this->crud->addField([
                        'name' => 'all_title',
                        'label' => 'Заголовок',
                        'fake' => true,
                        'store_in' => 'extras',
                    ]);
        $this->crud->addField([
                        'name' => 'all_image',
                        'label' => 'Изображение',
                        'type' => 'browse',
                        'upload' => true,
                        'fake' => true,
                        'store_in' => 'extras',
        ]);
        $this->crud->addField([
                        'name' => 'all_description',
                        'type' => 'wysiwyg',
                        'label' => 'Описание',
                        'fake' => true,
                        'store_in' => 'extras',
                    ]);
        // $this->crud->addField([
        //                 'name' => 'all_price',
        //                 'label' => 'Стоимость',
        //                 'fake' => true,
        //                 'store_in' => 'extras',
        //                 'type' => 'number'
        // ]);
        $this->crud->addField([
                    'name' => 'all_meta_title',
                    'label' => 'Meta title',
                    'fake' => true,
                    'store_in' => 'extras',
        ]);

        $this->crud->addField([
                    'name' => 'all_meta_desc',
                    'label' => 'Meta description',
                    'fake' => true,
                    'store_in' => 'extras',
        ]);

        $this->crud->addField([
            'name' => 'all_seo_text',
            'type' => 'wysiwyg',
            'label' => 'Seo-текст',
            'fake' => true,
            'store_in' => 'extras',
        ]);

        $this->crud->addField([   // CustomHTML
                        'name' => 'separator_3',
                        'type' => 'custom_html',
                        'value' => '<br><h2>Исследование рынка коттеджных городков</h2><hr>',
        ]);
        $this->crud->addField([
                        'name' => 'cottage_title',
                        'label' => 'Заголовок',
                        'fake' => true,
                        'store_in' => 'extras',
                    ]);
        $this->crud->addField([
                        'name' => 'cottage_image',
                        'label' => 'Изображение',
                        'type' => 'browse',
                        'upload' => true,
                        'fake' => true,
                        'store_in' => 'extras',
        ]);
        $this->crud->addField([
                        'name' => 'cottage_description',
                        'type' => 'wysiwyg',
                        'label' => 'Описание',
                        'fake' => true,
                        'store_in' => 'extras',
                    ]);
        // $this->crud->addField([
        //                 'name' => 'cottage_price',
        //                 'label' => 'Стоимость',
        //                 'fake' => true,
        //                 'store_in' => 'extras',
        //                 'type' => 'number'
        // ]);
        $this->crud->addField([
                    'name' => 'cottage_meta_title',
                    'label' => 'Meta title',
                    'fake' => true,
                    'store_in' => 'extras',
        ]);

        $this->crud->addField([
                    'name' => 'cottage_meta_desc',
                    'label' => 'Meta description',
                    'fake' => true,
                    'store_in' => 'extras',
        ]);

        $this->crud->addField([
            'name' => 'cottage_seo_text',
            'type' => 'wysiwyg',
            'label' => 'Seo-текст',
            'fake' => true,
            'store_in' => 'extras',
        ]);

        $this->crud->addField([   // CustomHTML
                        'name' => 'separator_4',
                        'type' => 'custom_html',
                        'value' => '<br><h2>Исследование компании "РеалЭкспо"</h2><hr>',
        ]);
        $this->crud->addField([
                        'name' => 'realexpo_title',
                        'label' => 'Заголовок',
                        'fake' => true,
                        'store_in' => 'extras',
                    ]);
        $this->crud->addField([
                        'name' => 'realexpo_image',
                        'label' => 'Изображение',
                        'type' => 'browse',
                        'upload' => true,
                        'fake' => true,
                        'store_in' => 'extras',
        ]);
        $this->crud->addField([
                        'name' => 'realexpo_description',
                        'type' => 'wysiwyg',
                        'label' => 'Описание',
                        'fake' => true,
                        'store_in' => 'extras',
                    ]);

        // $this->crud->addField([
        //                 'name' => 'realexpo_price',
        //                 'label' => 'Стоимость',
        //                 'fake' => true,
        //                 'store_in' => 'extras',
        //                 'type' => 'number'
        // ]);
        $this->crud->addField([
                    'name' => 'realexpo_meta_title',
                    'label' => 'Meta title',
                    'fake' => true,
                    'store_in' => 'extras',
        ]);

        $this->crud->addField([
                    'name' => 'realexpo_meta_desc',
                    'label' => 'Meta description',
                    'fake' => true,
                    'store_in' => 'extras',
        ]);

        $this->crud->addField([
            'name' => 'realexpo_seo_text',
            'type' => 'wysiwyg',
            'label' => 'Seo-текст',
            'fake' => true,
            'store_in' => 'extras',
        ]);
    }

    private function main()
    {
        $this->languages = Language::getActiveLanguagesNames();
        
        if($this->crud->getCurrentEntry()) {
            $this->crud->addField([
                      'name' => 'language_abbr',
                      'label' => 'Language',
                      'attributes' => [
                          'readonly' => 'radonly'
                      ]
                    ]);
        } else {
            $this->crud->addField([
                'name' => 'language_abbr',
                'label' => 'Language',
                'type' => 'select2_from_array',
                'options' => $this->languages,
              ]);
        }

        $this->crud->addField([
                        'name' => 'meta_title',
                        'label' => 'Заголовок (meta)',
                        'fake' => true,
                        'store_in' => 'extras',
                    ]);

        $this->crud->addField([
                        'name' => 'meta_desc',
                        'label' => 'Описание (meta)',
                        'fake' => true,
                        'store_in' => 'extras',
                    ]);

        $this->crud->addField([   // CustomHTML
            'name' => 'entry_separator',
            'type' => 'custom_html',
            'value' => '<br><h2>Вступительный текст</h2><hr>',
        ]);

        $this->crud->addField([
            'name' => 'entry_title',
            'label' => 'Заголовок',
            'fake' => true,
            'store_in' => 'extras',
        ]);

        $this->crud->addField([
            'name' => 'entry_text',
            'label' => 'Текст',
            'type' => 'ckeditor',
            'fake' => true,
            'store_in' => 'extras',
        ]);

        $this->crud->addField([   // CustomHTML
            'name' => 'hot_separator',
            'type' => 'custom_html',
            'value' => '<br><h2>Горящие предложения</h2><hr>',
        ]);

        $this->crud->addField([
            'name' => 'hot_title',
            'label' => 'Заголовок',
            'fake' => true,
            'store_in' => 'extras',
        ]);

        $this->crud->addField([
            'name' => 'hot_text',
            'label' => 'Текст',
            'fake' => true,
            'store_in' => 'extras',
            'type' => 'ckeditor',
        ]);

        $this->crud->addField([   // CustomHTML
            'name' => 'cottage_separator',
            'type' => 'custom_html',
            'value' => '<br><h2>Коттеджные городки</h2><hr>',
        ]);

        $this->crud->addField([
            'name' => 'cottage_title',
            'label' => 'Заголовок',
            'fake' => true,
            'store_in' => 'extras',
        ]);

        $this->crud->addField([
            'name' => 'cottage_text',
            'label' => 'Текст',
            'type' => 'ckeditor',
            'fake' => true,
            'store_in' => 'extras',
        ]);

        $this->crud->addField([
            'name' => 'cottage_button_text',
            'label' => 'Текст на кнопке',
            'fake' => true,
            'store_in' => 'extras',
        ]);

        $this->crud->addField([   // CustomHTML
            'name' => 'newbuild_separator',
            'type' => 'custom_html',
            'value' => '<br><h2>Новостройки</h2><hr>',
        ]);

        $this->crud->addField([
            'name' => 'newbuild_title',
            'label' => 'Заголовок',
            'fake' => true,
            'store_in' => 'extras',
        ]);

        $this->crud->addField([
            'name' => 'newbuild_text',
            'label' => 'Текст',
            'type' => 'ckeditor',
            'fake' => true,
            'store_in' => 'extras',
        ]);

        $this->crud->addField([
            'name' => 'newbuild_button_text',
            'label' => 'Текст на кнопке',
            'fake' => true,
            'store_in' => 'extras',
        ]);

        $this->crud->addField([   // CustomHTML
            'name' => 'promotions_separator',
            'type' => 'custom_html',
            'value' => '<br><h2>Акции</h2><hr>',
        ]);

        $this->crud->addField([
            'name' => 'promotions_title',
            'label' => 'Заголовок',
            'fake' => true,
            'store_in' => 'extras',
        ]);

        $this->crud->addField([
            'name' => 'promotions_text',
            'label' => 'Текст',
            'type' => 'ckeditor',
            'fake' => true,
            'store_in' => 'extras',
        ]);

        $this->crud->addField([
            'name' => 'promotions_button_text',
            'label' => 'Текст на кнопке',
            'fake' => true,
            'store_in' => 'extras',
        ]);

        $this->crud->addField([   // CustomHTML
            'name' => 'news_separator',
            'type' => 'custom_html',
            'value' => '<br><h2>Новости</h2><hr>',
        ]);

        $this->crud->addField([
            'name' => 'news_title',
            'label' => 'Заголовок',
            'fake' => true,
            'store_in' => 'extras',
        ]);

        $this->crud->addField([
            'name' => 'news_button_text',
            'label' => 'Текст на кнопке',
            'fake' => true,
            'store_in' => 'extras',
        ]);

        $this->crud->addField([
            'name' => 'news_text',
            'label' => 'Текст',
            'type' => 'ckeditor',
            'fake' => true,
            'store_in' => 'extras',
        ]);

        $this->crud->addField([   // CustomHTML
            'name' => 'companies_separator',
            'type' => 'custom_html',
            'value' => '<br><h2>Компании</h2><hr>',
        ]);

        $this->crud->addField([
            'name' => 'companies_title',
            'label' => 'Заголовок',
            'fake' => true,
            'store_in' => 'extras',
        ]);

        $this->crud->addField([
            'name' => 'companies_text',
            'label' => 'Текст',
            'type' => 'ckeditor',
            'fake' => true,
            'store_in' => 'extras',
        ]);

        $this->crud->addField([
            'name' => 'companies_button_text',
            'label' => 'Текст на кнопке',
            'fake' => true,
            'store_in' => 'extras',
        ]);

        $this->crud->addField([   // CustomHTML
            'name' => 'reviews_separator',
            'type' => 'custom_html',
            'value' => '<br><h2>Отзывы</h2><hr>',
        ]);

        $this->crud->addField([
            'name' => 'reviews_title',
            'label' => 'Заголовок',
            'fake' => true,
            'store_in' => 'extras',
        ]);

        $this->crud->addField([
            'name' => 'reviews_button_text',
            'label' => 'Текст на кнопке',
            'fake' => true,
            'store_in' => 'extras',
        ]);

        $this->crud->addField([   // CustomHTML
            'name' => 'numbers_separator',
            'type' => 'custom_html',
            'value' => '<br><h2>Zagorodna в цифрах</h2><hr>',
        ]);

        $this->crud->addField([
            'name' => 'numbers_title',
            'label' => 'Заголовок',
            'fake' => true,
            'store_in' => 'extras',
        ]);

        $this->crud->addField([
            'name' => 'numbers_content',
            'label' => 'Список',
            'type' => 'table',
            'entity_singular' => 'элемент',
            'columns' => [
                'text' => 'Текст'
            ],
            'max' => 4,
            'min' => 4,
            'fake' => true,
            'store_in' => 'extras',

        ]);

        $this->crud->addField([   // CustomHTML
            'name' => 'seo_separator',
            'type' => 'custom_html',
            'value' => '<br><h2>Seo-текст</h2><hr>',
        ]);

        $this->crud->addField([
            'name' => 'seo_title',
            'label' => 'Заголовок',
            'fake' => true,
            'store_in' => 'extras',
        ]);

        $this->crud->addField([
            'name' => 'seo_text',
            'label' => 'Текст',
            'type' => 'ckeditor',
            'fake' => true,
            'store_in' => 'extras',
        ]);
    }

    private function companies()
    {
        $this->languages = Language::getActiveLanguagesNames();
        
        if($this->crud->getCurrentEntry()) {
            $this->crud->addField([
                      'name' => 'language_abbr',
                      'label' => 'Language',
                      'attributes' => [
                          'readonly' => 'radonly'
                      ]
                    ]);
        } else {
            $this->crud->addField([
                'name' => 'language_abbr',
                'label' => 'Language',
                'type' => 'select2_from_array',
                'options' => $this->languages,
              ]);
        }

        $this->crud->addField([
                        'name' => 'meta_title',
                        'label' => 'Meta title',
                        'fake' => true,
                        'store_in' => 'extras',
        ]);

        $this->crud->addField([
                        'name' => 'meta_desc',
                        'label' => 'Meta description',
                        'fake' => true,
                        'store_in' => 'extras',
        ]);

        $this->crud->addField([
                        'name' => 'seo_title',
                        'label' => 'Seo-заголовок',
                        'fake' => true,
                        'store_in' => 'extras',
                    ]);

        $this->crud->addField([
                        'name' => 'seo_desc',
                        'type' => 'wysiwyg',
                        'label' => 'Seo-текст',
                        'fake' => true,
                        'store_in' => 'extras',
                    ]);
    }

    private function faq()
    {
        $this->languages = Language::getActiveLanguagesNames();
        
        if($this->crud->getCurrentEntry()) {
            $this->crud->addField([
                      'name' => 'language_abbr',
                      'label' => 'Language',
                      'attributes' => [
                          'readonly' => 'radonly'
                      ]
                    ]);
        } else {
            $this->crud->addField([
                'name' => 'language_abbr',
                'label' => 'Language',
                'type' => 'select2_from_array',
                'options' => $this->languages,
              ]);
        }

        $this->crud->addField([
                        'name' => 'meta_title',
                        'label' => 'Meta title',
                        'fake' => true,
                        'store_in' => 'extras',
        ]);

        $this->crud->addField([
                        'name' => 'meta_desc',
                        'label' => 'Meta description',
                        'fake' => true,
                        'store_in' => 'extras',
        ]);

        $this->crud->addField([
                        'name' => 'main_title',
                        'label' => 'Заголовок',
                        'fake' => true,
                        'store_in' => 'extras',
                    ]);

        $this->crud->addField([
                        'name' => 'form_title',
                        'label' => 'Заголовок формы',
                        'fake' => true,
                        'store_in' => 'extras',
                    ]);

        $this->crud->addField([
                        'name' => 'news_title',
                        'label' => 'Заголовок блока новостей',
                        'fake' => true,
                        'store_in' => 'extras',
        ]);

        $this->crud->addField([
            'name' => 'seo_text',
            'label' => 'Seo-текст',
            'type' => 'ckeditor',
            'fake' => true,
            'store_in' => 'extras',
        ]);
    }

    private function dictionary()
    {
        $this->languages = Language::getActiveLanguagesNames();
        
        if($this->crud->getCurrentEntry()) {
            $this->crud->addField([
                      'name' => 'language_abbr',
                      'label' => 'Language',
                      'attributes' => [
                          'readonly' => 'radonly'
                      ]
                    ]);
        } else {
            $this->crud->addField([
                'name' => 'language_abbr',
                'label' => 'Language',
                'type' => 'select2_from_array',
                'options' => $this->languages,
              ]);
        }

        $this->crud->addField([
                        'name' => 'meta_title',
                        'label' => 'Meta title',
                        'fake' => true,
                        'store_in' => 'extras',
        ]);

        $this->crud->addField([
                        'name' => 'meta_desc',
                        'label' => 'Meta description',
                        'fake' => true,
                        'store_in' => 'extras',
        ]);

        $this->crud->addField([
                        'name' => 'main_title',
                        'label' => 'Заголовок',
                        'fake' => true,
                        'store_in' => 'extras',
                    ]);

        $this->crud->addField([
                        'name' => 'main_text',
                        'label' => 'Текст',
                        'type' => 'ckeditor',
                        'fake' => true,
                        'store_in' => 'extras',
                    ]);

        $this->crud->addField([
                        'name' => 'news_title',
                        'label' => 'Заголовок блока новостей',
                        'fake' => true,
                        'store_in' => 'extras',
        ]);
    }

    private function reviews()
    {
        $this->languages = Language::getActiveLanguagesNames();

        if($this->crud->getCurrentEntry()) {
        $this->crud->addField([
                      'name' => 'language_abbr',
                      'label' => 'Language',
                      'attributes' => [
                          'readonly' => 'radonly'
                      ]
                    ]);
        } else {
            $this->crud->addField([
                'name' => 'language_abbr',
                'label' => 'Language',
                'type' => 'select2_from_array',
                'options' => $this->languages,
              ]);
        }

        $this->crud->addField([
                        'name' => 'h1',
                        'label' => 'Заголовок h1',
                        'fake' => true,
                        'store_in' => 'extras',
                        'tab' => 'Zagorodna'
        ]);

        $this->crud->addField([
                        'name' => 'meta_title',
                        'label' => 'Meta title',
                        'fake' => true,
                        'store_in' => 'extras',
                        'tab' => 'Zagorodna'
        ]);

        $this->crud->addField([
                        'name' => 'meta_desc',
                        'label' => 'Meta description',
                        'fake' => true,
                        'store_in' => 'extras',
                        'tab' => 'Zagorodna'
        ]);

        $this->crud->addField([
            'name' => 'seo_title',
            'label' => 'Seo-заголовок',
            'fake' => true,
            'store_in' => 'extras',
            'tab' => 'Zagorodna'
        ]);

        $this->crud->addField([
            'name' => 'seo_text',
            'label' => 'Seo-текст',
            'type' => 'ckeditor',
            'fake' => true,
            'store_in' => 'extras',
            'tab' => 'Zagorodna'
        ]);

        $this->crud->addField([
                    'name' => 'realexpo_h1',
                    'label' => 'Заголовок h1',
                    'fake' => true,
                    'store_in' => 'extras',
                    'tab' => 'РеалЭкспо'
        ]);

        $this->crud->addField([
                    'name' => 'realexpo_meta_title',
                    'label' => 'Meta title',
                    'fake' => true,
                    'store_in' => 'extras',
                    'tab' => 'РеалЭкспо'
        ]);

        $this->crud->addField([
                    'name' => 'realexpo_meta_desc',
                    'label' => 'Meta description',
                    'fake' => true,
                    'store_in' => 'extras',
                    'tab' => 'РеалЭкспо'
        ]);

        $this->crud->addField([
                    'name' => 'realexpo_seo_title',
                    'label' => 'Seo-заголовок',
                    'fake' => true,
                    'store_in' => 'extras',
                    'tab' => 'РеалЭкспо'
        ]);

        $this->crud->addField([
                    'name' => 'realexpo_seo_text',
                    'label' => 'Seo-текст',
                    'type' => 'ckeditor',
                    'fake' => true,
                    'store_in' => 'extras',
                    'tab' => 'РеалЭкспо'
        ]);

        $this->crud->addField([
            'name' => 'brand_h1',
            'label' => 'Заголовок h1',
            'fake' => true,
            'store_in' => 'extras',
            'tab' => 'Компании'
        ]);

        $this->crud->addField([
                    'name' => 'brand_meta_title',
                    'label' => 'Meta title',
                    'fake' => true,
                    'store_in' => 'extras',
                    'tab' => 'Компании'
        ]);

        $this->crud->addField([
                    'name' => 'brand_meta_desc',
                    'label' => 'Meta description',
                    'fake' => true,
                    'store_in' => 'extras',
                    'tab' => 'Компании'
        ]);

        $this->crud->addField([
                    'name' => 'brand_seo_title',
                    'label' => 'Seo-заголовок',
                    'fake' => true,
                    'store_in' => 'extras',
                    'tab' => 'Компании'
        ]);

        $this->crud->addField([
                    'name' => 'brand_seo_text',
                    'label' => 'Seo-текст',
                    'type' => 'ckeditor',
                    'fake' => true,
                    'store_in' => 'extras',
                    'tab' => 'Компании'
        ]);

        $this->crud->addField([
            'name' => 'cottage_h1',
            'label' => 'Заголовок h1',
            'fake' => true,
            'store_in' => 'extras',
            'tab' => 'Коттеджи'
        ]);

        $this->crud->addField([
                    'name' => 'cottage_meta_title',
                    'label' => 'Meta title',
                    'fake' => true,
                    'store_in' => 'extras',
                    'tab' => 'Коттеджи'
        ]);

        $this->crud->addField([
                    'name' => 'cottage_meta_desc',
                    'label' => 'Meta description',
                    'fake' => true,
                    'store_in' => 'extras',
                    'tab' => 'Коттеджи'
        ]);

        $this->crud->addField([
                    'name' => 'cottage_seo_title',
                    'label' => 'Seo-заголовок',
                    'fake' => true,
                    'store_in' => 'extras',
                    'tab' => 'Коттеджи'
        ]);

        $this->crud->addField([
                    'name' => 'cottage_seo_text',
                    'label' => 'Seo-текст',
                    'type' => 'ckeditor',
                    'fake' => true,
                    'store_in' => 'extras',
                    'tab' => 'Коттеджи'
        ]);

        $this->crud->addField([
            'name' => 'newbuild_h1',
            'label' => 'Заголовок h1',
            'fake' => true,
            'store_in' => 'extras',
            'tab' => 'Новостройки'
        ]);

        $this->crud->addField([
                    'name' => 'newbuild_meta_title',
                    'label' => 'Meta title',
                    'fake' => true,
                    'store_in' => 'extras',
                    'tab' => 'Новостройки'
        ]);

        $this->crud->addField([
                    'name' => 'newbuild_meta_desc',
                    'label' => 'Meta description',
                    'fake' => true,
                    'store_in' => 'extras',
                    'tab' => 'Новостройки'
        ]);

        $this->crud->addField([
                    'name' => 'newbuild_seo_title',
                    'label' => 'Seo-заголовок',
                    'fake' => true,
                    'store_in' => 'extras',
                    'tab' => 'Новостройки'
        ]);

        $this->crud->addField([
                    'name' => 'newbuild_seo_text',
                    'label' => 'Seo-текст',
                    'type' => 'ckeditor',
                    'fake' => true,
                    'store_in' => 'extras',
                    'tab' => 'Новостройки'
        ]);
    }

    private function promotions()
    {
        $this->languages = Language::getActiveLanguagesNames();
        
        if($this->crud->getCurrentEntry()) {
            $this->crud->addField([
                      'name' => 'language_abbr',
                      'label' => 'Language',
                      'attributes' => [
                          'readonly' => 'radonly'
                      ]
                    ]);
        } else {
            $this->crud->addField([
                'name' => 'language_abbr',
                'label' => 'Language',
                'type' => 'select2_from_array',
                'options' => $this->languages,
              ]);
        }
        
        $this->crud->addField([
                        'name' => 'main_title',
                        'label' => 'Заголовок',
                        'fake' => true,
                        'store_in' => 'extras',
        ]);

        $this->crud->addField([
                        'name' => 'meta_title',
                        'label' => 'Meta title',
                        'fake' => true,
                        'store_in' => 'extras',
        ]);

        $this->crud->addField([
                        'name' => 'meta_desc',
                        'label' => 'Meta description',
                        'fake' => true,
                        'store_in' => 'extras',
        ]);

        $this->crud->addField([
                        'name' => 'seo_title',
                        'label' => 'Seo-заголовок',
                        'fake' => true,
                        'store_in' => 'extras',
        ]);

        $this->crud->addField([
                        'name' => 'seo_text',
                        'label' => 'Seo-текст',
                        'type' => 'ckeditor',
                        'fake' => true,
                        'store_in' => 'extras',
        ]);

        foreach(['cottage' => 'Коттеджные городки', 'newbuild' => 'Новостройки'] as $type => $title) {
            $this->crud->addField([   // CustomHTML
                        'name' => $type . '_separator',
                        'type' => 'custom_html',
                        'value' => "<br><h2>$title</h2><hr>",
            ]);

            $this->crud->addField([
                            'name' => $type . '_slug',
                            'label' => 'Slug',
                            'fake' => true,
                            'store_in' => 'extras',
            ]);

            $this->crud->addField([
                            'name' => $type . '_main_title',
                            'label' => 'Заголовок',
                            'fake' => true,
                            'store_in' => 'extras',
            ]);

            $this->crud->addField([
                            'name' => $type . '_meta_title',
                            'label' => 'Meta title',
                            'fake' => true,
                            'store_in' => 'extras',
            ]);

            $this->crud->addField([
                            'name' => $type . '_meta_desc',
                            'label' => 'Meta description',
                            'fake' => true,
                            'store_in' => 'extras',
            ]);

            $this->crud->addField([
                            'name' => $type . '_seo_title',
                            'label' => 'Seo-заголовок',
                            'fake' => true,
                            'store_in' => 'extras',
            ]);

            $this->crud->addField([
                            'name' => $type . '_seo_text',
                            'label' => 'Seo-текст',
                            'type' => 'ckeditor',
                            'fake' => true,
                            'store_in' => 'extras',
            ]);
        }
    }

    private function catalog()
    {
        $this->languages = Language::getActiveLanguagesNames();
        
        if($this->crud->getCurrentEntry()) {
            $this->crud->addField([
                      'name' => 'language_abbr',
                      'label' => 'Language',
                      'attributes' => [
                          'readonly' => 'radonly'
                      ]
                    ]);
        } else {
            $this->crud->addField([
                'name' => 'language_abbr',
                'label' => 'Language',
                'type' => 'select2_from_array',
                'options' => $this->languages,
              ]);
        }

        $this->crud->addField([   // CustomHTML
                'name' => 'content_separator',
                'type' => 'custom_html',
                'value' => '<br><h2>Коттеджные городки и поселки</h2><hr>',
        ]);

        $this->crud->addField([
                    'name' => 'cottages_title',
                    'label' => 'Заголовок',
                    'fake' => true,
                    'store_in' => 'extras',
        ]);

        $this->crud->addField([
                        'name' => 'cottages_meta_title',
                        'label' => 'Meta title',
                        'fake' => true,
                        'store_in' => 'extras',
        ]);

        $this->crud->addField([
                        'name' => 'cottages_meta_desc',
                        'label' => 'Meta description',
                        'fake' => true,
                        'store_in' => 'extras',
        ]);

        $this->crud->addField([
            'name' => 'cottages_seo_title',
            'label' => 'Seo-заголовок',
            'fake' => true,
            'store_in' => 'extras',
        ]);

        $this->crud->addField([
            'name' => 'cottages_seo_text',
            'label' => 'Seo-текст',
            'type' => 'ckeditor',
            'fake' => true,
            'store_in' => 'extras',
        ]);

        $this->crud->addField([   // CustomHTML
            'name' => 'content_separator_2',
            'type' => 'custom_html',
            'value' => '<br><h2>Новостройки</h2><hr>',
        ]);

        $this->crud->addField([
                    'name' => 'newbuilds_title',
                    'label' => 'Заголовок',
                    'fake' => true,
                    'store_in' => 'extras',
        ]);

        $this->crud->addField([
                    'name' => 'newbuilds_meta_title',
                    'label' => 'Meta title',
                    'fake' => true,
                    'store_in' => 'extras',
        ]);

        $this->crud->addField([
                    'name' => 'newbuilds_meta_desc',
                    'label' => 'Meta description',
                    'fake' => true,
                    'store_in' => 'extras',
        ]);

        $this->crud->addField([
            'name' => 'newbuilds_seo_title',
            'label' => 'Seo-заголовок',
            'fake' => true,
            'store_in' => 'extras',
        ]);

        $this->crud->addField([
            'name' => 'newbuilds_seo_text',
            'label' => 'Seo-текст',
            'type' => 'ckeditor',
            'fake' => true,
            'store_in' => 'extras',
        ]);
    }

    private function precatalog()
    {
        $this->languages = Language::getActiveLanguagesNames();
        
        if($this->crud->getCurrentEntry()) {
            $this->crud->addField([
                      'name' => 'language_abbr',
                      'label' => 'Language',
                      'attributes' => [
                          'readonly' => 'radonly'
                      ]
                    ]);
        } else {
            $this->crud->addField([
                'name' => 'language_abbr',
                'label' => 'Language',
                'type' => 'select2_from_array',
                'options' => $this->languages,
              ]);
        }

        $this->crud->addField([   // CustomHTML
            'name' => 'hint',
            'type' => 'custom_html',
            'value' => '<br><p>Переменные для вставки:</p>
            <p>{region} / {region_genitive} - область (Киевская/Киевской) / район (Шевченковский/Шевченковского) / нас. пункт (Киев/Киева)</p>
            <p>{type} / {type_genitive} / {type_plural} / {type_plural_genitive} - тип (таунхаус/таунхауса/таунхаусы/таунхаусов)</p>
            <p>{status_plural} - статус (строящиеся)</p>
            <p>В случае отсутствия значения переменная {status} будет проигнорирована, {region} / {region_genitive} = "Украина" / "Украины", {type} = "коттеджный поселок"/"новостройка"</p>
            ',
        ]);

        $this->crud->addField([ 
            'name' => 'cottage_h1',
            'label' => 'Заголовок h1 (шаблон)',
            'fake' => true,
            'store_in' => 'extras',
            'tab' => 'Коттеджные поселки'
        ]);

        $this->crud->addField([ 
            'name' => 'cottage_meta_title',
            'label' => 'Meta title (шаблон)',
            'fake' => true,
            'store_in' => 'extras',
            'tab' => 'Коттеджные поселки'
        ]);

        $this->crud->addField([
            'name' => 'cottage_meta_desc',
            'label' => 'Meta description (шаблон)',
            'fake' => true,
            'store_in' => 'extras',
            'tab' => 'Коттеджные поселки'
        ]);

        $this->crud->addField([
            'name' => 'cottage_seo_title',
            'label' => 'Seo-заголовок (шаблон)',
            'fake' => true,
            'store_in' => 'extras',
            'tab' => 'Коттеджные поселки'
        ]);

        $this->crud->addField([
            'name' => 'cottage_seo_text',
            'label' => 'Seo-текст  (шаблон)',
            'type' => 'ckeditor',
            'fake' => true,
            'store_in' => 'extras',
            'tab' => 'Коттеджные поселки'
        ]);

        $this->crud->addField([   // CustomHTML
            'name' => 'content_separator',
            'type' => 'custom_html',
            'value' => '<br><h2>Коттеджи по типу</h2><hr>',
            'tab' => 'Коттеджные поселки'
        ]);

        $this->crud->addField([
            'name' => 'cottage_types_text',
            'label' => 'Текст',
            'type' => 'ckeditor',
            'fake' => true,
            'store_in' => 'extras',
            'tab' => 'Коттеджные поселки'
        ]);

        $this->crud->addField([   // CustomHTML
            'name' => 'content_separator_11',
            'type' => 'custom_html',
            'value' => '<br><h2>Компании</h2><hr>',
            'tab' => 'Коттеджные поселки'
        ]);

        $this->crud->addField([
            'name' => 'cottage_companies_text',
            'label' => 'Текст',
            'type' => 'ckeditor',
            'fake' => true,
            'store_in' => 'extras',
            'tab' => 'Коттеджные поселки'
        ]);

        $this->crud->addField([   // CustomHTML
            'name' => 'content_separator_2',
            'type' => 'custom_html',
            'value' => '<h3>Изображения</h3>',
            'tab' => 'Коттеджные поселки'
        ]);

        foreach(__('attributes.cottage_types') as $key => $type) {
                $this->crud->addField([
                            'name' => 'cottage_types_' . $key,
                            'label' => $type,
                            'type' => 'browse',
                            'upload' => 'true',
                            'fake' => true,
                            'store_in' => 'extras',
                            'tab' => 'Коттеджные поселки'
                ]);
        }

        $this->crud->addField([ 
            'name' => 'newbuild_h1',
            'label' => 'Заголовок h1 (шаблон)',
            'fake' => true,
            'store_in' => 'extras',
            'tab' => 'Новостройки'
        ]);

        $this->crud->addField([ 
            'name' => 'newbuild_meta_title',
            'label' => 'Meta title (шаблон)',
            'fake' => true,
            'store_in' => 'extras',
            'tab' => 'Новостройки'
        ]);

        $this->crud->addField([
            'name' => 'newbuild_meta_desc',
            'label' => 'Meta description (шаблон)',
            'fake' => true,
            'store_in' => 'extras',
            'tab' => 'Новостройки'
        ]);

        $this->crud->addField([
            'name' => 'newbuild_seo_title',
            'label' => 'Seo-заголовок  (шаблон)',
            'fake' => true,
            'store_in' => 'extras',
            'tab' => 'Новостройки'
        ]);

        $this->crud->addField([
            'name' => 'newbuild_seo_text',
            'label' => 'Seo-текст  (шаблон)',
            'type' => 'ckeditor',
            'fake' => true,
            'store_in' => 'extras',
            'tab' => 'Новостройки'
        ]);

        $this->crud->addField([   // CustomHTML
            'name' => 'content_separator_5',
            'type' => 'custom_html',
            'value' => '<br><h2>Новостройки по типу</h2><hr>',
            'tab' => 'Новостройки'
        ]);

        $this->crud->addField([
            'name' => 'newbuild_types_text',
            'label' => 'Текст',
            'type' => 'ckeditor',
            'fake' => true,
            'store_in' => 'extras',
            'tab' => 'Новостройки'
        ]);

        $this->crud->addField([   // CustomHTML
            'name' => 'content_separator_10',
            'type' => 'custom_html',
            'value' => '<br><h2>Компании</h2><hr>',
            'tab' => 'Новостройки'
        ]);

        $this->crud->addField([
            'name' => 'newbuild_companies_text',
            'label' => 'Текст',
            'type' => 'ckeditor',
            'fake' => true,
            'store_in' => 'extras',
            'tab' => 'Новостройки'
        ]);

        $this->crud->addField([   // CustomHTML
            'name' => 'content_separator_2',
            'type' => 'custom_html',
            'value' => '<h3>Изображения</h3>',
            'tab' => 'Новостройки'
        ]);

        foreach(__('attributes.newbuild_types') as $key => $type) {
            $this->crud->addField([
                        'name' => 'newbuild_types_' . $key,
                        'label' => $type,
                        'type' => 'browse',
                        'upload' => 'true',
                        'fake' => true,
                        'store_in' => 'extras',
                        'tab' => 'Новостройки'
            ]);
        }
    }

    private function policy()
    {
        $this->languages = Language::getActiveLanguagesNames();
        
        if($this->crud->getCurrentEntry()) {
            $this->crud->addField([
                      'name' => 'language_abbr',
                      'label' => 'Language',
                      'attributes' => [
                          'readonly' => 'radonly'
                      ]
                    ]);
        } else {
            $this->crud->addField([
                'name' => 'language_abbr',
                'label' => 'Language',
                'type' => 'select2_from_array',
                'options' => $this->languages,
              ]);
        }
        
        $this->crud->addField([
                        'name' => 'main_title',
                        'label' => 'Заголовок',
                        'fake' => true,
                        'store_in' => 'extras',
        ]);

        $this->crud->addField([
                        'name' => 'meta_title',
                        'label' => 'Meta title',
                        'fake' => true,
                        'store_in' => 'extras',
        ]);

        $this->crud->addField([
                        'name' => 'meta_desc',
                        'label' => 'Meta description',
                        'fake' => true,
                        'store_in' => 'extras',
        ]);

        $this->crud->addField([
            'name' => 'content',
            'label' => 'Контент',
            'type' => 'ckeditor',
            'fake' => true,
            'store_in' => 'extras',
        ]);
    }

    private function cookies()
    {
        $this->languages = Language::getActiveLanguagesNames();
        
        if($this->crud->getCurrentEntry()) {
            $this->crud->addField([
                      'name' => 'language_abbr',
                      'label' => 'Language',
                      'attributes' => [
                          'readonly' => 'radonly'
                      ]
                    ]);
        } else {
            $this->crud->addField([
                'name' => 'language_abbr',
                'label' => 'Language',
                'type' => 'select2_from_array',
                'options' => $this->languages,
              ]);
        }
        
        $this->crud->addField([
                        'name' => 'main_title',
                        'label' => 'Заголовок',
                        'fake' => true,
                        'store_in' => 'extras',
        ]);

        $this->crud->addField([
                        'name' => 'meta_title',
                        'label' => 'Meta title',
                        'fake' => true,
                        'store_in' => 'extras',
        ]);

        $this->crud->addField([
                        'name' => 'meta_desc',
                        'label' => 'Meta description',
                        'fake' => true,
                        'store_in' => 'extras',
        ]);

        $this->crud->addField([
            'name' => 'content',
            'label' => 'Контент',
            'type' => 'ckeditor',
            'fake' => true,
            'store_in' => 'extras',
        ]);
    }


    private function about()
    {
        $this->languages = Language::getActiveLanguagesNames();
        
        if($this->crud->getCurrentEntry()) {
            $this->crud->addField([
                      'name' => 'language_abbr',
                      'label' => 'Language',
                      'attributes' => [
                          'readonly' => 'radonly'
                      ]
                    ]);
        } else {
            $this->crud->addField([
                'name' => 'language_abbr',
                'label' => 'Language',
                'type' => 'select2_from_array',
                'options' => $this->languages,
              ]);
        }
        
        $this->crud->addField([
                        'name' => 'main_title',
                        'label' => 'Заголовок',
                        'fake' => true,
                        'store_in' => 'extras',
        ]);

        $this->crud->addField([
                        'name' => 'meta_title',
                        'label' => 'Meta title',
                        'fake' => true,
                        'store_in' => 'extras',
        ]);

        $this->crud->addField([
                        'name' => 'meta_desc',
                        'label' => 'Meta description',
                        'fake' => true,
                        'store_in' => 'extras',
        ]);

        $this->crud->addField([
            'name' => 'content',
            'label' => 'Контент',
            'type' => 'ckeditor',
            'fake' => true,
            'store_in' => 'extras',
        ]);
    }

    private function precatalog_statistics()
    {
        $this->languages = Language::getActiveLanguagesNames();
        
        if($this->crud->getCurrentEntry()) {
            $this->crud->addField([
                      'name' => 'language_abbr',
                      'label' => 'Language',
                      'attributes' => [
                          'readonly' => 'radonly'
                      ]
                    ]);
        } else {
            $this->crud->addField([
                'name' => 'language_abbr',
                'label' => 'Language',
                'type' => 'select2_from_array',
                'options' => $this->languages,
              ]);
        }
        
        $this->crud->addField([
                        'name' => 'cottage_title',
                        'label' => 'Заголовок',
                        'fake' => true,
                        'store_in' => 'extras',
                        'tab' => 'Коттеджные поселки'
        ]);

        $this->crud->addField([
                        'name' => 'cottage_meta_title',
                        'label' => 'Meta title',
                        'fake' => true,
                        'store_in' => 'extras',
                        'tab' => 'Коттеджные поселки'
        ]);

        $this->crud->addField([
                        'name' => 'cottage_meta_desc',
                        'label' => 'Meta description',
                        'fake' => true,
                        'store_in' => 'extras',
                        'tab' => 'Коттеджные поселки'
        ]);

        $this->crud->addField([
                    'name' => 'cottage_seo_text',
                    'label' => 'Seo-текст',
                    'type' => 'ckeditor',
                    'fake' => true,
                    'store_in' => 'extras',
                    'tab' => 'Коттеджные поселки'
        ]);
        $this->crud->addField([
                    'name' => 'newbuild_title',
                    'label' => 'Заголовок',
                    'fake' => true,
                    'store_in' => 'extras',
                    'tab' => 'Новостройки'
        ]);

        $this->crud->addField([
                    'name' => 'newbuild_meta_title',
                    'label' => 'Meta title',
                    'fake' => true,
                    'store_in' => 'extras',
                    'tab' => 'Новостройки'
        ]);

        $this->crud->addField([
                    'name' => 'newbuild_meta_desc',
                    'label' => 'Meta description',
                    'fake' => true,
                    'store_in' => 'extras',
                    'tab' => 'Новостройки'
        ]);

        $this->crud->addField([
                'name' => 'newbuild_seo_text',
                'label' => 'Seo-текст',
                'type' => 'ckeditor',
                'fake' => true,
                'store_in' => 'extras',
                'tab' => 'Новостройки'
        ]);
    }

    private function contacts()
    {
        $this->languages = Language::getActiveLanguagesNames();
        
        if($this->crud->getCurrentEntry()) {
            $this->crud->addField([
                      'name' => 'language_abbr',
                      'label' => 'Language',
                      'attributes' => [
                          'readonly' => 'radonly'
                      ]
                    ]);
        } else {
            $this->crud->addField([
                'name' => 'language_abbr',
                'label' => 'Language',
                'type' => 'select2_from_array',
                'options' => $this->languages,
              ]);
        }

        $this->crud->addField([
                    'name' => 'main_title',
                    'label' => 'Заголовок',
                    'fake' => true,
                    'store_in' => 'extras',
        ]);

        $this->crud->addField([
                        'name' => 'meta_title',
                        'label' => 'Meta title',
                        'fake' => true,
                        'store_in' => 'extras',
        ]);

        $this->crud->addField([
                        'name' => 'meta_desc',
                        'label' => 'Meta description',
                        'fake' => true,
                        'store_in' => 'extras',
        ]);

        $this->crud->addField([
                    'name' => 'seo_text',
                    'label' => 'Seo-текст',
                    'type' => 'ckeditor',
                    'fake' => true,
                    'store_in' => 'extras',
        ]);
    }

    private function tags()
    {
        $this->languages = Language::getActiveLanguagesNames();
        
        if($this->crud->getCurrentEntry()) {
            $this->crud->addField([
                      'name' => 'language_abbr',
                      'label' => 'Language',
                      'attributes' => [
                          'readonly' => 'radonly'
                      ]
                    ]);
        } else {
            $this->crud->addField([
                'name' => 'language_abbr',
                'label' => 'Language',
                'type' => 'select2_from_array',
                'options' => $this->languages,
              ]);
        }

        $this->crud->addField([
                        'name' => 'meta_title',
                        'label' => 'Meta title',
                        'fake' => true,
                        'store_in' => 'extras',
        ]);

        $this->crud->addField([
                        'name' => 'meta_desc',
                        'label' => 'Meta description',
                        'fake' => true,
                        'store_in' => 'extras',
        ]);

        $this->crud->addField([
                    'name' => 'seo_text',
                    'label' => 'Seo-текст',
                    'type' => 'ckeditor',
                    'fake' => true,
                    'store_in' => 'extras',
        ]);
    }

    private function map()
    {
        $this->languages = Language::getActiveLanguagesNames();
        
        if($this->crud->getCurrentEntry()) {
            $this->crud->addField([
                      'name' => 'language_abbr',
                      'label' => 'Language',
                      'attributes' => [
                          'readonly' => 'radonly'
                      ]
                    ]);
        } else {
            $this->crud->addField([
                'name' => 'language_abbr',
                'label' => 'Language',
                'type' => 'select2_from_array',
                'options' => $this->languages,
              ]);
        }

        $this->crud->addField([   // CustomHTML
            'name' => 'hint',
            'type' => 'custom_html',
            'value' => '<br><p>Переменные для вставки:</p>
            <p>{region} / {region_genitive} - область (Киевская/Киевской) / район (Шевченковский/Шевченковского) / нас. пункт (Киев/Киева)</p>
            <p>{type} / {type_genitive} / {type_plural} / {type_plural_genitive} - тип (таунхаус/таунхауса/таунхаусы/таунхаусов)</p>
            <p>{status_plural} - статус (строящиеся)</p>
            <p>В случае отсутствия значения переменная {status} будет проигнорирована, {region} / {region_genitive} = "Украина" / "Украины", {type} = "коттеджный поселок"/"новостройка"</p>
            ',
        ]);

        $this->crud->addField([ 
            'name' => 'cottage_h1',
            'label' => 'Заголовок h1 (шаблон)',
            'fake' => true,
            'store_in' => 'extras',
            'tab' => 'Коттеджные поселки'
        ]);

        $this->crud->addField([ 
            'name' => 'cottage_meta_title',
            'label' => 'Meta title (шаблон)',
            'fake' => true,
            'store_in' => 'extras',
            'tab' => 'Коттеджные поселки'
        ]);

        $this->crud->addField([
            'name' => 'cottage_meta_desc',
            'label' => 'Meta description (шаблон)',
            'fake' => true,
            'store_in' => 'extras',
            'tab' => 'Коттеджные поселки'
        ]);

        $this->crud->addField([
            'name' => 'cottage_seo_title',
            'label' => 'Seo-заголовок (шаблон)',
            'fake' => true,
            'store_in' => 'extras',
            'tab' => 'Коттеджные поселки'
        ]);

        $this->crud->addField([
            'name' => 'cottage_seo_text',
            'label' => 'Seo-текст  (шаблон)',
            'type' => 'ckeditor',
            'fake' => true,
            'store_in' => 'extras',
            'tab' => 'Коттеджные поселки'
        ]);

        $this->crud->addField([ 
            'name' => 'newbuild_h1',
            'label' => 'Заголовок h1 (шаблон)',
            'fake' => true,
            'store_in' => 'extras',
            'tab' => 'Новостройки'
        ]);

        $this->crud->addField([ 
            'name' => 'newbuild_meta_title',
            'label' => 'Meta title (шаблон)',
            'fake' => true,
            'store_in' => 'extras',
            'tab' => 'Новостройки'
        ]);

        $this->crud->addField([
            'name' => 'newbuild_meta_desc',
            'label' => 'Meta description (шаблон)',
            'fake' => true,
            'store_in' => 'extras',
            'tab' => 'Новостройки'
        ]);

        $this->crud->addField([
            'name' => 'newbuild_seo_title',
            'label' => 'Seo-заголовок  (шаблон)',
            'fake' => true,
            'store_in' => 'extras',
            'tab' => 'Новостройки'
        ]);

        $this->crud->addField([
            'name' => 'newbuild_seo_text',
            'label' => 'Seo-текст  (шаблон)',
            'type' => 'ckeditor',
            'fake' => true,
            'store_in' => 'extras',
            'tab' => 'Новостройки'
        ]);
    }

    private function favorite()
    {
        $this->languages = Language::getActiveLanguagesNames();
        
        if($this->crud->getCurrentEntry()) {
            $this->crud->addField([
                      'name' => 'language_abbr',
                      'label' => 'Language',
                      'attributes' => [
                          'readonly' => 'radonly'
                      ]
                    ]);
        } else {
            $this->crud->addField([
                'name' => 'language_abbr',
                'label' => 'Language',
                'type' => 'select2_from_array',
                'options' => $this->languages,
              ]);
        }

        $this->crud->addField([
                        'name' => 'main_title',
                        'label' => 'Заголовок',
                        'fake' => true,
                        'store_in' => 'extras',
        ]);

        $this->crud->addField([
                        'name' => 'meta_title',
                        'label' => 'Meta title',
                        'fake' => true,
                        'store_in' => 'extras',
        ]);

        $this->crud->addField([
                        'name' => 'meta_desc',
                        'label' => 'Meta description',
                        'fake' => true,
                        'store_in' => 'extras',
        ]);
    }

    private function company()
    {
        $this->languages = Language::getActiveLanguagesNames();
        
        if($this->crud->getCurrentEntry()) {
            $this->crud->addField([
                      'name' => 'language_abbr',
                      'label' => 'Language',
                      'attributes' => [
                          'readonly' => 'radonly'
                      ]
                    ]);
        } else {
            $this->crud->addField([
                'name' => 'language_abbr',
                'label' => 'Language',
                'type' => 'select2_from_array',
                'options' => $this->languages,
              ]);
        }

        $this->crud->addField([   // CustomHTML
            'name' => 'hint',
            'type' => 'custom_html',
            'value' => '<br><p>Переменные для вставки:</p>
            <p>{name} - название компании</p>
            <p>{category} - название категории</p>
            ',
        ]);

        $tabs = [
            'main' => 'О компании',
            'map' => 'Карта',
            'video' => 'Видео',
            'reviews' => 'Отзывы',
            'promotions' => 'Акции',
        ];

        foreach($tabs as $tab => $title) {
            $this->crud->addField([
                            'name' => $tab . '_main_title',
                            'label' => 'Заголовок',
                            'fake' => true,
                            'store_in' => 'extras',
                            'tab' => $title
            ]);

            $this->crud->addField([
                            'name' => $tab . '_meta_title',
                            'label' => 'Meta title',
                            'fake' => true,
                            'store_in' => 'extras',
                            'tab' => $title
            ]);

            $this->crud->addField([
                            'name' => $tab . '_meta_desc',
                            'label' => 'Meta description',
                            'fake' => true,
                            'store_in' => 'extras',
                            'tab' => $title
            ]);
        }


    }
}