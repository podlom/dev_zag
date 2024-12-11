<?=
/* Using an echo tag here so the `<? ... ?>` won't get parsed as short tags */
'<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL
?>
@php

@endphp
<rss version="2.0">
    <channel>
        <title><![CDATA[{{ $meta['title'] }}]]></title>
        <link><![CDATA[{{ $meta['language'] === 'uk-UA'? url('feed?lang=uk') : url('feed') }}]]></link>
        <description><![CDATA[{{ $meta['description'] }}]]></description>
        <language>{{ $meta['language'] }}</language>
        <pubDate>{{ $meta['updated'] }}</pubDate>

        @foreach($items as $item)
            <item>
                <title><![CDATA[{{ $item->title }}]]></title>
                <link>{{ url($item->link) }}</link>
                <enclosure url="{{ $item->image }}" type="image/jpeg"/>
                <description><![CDATA[{!! $item->summary !!}]]></description>
                <full-text><![CDATA[{!! $item->theContent !!}]]></full-text>
                <author><![CDATA[{{ $item->author }}]]></author>
                <guid isPermaLink="false">{{ $item->id }}</guid>
                <pubDate>{{ $item->updated->toRssString() }}</pubDate>
                @foreach($item->category as $category)
                    <category>{{ $category }}</category>
                @endforeach
            </item>
        @endforeach
    </channel>
</rss>
