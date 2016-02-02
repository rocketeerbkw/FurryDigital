{%- macro thumbnails(rows) %}
    {% for row in rows %}
        <b id="sid_{{ row['id'] }}" class="r-{{ row['rating_text'] }}"><u><s><a href="{{ url.get('view/'~row['id'])  }}"><img alt="" src="{{ row['thumbnail_url'] }}"/></a></s></u></b>
    {% endfor %}
{%- endmacro %}

<div id="standardpage">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Recent Submissions</h3>
        </div>
        <div class="panel-body">
            <center class="flow frontpage submissions threelines">
                {{ thumbnails(records['images']) }}
            </center>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Recent Writing &amp; Poetry</h3>
        </div>
        <div class="panel-body">
            <center class="flow frontpage stories with-nothing twolines rounded">
                {{ thumbnails(records['text']) }}
            </center>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Recent Music &amp; Audio</h3>
        </div>
        <div class="panel-body">
            <center class="flow frontpage stories with-nothing twolines rounded">
                {{ thumbnails(records['audio']) }}
            </center>
        </div>
    </div>
</div>