<?
$GLOBALS['pageResources'] = [
    'css' => ['/assets/css/home/dashboard.css'],
    'js' => ['/assets/js/home/dashboard.js']
];
?>

<div class="main-daemoon">
    <div class="daemoon-img">
        <img src="https://i.imgur.com/cd5JYxY.png" alt="카페대문">
    </div>
    <div class="daemoom-controller">
        <span class="toggle-daemoon"></span>
    </div>
</div>

<div class="main-contents-area">
    <div class="article-list-all-view">
        <div class="list-title">
            <h3 class="list-title-text">
                <a href="/article/articlelistallcontroller">
                    전체글보기
                </a>
            </h3>
            <span class="more-btn">
                <a class="more-link" href="/article/articlelistallcontroller">
                    <span>더보기</span>
                    <span><i class="fa-solid fa-chevron-right fa-xs"></i></span>
                </a>
            </span>
        </div>
        <ul class="album-box">
            <? foreach ($articleListAllArticles as $articleListAllArticle) : ?>
                <li class="album-box-li">
                    <dl>
                        <dt class="article-list-all-photo-box">
                            <a href="/article/articledetailcontroller/index/<?= $articleListAllArticle->getId() ?>" class="article-link">
                                <img src="<?= $articleListAllimgfileUrls[$articleListAllArticle->getId()] ?? '기본 이미지 경로' ?>" alt="" class="article-list-all-img">
                            </a>
                        </dt>
                        <dd class="article-list-all-title-box">
                            <a href="/article/articledetailcontroller/index/<?= $articleListAllArticle->getId() ?>" class="article-link">
                                <span class="article-list-all-title-box-text">
                                    <?= $articleListAllArticle->getTitle() ? htmlspecialchars($articleListAllArticle->getTitle(), ENT_QUOTES, 'UTF-8') : '';  ?>
                                </span>
                                <? $commentCount = $articleListAllcommentCounts[$articleListAllArticle->getId()] ?? 0; ?>
                                <? if ($commentCount !== 0) : ?>
                                    <span class="article-list-all-comment-count">
                                        <?= '[' . $commentCount . ']' ?>
                                    </span>
                                <? endif; ?>
                            </a>
                        </dd>
                        <dd>
                            <a href="/article/articledetailcontroller/index/<?= $articleListAllArticle->getId() ?>" class="article-link">
                                <span class="article-list-all-author-box-text">
                                    <?= $articleListAllArticle->getMember() ? htmlspecialchars($articleListAllArticle->getMember()->getNickName(), ENT_QUOTES, 'UTF-8') : ''; ?>
                                </span>
                            </a>
                        </dd>
                        <dd>
                            <div class="article-list-all-date-and-hit">
                                <span class="article-list-all-date">
                                    <?= $articleListAllArticle->getModifyDate() ? $articleListAllArticle->getModifyDate()->format('Y.m.d') : $articleListAllArticle->getCreateDate()->format('Y.m.d'); ?>
                                </span>
                                ㆍ
                                <span class="article-list-all-hit">
                                    조회 <?= $articleListAllArticle ? $articleListAllArticle->getHit() : 0; ?>
                                </span>
                            </div>
                        </dd>
                    </dl>
                </li>
            <? endforeach; ?>
        </ul>
    </div>

    <div class="main-free-board-and-qna-board">
        <div class="main-free-board">
            <div class="list-title">
                <h3 class="list-title-text">
                    <a href="/article/articlelistcontroller/index/1">
                        자유게시판
                    </a>
                </h3>
                <span class="more-btn">
                    <a class="more-link" href="/article/articlelistcontroller/index/1">
                        <span>더보기</span>
                        <span><i class="fa-solid fa-chevron-right fa-xs"></i></span>
                    </a>
                </span>
            </div>
            <ul class="main-free-board-ul">
                <? foreach ($freeBoardArticles as $freeBoardArticle) : ?>
                    <a href="/article/articledetailcontroller/index/<?= $freeBoardArticle->getId() ?>" class="article-link">
                        <li class="main-free-board-li">
                            <div class="main-free-board-card-area">
                                <div class="main-free-board-card-info">
                                    <div class="main-free-board-card-title">
                                        <?= $freeBoardArticle->getTitle() ? htmlspecialchars($freeBoardArticle->getTitle(), ENT_QUOTES, 'UTF-8') : ''; ?>
                                    </div>
                                    <div class="main-free-board-card-content">
                                        <?= $freeBoardArticle->getContent() ? htmlspecialchars($freeBoardArticle->getContent(), ENT_QUOTES, 'UTF-8') : ''; ?>
                                    </div>
                                    <div class="main-free-board-card-author">
                                        <?= $freeBoardArticle->getMember() ? htmlspecialchars($freeBoardArticle->getMember()->getNickName(), ENT_QUOTES, 'UTF-8') : ''; ?>
                                    </div>
                                    <div class="main-free-board-card-date-and-hit">
                                        <span class="main-free-board-card-date">
                                            <?= $freeBoardArticle->getModifyDate() ? $freeBoardArticle->getModifyDate()->format('Y.m.d') : $freeBoardArticle->getCreateDate()->format('Y.m.d'); ?>
                                        </span>
                                        <span class="main-free-board-card-hit">
                                            조회 <?= $freeBoardArticle ? $freeBoardArticle->getHit() : 0; ?>
                                        </span>
                                    </div>
                                </div>
                                <? if (isset($freeBoardArticlesimgfileUrls[$freeBoardArticle->getId()])) : ?>
                                    <div class="main-free-board-card-img-box">
                                        <img src="<?= $freeBoardArticlesimgfileUrls[$freeBoardArticle->getId()] ?? '기본 이미지 경로' ?>" alt="" class="main-free-board-card-img">
                                    </div>
                                <? endif; ?>
                            </div>
                        </li>
                    </a>
                <? endforeach; ?>
            </ul>
        </div>
        <div class="main-qna-board">
            <div class="list-title">
                <h3 class="list-title-text">
                    <a href="/article/articlelistcontroller/index/5">
                        질문/답변게시판
                    </a>
                </h3>
                <span class="more-btn">
                    <a class="more-link" href="/article/articlelistcontroller/index/5">
                        <span>더보기</span>
                        <span><i class="fa-solid fa-chevron-right fa-xs"></i></span>
                    </a>
                </span>
            </div>
            <div class="main-qna-board-table">
                <div>
                    <? foreach ($qnaBoardArticles as $qnaBoardArticle) : ?>

                        <?
                        $styleAttributes = '';
                        if ($qnaBoardArticle->getDepth() > 0) {
                            $parentArticleDeleted = '';
                            $paddingVal = $qnaBoardArticle->getDepth() * 35;
                            $styleAttributes = 'style="padding-left:' . $paddingVal . 'px;"';
                        }
                        ?>

                        <div class="main-qna-board-row">
                            <div class="main-qna-board-title" <?= $styleAttributes ?>>
                                <a href="/article/articledetailcontroller/index/<?= $qnaBoardArticle->getId() ?>" class="article-link">
                                    <? if ($qnaBoardArticle->getDepth() == 0) : ?>
                                        <span>ㆍ</span>
                                    <? elseif ($qnaBoardArticle->getDepth() > 0) : ?>
                                        <span>┗</span>
                                    <? endif; ?>
                                    <?= $qnaBoardArticle->getTitle() ? htmlspecialchars($qnaBoardArticle->getTitle(), ENT_QUOTES, 'UTF-8') : ''; ?>
                                    <? $commentCount = $commentCounts[$qnaBoardArticle->getId()] ?? 0; ?>
                                    <? if ($commentCount !== 0) : ?>
                                        <span class="article-list-all-comment-count">
                                            <?= '[' . $commentCount . ']' ?>
                                        </span>
                                    <? endif; ?>
                                </a>
                            </div>
                            <div class="main-qna-board-hit">
                                <a href="/article/articledetailcontroller/index/<?= $qnaBoardArticle->getId() ?>" class="article-link">
                                    <?= $qnaBoardArticle->getHit() ?>
                                </a>
                            </div>
                        </div>

                    <? endforeach; ?>
                </div>
            </div>
        </div>
    </div>

</div>