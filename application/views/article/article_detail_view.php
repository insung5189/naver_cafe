<?
$GLOBALS['pageResources'] = [
    'css' => ['/assets/css/article/articleDetailView.css'],
    'js' => ['/assets/js/article/articleDetailView.js']
];
?>
<div class="article-wrap">
    <div class="article-top-btn">
        <div class="article-top-left-btn">
            <!-- <a href="/이동_링크" class="list-article-btn">
                이동
            </a> -->
            <a href="/수정_링크" class="list-article-btn">
                수정
            </a>
            <a href="/삭제_링크" class="list-article-btn">
                삭제
            </a>
        </div>
        <div class="article-top-right-btn">
            <a href="/이전글_링크" class="prev-article-btn">
                <i class="fa-solid fa-angle-up"></i>
                이전글
            </a>
            <a href="/다음글_링크" class="next-article-btn">
                <i class="fa-solid fa-angle-down"></i>
                다음글
            </a>
            <a href="/목록_링크" class="list-article-btn">
                목록
            </a>
        </div>
    </div>

    <section class="section-container">
        <div class="container">
            <div class="article-content-box">
                <h1 class="title">
                    <i class="fas fa-newspaper"></i>
                    게시글 상세보기
                </h1>

                <? if ($this->session->flashdata('error_messages')) : ?>
                    <div class="error-messages">
                        <p class="error-alert"><strong>⚠️ 문제가 발생했습니다!</strong></p>
                        <? foreach ($this->session->flashdata('error_messages') as $field => $error) : ?>
                            <p class="error-alert"><strong><?= htmlspecialchars($field) ?>:</strong> <?= htmlspecialchars($error) ?></p>
                        <? endforeach; ?>
                    </div>
                <? endif; ?>

                <a href="/해당 게시판 링크" class="board-name">
                    <?= $article->getArticleBoard() ? htmlspecialchars($article->getArticleBoard()->getBoardName(), ENT_QUOTES, 'UTF-8') : '게시판 없음'; ?>
                    <i class="fa-solid fa-angle-right"></i>
                </a>
                <div id="article" data-article-id="<?= $article->getId() ?>"></div>
                <h1 class="article-title">
                    <span class="article-prefix">
                        <?= $article->getPrefix() ? '[' . htmlspecialchars($article->getPrefix(), ENT_QUOTES, 'UTF-8') . ']' : ''; ?>
                    </span>
                    <span class="article-title-text">
                        <?= $article->getTitle() ? htmlspecialchars($article->getTitle(), ENT_QUOTES, 'UTF-8') : '제목 없음'; ?>
                    </span>
                </h1>

                <div class="author-and-article-tool">
                    <div class="author-info">
                        <div class="prfl-thumb">
                            <!-- 사용자 프로필 이미지 -->
                            <?
                            $profileImageName = $article->getMember() && $article->getMember()->getMemberFileName() !== 'default.png'
                                ? $article->getMember()->getMemberFileName()
                                : 'defaultImg/default.png';
                            ?>
                            <img class="prfl-img-thumb" src="<?= $memberPrflFileUrl . $profileImageName; ?>" alt="<?= htmlspecialchars($article->getMember()->getNickName(), ENT_QUOTES, 'UTF-8') . '프로필이미지'; ?>">
                        </div>
                        <div class="author-prfl-info">
                            <div class="author-prfl-nickname">
                                <?= $article->getMember() ? htmlspecialchars($article->getMember()->getNickName(), ENT_QUOTES, 'UTF-8') : '작성자 닉네임 없음'; ?>
                            </div>
                            <div class="article-detail-info">
                                <span class="article-date">
                                    <?= $article->getModifyDate() ? $article->getModifyDate()->format('Y.m.d H:i') : $article->getCreateDate()->format('Y.m.d H:i'); ?>
                                </span>
                                <span class="article-hit">
                                    조회
                                    <span id="hitCount">
                                        <?= $article->getHit() ? htmlspecialchars($article->getHit(), ENT_QUOTES, 'UTF-8') : '조회수 없음'; ?>
                                    </span>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="article-tool">
                        <div class="comment-anchor">
                            <i class="fa-solid fa-comment-dots fa-lg fa-flip-horizontal"></i>
                            <a href="javascript:void(0);">댓글
                                <?= count($comments) ? htmlspecialchars(count($comments), ENT_QUOTES, 'UTF-8') : '0'; ?>
                            </a>
                        </div>
                        <div class="url-copy">
                            <a href="javascript:void(0);" id="articleLink">이 게시물 링크복사하기</a>
                        </div>
                    </div>
                </div>
                <hr class="hr-line">

                <div class="article-container">

                    <div class="article-file-area">
                        <? if (isset($articleFilesInfo) && !empty($articleFilesInfo)) : ?>
                            <a class="file-array-toggle" href="javascript:void(0);" data-session="<?= isset($user) ? 'true' : 'false' ?>">
                                <i class="fa-regular fa-folder-open"></i>
                                첨부파일 모아보기
                                <span class="file-array-count-num">
                                    <?= count($articleFilesInfo); ?>
                                </span>
                            </a>

                            <!-- 게시물 첨부파일 리스트 -->
                            <div class="article-file-list" style="display:none;">
                                <? foreach ($articleFilesInfo as $articleFileInfo) : ?>
                                    <span class="article-file-list-name">
                                        <?= htmlspecialchars($articleFileInfo['name'], ENT_QUOTES, 'UTF-8'); ?>
                                    </span>
                                    <span class="boundary-pipe">
                                        |
                                    </span>
                                    <a class="article-file-download-link" href="<?= htmlspecialchars($articleFileInfo['fullPath'], ENT_QUOTES, 'UTF-8'); ?>" download="<?= htmlspecialchars($articleFileInfo['name'], ENT_QUOTES, 'UTF-8'); ?>">
                                        내 PC 저장
                                    </a>
                                    <br>
                                <? endforeach; ?>
                            </div>
                        <? endif; ?>
                    </div>
                    <div class="article-viewer">
                        <?= $article->getContent() ? htmlspecialchars($article->getContent(), ENT_QUOTES, 'UTF-8') : '내용 없음'; ?>
                    </div>

                    <div class="article-author">
                        <a class="article-author-link" href="/작성자의_활동내역_링크">
                            <!-- 사용자 프로필 이미지 -->
                            <img class="prfl-img-thumb" src="<?= $memberPrflFileUrl . $profileImageName; ?>" alt="<?= htmlspecialchars($article->getMember()->getNickName(), ENT_QUOTES, 'UTF-8') . '프로필이미지'; ?>">
                            <span class="author-prfl-nickname-box">
                                <div class="author-prfl-nickname article-author-nickname-text"><?= $article->getMember() ? htmlspecialchars($article->getMember()->getNickName(), ENT_QUOTES, 'UTF-8') : '작성자 닉네임 없음'; ?></div>
                                <span>님의 게시글 더보기</span>
                            </span>
                            <i class="fa-solid fa-xs fa-chevron-right"></i>
                        </a>
                    </div>

                    <div class="article-reply-box">
                        <div class="article-reply-box-left">
                            <div class="like-ico">
                                <a href="#">
                                    <i class="fa-regular fa-lg fa-heart" style="color: #f53535;"></i>
                                    <i class="fa-solid fa-lg fa-heart" style="color: #f53535; display:none;"></i>
                                    <span>좋아요</span>
                                    <span class="like-count-num">
                                        <?= $likeCountByArticle ? htmlspecialchars($likeCountByArticle, ENT_QUOTES, 'UTF-8') : '0'; ?>
                                    </span>
                                </a>
                            </div>
                            <div class="comment-ico">
                                <i class="fa-solid fa-comment-dots fa-lg fa-flip-horizontal"></i>
                                <a href="javascript:void(0);">
                                    <span>댓글</span>
                                    <span class="comment-count-num"><?= count($comments) ? htmlspecialchars(count($comments), ENT_QUOTES, 'UTF-8') : '0'; ?></span>
                                </a>
                            </div>
                        </div>

                        <div class="article-reply-box-right">
                            <div>
                                <!-- 공유버튼 자리 -->
                            </div>
                        </div>
                    </div>

                    <hr class="hr-line">

                    <div class="article-commment-box">
                        <div class="comment-sort-box">
                            <h3 class="comment-subject">댓글</h3>
                            <a href="#" id="sort-asc-btn" class="sort-btn-active" data-sort="ASC" data-article-id="<?= $article->getId() ?>">
                                <div class="create-date-asc-btn">
                                    등록순
                                </div>
                            </a>
                            <a href="#" id="sort-desc-btn" class="sort-btn-deactivate" data-sort="DESC" data-article-id="<?= $article->getId() ?>">
                                <div class="create-date-desc-btn">
                                    최신순
                                </div>
                            </a>
                            <label class="depth-option-checkbox">
                                <input type="checkbox" id="depthOptionCheckbox" data-article-id="<?= $article->getId() ?>"> depth별 정렬
                            </label>
                            <label class="tree-option-checkbox">
                                <input type="checkbox" id="treeOptionCheckbox" data-article-id="<?= $article->getId() ?>"> 트리구조 정렬
                            </label>
                        </div>
                    </div>
                </div>
                <div class="comment-foreach-box">
                    <ul>
                        <? if (isset($comments) && !empty($comments)) : ?>
                            <? foreach ($comments as $comment) : ?>
                                <?
                                $commentImageName = $comment->getCommentFileName() ? htmlspecialchars($comment->getCommentFileName(), ENT_QUOTES, 'UTF-8') : '';
                                $styleAttributes = '';

                                if ($comment->getDepth() > 0) {
                                    $styleAttributes .= 'padding-left: 50px;';
                                }

                                $interval = date_diff($comment->getCreateDate(), new DateTime());

                                if ($interval->i < 1 && $interval->h == 0 && $interval->days == 0) {
                                    $styleAttributes .= ' background-color: #ffffe0;';
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
                                            </div>
                                        </div>
                                        <? if (isset($user) && is_array($user) && isset($user['user_id']) && $user['user_id'] === $comment->getMember()->getId()) : ?>
                                            <div class="comment-edit-delete-btn">
                                                <a href="javascript:void(0);" class="comment-edit-delete-toggle" data-comment-id="<?= $comment->getId(); ?>">
                                                    <i class="fa-solid fa-xl fa-ellipsis-vertical"></i>
                                                </a>
                                                <div class="comment-edit-and-delete-btn-box" style="display:none;" id="comment-edit-delete-toggle-box">
                                                    <a href="javascript:void(0);" class="comment-edit-btn" data-comment-image-url="<?= $commentFileUrl . $commentImageName; ?>" data-edited-comment-id="<?= $comment->getId(); ?>" data-comment-content="<?= htmlspecialchars($comment->getContent(), ENT_QUOTES, 'UTF-8'); ?>">
                                                        수정
                                                    </a>
                                                    <a href="javascript:void(0);" class="comment-delete-btn" data-delete-comment-id="<?= $comment->getId(); ?>">
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
                                            <span id="comment-content-<?= $comment->getId() ?>" class="comment-content-box">
                                                <?= $comment->getContent() ? htmlspecialchars($comment->getContent(), ENT_QUOTES, 'UTF-8') : ''; ?>
                                            </span>
                                        </p>
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
                        <? endif; ?>
                    </ul>
                </div>

                <div class="session-comment-write-box">
                    <? if (isset($user) && !empty($user)) : ?>
                        <form action="/article/ArticleDetailController/createComment" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="articleId" value="<?= $article->getId(); ?>">
                            <input type="hidden" name="memberId" value="<?= $user['user_id']; ?>">
                            <input type="hidden" name="depth" value="0">
                            <input type="hidden" name="parentId" value="">
                            <div class="comment-writer">
                                <div class="name-and-textarea">
                                    <div class="nickname-and-text-caculate">

                                        <div class="session-comment-author-nickname">
                                            <?= $user['nickName'] ? htmlspecialchars($user['nickName'], ENT_QUOTES, 'UTF-8') : ''; ?>
                                        </div>
                                        <div class="text-caculate" style="display:none;">
                                            0 / 3000
                                        </div>

                                    </div>
                                    <textarea class="comment-text-area" name="content" id="commentTextArea" cols="30" rows="10" placeholder="댓글을 남겨보세요" maxlength="3000"></textarea>
                                    <div class="comment-img-preview" id="imgPreview">
                                    </div>

                                    <div class="comment-img-file-upload-ico">

                                        <label for="commentImage" class="upload-ico">
                                            <i class="fa-solid fa-lg fa-camera"></i>
                                        </label>

                                        <input type="file" id="commentImage" name="commentImage" accept="image/jpg, image/jpeg, image/png, image/bmp, image/webp, image/gif" style="display: none;">

                                        <div class="comment-submit-btn">
                                            <input type="submit" value="등록">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    <? else : ?>
                        <a href="/member/signupcontroller" class="signup-link-btn">
                            지금 가입하고 댓글에 참여해보세요!
                            <i class="fa-solid fa-angle-right ml-2"></i>
                        </a>
                    <? endif; ?>
                </div>
            </div>

            <div class="article-bottom-btn-box">
                <div class="article-bottom-btn-left-box">
                    <a href="/article/articleeditcontroller" class="article-write">
                        <i class="fa-solid fa-pen-clip fa-sm"></i>
                        글쓰기
                    </a>
                    <a href="/해당_게시물에_답글쓰기_URL" class="article-base-btn">답글</a>
                    <a href="/해당_게시물_수정하기_URL" class="article-base-btn">수정</a>
                    <a href="/해당_게시물_삭제하기_URL" class="article-base-btn">삭제</a>
                </div>
                <div class="article-bottom-btn-right-box">
                    <a href="/진입했던_게시판_목록보기" class="article-base-btn">목록</a>
                    <a href="/스크롤_맨위로_이동하기" class="article-base-btn">
                        <i class="fa-solid fa-caret-up"></i>
                        TOP
                    </a>
                </div>
            </div>

            <hr class="hr-line">

            <div class="related-articles">
                <div class="related-articles-board-name">
                    <?= $article->getArticleBoard() ? htmlspecialchars($article->getArticleBoard()->getBoardName(), ENT_QUOTES, 'UTF-8') : '게시판 없음'; ?>
                </div>
                <ul>
                    <li>
                        같은 게시판 관련 게시글 반복
                    </li>
                </ul>
                <div class="related-articles-pagination">
                    관련 게시글 페이지네이션 자리
                </div>
                <div class="board-link">
                    전체보기(해당 게시판으로 가는 링크)
                </div>
            </div>
        </div>
    </section>
</div>