{% extends "cpanel.volt" %}

{% block content %}
    {% set title='View Trouble Ticket' %}

    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="floatright">
                <a class="btn btn-primary" href="{{ url.routeFromHere(['action': 'index', 'id': null]) }}">Back to Ticket List</a>
            </div>

            <h3 class="panel-title">{% if ticket.is_resolved %}<span>[Closed] </span>{% else %}<span>[Open] </span>{% endif %} {{ ticket.getIssueTypeName() }}{% if ticket.other %} ({{ ticket.other }}){% endif %}</h3>
            Opened {{ fa.formatDate(ticket.created_at) }}
        </div>
        <div class="panel-body">
            <div class="lineitem">
                <div class="cell" style="width:110px">
                    <img class="alignleft" src="{{ ticket.user.getAvatar() }}">
                </div>
                <div class="cell valigntop">
                    {{ ticket.message|e|nl2br }}
                </div>
            </div>
        {% if ticket.is_resolved != true %}
            <div class="alignright">
                <a class="btn btn-default" href="{{ url.routeFromHere(['action': 'close']) }}">Close Ticket</a>
            </div>
        {% endif %}
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Ticket Correspondence</h3>
        </div>
        <div class="panel-body">
        {% for row in ticket.comments %}
            <div class="lineitem clearfix p10b">
                <div class="cell valigntop " style="min-width:100px;width:100px;height:100%;padding-right:25px">
                    <a class="orange" href="{{ url.get('user/'~row.user.lower) }}"><img src="{{ row.user.getAvatar() }}"></a>
                </div>

                <div class="cell bg3 usercomment valigntop auto_link">
                    <div class="p5t p10l p10r">
                        <div class="responsenav fontsize12 auto_link">
                            <span class="fontcolor3 fontsize12 floatright">posted {{ fa.formatDate(row.created_at) }}</span>

                            <h3>
                                <a class="orange" href="{{ url.get('user/'~row.user.lower) }}"><strong>{{ row.user.username }}</strong></a>
                                {% if row.is_staff %}<img src="{{ url.getStatic('img/tail.png') }}" title="Staff Member">{% endif %}
                            </h3>
                        </div>
                        <div class="p5t floatnone">
                            <hr>
                        </div>
                        <div class="p5t floatnone">
                            {{ row.message|e|nl2br }}
                        </div>
                    </div>
                </div>
            </div>

        {% else %}
            There are no replies to date...
        {% endfor %}
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Respond to Ticket</h3>
        </div>
        <div class="panel-body">
            {{ form.render() }}
        </div>
    </div>

{% endblock %}