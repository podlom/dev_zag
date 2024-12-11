@if (count($breadcrumbs))
    <div class="breadcrumbs__list">
    @foreach ($breadcrumbs as $breadcrumb)

        @if ($breadcrumb->url && !$loop->last)
            <a href="{{ $breadcrumb->url }}" class="breadcrumbs__link">{{ $breadcrumb->title }}</a>
        @else
            <a href="#" class="breadcrumbs__link">{{ $breadcrumb->title }}</a>
        @endif
    
    @endforeach
    </div>
@endif
