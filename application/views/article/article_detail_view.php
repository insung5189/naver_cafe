<?
$GLOBALS['pageResources'] = [
    'css' => ['/assets/css/article/articleDetailView.css'],
    'js' => ['/assets/js/article/articleDetailView.js']
];
?>
<div class="article-wrap">
    <div class="article-top-btn">
        <div class="article-top-left-btn">
            <a href="/이동_링크" class="list-article-btn">
                이동
            </a>
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
                <a href="/해당 게시판 링크" class="board-name">
                    <?= $article->getArticleBoard() ? htmlspecialchars($article->getArticleBoard()->getBoardName(), ENT_QUOTES, 'UTF-8') : '게시판 없음'; ?>
                    <i class="fa-solid fa-angle-right"></i>
                </a>

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
                            $fileUrl = base_url("assets/file/images/");
                            $profileImageName = $article->getMember() && $article->getMember()->getMemberFileName() !== 'default.png'
                                ? $article->getMember()->getMemberFileName()
                                : 'defaultImg/default.png';
                            ?>
                            <img class="prfl-img-thumb" src="<?= $fileUrl . $profileImageName; ?>" alt="<?= htmlspecialchars($article->getMember()->getNickName(), ENT_QUOTES, 'UTF-8') . '프로필이미지'; ?>">
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
                                    <?= $article->getHit() ? htmlspecialchars($article->getHit(), ENT_QUOTES, 'UTF-8') : '조회수 없음'; ?>
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
                    <div class="article-file-list">
                        첨부파일 모아보기(justify-content : flex-end;)
                    </div>
                    <div class="article-viewer">

                    </div>

                    <div class="article-author">
                        <a class="article-author-link" href="/작성자의_활동내역_링크">
                            <!-- 사용자 프로필 이미지 -->
                            <?
                            $fileUrl = base_url("assets/file/images/");
                            $profileImageName = $article->getMember() && $article->getMember()->getMemberFileName() !== 'default.png'
                                ? $article->getMember()->getMemberFileName()
                                : 'defaultImg/default.png';
                            ?>
                            <img class="prfl-img-thumb" src="<?= $fileUrl . $profileImageName; ?>" alt="<?= htmlspecialchars($article->getMember()->getNickName(), ENT_QUOTES, 'UTF-8') . '프로필이미지'; ?>">
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
                                    <span>
                                        좋아요
                                    </span>
                                    <span class="like-count-num">
                                        좋아요 갯수 표시
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
                                공유
                            </div>
                        </div>
                    </div>

                    <hr class="hr-line">

                    <div class="article-commment-box">
                        <div class="comment-sort-box">
                            <h3 class="comment-subject">댓글</h3>
                            <a href="">
                                <div class="create-date-asc-btn">
                                    등록순
                                </div>
                            </a>
                            <a href="">
                                <div class="create-date-desc-btn">
                                    최신순
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="comment-foreach-box">
                    <ul>
                        <? if (isset($comments)) : ?>
                            <? foreach ($comments as $comment) : ?>
                                <li>
                                    <div class="comment-box">
                                        <!-- 사용자 프로필 이미지 -->
                                        <?
                                        $fileUrl = base_url("assets/file/images/");
                                        $commmentsProfileImageName = $comment->getMember() && $comment->getMember()->getMemberFileName() !== 'default.png'
                                            ? $article->getMember()->getMemberFileName()
                                            : 'defaultImg/default.png';
                                        ?>
                                        <a href="/작성자의_활동내역_링크">
                                            <img class="prfl-img-thumb" src="<?= $fileUrl . $commmentsProfileImageName; ?>" alt="<?= htmlspecialchars($comment->getMember()->getNickName(), ENT_QUOTES, 'UTF-8') . '프로필이미지'; ?>">
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
                                    <hr class="hr-line">
                                </li>
                            <? endforeach; ?>
                        <? endif; ?>
                    </ul>
                </div>
                <div class="session-comment-write-box">
                    <form action="/댓글_작성하는_URL">
                        <div class="session-comment-author-nickname">
                        </div>
                        <textarea name="" id="" cols="30" rows="10" placeholder="댓글을 남겨보세요"></textarea>
                        <div class="comment-img-file-upload-ico">

                        </div>
                        <input type="submit">
                    </form>
                </div>


            </div>
            <div class="article-bottom-btn-box">
                <div class="article-bottom-btn-left-box">
                    <div>
                        게시글쓰기 버튼
                    </div>
                    <div>
                        답글쓰기 버튼
                    </div>
                </div>
                <div class="article-bottom-btn-right-box">
                    <div>
                        목록보기 버튼
                    </div>
                    <div>
                        top 으로 스크롤 이동 버튼
                    </div>
                </div>
            </div>
            <div class="related-articles">
                <div class="related-articles-board-name">

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