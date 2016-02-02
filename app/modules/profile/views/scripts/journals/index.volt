{% set title=owner.username|e~"'s Journals" %}

{% include('usernav') %}

<div id="columnpage">
    <div class="one">
        <div class="onecontent">
            <div class="pagination">
                {{ paginate(pager) }}
            </div>

            <div class="auto_link">
            {% for row in pager %}
                <div class="page-controls-journal-links bgtrans" style="padding:5px 0">
                    <a href="#jid:{{ row.id }}"><strong>{{ row.subject|e }}</strong></a><br>
                    <span class="fontsize12">Posted: {{ fa.formatDate(row.created_at) }}</span>
                </div>
            {% endfor %}
            </div>
        </div>
    </div>

    <div class="two">
        <div class="twocontent" style="padding-top:10px">

            {% for row in pager %}
            <div class="auto_link p20b">
                <div id="jid:{{ row.id }}" class="userpage-section-header">
                    <div class="userpage-module bg3 rounded">
                        <div class="inline"><h2 class="p20l p10t"><strong>{{ row.subject|e }}</strong></h2></div>
                        <div class="fontcolor3 fontsize12 journalfloat p20r"> posted: {{ fa.formatDate(row.created_at) }}</div>
                        <div class="p20lr"><hr></div>
                        <div id="journalcontent" class="journalcontent p20lr p10b">
                            {{ parser.message(row.message) }}
                        </div>
                        <div class="data-footer roundedbottom bg4">
                            <div class="data">
                                <a href="{{ url.named('journal_view', ['id': row.id]) }}">Read More</a> | <a href="{{ url.named('journal_view', ['id': row.id]) }}">Comments ({{ row.num_comments }})</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {% endfor %}

            <div class="p20lr p10b pagination" style="margin-top:-10px">
                {{ paginate(pager) }}
            </div>
        </div>
    </div>
</div>