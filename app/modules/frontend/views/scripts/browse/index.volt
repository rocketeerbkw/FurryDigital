{% set title='Browse' %}

<div class="row">
    <div class="col-md-3 col-sm-3">
        <h3>Browse</h3>
        {{ form.render() }}
    </div>
    <div class="col-md-9 col-sm-9">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="floatright">
                    {{ paginate(pager) }}
                </div>
                <h3 class="panel-title">Page {{ page_current }} of {{ page_count }}</h3>
            </div>
            <div class="panel-body">
                <div class="grid">
                    <div class="grid-sizer"></div>
                    {% for row in pager %}
                        <div id="sid_{{ row['id'] }}" class="grid-item r-{{ row.getRatingReadable() }} t-{{ row.getUploadTypeName() }}">
                            <a class="image" href="{{ url.get('view/'~row['id']) }}">
                                <img alt="" src="{{ row['thumbnail_url'] }}">
                            </a>
                            <span class="title" title="{{ row.title }}">{{ row.title }}</span>
                            <span class="artist"><a href="{{ url.named('user_view', ['username': row.user.lower]) }}">{{ row.user.username }}</a></span>
                        </div>
                    {% endfor %}
                </div>

                {#
                <center class="flow browse with-titles-usernames thumb-size-{{ thumbnail_size }}">
                    {% for row in pager %}
                        <b><u><s><a href="{{ url.named('upload_view', ['id': row['id']]) }}"><img alt="" src="{{ row.getThumbnailUrl() }}"><i class="icon" title="Click for description"></i></a></s></u><span title="{{ row.title }}">{{ row.title }}</span><small><a href="{{ url.named('user_view', ['username': row.user.lower]) }}">{{ row.user.username }}</a></small></b>
                    {% endfor %}
                </center>
                #}
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