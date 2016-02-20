<div class="grid">
    <div class="grid-sizer"></div>
    {% for row in records %}
        <div id="sid_{{ row['id'] }}" class="grid-item r-{{ row['rating_text'] }}">
            <a class="image" href="{{ url.named('upload_view', ['id': row['id']]) }}">
                <img alt="" src="{{ row['thumbnail_url'] }}">
                <div class="image-type" title="{{ row['upload_type_name']|capitalize }}"><i class="{{ row['upload_type_icon'] }}"></i></div>
            </a>
        </div>
    {% endfor %}
</div>