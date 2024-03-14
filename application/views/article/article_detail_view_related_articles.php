<? foreach ($relatedArticles as $relatedArticle) : ?>
    <ul>
        <li>
            <div>
                <?= $relatedArticle->getTitle() ? htmlspecialchars($relatedArticle->getTitle(), ENT_QUOTES, 'UTF-8') : '제목 없음'; ?>
            </div>
            <div>
                <?= $relatedArticle->getMember() ? htmlspecialchars($relatedArticle->getMember()->getNickName(), ENT_QUOTES, 'UTF-8') : '작성자 닉네임 없음'; ?>
            </div>
            <div>
                <?= $relatedArticle->getModifyDate() ? $relatedArticle->getModifyDate()->format('Y.m.d H:i') : $relatedArticle->getCreateDate()->format('Y.m.d H:i'); ?>
            </div>
        </li>
    </ul>
<? endforeach; ?>
<div class="pagination-and-board-link">
    <div class="pagination-box">
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
        <a href="/article/articlelistcontroller/index/<?= $relatedArticle->getArticleBoard()->getId() ?>">
            전체보기(해당 게시판으로 가는 링크)
        </a>
    </div>
</div>