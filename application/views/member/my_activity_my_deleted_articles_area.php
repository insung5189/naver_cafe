<!-- 삭제한 글 -->
<!-- my_activity_my_deleted_articles_area.php -->

<div id="myDeletedArticlesArea">
    <div id="myarticles-deleted-col-table">
        <table>
            <colgroup>
                <col>
                <col style="width:100px">
            </colgroup>
            <thead id="myArticlesDeletedThead">
                <tr class="normalTableTitleCol">
                    <th scope="col">
                        <span class="article-deleted-title-col">제목</span>
                    </th>
                    <th scope="col">
                        <span class="article-deleted-date-col">삭제일</span>
                    </th>
                </tr>
            </thead>
            <tbody id="myarticles-deleted-tbody">
                <? if ($deletedArticlesByPage) : ?>
                    <? foreach ($deletedArticlesByPage as $deletedArticle) : ?>
                        <tr class="normalTableTitleRow">

                            <td scope="col" class="td-article">
                                <div class="deleted-title-list">
                                    <div class="inner-deleted-title-name">
                                        <? if (!empty($deletedArticle->getPrefix())) : ?>
                                            <span class="deleted-article-prefix">[<?= htmlspecialchars($deletedArticle->getPrefix(), ENT_QUOTES, 'UTF-8'); ?>]</span>
                                        <? endif; ?>
                                        <?= $deletedArticle->getTitle() ? htmlspecialchars($deletedArticle->getTitle(), ENT_QUOTES, 'UTF-8') : '제목을 찾을 수 없음'; ?>
                                    </div>
                                </div>
                            </td>

                            <td scope="col" class="td-deleted-date">
                                <span class="article-create-date-row"><?= $deletedArticle->getDeletedDate() ? htmlspecialchars($deletedArticle->getDeletedDate()->format('Y-m-d')) : NULL ?></span>
                            </td>

                        </tr>
                    <? endforeach; ?>
                <? else : ?>
                    <tr>
                        <td colspan="2" class="article-absence">
                            <span>삭제된 게시글이 없습니다.</span>
                        </td>
                    </tr>
                <? endif; ?>
            </tbody>
        </table>
    </div>
    <div class="pagination">
        <?
        $startPage = max(1, $currentPage - 2);
        $endPage = min($totalPages, $currentPage + 2);

        // 첫 페이지로 가기 버튼
        if ($currentPage > 1) {
            echo '<a href="javascript:void(0);" class="page-btn page-start-btn" data-page="1"><i class="fa-solid fa-angles-left"></i></a>';
        }

        // 이전 페이지로 가는 버튼 (현재 페이지가 1페이지가 아닐 경우 항상 표시)
        if ($currentPage > 1) {
            echo '<a href="javascript:void(0);" class="page-btn page-prev-btn" data-page="' . ($currentPage - 1) . '"><i class="fa-solid fa-angle-left"></i></a>';
        }

        for ($page = $startPage; $page <= $endPage; $page++) {
            $isActive = ($page == $currentPage) ? 'active' : '';
            echo '<a href="javascript:void(0);" class="page-btn ' . $isActive . '" data-page="' . $page . '">' . $page . '</a>';
        }

        // 다음 페이지로 가는 버튼 (현재 페이지가 마지막 페이지가 아닐 경우 항상 표시)
        if ($currentPage < $totalPages) {
            echo '<a href="javascript:void(0);" class="page-btn page-next-btn" data-page="' . ($currentPage + 1) . '"><i class="fa-solid fa-angle-right"></i></a>';
        }

        // 마지막 페이지로 가기 버튼
        if ($currentPage < $totalPages) {
            echo '<a href="javascript:void(0);" class="page-btn page-end-btn" data-page="' . $totalPages . '"><i class="fa-solid fa-angles-right"></i></a>';
        }
        ?>
    </div>
</div>