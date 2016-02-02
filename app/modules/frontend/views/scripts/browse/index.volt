{% set title='Browse' %}

<div id="columnpage" class="page-browse">
    <div class="onevisible">
        <div class="onecontent">

            <div class="search-flex-container p10t">
                <div class="search-flex-item-holder">
                    <h3 class="search-flex-item-title">Browse</h3>

                    {{ form.render() }}
                </div>
            </div>

        </div>
    </div>

    <div class="two">
        <div class="twocontent">

            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="floatright">
                        {{ paginate(pager) }}
                    </div>
                    <h3 class="panel-title">Page {{ page_current }} of {{ page_count }}</h3>
                </div>
                <div class="panel-body">
                    <center class="flow browse with-titles-usernames thumb-size-{{ thumbnail_size }}">
                        {% for row in pager %}
                            <b id="sid_{{ row['id'] }}" class="r-{{ row.getRatingReadable() }} t-{{ row.getUploadTypeName() }}"><u><s><a href="{{ url.named('upload_view', ['id': row['id']]) }}"><img alt="" src="{{ row.getThumbnailUrl() }}"><i class="icon" title="Click for description"></i></a></s></u><span title="{{ row.title }}">{{ row.title }}</span><small><a href="{{ url.named('user_view', ['username': row.user.lower]) }}">{{ row.user.username }}</a></small></b>
                        {% endfor %}
                    </center>
                </div>
                <div class="panel-footer">
                    <div class="floatright">
                        {{ paginate(pager) }}
                    </div>
                    <h3 class="panel-title">Page {{ page_current }} of {{ page_count }}</h3>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
jQuery(function($) {

    $('ul.pagination a').click(function(e) {
        e.preventDefault();

        var page_num = $(this).attr('rel');
        $('input[name="page"]').val(page_num);

        $('form#browse_filters').submit();
        return false;
    });

});
</script>

{#
<script type="text/javascript">
    var descriptions = <?=json_encode($image_data)?>;

    Queue.add(function(){
        // do stuff
        $$('center.flow > b i.icon').each(function(elm){
            elm.observe('click', description_icon_click);
        });
        $('toggle-descriptions').observe('click', gallery_toggle_descriptions);

        //
        $$('.page-browse select.listbox[name="cat"]', '.page-browse select.listbox[name="atype"]', '.page-browse select.listbox[name="species"]', '.page-browse select.listbox[name="gender"]').invoke('observe', 'change', function(evt) {
            var elm = $('manual-page');
            if(elm) {
                elm.value = 1;
            }
        });
    });
</script>
#}