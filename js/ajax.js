/**
 * AJAX Functions - Like & Comment using jQuery $.ajax()
 * 
 * Why AJAX: Updates UI without page reload = better UX.
 * Uses jQuery $.ajax() for cross-browser compatibility.
 * All responses are JSON from PHP API endpoints.
 */
$(document).ready(function () {

    // ==================== LIKE TOGGLE ====================
    $('.like-btn').on('click', function () {
        const $btn = $(this);
        const recipeId = $btn.data('recipe-id');

        $.ajax({
            url: 'api/like.php',
            method: 'POST',
            data: { recipe_id: recipeId },
            dataType: 'json',
            success: function (res) {
                if (res.success) {
                    // Update like count and icon without reload
                    $btn.find('.like-count').text(res.count);
                    $btn.find('.like-icon').text(res.liked ? '❤️' : '🤍');
                    $btn.toggleClass('liked', res.liked);
                }
            },
            error: function () {
                alert('Error — please try again.');
            }
        });
    });

    // ==================== POST COMMENT ====================
    $('#comment-form').on('submit', function (e) {
        e.preventDefault(); // Prevent page reload

        const $form = $(this);
        const recipeId = $form.find('[name="recipe_id"]').val();
        const commentText = $form.find('[name="comment_text"]').val().trim();

        if (!commentText) return;

        $.ajax({
            url: 'api/comment.php',
            method: 'POST',
            data: { recipe_id: recipeId, comment_text: commentText },
            dataType: 'json',
            success: function (res) {
                if (res.success) {
                    // Append new comment dynamically
                    const comment = res.comment;
                    const html = `
                        <div class="comment" style="display:none;">
                            <strong>${$('<span>').text(comment.username).html()}</strong>
                            <small>${comment.created_at}</small>
                            <p>${$('<span>').text(comment.comment_text).html()}</p>
                        </div>
                    `;
                    $('#comments-list').append(html);
                    $('#comments-list .comment:last').fadeIn(300);

                    // Update comment count
                    const count = parseInt($('#comment-count').text()) + 1;
                    $('#comment-count').text(count);

                    // Clear form
                    $form.find('textarea').val('');
                }
            },
            error: function () {
                alert('Error posting comment. Please try again.');
            }
        });
    });
});
