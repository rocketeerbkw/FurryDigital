{% set title=owner.username|e~"'s Favorites" %}

{% include('usernav') %}

{% if pager %}
    <div class="pagination">
        {{ paginate(pager) }}
    </div>

    <div class="grid">
        <div class="grid-sizer"></div>
        {% for fave in pager %}
            {% set row=fave.upload %}
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
{% endif %}

{#
<?if($image_data){?>
<script type="text/javascript">
    var descriptions = <?=json_encode($image_data)?>;

    Queue.add(function(){
        // do stuff
        $$('center.flow > b i.icon').each(function(elm){
            elm.observe('click', description_icon_click);
        });
        $('toggle-descriptions').observe('click', gallery_toggle_descriptions);
    });
</script>
<?}?>
#}