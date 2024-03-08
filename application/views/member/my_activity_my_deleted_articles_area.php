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
                <? foreach ($deletedArticles as $deletedArticle) : ?>
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
                            <span class="article-create-date-row"><?= $deletedArticle->getDeletedDate()->format('Y-m-d'); ?></span>
                        </td>

                    </tr>
                <? endforeach; ?>
            </tbody>
        </table>
    </div>

</div>