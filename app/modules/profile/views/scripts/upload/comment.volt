{% for comment in upload_comments %}
    <table id="cid:{{ comment.id }}" cellpadding="0" cellspacing="0" align="right" class="container-comment{% if comment.isAdminComment() %} admin-comment{% endif %}" width="{{ comment.getLevelWidth() }}" data-timestamp="{{ comment.date_posted }}">
    {% if !comment.isHidden() or acl.isAllowed('administer all') %}
        <tr>
            <td>
                <div class="comments-flex">

                    <div class="comments-flex-item-icon">
                        <a href="/user/{{ comment.user.lower }}/"><img class="usericonmobile" src="{{ comment.user.getUserAvatar() }}" alt="{{ comment.user.lower }}" /></a>
                    </div>

                    <div class="comments-flex-item-main usercomment bg3">
                        <div class="comment-content">
                            <div class="comments-userline-flex">

                                <div class="replyto-name comments-userline-username">
                                    {% if comment.hasBeenEdited() %}
                                        <img class="floatleft" style="margin-top:4px" src="{{ static_url('/img/edited.png') }}" title="Comment has been edited by its owner" />
                                    {% endif %}
                                    <h3><a class="fonthighlight" href="/user/{{ comment.user.lower }}/"><strong>{{ comment.user.lower }}</strong></a> {% if comment.isAdminComment() %}<img src="{{ static_url('/img/tail.gif') }}" title="Staff Member"/>{% endif %}</h3>
                                </div>

                                <div class="comments-userline-datetime fontsize12">
                                    <abbr class="moment-ago" mtime="{{ comment.created_at }}">{{ comment.getFormattedDate() }}</abbr> <a class="hideonmobile" href="#cid:{{ comment.id }}" title="Link to this Comment"><span class="fontcolor3">#link</span></a>
                                </div>

                            </div>


                            <div class="auto_link fontsize12 p5b">
                                <span class="hideonmobile">
                                    Comments: {{ comment.user.getTotalComments() }} |
                                    <a href="/gallery/{{ comment.user.lower }}/">Gallery</a> |
                                    <a href="/journals/{{ comment.user.lower }}/">Journals</a>
                                    {% if auth.isLoggedIn() %}
                                        | <a href="/newpm/{{ comment.user.lower }}/">Note</a>
                                    {% endif %}
                                </span>
                                
                                <? if ($comment->canHide($this->user)): ?>
                                    <a href="{{ url.named('upload_comment_hide', ['id': comment.id, 'key': comment_csrf_str]) }}" onclick="javascript: return(confirm(\'Are you sure you want to hide this comment?{% if !acl.isAllowed('administer all') %} You will not be able to restore it once you do.{% endif %}\'))"><span class="hideonmobile">| </span>{% if comment.isHidden() %}Unh{% else %}H{% endif %}ide Comment</a>
                                <? endif; ?>
                            </div>

                            <hr>

                            <div class="p5t replyto-message{% if comment.deleted_id %} comment-deleted{% endif %}">
                                {% if comment.isHidden() %}
                                    <span style="color:orange"><a href="/user/{{ comment.deleting_user.lower }}">{{ comment.deleting_user.username }}</a> hid this comment.<br />
                                    <br />
                                    
                                    Original comment as follows:<br />
                                    </span>
                                {% endif %}

                                <div class="comment_text usershoutbubble" style="min-height:38px">{{ comment.message }}</div>

                                {% if auth.isLoggedIn() and !upload.comments_locked %}
                                    <div class="alignright">
                                        <? if (true): //($comment->canEdit($this->user)): ?>
                                            <a href="#" class="hide alignright auto_link edit-link" action="{{ url.named('upload_comment_edit', ['id': comment.id]) }}">&nbsp;&nbsp;&nbsp;Edit Comment</a> | 
                                        <? endif; ?>

                                        <a href="#" class="responsenav reply auto_link replyto-link" comment="{{ comment.id }}" action="{{ url.named('upload_comment_new', ['id': upload.id]) }}">Reply</a>
                                    </div>
                                {% endif %}
                            </div>
                        </div>
                    </div>
                </div>
            </td>
        </tr>
    {% else %}
        <tr>
            <td width="20">&nbsp;</td>
            <td class="comment-deleted"><strong>Comment hidden by {% if comment.deleting_user.id == comment.user.id %}its author{% else %}{% if comment.deleting_user.id == upload.user.id %}the page owner{% else %}the Administration{% endif %}{% endif %}</strong></td>
        </tr>
    {% endif %}
    </table>
{% endfor %}

{% if auth.isLoggedIn() and !upload.comments_locked %}
    <div id="responsebox" class="aligncenter">
        <h2 class="alignleft">Leave a Comment</h2>
        
        <div class="p10t p20b">
            <span class="floatleft"><?=$bbcode?></span>
        </div>
        
        {{ comment_form.render() }}
    </div>

    <br/>
    
    <div id="replyto-box" class="aligncenter" style="display: none">
    
        <hr>
        
        <h2 class="alignleft">Reply to comment</h2>
        <div class="p10t p20b">
            <span class="floatleft"><?=$bbcode?></span>
        </div>

        {{ reply_form.render() }}
        
        <button id="replyto-cancel" class="button centerthis floatright" style="margin: 0 10px 0 0">Cancel</button>
    </div>

    <div id="edit-box" style="display: none">  
        {{ edit_form.render() }}
        
        <button id="edit-cancel" class="button centerthis floatright" style="margin: 0 10px 0 0">Cancel</button>
    </div>

    <script type="text/javascript">
        (function() {
            var reply_box = jQuery('#replyto-box');
        
            // REPLY
            jQuery('a.replyto-link').click(function() {
                var this_elem = jQuery(this),
                    parent_id = this_elem.attr('comment'),
                    comment_elem = this_elem.closest('.comment-content');
                
                // Put the reply box within the element of the comment frame
                comment_elem.after(reply_box);
                
                // Populate the action
                reply_box.find('form').attr('action', this_elem.attr('action'));
                
                reply_box.find('#parent_id').val(parent_id);
                
                // Show the element
                reply_box.show();
                
                // Override any other actions by the link.
                return false;
            });
            
            jQuery('#replyto-cancel').click(function() {
                reply_box.hide();
                
                // Override any other actions by the link.
                return false;
            });
            
            // EDIT
            var curr_edit_comm,
                edit_box = jQuery('#edit-box'); // The currently edited comment
            
            function clearComment(skip_edit) {
                if (curr_edit_comm) {
                    curr_edit_comm.show();
                    
                    // Clear it!
                    curr_edit_comm = undefined;
                }
                
                // If skip_edit isn't true, then hide the edit element
                if (!skip_edit)
                    edit_box.hide();
            }
            
            jQuery('a.edit-link').click(function() {
                var this_elem = jQuery(this),
                    comment_id = this_elem.attr('comment'),
                    comment_elem = this_elem.closest('.comment-content'),
                    comment_message = comment_elem.find('.comment_text').text();
                
                // If we have a comment already being edited, we'll need to show it again before taking over another!
                clearComment(true);
                
                // Set the current comment for later use
                curr_edit_comm = comment_elem;
                
                // Hide the current comment
                comment_elem.hide();
                
                // Put the edit box within the element of the comment frame
                comment_elem.after(edit_box);
                
                // Populate the action
                edit_box.find('form').attr('action', this_elem.attr('action'));
                
                // Populate the comment textarea with the current comment text
                edit_box.find('#JSMessage').val(comment_message);
                
                // Show the element
                edit_box.show();
                
                // Override any other actions by the link
                return false;
            });
            
            jQuery('#edit-cancel').click(function() {
                clearComment()
                
                // Override any other actions by the link.
                return false;
            });
        })();
    </script>
{% endif %}

