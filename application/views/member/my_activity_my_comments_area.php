<!-- 작성댓글 -->
<!-- my_activity_my_comments_area.php -->
<div id="myCommentsArea">

    <div id="mycomments-col-table">
        <table>
            <colgroup>
                <col>
                <col style="width:100px">
            </colgroup>
            <thead id="myCommentsThead">
                <tr class="normalTableTitleCol">
                    <th scope="col">
                        <span class="comments-title-col">댓글</span>
                    </th>
                </tr>
            </thead>
            <tbody id="mycomments-tbody">
                <? foreach ($commentsByPage as $comment) : ?>
                    <tr class="normalTableTitleRow">

                        <td scope="col" class="td-comment">
                            <div class="comment-content-list">
                                <div class="inner-content-detail">
                                    <div class="check-box-cell check-box-comment-cell">
                                        <input id="check-comment-<?= $comment->getId() ?>" type="checkbox" class="input_check_comment">
                                    </div>
                                    <a href="javascript:void(0);" class="link-to-commented-article-contents" data-comment-id="<?= $comment->getId(); ?>" data-comment-article-id="<?= $comment->getArticle()->getId(); ?>">
                                        <div class="comment-content-detail">
                                            <?= $comment->getContent() ? htmlspecialchars($comment->getContent(), ENT_QUOTES, 'UTF-8') : '내용없음.'; ?>
                                        </div>
                                        <div class="comment-create-date">
                                            <?= $comment->getModifyDate() ? $comment->getModifyDate()->format('Y.m.d H:i') : $comment->getCreateDate()->format('Y.m.d H:i'); ?>
                                        </div>
                                        <div class="article-title-for-comment">
                                            <span>
                                                <?= $comment->getArticle() ? htmlspecialchars($comment->getArticle()->getTitle(), ENT_QUOTES, 'UTF-8') : '게시글 제목없음.'; ?>
                                            </span>
                                            <? $commentCountByArticleNum = $commentCountByArticle[$comment->getArticle()->getId()] ?? 0; ?>
                                            <? if ($commentCountByArticleNum !== 0) : ?>
                                                <span class="articles-comment-count">
                                                    <?= '[' . $commentCountByArticleNum . ']' ?>
                                                </span>
                                            <? endif; ?>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </td>

                        <td scope="col" class="td-comment-img">
                            <?
                            $commentImageName = $comment->getCommentFileName() ? htmlspecialchars($comment->getCommentFileName(), ENT_QUOTES, 'UTF-8') : '';
                            ?>
                            <? if (!empty($commentImageName)) : ?>
                                <a href="javascript:void(0);" class="link-to-commented-article" data-comment-id="<?= $comment->getId(); ?>" data-comment-article-id="<?= $comment->getArticle()->getId(); ?>">
                                    <img id="uploadedImage-<?= $comment->getId(); ?>" src="<?= $commentFileUrl . $commentImageName; ?>" alt="댓글 첨부사진" width="72" height="72">
                                </a>
                            <? endif; ?>
                        </td>

                    </tr>
                <? endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="my-comments-control-box">
        <div class="all-check-box-btn">
            <input id="check-comment-all-this-page" type="checkbox" class="input-checked-all">
            <label for="check-comment-all-this-page" class="input-checked-all-label">
                전체 선택
            </label>
        </div>
        <div class="my-comments-delete-and-write-btn">
            <a href="javascript:void(0);" class="my-comments-delete-btn">삭제</a>
            <a href="/article/articleEditController" target="_blank" class="my-articles-write-btn">글쓰기</a>
        </div>
    </div>


</div>