{% set title=owner.username|e~"'s Gallery" %}
{% if scraps_mode %}
    {% set usernav_tab = 'scraps' %}
{% endif %}

{% include('usernav') %}

<div id="page-galleryscraps">
    {#
    <div class="bg3 p20l aligncenter page-options">
        <form class="hrblock perpage_select" name="replyform" method="post" action="<?=$this_page_url?>">
            <?=$perpage_select?> submissions at a time.
            <noscript><input class="submitbutton type-edit" type="submit" name="go" value="Update"></noscript>
        </form>
        <input type="button" class="submitbutton hrblock" id="toggle-descriptions" value="Toggle Descriptions" />
    </div>
    #}

    <div class="gallery-content">
        {% if folder_list %}
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
        {% endif %}

        <div class="submission-list">
            {% if folder %}
            <div class="folder-description">
                <h4>{{ folder.name }}</h4>
                {{ folder.description }}
            </div>
            <hr>
            {% endif %}

            <div class="pagination">
                {{ paginate(pager) }}
            </div>

            <center class="flow gallery with-titles">
            {% for row in pager %}
                <b id="sid_{{ row.id }}" class="r-{{ row.getRatingReadable() }}"><u><s><a href="{{ url.named('upload_view', ['id': row.id]) }}"><img alt="" src="{{ row.getThumbnailUrl() }}"></a></s></u><span title="{{ row.title|e }}">{{ row.title|e }}</span></b>
            {% endfor %}
            </center>

            <div class="pagination">
                {{ paginate(pager) }}
            </div>
        </div>
    </div>
</div>

{#
<script type="text/javascript">
    var descriptions = <?=json_encode($image_data)?>;

    Queue.add(function(){
        // do stuff
        $$('#page-galleryscraps center.flow > b i.icon').each(function(elm){
            elm.observe('click', description_icon_click);
        });
        $('toggle-descriptions').observe('click', gallery_toggle_descriptions);

        // auto-submit perpage selects. hide the submit button
        $$('#page-galleryscraps .perpage_select select').invoke('observe', 'change', function(evt) {
            evt.findElement('form').submit();
        });
    });
</script>
#}