<div class="page-search">
    <div id="columnpage">
        <div class="onevisible">
            <div class="onecontent">

                <h3 class="search-flex-item-title p10b">Search</h3>

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
                        <center class="flow search with-titles-usernames thumb-size-{{ thumbnail_size }}">
                            {% for row in pager %}
                                <b id="sid_{{ row['id'] }}" class="r-{{ row.getRatingReadable() }} t-{{ row.getUploadTypeName() }}"><u><s><a href="{{ url.named('upload_view', ['id': row.id]) }}"><img alt="" src="{{ row.getThumbnailUrl() }}"><i class="icon" title="Click for description"></i></a></s></u><span title="{{ row.title }}">{{ row.title }}</span><small><a href="{{ url.named('user_view', ['username': row.user.lower]) }}">{{ row.user.username }}</a></small></b>
                            {% else %}
                                No results were found. Please update your query using the sidebar on the left.
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
</div>

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