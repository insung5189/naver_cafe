<!-- 좋아요 한 글 -->
<!-- my_activity_my_liked_articles_area.php -->

<div id="myLikedArticlesArea">
    <div id="myliked-articles-col-table">
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
            <tbody id="myliked-articles-tbody">
                <? if ($likeArticlesByPage) : ?>
                    <? foreach ($likeArticlesByPage as $likedArticle) : ?>
                        <tr class="normalTableTitleRow">

                            <td scope="col" class="td-article">
                                <div class="check-box-cell">
                                    <input id="check-liked-article-<?= $likedArticle->getId() ?>" type="checkbox" class="input_check_liked_article">
                                </div>
                                <div class="article-num-cell">
                                    <span>
                                        <?= $likedArticle->getId(); ?>
                                    </span>
                                </div>
                                <div class="title-list">
                                    <div class="inner-title-name">
                                        <a href="/article/articledetailcontroller/index/<?= $likedArticle->getId(); ?>" target="_blank" class="article-title-link">
                                            <? if (!empty($likedArticle->getPrefix())) : ?>
                                                <span class="prefix">[<?= htmlspecialchars($likedArticle->getPrefix(), ENT_QUOTES, 'UTF-8'); ?>]</span>
                                            <? endif; ?>
                                            <?= $likedArticle->getTitle() ? htmlspecialchars($likedArticle->getTitle(), ENT_QUOTES, 'UTF-8') : '제목을 찾을 수 없음'; ?>
                                            <? $commentCountByArticleNum = $commentCountByArticle[$likedArticle->getId()] ?? 0; ?>
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
                                    <?= $likedArticle->getMember() ? htmlspecialchars($likedArticle->getMember()->getNickName()) : '작성자 없음'; ?>
                                </span>
                            </td>

                            <td scope="col" class="td-create-date">
                                <span class="article-create-date-row">
                                    <?= $likedArticle->getModifyDate() ? $likedArticle->getModifyDate()->format('Y.m.d H:i') : $likedArticle->getCreateDate()->format('Y.m.d H:i'); ?>
                                </span>
                            </td>

                            <td scope="col" class="td-hit">
                                <span class="article-hit-row"><?= $likedArticle->getHit() ? htmlspecialchars($likedArticle->getHit()) : '조회수 없음'; ?></span>
                            </td>

                        </tr>
                    <? endforeach; ?>
                <? else : ?>
                    <tr class="normalTableTitleRow">
                        <td scope="col" class="td-article">
                            <span>좋아요 한 게시글이 없습니다.</span>
                        </td>
                    </tr>
                <? endif; ?>
            </tbody>
        </table>
    </div>
    <div class="my-articles-control-box">
        <div class="all-check-box-btn">
            <input id="check-liked-article-all-this-page" type="checkbox" class="input-checked-all">
            <label for="check-liked-article-all-this-page" class="input-checked-all-label">
                전체 선택
            </label>
        </div>
        <div class="my-articles-delete-and-write-btn">
            <a href="javascript:void(0);" class="my-liked-articles-cancel-btn">좋아요 취소</a>
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