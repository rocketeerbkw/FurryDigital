{% set title=journal.subject|e %}

{% include('usernav') %}

<div id="standardpage" style="padding:0 !important">
    <div class="lineitem">
        <div class="cell bg3 journalbody valigntop auto_link">
            <div class="p5t p10l p10r">

                <div class="responsenav fontsize12 auto_link">
                    <span class="fontcolor3 fontsize12 floatright p10t">posted {{ fa.formatDate(journal.created_at) }}</span>
                    <h2><strong>{{ journal.subject|e }}</strong></h2>
                </div>

                <div class="p5t floatnone"><hr></div>

                <div class="p5t floatnone">
                    {% if owner.journalheader %}
                        {{ parser.message(owner.journalheader) }}
                        <hr width="30%">
                    {% endif %}

                    {{ parser.message(journal.message) }}

                    {% if owner.journalfooter %}
                        <hr width="30%">
                        {{ parser.message(owner.journalfooter) }}
                    {% endif %}
                </div>
            </div>
        </div>
    </div>

    {#
    <?if($_USER['accesslevel'] == 1 && $number_of_deleted_comments != 0){?>
    <div style="color:orange; padding: 5px 0; text-align: center">Number of deleted comments on the page: <?=$number_of_deleted_comments?>. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;By admin: <?=$number_of_deleted_comments_by_admin?>. By page owner: <?=$number_of_deleted_comments_by_page_owner?>. By comment owner: <?=$number_of_deleted_comments_by_comment_owner?></div>
    <?}?>

    <div class="submissionview_other">
        <?=$admin_options?>
    </div>
    #}
</div>

<div id="ad-extra-comments" class="leaderboard2"></div>

<div style="clear:both">
    <?=$comments?>
</div>

{% if auth.isLoggedIn() %}
    {% if journal.disable_comments %}
        <div id="responsebox" class="aligncenter" style="padding:35px">
            Comment posting has been disabled by the journal owner.
        </div>
    {% else %}
        <div id="responsebox" class="aligncenter">
            <h2 class="alignleft">Leave a Comment</h2>
            <form name="myform" method="post" action="/journal/<?=$journal_id?>/" id="add_comment_form">

                <input type="hidden" name="action" value="reply" id="form-action">
                <input type="hidden" name="replyto" id="form-replyto" value=""/>
                <div class="replyto_comment hidden"></div>
                <textarea id="JSMessage" name="reply" class="textarea textarearesize" placeholder="Click here to leave a comment."></textarea>
                <!-- <?=$smilielist?><br/> -->
                <div class="p10t p20b">
                    <span class="floatleft"><?=$bbcode?></span>
                    <input class="button floatright" type="submit" name="submit" value="Post Your Comment"/>
                </div>
            </form>
        </div>
    {% endif %}
{% endif %}

{#
<?if($_USER['userid']!=0 && !$comments_disabled){?>
<script type="text/javascript">
    Queue.add(function(){LazyLoad.load("<?=STATIC_PATH?>/js/quick-replyto.js?u=<?=STATIC_ASSET_MODIFICATION_DATE?>", function(){})})
</script>
<?}?>
#}

{#
<script type="text/javascript">
    <?=$javascript_data?>

    var comment_edit_window_sec = <?=$edit_duration_sec?>;

    Queue.add(function(){
        // hide comment edit links upon comment edit window expiring
        edit_links_hide_handler();
    });

    <?=$script?>
</script>
#}