{% extends "cpanel.volt" %}

{% block content %}
    {% set title='My Shouts' %}

    <form method="post" id="frm-shouts" action="{{ url.routeFromHere(['action': 'delete']) }}">

        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">{{ title }}</h3>
            </div>
            <div class="panel-body">
                <p>You can remove any shout on your user page. To remove a shout simply click the box next to the user's name then click the <strong>Remove Shouts</strong> button below.</p>

                <div class="buttons">
                    <button type="button" id="btn-toggle-select" class="btn btn-default">Select All</button>
                    <button type="submit" id="btn-delete" class="btn btn-danger">Delete Selected</button>
                </div>
            </div>
        </div>

    {% for row in shouts %}
        <table class="p10b" width="100%" id="shout-{{ row.id }}">
            <tr>
                <td valign="top" >
                    <div class="lineitem">
                        <div class="cell aligncenter valigntop" style="min-width:100px;width:100px;height:100%;padding-right:25px">
                            <a href="{{ url.get('user/'~row.sender.lower) }}"><img src="{{ row.sender.getAvatar() }}" alt="{{ row.sender.lower }}" /></a>
                        </div>

                        <div class="cell bg3 usercomment valigntop auto_link">
                            <div class="p5t p10l p10r">
                                <span class="fontcolor3 fontsize12 floatright">{{ fa.formatDate(row.created_at) }}</span>
                                <span class="replyto-name">
                                    <h3>
                                        <span class="p10r"><input type="checkbox" name="shouts[]" value="{{ row.id }}"></span><a class="orange" href="{{ url.get('user/'~row.sender.lower) }}"><strong>{{ row.sender.username }}</strong></a>
                                    </h3>
                                </span>
                                <div class="p5t floatnone"><hr></div>
                            </div>

                            <div class="p5t p10lr">
                                {{ parser.message(row.message) }}
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    {% endfor %}
    </form>

    <script type="text/javascript">
    jQuery(function($) {

        var all_shouts_selected = false;

        $('#btn-toggle-select').click(function(e) {
            e.preventDefault();

            if(all_shouts_selected) {
                all_shouts_selected = false;
                $('form#frm-shouts input[type=checkbox]').removeAttr('checked');
                $(this).attr('value', 'Select All');
            } else {
                all_shouts_selected = true;
                $('form#frm-shouts input[type=checkbox]').attr('checked', 'checked');
                $(this).attr('value', 'Unselect All');
            }
        });

    });
    </script>
{% endblock %}