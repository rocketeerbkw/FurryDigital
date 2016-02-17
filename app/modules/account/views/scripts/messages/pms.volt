{% set title='Notes' %}

<div class="row" id="page-pms">
    <div class="col-md-4">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="btn-toolbar" role="toolbar">
                    <div class="btn-group" role="group">
                        <a class="btn btn-sm btn-primary" href="{{ url.routeFromHere(['action': 'compose']) }}"><i class="fa fa-paper-plane"></i> Compose</a>
                    </div>
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-folder-open"></i> {{ folder_name }} <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                        {% for folder_key, folder_label in folders %}
                            <li><a href="{{ url.routeFromHere(['folder': folder_key, 'id': null]) }}">{{ folder_label }}</a></li>
                        {% endfor %}
                        </ul>
                    </div>
                </div>
                <br>

                {% for message in messages %}
                <a class="media" href="{{ url.routeFromHere(['id': message['id']]) }}">
                    <div class="media-left">
                    {% if message['is_read'] %}
                        <i class="fa fa-envelope-o"></i>
                    {% else %}
                        <i class="fa fa-envelope"></i>
                    {% endif %}
                    </div>
                    <div class="media-body">
                        <h4 class="media-heading">
                            {% if folder == "outbox" %}{{ message['recipient']['username']|e }}{% else %}{{ message['sender']['username']|e }}{% endif %}
                            <br><small>{{ app.formatDate(message['created_at']) }}</small>
                        </h4>
                        {{ message['subject']|e }}
                    </div>
                </a>
                {% endfor %}
            </div>
        </div>
    </div>
    <div class="col-md-8">
        {% if record %}
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">{{ record.subject|e }}</h3>
                </div>
                <div class="panel-body">
                    {{ parser.message(record.message) }}
                </div>
            </div>

            {% if reply_form %}
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Reply</h3>
                </div>
                <div class="panel-body">
                    {{ reply_form.render() }}
                </div>
            </div>
            {% endif %}
        {% else %}
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Message Center</h3>
                </div>
                <div class="panel-body">
                    Select a message from the left to view details.
                </div>
            </div>
        {% endif %}
    </div>
</div>