{% set title=owner.username|e~"'s Favorites" %}

{% include('usernav') %}

{% if pager %}
    {{ paginate(pager) }}

    <center class="flow gallery with-titles">
    {% for fave in pager %}
        {% set row=fave.upload %}
        <b id="sid_{{ row.id }}" class="r-{{ row.getRatingReadable() }}"><u><s><a href="{{ url.named('upload_view', ['id': row.id]) }}"><img alt="" src="{{ row.getThumbnailUrl() }}"></a></s></u><span title="{{ row.title|e }}">{{ row.title|e }}</span></b>
    {% endfor %}
    </center>

    {{ paginate(pager) }}
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