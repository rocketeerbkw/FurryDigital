{% set title='Other Messages' %}

{% for notify_key, notify_info in notifications %}
<form method="post" action="{{ url.routeFromHere(['action': 'process']) }}">
    <input type="hidden" name="type" value="{{ notify_key }}">
    <input type="hidden" name="csrf" value="{{ csrf.generate('messages') }}">

    <div class="panel panel-default" id="{{ notify_key }}">
        <div class="panel-heading">
            <div class="floatright">
                <button class="btn btn-sm btn-danger" type="submit" name="do" value="remove_all">Remove All</button>
            </div>

            <h3 class="panel-title">{{ notify_info['title_plural'] }} {% if notify_info['count'] > 0 %}<span class="label label-default">{{ notify_info['count'] }}</span>{% endif %}</h3>
        </div>
        <div class="panel-body">
            {{ partial('messages/partial_'~notify_key) }}

            <div class="section-controls alignright">
                <button class="btn btn-sm btn-default mark-all" type="button">Select All</button>
                <button class="btn btn-sm btn-default mark-none" type="button">Deselect All</button>
                <button class="btn btn-sm btn-danger" type="submit" name="do" value="remove_selected">Remove Selected</button>
            </div>
        </div>
    </div>
</form>
{% endfor %}

<script type="text/javascript">
jQuery(function($) {
    $('button.mark-all,button.mark-none').on('click', function(e) {
        var checkboxes = $(this).closest('div.panel').find('input[type="checkbox"]');

        if ($(this).hasClass('mark-all'))
            checkboxes.prop('checked', 'checked');
        else
            checkboxes.prop('checked', false);

        return false;
    });
});
</script>