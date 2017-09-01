<?php if ($comments) { ?>
<?php foreach ($comments as $comment) : ?>
    <div class="media comments">

        <a class="pull-left" href="#"> <i class="fa fa-comment-o" aria-hidden="true"></i></a>
        <div class="media-body">
            <div class="media-title">
                <a id="comment_reply" class="pull-right link" onclick="setArticleId(<?php echo $comment['simple_blog_comment_id']; ?>);"><?php echo $text_reply_comment; ?></a>
                <span class="username">by <b><?php echo $comment['author']; ?></b></span>
                <div class="time"><i class="fa fa-clock-o" aria-hidden="true"></i><?php echo $comment['date_added']; ?></div>
            </div>
            <div class="media-content">
                <p><?php echo $comment['comment']; ?></p>
            </div>
        </div>


    </div>
    <?php if($comment['comment_reply']) { ?>
        <?php foreach($comment['comment_reply'] as $comment_reply) { ?>
            <div class="media comments comments-level-1">
                 <a class="pull-left" href="#"> <i class="fa fa-comment-o" aria-hidden="true"></i></a>
                <div class="media-body">
                    <div class="media-title">
                        <span class="username">by <b><?php echo $comment_reply['author']; ?></b></span>
                        <div class="time"><span class="icon icon-access_time"></span><?php echo $comment_reply['date_added']; ?></div>
                    </div>
                    <div class="media-content">
                        <p><?php echo $comment_reply['comment']; ?></p>
                    </div>
                </div>

            </div>
        <?php } ?>
    <?php } ?>

<?php endforeach; ?>

<div class="form-group">
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 text-left"><?php echo $pagination; ?></div>
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 text-right"><?php echo $results; ?></div>
</div>

<?php } else { ?>
<h5><?php echo $text_no_blog; ?></h5>
<?php } ?>

<script type="text/javascript">
    function setArticleId(article_id) {
        $("#blog-reply-id").val(article_id);
        $("#reply-remove").css('display', 'inline');
    }
    $("#comment_reply").click(function (){
        var tabTop = $("#review-title").offset().top;
        $("html, body").animate({ scrollTop:tabTop }, 1000);
    });

</script>