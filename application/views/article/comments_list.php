<? if (isset($article)) : ?>
    <? if (isset($comments) && !empty($comments)) : ?>
        <? foreach ($comments as $comment) : ?>
            <?
            $interval = date_diff($comment->getCreateDate(), new DateTime());

            // 차이가 1분 이내인지 확인
            if ($interval->i < 1 && $interval->h == 0 && $interval->days == 0) {
                $backgroundColor = 'style="background-color: #ffffe0;"';
            } else {
                $backgroundColor = '';
            }
            ?>
            <li id="comment-<?= $comment->getId(); ?>" <?= $backgroundColor ?>>

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
                        </div>
                    </div>
                    <? if (isset($user) && is_array($user) && isset($user['user_id']) && $user['user_id'] === $comment->getMember()->getId()) : ?>
                        <div class="comment-edit-delete-btn">
                            <a href="javascript:void(0);" class="comment-edit-delete-toggle" data-comment-id="<?= $comment->getId(); ?>">
                                <i class="fa-solid fa-xl fa-ellipsis-vertical"></i>
                            </a>
                            <div class="comment-edit-and-delete-btn-box" style="display:none;" id="comment-edit-delete-toggle-box">
                                <a href="javascript:void(0);" class="comment-edit-btn">
                                    수정
                                </a>
                                <a href="javascript:void(0);" class="comment-delete-btn">
                                    삭제
                                </a>
                            </div>
                        </div>
                    <? else : ?>
                        <div class="comment-edit-delete-btn"></div>
                    <? endif; ?>
                </div>

                <div class="comment-content-area">
                    <p>
                        <span>
                            <?= $comment->getContent() ? htmlspecialchars($comment->getContent(), ENT_QUOTES, 'UTF-8') : ''; ?>
                        </span>
                    </p>
                    <!-- 댓글 컨텐츠 이미지 -->
                    <?
                    $commentImageName = $comment->getCommentFileName() ? htmlspecialchars($comment->getCommentFileName(), ENT_QUOTES, 'UTF-8') : '';
                    ?>
                    <? if (!empty($commentImageName)) : ?>
                        <div>
                            <img src="<?= $commentFileUrl . $commentImageName; ?>" alt="<?= '댓글 첨부사진' ?>">
                        </div>
                    <? endif; ?>
                    <div class="comment-info-box">
                        <span>
                            <?= $comment->getModifyDate() ? $comment->getModifyDate()->format('Y.m.d H:i') : $comment->getCreateDate()->format('Y.m.d H:i'); ?>
                        </span>
                        <? if (isset($user) && !empty($user)) : ?>
                            <a href="/해당댓글_답글쓰기 링크">
                                답글쓰기
                            </a>
                            <div class="session-comment-reply-write-box" id="reply-comment" style="display:none;">
                                <form action="/댓글_작성하는_URL">
                                    <div class="comment-writer">
                                        <div class="name-and-textarea">
                                            <div class="session-comment-reply-author-nickname">
                                                <?= $user['nickName'] ? htmlspecialchars($user['nickName'], ENT_QUOTES, 'UTF-8') : ''; ?>
                                            </div>
                                            <textarea class="comment-text-area" name="" id="" cols="30" rows="10" placeholder="답글을 남겨보세요"></textarea>
                                        </div>
                                        <div class="comment-reply-img-file-upload-ico">
                                            <div class="upload-ico">
                                                <a href="/댓글_사진_첨부하는_URL">
                                                    <i class="fa-solid fa-camera"></i>
                                                </a>
                                            </div>
                                            <div class="comment-submit-btn">
                                                <a href="/댓글에_답글_작성_취소하는_URL">취소</a>
                                                <input type="submit" value="등록">
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        <? endif; ?>
                    </div>
                </div>
                <hr class="comment-hr-line">
            </li>
        <? endforeach; ?>
    <? elseif (!isset($comments)) : ?>
        <p>댓글 정보를 불러오는데 실패했습니다.</p>
    <? elseif (!isset($user)) : ?>
        <p>세션이 만료되었습니다.</p>
    <? endif; ?>
<? else : ?>
    <p>존재하지 않는 게시물입니다.</p>
<? endif; ?>