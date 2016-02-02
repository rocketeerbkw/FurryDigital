{% set title=owner.username|e %}

{% include('usernav') %}

<div id="userpage">
    <div class="userline">

        <!-- START PROFILE -->
        <div class="user-profile-flex">
            {% if profile_pic %}
            <div class="user-profile-pic">
                <b id="sid_{{ profile_pic.id }}" class="r-{{ profile_pic.getRatingReadable() }}">
                    <a href="{{ url.named('upload_view', ['id': profile_pic.id]) }}"><img class="profilepic aligncenter" src="{{ profile_pic.getThumbnailUrl() }}"></a>
                </b>
            </div>
            {% endif %}

            <div class="user-profile-desc valigntop">
                {% if owner.profileinfo %}
                    {{ parser.message(owner.profileinfo) }}
                {% else %}
                    <i>Not Available...</i>
                {% endif %}
            </div>
        </div>
        <!-- END PROFILE -->

        <!-- START GALLERY -->
        <div class="p10t">
            <div class="bg4">
                <div class="p10">
                    {% if featured_pic %}
                    <div class="p20r">
                        <div class="floatleft p10r p10l hideonmobile">
                            <h3 class="p10b">Featured Submission</h3>
                            <a href="{{ url.named('upload_view', ['id': featured_pic.id]) }}"><img class="profilepic hideonmobile aligncenter" alt="" src="{{ featured_pic.getThumbnailUrl() }}"></a>
                        </div>
                    </div>
                    {% endif %}

                    {% if latest_uploads %}
                    <div>
                        <h3><a href="{{ url.named('user_gallery', ['username': owner.lower]) }}">Gallery</a></h3>
                        <center class="flow userpage-submissions linesubs">
                        {% for row in latest_uploads %}
                            <b id="sid_{{ row.id }}" class="r-{{ row.getRatingReadable() }}"><u><s><a href="{{ url.named('upload_view', ['id': row.id]) }}"><img class="enlthumb" alt="" preview_src="{{ row.getSmallUrl() }}" src="{{ row.getThumbnailUrl() }}"></a></s></u></b>
                        {% endfor %}
                        </center>
                    </div>
                    {% endif %}

                    {% if latest_faves %}
                    <div>
                        <h3><a href="{{ url.named('user_favorites', ['username': owner.lower]) }}">Favorites</a></h3>
                        <center class="flow userpage-favorites linefavs">
                        {% for fave in latest_faves %}
                            {% set row=fave.upload %}
                            <b id="sid_{{ row.id }}" class="r-{{ row.getRatingReadable() }}"><u><s><a href="{{ url.named('upload_view', ['id': row.id]) }}"><img class="enlthumb" alt=""  preview_src="{{ row.getSmallUrl() }}" src="{{ row.getThumbnailUrl() }}"></a></s></u></b>
                        {% endfor %}
                        </center>
                    </div>
                    {% endif %}
                </div>
            </div>
        </div>
        <!-- END GALLERY -->


        <div class="flex-container-userpage">
            <div class="flex-item-userpage">

                <!--- START STATS --->
                <h3 class="p10b">Statistics</h3>
                <div class="">
                    <div class="p10 bg3 rounded">
                        <div class="stats-flex-container">
                            <div class="stats-flex-item">
                                <span class="stats"><strong>Page Views:</strong></span> {{ owner.pageviews }}<br/>
                                <span class="stats"><strong>Uploads:</strong></span> {{ owner.num_uploads }}<br/>
                                <span class="stats"><strong>Comments (R):</strong></span> {{ owner.num_comments_received }}<br/>
                                <span class="stats"><strong>Comments (M):</strong></span> {{ owner.num_comments_sent }}
                            </div>

                            <div class="stats-flex-item ">
                                <span class="stats2"><strong>Journals:</strong></span> {{ owner.num_journals }}<br/>
                                <span class="stats2"><strong>Favorites:</strong></span> {{ owner.num_favorites }}<br/>
                                <span class="stats2"><strong>Watchers:</strong></span> {{ num_watched_by }}{#<a href="/budslist/?name=<?=$artist_lower?>&uid=<?=$artist_userid?>&mode=watched_by"><?=$watched_by_count?> (List)</a>#}<br/>
                                <span class="stats2"><strong>Watching:</strong></span> {{ num_watching }}{#<a href="/budslist/?name=<?=$artist_lower?>&uid=<?=$artist_userid?>&mode=watches"><?=$is_watching_count?> (List)</a>#}
                            </div>
                        </div>
                    </div>
                </div>
                <!--- END STATS --->

                <!--- START STATS --->
                <h3 class="p10t p10b">Trade/Commission Status</h3>
                <div class="">
                    <div class="p10 bg3 rounded">
                        <strong>Accepting Trades:</strong> {% if accept_trades %}Yes{% else %}No{% endif %}<br>
                        <strong>Accepting Commissions:</strong> {% if accept_commissions %}Yes{% else %}No{% endif %}
                    </div>
                </div>
                <!--- END STATS --->

                <!--- START PROFILE --->
                <h3 class="p10t p10b">Personal Info</h3>
                <div class="bg3 rounded p10t p10b">
                {% if owner.species %}
                    <div class="p10l"><strong class="p5r">Character Species</strong><br/>{{ owner.getSpeciesName() }}</div>
                {% endif %}
                {% if owner.age %}
                    <div class="p10l p10t"><strong class="p5r">Age</strong><br/>{{ owner.age }}</div>
                {% endif %}
                {% if owner.music %}
                    <div class="p10l p10t"><strong class="p5r">Favorite Music Type/Genre</strong><br/>{{ owner.music|e }}</div>
                {% endif %}
                {% if owner.favoritemovie %}
                    <div class="p10l p10t"><strong class="p5r">Favorite Movies and Films</strong><br/>{{ owner.favoritemovie|e }}</div>
                {% endif %}
                {% if owner.favoritegame %}
                    <div class="p10l p10t"><strong class="p5r">Favorite Games</strong><br/>{{ owner.favoritegame|e }}</div>
                {% endif %}
                {% if owner.favoriteplatform %}
                    <div class="p10l p10t"><strong class="p5r">Favorite Gaming Consoles</strong><br/>{{ owner.favoriteplatform|e }}</div>
                {% endif %}
                {% if owner.favoriteanimal %}
                    <div class="p10l p10t"><strong class="p5r">Favorite Animals</strong><br/>{{ owner.favoriteanimal|e }}</div>
                {% endif %}
                {% if owner.favoritewebsite %}
                    <div class="p10l p10t"><strong class="p5r">Favorite Website</strong><br/>{{ owner.favoritewebsite|e }}</div>
                {% endif %}
                {% if owner.favoritefood %}
                    <div class="p10l p10t"><strong class="p5r">Favorite Noms</strong><br/>{{ owner.favoritefood|e }}</div>
                {% endif %}
                {% if owner.quote %}
                    <div class="p10l p10t"><strong>Favorite Quote or Lyric</strong><br/> {{ owner.quote|e }}</div>
                {% endif %}
                {% if owner.favoriteartist %}
                    <div class="p10l p10t"><strong>Favorite Artists</strong><br/> {{ owner.favoriteartist|e }}</div>
                {% endif %}
                </div>
                <!--- END PROFILE --->

                <!--- START CONTACT INFO --->
                {% if owner_social %}
                <div class="contactinfo p10t">

                    <h3 class="p10b p10t hideonmobile">Contact Information</h3>

                    {% for social_item in owner_social %}
                        <div>{{ social_item }}</div>
                    {% endfor %}
                </div>
                {% endif %}
            </div>

            <div class="flex-item-userpage2">
                {% if journal %}
                <!-- START RECENT JOURNAL -->
                <div class="p10b">
                    <h3 class="inline p10b"><?=$is_featured?'Featured':'Recent'?> Journal</h3>
                    <div class="floatright inline"><a href="{{ url.named('user_journals', ['username': owner.lower]) }}"><h3>View Recent Journals</h3></a></div>
                    <!-- {latestjournal} -->
                    <div class="container-item-mid roundedul roundedur">
                        <div class="fontcolor3 fontsize12 floatright inline p10t">
                            {{ fa.formatDate(journal.created_at) }}
                            <?=$date?>
                        </div>
                        <h2><a href="{{ url.named('journal_view', ['id': journal.id]) }}"><strong>{{ journal.subject|e }}</strong></a></h2>
                        <hr>
                        {{ parser.message(journal.message) }}
                    </div>

                    <div class="container-item-bot-settings aligncenter">
                        <a href="{{ url.named('journal_view', ['id': journal.id]) }}">Read More</a> | <a href="{{ url.named('journal_view', ['id': journal.id]) }}">Comments ({{ journal.num_comments }})</a>
                    </div>
                    <!-- {/latestjournal} -->
                </div>
                <!-- END RECENT JOURNAL -->
                {% endif %}

                <!-- START SHOUTS-->
                <div class="p20r">
                    <h3 class="inline p10b">Shouts</h3>
                </div>
                <!-- LEAVE A SHOUT -->

                {% if auth.isLoggedIn() %}
                <div id="shoutboxentry">
                    <div class="shoutboxcontainer">
                        <div class="shoutboxcontent p10b">
                            {{ shout_form.render() }}
                        </div>
                    </div>
                    <div class="leftcol">
                        <img alt="{{ user.lower }}" src="{{ user.getAvatar() }}">
                    </div>
                </div>
                {% endif %}
                <!-- END LEAVE A SHOUT -->

                {% if shouts %}
                <div class="clear">
                    {% for row in shouts %}
                    <div class="clearfix p10t">
                        <table width="100%" id="shout-{{ row.id }}">
                            <tr>
                                <td>
                                    <div class="comments-flex-shouts">

                                        <div class="comments-flex-item-icon">
                                            <a href="{{ url.named('user_view', ['username': row.sender.lower]) }}"><img class="usericonmobile valigntop" src="{{ row.sender.getAvatar() }}" alt="{{ row.sender.username|e }}"></a>
                                        </div>

                                        <div class="comments-flex-item-main usercomment bg3">
                                            <div class="comments-userline-flex">
                                                <div class="replyto-name comments-userline-username">
                                                    <h3><a class="fonthighlight" href="{{ url.named('user_view', ['username': row.sender.lower]) }}"><strong>{{ row.sender.username|e }}</strong></a></h3>
                                                </div>
                                                <div class="comments-userline-datetime fontsize12 p5b">
                                                    {{ fa.formatDate(row.created_at) }}
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="p5t">
                                                <div class="comment_text usershoutbubble" style="min-height:38px">{{ parser.message(row.message) }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                    {% endfor %}
                </div>
                {% endif %}
            </div>
            <!-- END SHOUTS -->
        </div>
    </div>
</div>