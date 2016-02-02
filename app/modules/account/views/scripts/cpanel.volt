<div id="columnpage">
    <div class="one">
        <div class="onecontent">
            {% block navigation %}
                <div id="controlpanelnav" class="hideonmobile">
                    <h3>Customize</h3>
                    <div><a href="{{ url.route(['module': 'account', 'controller': 'settings', 'action': 'index']) }}">Site Settings</a></div>
                    <div><a href="{{ url.route(['module': 'account', 'controller': 'settings', 'action': 'profile']) }}">Edit Profile Page</a></div>
                    <div><a href="{{ url.route(['module': 'account', 'controller': 'avatar']) }}">My Avatar</a></div>

                    <h3>My Content</h3>
                    <div><a href="{{ url.route(['module': 'account', 'controller': 'uploads']) }}">Uploads</a></div>
                    <div><a href="{{ url.route(['module': 'account', 'controller': 'journals']) }}">Journals</a></div>
                    <div><a href="{{ url.route(['module': 'account', 'controller': 'folders']) }}">Folders</a></div>
                    <div><a href="{{ url.route(['module': 'account', 'controller': 'favorites']) }}">Favorites</a></div>
                    <div><a href="{{ url.route(['module': 'account', 'controller': 'watches']) }}">Watches</a></div>
                    <div><a href="{{ url.route(['module': 'account', 'controller': 'shouts']) }}">Shouts</a></div>

                    <h3>Support</h3>
                    <div><a href="{{ url.route(['module': 'account', 'controller': 'tickets']) }}">Trouble Tickets</a></div>
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

