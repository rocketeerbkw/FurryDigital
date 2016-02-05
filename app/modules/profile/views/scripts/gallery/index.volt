{% set title=owner.username|e~"'s Gallery" %}
{% if scraps_mode %}
    {% set usernav_tab = 'scraps' %}
{% endif %}

{%- macro thumbnails(pager) %}
    <div class="pagination">
        {{ paginate(pager) }}
    </div>

    <div class="grid">
        <div class="grid-sizer"></div>
        {% for row in pager %}
            <div id="sid_{{ row['id'] }}" class="grid-item r-{{ row.getRatingReadable() }}">
                <a class="image" href="{{ url.named('upload_view', ['id': row['id']]) }}">
                    <img alt="" src="{{ row['thumbnail_url'] }}">
                </a>
            </div>
        {% endfor %}
    </div>

    <div class="pagination">
        {{ paginate(pager) }}
    </div>
{%- endmacro %}

{% include('usernav') %}

{% if folder_list %}
    <div class="row">
        <div class="col-md-2">
            <div class="folder-list">
                <div class="user-folders">
                    <h4>Gallery Folders</h4>
                    <div class="default-folders">
                        <ul>
                            {% if folder %}
                                <li><a href="{{ url.named('user_gallery', ['username': owner.lower]) }}" class="dotted">Main Gallery</a></li>
                                <li><a href="{{ url.named('user_scraps', ['username': owner.lower]) }}" class="dotted">Scraps</a></li>
                            {% elseif scraps_mode %}
                                <li><a href="{{ url.named('user_gallery', ['username': owner.lower]) }}" class="dotted">Main Gallery</a></li>
                                <li class="active">&#x276f;&#x276f; <strong>Scraps</strong></li>
                            {% else %}
                                <li class="active">&#x276f;&#x276f; <strong>Main Gallery</strong></li>
                                <li><a href="{{ url.named('user_scraps', ['username': owner.lower]) }}" class="dotted">Scraps</a></li>
                            {% endif %}
                        </ul>
                    </div>

                    {% for group_name, group_folders in folder_list %}
                        <h5>{{ group_name }}</h5>
                        <ul class="default-group">
                            {% for folder_id, folder_name in group_folders %}
                                {% if folder_id == folder.id %}
                                    <li class="active">
                                        &#x276f;&#x276f; <strong>{{ folder_name }}</strong>
                                    </li>
                                {% else %}
                                    <li>
                                        <a href="{{ url.named('user_gallery_folder', ['username': owner.lower, 'folder': folder_id]) }}" class="dotted">{{ folder_name }}</a>
                                    </li>
                                {% endif %}
                            {% endfor %}
                        </ul>
                    {% endfor %}
                </div>
            </div>
        </div>
        <div class="col-md-10">
            {% if folder %}
                <div class="folder-description">
                    <h4>{{ folder.name }}</h4>
                    {{ folder.description }}
                </div>
                <hr>
            {% endif %}

            {{ thumbnails(pager) }}
        </div>
    </div>
{% else %}
    {{ thumbnails(pager) }}
{% endif %}

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