<script type="text/javascript">
    var comments = 10;
</script>
<script type="text/javascript" src="<?= $this->RootUrl ?>template/js/comments.js"></script>
<? if (!$User->IsAnonymous && $User->IsActive && !$User->IsBlocked) { ?>
    <div class="pageTitle">Leave comment</div>
    <div class="contentBlock">
        <div class="contentTitle">
        </div>
        <div class="contentText">
            <textarea class="commentTextarea"></textarea>
        </div>
        <div class="contentBottom center">
            <button class="commentSend">Send comment</button>
        </div>
    </div>
<? } ?>
<div class="pageTitle">
    <div class="pagesLine"></div>
    Comments <span class="commentsCount"></span>

</div>
<div class="comments">
</div>

