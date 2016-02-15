{% extends "cpanel.volt" %}

{% block content %}
    {% set title='Trouble Tickets' %}

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">{{ title }}</h3>
            Dealing with a problem user? Having site issues or technical issues? We're here to help! From the Trouble Ticket page you can reach out to the FurryDigital staff to report problems and get assistance with the site.
        </div>
        <div class="panel-body">
            <ul class="nobullet">
                <li class="p5b">
                    <div class="floatleft fonthighlight"><strong>Short & Simple</strong></div>
                    <div class="ttguideline">Keep your ticket short, simple and focus on the facts. The easier it is for our staff to review the faster we can help resolve your problem.</div>
                </li>
                <li class="p5b">
                    <div class="floatleft fonthighlight"><strong>Category</strong></div>
                    <div class="ttguideline">Please select the most specific category that applies to your issue. This will help ensure the right staff get your ticket.</div>
                </li>
                <li class="p5b">
                    <div class="floatleft fonthighlight"><strong>Evidence</strong></div>
                    <div class="ttguideline">Include ALL relevant links to evidence (userpages of any users involved, comments, notes, shouts or submissions). We can not take action without proof.</div>
                </li>
                <li class="p5b">
                    <div class="floatleft fonthighlight"><strong>Screenshots</strong></div>
                    <div class="ttguideline">Screenshots can in most cases only be used as supporting evidence. If you feel you need to submit a screenshot, please use a service like [link]Archive[/link] to create a webpage snapshot, rather than submitting personal screenshots.</div>
                </li>
                <li class="p5b">
                    <div class="floatleft fonthighlight"><strong>Harassment</strong></div>
                    <div class="ttguideline">What's harassing to you may not be harassing to others.</div>
                </li>
                <li class="p5b">
                    <div class="floatleft fonthighlight"><strong>Notes</strong></div>
                    <div class="ttguideline">Staff <strong>CAN NOT</strong> view your notes unless you A) link to them in the ticket and B) consent to let us view the tickets (see the toggle below).</div>
                </li>
                <li>
                    <div class="floatleft fonthighlight"><strong>Off-Site Issues</strong></div>
                    <div class="ttguideline">We cannot take action on issues happening off-site UNLESS the user in question is linking to them on App.</div>
                </li>
            </ul>
        </div>
    </div>

    {% if tickets %}
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Existing Tickets</h3>
            </div>
            <div class="panel-body">
                <table cellpadding="0" cellspacing="0" border="0" width="100%" class="table table-striped table-nopadding">
                    <thead>
                        <tr>
                            <th width="15%" align="center"><b>Filed</b></th>
                            <th width="40%" align="left"><b>Subject</b></th>
                            <th width="20%" align="center"><b>Last reply by (admin)</b></th>
                            <th width="10%" align="center"><b>Replies</b></th>
                            <th width="15%" align="center"><b>Reply Date</b></th>
                        </tr>
                    </thead>
                    <tbody>
                    {% for row in tickets %}
                        <tr>
                            <td class="alt1" align="center">
                                {{ app.formatDate(row.created_at) }}
                            </td>
                            <td class="alt1" align="left">
                                <a href="{{ url.routeFromHere(['action': 'view', 'id': row.id]) }}">Ticket ID #{{ row.id }} {% if row.is_resolved %}(Closed){% endif %}</a><br>
                                {{ row.getIssueTypeName() }}{% if row.other %} ({{ row.other }}){% endif %}
                            </td>
                            <td class="alt1" align="center">
                                {% if row.admin %}
                                    {{ row.admin }}
                                {% else %}
                                    <i>No Replies</i>
                                {% endif %}
                            </td>
                            <td class="alt1" align="center">
                                {{ row.replies }}
                            </td>
                            <td class="alt1" align="center">
                                {% if row.last_reply_date %}
                                    {{ app.formatDate(row.last_reply_date) }}
                                {% else %}
                                    <i>Never</i>
                                {% endif %}
                            </td>
                        </tr>
                    {% endfor %}
                    </tbody>
                </table>
            </div>
        </div>
    {% endif %}

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Open a New Trouble Ticket</h3>
        </div>
        <div class="panel-body">
            {{ form.render() }}
        </div>
    </div>

{% endblock %}