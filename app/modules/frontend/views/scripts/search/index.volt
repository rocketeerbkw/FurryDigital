<div class="row">
    <div class="col-md-3 col-sm-3">
        <h3>Search</h3>

        {{ form.render() }}

        <div id="section-help" class="search-flex-item-1 bg5 rounded hidden">
            <h3>Power Search</h3>
            <p>
                Search understands <strong>basic boolean</strong> operators:
            <ul>
                <li><u>AND</u>: hello & world</li>
                <li><u>OR&nbsp;</u>: hello | world</li>
                <li><u>NOT</u>: hello -world <u>-or-</u> hello !world</li>
                <li><u>Grouping</u>: (hello world)</li>
            </ul>
            <u>Example:</u> ( cat -dog ) | ( cat -mouse)
            </p>

            <p>
                It also supports the following <strong>extended matching capabilities</strong>:
            <ul>
                <li><u>Field searching</u>: @title hello @message world</li>
                <li><u>Phrase searching</u>: "hello world"</li>
                <li><u>Word proximity searching</u>: "hello world"~10</li>
                <li><u>Quorum matching</u>: "the world is a wonderful place"/3</li>
            </ul>
            <u>Example</u>: "hello world" @title "example program"~5 @message python -(php|perl)
            </p>

            <p>
                <b>List of available fields:</b>
            <ul>
                <li>@title</li>
                <li>@message</li>
                <li>@filename</li>
                <li>@lower (artist name as it appears in their userpage URL)</li>
                <li>@keywords</li>
            </ul>
            <u>Example: </u> fender @title fender -dragoneer -ferrox @message -rednef -dragoneer
            </p>
        </div>
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
                    {% else %}
                        No results were found. Please update your query using the sidebar on the left.
                    {% endfor %}
                </div>
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

{{ javascript_include('//cdnjs.cloudflare.com/ajax/libs/masonry/4.0.0/masonry.pkgd.min.js') }}
<script type="text/javascript">
    $(window).load(function(e) {
        $('div.grid').masonry({
            columnWidth: '.grid-sizer',
            itemSelector: '.grid-item',
            gutter: 10
        });
    });
</script>

{#
<script type="text/javascript">
    Queue.add(function(){
        $('q').focus();

        $('button-help').observe('click', function(evt){
            evt.stop();
            $('section-help').toggleClassName('hidden');
        });


        $('button-advanced').observe('click', function(evt){
            evt.stop();
            $('search-advanced').toggleClassName('hiddensearch');
            $('search-advanced2').toggleClassName('hiddensearch');
        });
    });
</script>
#}