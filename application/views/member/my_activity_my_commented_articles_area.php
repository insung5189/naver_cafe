<!-- 댓글단 글 -->
<!-- my_activity_my_commented_articles_area.php -->

<div id="myCommentedArticlesArea">
    <div id="mycommented-article-col-table">
        <table>
            <colgroup>
                <col>
                <col style="width:120px">
                <col style="width:120px">
                <col style="width:80px">
            </colgroup>
            <thead id="myArticlesThead">
                <tr class="normalTableTitleCol">
                    <th scope="col">
                        <span class="article-title-col">제목</span>
                    </th>
                    <th scope="col">
                        <span class="article-author-col">작성자</span>
                    </th>
                    <th scope="col">
                        <span class="article-create-date-col">작성일</span>
                    </th>
                    <th scope="col">
                        <span class="article-hit-col">조회수</span>
                    </th>
                </tr>
            </thead>
            <tbody id="mycommented-article-tbody">
                <? if ($commentedArticlesByMemberIdAndPage) : ?>
                    <? foreach ($commentedArticlesByMemberIdAndPage as $commentedArticle) : ?>
                        <tr class="normalTableTitleRow">

                            <td scope="col" class="td-article">
                                <div class="article-num-cell">
                                    <span>
                                        <?= $commentedArticle->getId(); ?>
                                    </span>
                                </div>
                                <div class="title-list">
                                    <div class="inner-title-name">
                                        <a href="/article/articledetailcontroller/index/<?= $commentedArticle->getId(); ?>" target="_blank" class="article-title-link">
                                            <? if (!empty($commentedArticle->getPrefix())) : ?>
                                                <span class="prefix">[<?= htmlspecialchars($commentedArticle->getPrefix(), ENT_QUOTES, 'UTF-8'); ?>]</span>
                                            <? endif; ?>
                                            <?= $commentedArticle->getTitle() ? htmlspecialchars($commentedArticle->getTitle(), ENT_QUOTES, 'UTF-8') : '제목을 찾을 수 없음'; ?>
                                            <? $commentCountByArticleNum = $commentCountByArticle[$commentedArticle->getId()] ?? 0; ?>
                                            <? if ($commentCountByArticleNum !== 0) : ?>
                                                <span class="articles-comment-count">
                                                    <?= '[' . $commentCountByArticleNum . ']' ?>
                                                </span>
                                            <? endif; ?>
                                        </a>
                                    </div>
                                </div>
                            </td>

                            <td scope="col" class="td-author">
                                <span class="article-author-row">
                                    <?= $commentedArticle->getMember() ? htmlspecialchars($commentedArticle->getMember()->getNickName()) : '작성자 없음'; ?>
                                </span>
                            </td>

                            <td scope="col" class="td-create-date">
                                <span class="article-create-date-row">
                                    <?= $commentedArticle->getModifyDate() ? $commentedArticle->getModifyDate()->format('Y.m.d H:i') : $commentedArticle->getCreateDate()->format('Y.m.d H:i'); ?>
                                </span>
                            </td>

                            <td scope="col" class="td-hit">
                                <span class="article-hit-row"><?= $commentedArticle->getHit() ? htmlspecialchars($commentedArticle->getHit()) : '조회수 없음'; ?></span>
                            </td>

                        </tr>
                    <? endforeach; ?>
                <? else : ?>
                    <tr>
                        <td colspan="4" class="article-absence">
                            <span>댓글을 남기신 게시글이 없습니다.</span>
                        </td>
                    </tr>
                <? endif; ?>
            </tbody>
        </table>
    </div>
    <div class="my-commented-articles-control-box">
        <div class="my-commented-articles-delete-and-write-btn">
            <a href="/article/articleEditController" target="_blank" class="my-articles-write-btn">글쓰기</a>
        </div>
    </div>
    <div class="pagination">
        <?
        for ($page = 1; $page <= $totalPages; $page++) {
            $isActive = ($page == $currentPage) ? 'active' : '';
            echo '<a href="javascript:void(0);" class="page-btn ' . $isActive . '" data-page="' . $page . '">' . $page . '</a>';
        }
        ?>
    </div>
</div>