$$('a.replyto-link').each(function(elm){Event.observe(elm, 'click', handle_replyto_click);});
var quick_reply_active = false;


//
////  "Reply to" onlick handler
//
function handle_replyto_click(evt) {
    if(quick_reply_active) {
        Event.stop(evt);
        return;
    }

    quick_reply_active = true;

    var elm = Event.element(evt);

    var reply_form   = $('add_comment_form');
    var form_action  = $('form-action');
    var form_replyto = $('form-replyto');
    var form_message = $('JSMessage');


    //  Get the replyTo name and id from the comment we clicked on
    //
    tmp = elm.up('table');

    var replyto_id_raw = tmp.id;
    var replyto_id    = tmp.id.replace('cid:', '');
    var replyto_name  = $(tmp).getElementsByClassName('replyto-name')[0].innerHTML;
    var replyto_text  = $(tmp).getElementsByClassName('comment_text')[0].innerHTML;

    //  Change the reply form title, "replyto" and "action" input fields
    //
    var new_form_title = '<div><span class="replyto-close" onclick="javascript:cancel_reply(event)">[Cancel]</span> Replying to: <em class="replyto-target" title="Click here to scroll to the original comment" onclick="javascript:$(\''+replyto_id_raw+'\').scrollTo();"><strong>' + replyto_name + '</strong></em></div><hr width="100%" style="clear: both" /><br />';

    form_action.value    = 'replyto';
    form_replyto.value   = replyto_id;

    //  Quote the original poster
    //
    var reply_table = reply_form.down('.replyto_comment');
    var quote_html  = '<div class="bbcode bbcode_quote" id="reply-op-preview" style="text-align: left; margin-bottom: 15px;">'+new_form_title + replyto_text + '</div>';

    new Insertion.Top(reply_table, quote_html);
    reply_table.removeClassName('hidden');


    //  Focus the reply form
    //
    reply_form.scrollTo();
    form_message.focus();


    //  Stop the click event from propagating further, e.g cancel a link click.
    //
    Event.stop(evt);
}


//
////  Cancel "replyto" event. Revert the form back.
//
function cancel_reply(evt) {
    quick_reply_active = false;

    var reply_form  = $('add_comment_form');
    var reply_table = reply_form.down('.replyto_comment');
    reply_table.innerHTML = '';
    reply_table.addClassName('hidden');

    $('form-replyto').value = '';
    $('form-action').value  = 'reply';
}
