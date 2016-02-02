<div id="columnpage">
    <div class="one">
        <div class="onecontent">
            {% block navigation %}
                <div id="controlpanelnav" class="hideonmobile">
                    <h3>Administration</h3>
                    <div><a href="{{ url.route(['module': 'admin']) }}">Admin Home</a></div>
                    <div><a href="{{ url.route() }}">FA Home</a></div>

                    <h3>Trouble Tickets</h3>
                    <div><a href="#">Open Issues</a></div>
                    <div><a href="#">Resolved Issues</a></div>
                    <div><a href="#">Advanced Search (Beta)</a></div>

                    <h3>User Management</h3>
                    <div><a href="#">Find User</a></div>
                    <div><a href="#">IP Bans</a></div>

                    <h3>Advanced Tools</h3>
                    <div><a href="#">Users on IPs</a></div>
                    <div><a href="#">Newest Users</a></div>
                    <div><a href="#">Registration Statistics</a></div>
                    <div><a href="#">Submission Statistics</a></div>
                    <div><a href="#">Submission Sorter</a></div>
                    <div><a href="#">User IPs</a></div>
                    <div><a href="#">Admin Activity Log</a></div>
                    <div><a href="#">User Activity Log</a></div>

                    <h3>System Settings</h3>
                    <div><a href="#">System Settings</a></div>
                    <div><a href="#">System Log</a></div>
                </div>
            {% endblock %}
        </div>
    </div>
    <div class="two">
        <div class="twocontent">
            {% block content %}

            {% endblock %}
        </div>
    </div>
</div>

