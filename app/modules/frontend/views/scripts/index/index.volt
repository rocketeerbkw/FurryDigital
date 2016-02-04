{%- macro thumbnails(rows) %}
    <div class="grid">
        <div class="grid-sizer"></div>
    {% for row in rows %}
        <a id="sid_{{ row['id'] }}" class="grid-item r-{{ row['rating_text'] }}" href="{{ url.get('view/'~row['id']) }}">
            <img alt="" src="{{ row['thumbnail_url'] }}">
        </a>
    {% endfor %}
    </div>
{%- endmacro %}

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Recent Submissions</h3>
    </div>
    <div class="panel-body">
        {{ thumbnails(records['images']) }}

        <!--
        <center class="flow frontpage submissions threelines">

        </center>
        -->
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Recent Writing &amp; Poetry</h3>
    </div>
    <div class="panel-body">
        {{ thumbnails(records['text']) }}

        <!--
        <center class="flow frontpage stories with-nothing twolines rounded">

        </center>
        -->
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Recent Music &amp; Audio</h3>
    </div>
    <div class="panel-body">
        {{ thumbnails(records['audio']) }}

        <!--
        <center class="flow frontpage stories with-nothing twolines rounded">

        </center>
        -->
    </div>
</div>

{% block footerjs %}
    {{ super() }}

    {{ javascript_include('//cdnjs.cloudflare.com/ajax/libs/masonry/4.0.0/masonry.pkgd.min.js') }}

    <script type="text/javascript">
    jQuery(function($) {
        $('div.grid').masonry({
            columnWidth: '.grid-sizer',
            itemSelector: '.grid-item',
            gutter: 10
        });
    });
    </script>
{% endblock %}