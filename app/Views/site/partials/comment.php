<?php
$comment = isset($comment) && is_array($comment) ? $comment : array();
$post = isset($post) && is_array($post) ? $post : array();
$currentUser = isset($currentUser) ? $currentUser : null;
$replies = isset($comment['replies']) && is_array($comment['replies']) ? $comment['replies'] : array();
$depth = isset($depth) ? (int) $depth : 0;
$authorName = !empty($comment['user_name']) ? $comment['user_name'] : (isset($comment['guest_name']) && $comment['guest_name'] ? $comment['guest_name'] : 'Guest');
$authorMeta = !empty($comment['user_username']) ? '@' . $comment['user_username'] : (isset($comment['guest_email']) && $comment['guest_email'] ? $comment['guest_email'] : 'Guest');
?>
<article class="comment-card card-surface" data-comment-depth="<?php echo e($depth); ?>">
    <div class="comment-card__top">
        <div>
            <strong><?php echo e($authorName); ?></strong>
            <span><?php echo e($authorMeta); ?></span>
        </div>
        <time datetime="<?php echo e(isset($comment['created_at']) ? $comment['created_at'] : ''); ?>"><?php echo e(isset($comment['created_at']) ? $comment['created_at'] : ''); ?></time>
    </div>
    <div class="comment-card__body">
        <?php echo nl2br(e($comment['body'])); ?>
    </div>
    <details class="reply-disclosure">
        <summary>Reply</summary>
        <form class="reply-form" method="post" action="<?php echo e(url('/content/' . $post['id'] . '/comments')); ?>">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="parent_id" value="<?php echo e($comment['id']); ?>">
            <label>
                <span>Reply</span>
                <textarea name="body" rows="4" required maxlength="2000" placeholder="Write a reply."></textarea>
            </label>
            <?php if (!is_array($currentUser)) : ?>
                <div class="comment-form__grid">
                    <label>
                        <span>Name</span>
                        <input type="text" name="guest_name" maxlength="120" required>
                    </label>
                    <label>
                        <span>Email</span>
                        <input type="email" name="guest_email" maxlength="190" required>
                    </label>
                </div>
            <?php endif; ?>
            <button class="btn btn-secondary" type="submit">Post reply</button>
        </form>
    </details>

    <?php if ($replies !== array()) : ?>
        <div class="comment-replies">
            <?php foreach ($replies as $reply) : ?>
                <?php echo view('site/partials/comment', array('comment' => $reply, 'post' => $post, 'currentUser' => $currentUser, 'depth' => $depth + 1), array('layout' => false)); ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</article>