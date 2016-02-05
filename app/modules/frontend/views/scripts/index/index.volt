{%- macro thumbnails(rows) %}
    <div class="grid">
        <div class="grid-sizer"></div>
    {% for row in rows %}
        <div id="sid_{{ row['id'] }}" class="grid-item r-{{ row['rating_text'] }}">
            <a class="image" href="{{ url.named('upload_view', ['id': row['id']]) }}">
                <img alt="" src="{{ row['thumbnail_url'] }}">
            </a>
        </div>
    {% endfor %}
    </div>
{%- endmacro %}

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Recent Submissions</h3>
    </div>
    <div class="panel-body">
        {{ thumbnails(records['images']) }}
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Recent Writing &amp; Poetry</h3>
    </div>
    <div class="panel-body">
        {{ thumbnails(records['text']) }}
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Recent Music &amp; Audio</h3>
    </div>
    <div class="panel-body">
        {{ thumbnails(records['audio']) }}
    </div>
</div>

{{ javascript_include('//cdnjs.cloudflare.com/ajax/libs/masonry/4.0.0/masonry.pkgd.min.js') }}
<script type="text/javascript">
$(window).load(function(e) {
    $('div.grid').masonry({
        columnWidth: '.grid-sizer',
        itemSelector: '.grid-item',
        gutter: 10
    });
});
</script>