@if(!$entry->original && $entry->translations->count() < count(\Backpack\LangFileManager\app\Models\Language::getActiveLanguagesNames()) - 1 && $crud->hasAccess('translate'))
<a href="{{ url($crud->route.'/'.$entry->getKey().'/translate') }}" class="btn btn-sm btn-link"><i class="fa fa-th-list" ></i> Создать перевод </a>
@endif