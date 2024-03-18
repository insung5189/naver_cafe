<div>
    <ul id="relatedArticlesList">
        <? foreach ($relatedArticles as $relatedArticle) : ?>
            <span>
                <li class="related-article-list-item" id="relatedArticleItemLi" data-related-article-id="<?= $relatedArticle->getId(); ?>">
                    <a href="/article/articledetailcontroller/index/<?= $relatedArticle->getId(); ?>">
                        <div class="related-article-title-area">
                            <? if (!empty($relatedArticle->getPrefix()) && ($articleBoard->getId() == 4 || $articleBoard->getId() == 5)) : ?>
                                <span class="prefix">[<?= htmlspecialchars($relatedArticle->getPrefix(), ENT_QUOTES, 'UTF-8'); ?>]</span>
                            <? endif; ?>
                            <?= $relatedArticle->getTitle() ? htmlspecialchars($relatedArticle->getTitle(), ENT_QUOTES, 'UTF-8') : '제목 없음'; ?>
                            <? $commentCount = $commentCounts[$relatedArticle->getId()] ?? 0; ?>
                            <? if ($commentCount !== 0) : ?>
                                <span class="articles-comment-count">
                                    <?= '[' . $commentCount . ']' ?>
                                </span>
                            <? endif; ?>
                        </div>
                    </a>
                    <?
                    $rquserId = '';
                    if ($relatedArticle->getMember()->getId() === "58") {
                        $rquserId = 'manager';
                    } else {
                        $rquserId = $relatedArticle->getMember()->getId();
                    }
                    ?>
                    <a href="/member/userActivityController/index/<?= $rquserId ?>" class="article-title-link">
                        <div class="related-article-author-area">
                            <?= $relatedArticle->getMember() ? htmlspecialchars($relatedArticle->getMember()->getNickName(), ENT_QUOTES, 'UTF-8') : '작성자 닉네임 없음'; ?>
                        </div>
                    </a>
                    <a href="/article/articledetailcontroller/index/<?= $relatedArticle->getId(); ?>">
                        <div class="related-article-date-area">
                            <?= $relatedArticle->getModifyDate() ? $relatedArticle->getModifyDate()->format('Y.m.d') : $relatedArticle->getCreateDate()->format('Y.m.d'); ?>
                        </div>
                    </a>
                </li>
            </span>
        <? endforeach; ?>
    </ul>
</div>
<div id="relatedArticleTargetPage" data-target-page="<?= $targetPage ?>"></div>
<div class="pagination-and-board-link">
    <div class="paginationbox">
        <div class="pagination">
            <?
            for ($page = 1; $page <= $totalPages; $page++) {
                $isActive = ($page == $currentPage) ? 'active' : '';
                echo '<a href="javascript:void(0);" class="related-article-board-list-page-btn page-btn ' . $isActive . '" data-page="' . $page . '">' . $page . '</a>';
            }
            ?>
        </div>
    </div>
    <div class="board-link">
        <a href="/article/articlelistcontroller/index/<?= $boardId ?>">
            전체보기
        </a>
    </div>
</div>