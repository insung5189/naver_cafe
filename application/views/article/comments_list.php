<?
$GLOBALS['pageResources'] = [
    'css' => ['/assets/css/article/articleDetailView.css'],
    'js' => ['/assets/js/article/articleDetailView.js']
];
?>

<? if (isset($article)) : ?>
    <? if (isset($comments) && !empty($comments)) : ?>














        <? foreach ($comments as $comment) : ?>
            <?
            $commentImageName = $comment->getCommentFileName() ? htmlspecialchars($comment->getCommentFileName(), ENT_QUOTES, 'UTF-8') : '';
            $styleAttributes = '';
            $isNewCommentBadge = false;

            if ($comment->getDepth() > 0) {
                $paddingVal = $comment->getDepth() * 30;
                $styleAttributes .= 'padding-left:' . $paddingVal . 'px;"';
            }

            // 댓글이 새로 작성되면 1분동안 빨간색 뱃지를 표시함.
            $interval = date_diff($comment->getCreateDate(), new DateTime());

            if ($interval->i < 1 && $interval->h == 0 && $interval->days == 0) {
                $isNewCommentBadge = true;
            }

            $styleAttribute = !empty($styleAttributes) ? 'style="' . $styleAttributes . '"' : '';
            ?>
            <li id="comment-<?= $comment->getId(); ?>" <?= $styleAttribute ?>>
                <div class="comment-author-action-box">
                    <div class="comment-author-box">
                        <!-- 사용자 프로필 이미지 -->
                        <?
                        $commmentsProfileImageName = $comment->getMember() && $comment->getMember()->getMemberFileName() !== 'default.png'
                            ? $comment->getMember()->getMemberFileName()
                            : 'defaultImg/default.png';
                        ?>
                        <a href="/작성자의_활동내역_링크">
                            <img class="prfl-img-thumb" src="<?= $memberPrflFileUrl . $commmentsProfileImageName; ?>" alt="<?= htmlspecialchars($comment->getMember()->getNickName(), ENT_QUOTES, 'UTF-8') . '프로필이미지'; ?>">
                        </a>
                        <div class="comment-content-each">
                            <div class="comment-author">
                                <?= $comment->getMember() ? htmlspecialchars($comment->getMember()->getNickName(), ENT_QUOTES, 'UTF-8') : '작성자 닉네임 없음'; ?>
                            </div>
                            <? if ($comment->getMember()->getNickName() === $article->getMember()->getNickName()) : ?>
                                <div class="is-article-author">
                                    작성자
                                </div>
                            <? endif; ?>
                            <? if ($isNewCommentBadge) : ?>
                                <div class="is-new-comment">
                                    N
                                </div>
                            <? endif; ?>
                        </div>
                    </div>
                    <? if (isset($user) && is_array($user) && ((isset($user['user_id']) && $user['user_id'] === $comment->getMember()->getId()) || (isset($user['role']) && in_array($user['role'], ['ROLE_ADMIN', 'ROLE_MASTER'])))) : ?>
                        <div class="comment-edit-delete-btn">
                            <a href="javascript:void(0);" class="comment-edit-delete-toggle" data-comment-id="<?= $comment->getId(); ?>">
                                <i class="fa-solid fa-xl fa-ellipsis-vertical"></i>
                            </a>
                            <div class="comment-edit-and-delete-btn-box" style="display:none;" id="comment-edit-delete-toggle-box">
                                <? if (isset($user) && is_array($user) && ((isset($user['user_id']) && $user['user_id'] === $comment->getMember()->getId()))) : ?>
                                    <a href="javascript:void(0);" class="comment-edit-btn" data-comment-image-url="<?= $commentFileUrl . $commentImageName; ?>" data-edited-comment-id="<?= $comment->getId(); ?>" data-comment-content="<?= htmlspecialchars($comment->getContent(), ENT_QUOTES, 'UTF-8'); ?>">
                                        수정
                                    </a>
                                <? endif; ?>
                                <? if (isset($user) && is_array($user) && ((isset($user['user_id']) && $user['user_id'] === $comment->getMember()->getId()) || (isset($user['role']) && in_array($user['role'], ['ROLE_ADMIN', 'ROLE_MASTER'])))) : ?>
                                    <a href="javascript:void(0);" class="comment-delete-btn" data-delete-comment-id="<?= $comment->getId(); ?>">
                                        삭제
                                    </a>
                                <? endif; ?>
                            </div>
                        </div>
                    <? else : ?>
                        <div class="comment-edit-delete-btn"></div>
                    <? endif; ?>
                </div>

                <div class="comment-content-area">
                    <div>
                        <span id="comment-content-<?= $comment->getId() ?>" class="comment-content-box">
                            <?= $comment->getContent() ? htmlspecialchars($comment->getContent(), ENT_QUOTES, 'UTF-8') : ''; ?>
                        </span>
                    </div>
                    <!-- 댓글 컨텐츠 이미지 -->

                    <div id="comment-content-img-<?= $comment->getId(); ?>">
                        <? if (!empty($commentImageName)) : ?>
                            <img id="uploadedImage-<?= $comment->getId(); ?>" src="<?= $commentFileUrl . $commentImageName; ?>" alt="댓글 첨부사진">
                        <? endif; ?>
                    </div>

                    <div class="comment-info-box" id="comment-reply-<?= $comment->getId(); ?>">
                        <span>
                            <?= $comment->getModifyDate() ? $comment->getModifyDate()->format('Y.m.d H:i') : $comment->getCreateDate()->format('Y.m.d H:i'); ?>
                        </span>
                        <? if (isset($user) && !empty($user)) : ?>
                            <a href="javascript:void(0);" class="create-comment-reply-btn" data-comment-reply-id="<?= $comment->getId(); ?>">
                                답글쓰기
                            </a>
                            <span class="text-end">depth : <?= $comment->getDepth() ?></span>
                            <div class="session-comment-reply-write-box" id="reply-comment" style="display:none;">
                                <form action="/article/articledetailcontroller/createReply" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="articleId" value="<?= $article->getId(); ?>">
                                    <input type="hidden" name="memberId" value="<?= $user['user_id']; ?>">
                                    <input type="hidden" name="depth" value="<?= $comment->getDepth() + 1 ?>">
                                    <input type="hidden" name="parentId" value="<?= $comment->getId() ?>">
                                    <input type="hidden" name="orderGroup" value="<?= $comment->getOrderGroup() ?>">

                                    <div class="comment-writer">

                                        <div class="name-and-textarea">

                                            <div class="nickname-and-text-caculate">

                                                <div class="session-comment-reply-author-nickname">
                                                    <?= $user['nickName'] ? htmlspecialchars($user['nickName'], ENT_QUOTES, 'UTF-8') : ''; ?>
                                                </div>
                                                <div class="text-caculate-reply" data-text-calculate-reply-id="<?= $comment->getId(); ?>" style="display:none;">0 / 3000</div>

                                            </div>

                                            <textarea class="comment-text-area-reply" name="content" placeholder="답글을 남겨보세요" maxlength="3000" data-comment-reply-id="<?= $comment->getId(); ?>"></textarea>

                                            <div class="comment-img-preview" id="imgPreviewReply" data-img-preview-reply-id="<?= $comment->getId(); ?>">
                                            </div>

                                            <div class="comment-edit-img-file-upload-ico">

                                                <label for="commentImageReply-<?= $comment->getId(); ?>" class="upload-ico">
                                                    <i class="fa-solid fa-lg fa-camera"></i>
                                                </label>

                                                <input type="file" name="commentImage" id="commentImageReply-<?= $comment->getId(); ?>" data-comment-image-reply-id="<?= $comment->getId(); ?>" accept="image/jpg, image/jpeg, image/png, image/bmp, image/webp, image/gif" style="display: none;">

                                                <div class="comment-submit-btn">
                                                    <a href="javascript:void(0);" class="cancel-comment-reply-btn" data-comment-reply-id="<?= $comment->getId(); ?>">취소</a>
                                                    <input type="submit" value="등록">
                                                </div>
                                            </div>

                                        </div>

                                    </div>
                                </form>
                            </div>
                        <? endif; ?>
                    </div>
                </div>

                <? if (isset($user) && $user['user_id'] === $comment->getMember()->getId()) : ?>
                    <div class="comment-edited-form-box" style="display: none;">
                        <form action="/article/articledetailcontroller/editComment/<?= $comment->getId(); ?>" method="POST" enctype="multipart/form-data" data-update-comment-id="<?= $comment->getId(); ?>">
                            <input type="hidden" name="articleId" value="<?= $article->getId(); ?>">
                            <input type="hidden" name="memberId" value="<?= $user['user_id']; ?>">

                            <div class="comment-writer">

                                <div class="name-and-textarea">

                                    <div class="nickname-and-text-caculate">

                                        <div class="session-comment-reply-author-nickname">
                                            <?= isset($user['nickName']) ? htmlspecialchars($user['nickName'], ENT_QUOTES, 'UTF-8') : ''; ?>
                                        </div>
                                        <div class="text-caculate-reply" data-text-calculate-reply-id="<?= $comment->getId(); ?>" style="display:none;">0 / 3000</div>

                                    </div>

                                    <textarea class="comment-text-area-edit" name="commentEditContent" maxlength="3000" data-comment-reply-id="<?= $comment->getId(); ?>"><?= $comment->getContent() ? htmlspecialchars($comment->getContent(), ENT_QUOTES, 'UTF-8') : ''; ?></textarea>

                                    <div class="comment-edit-img-preview" id="imgPreviewEdit" data-img-preview-edit-id="<?= $comment->getId(); ?>">
                                    </div>

                                    <div class="comment-edit-img-file-upload-ico">

                                        <label for="commentImageEdit-<?= $comment->getId(); ?>" class="upload-ico">
                                            <i class="fa-solid fa-lg fa-camera"></i>
                                        </label>

                                        <input type="file" name="commentImage" id="commentImageEdit-<?= $comment->getId(); ?>" data-comment-image-edit-id="<?= $comment->getId(); ?>" accept="image/jpg, image/jpeg, image/png, image/bmp, image/webp, image/gif" style="display: none;">

                                        <div class="comment-submit-btn">
                                            <a href="javascript:void(0);" class="cancel-comment-edit-btn" data-comment-edited-cancel-id="<?= $comment->getId(); ?>">취소</a>
                                            <input type="submit" value="등록">
                                        </div>
                                    </div>

                                </div>

                            </div>
                        </form>
                    </div>
                <? else : ?>
                    <div></div>
                <? endif; ?>
                <hr class="comment-hr-line">
            </li>
        <? endforeach; ?>














    <? elseif (!isset($comments)) : ?>
        <p>댓글 정보를 불러오는데 실패했습니다.</p>
    <? endif; ?>
<? else : ?>
    <p>존재하지 않는 게시물입니다.</p>
<? endif; ?>