<?
$GLOBALS['pageResources'] = [
    'css' => ['/assets/css/member/myActivity.css'],
    'js' => ['/assets/js/member/myActivity.js']
];
?>
<!-- my_activity.php -->
<section class="section-container">
    <div class="container">
        <h1 class="title">
            <i class="fa-solid fa-file-pen"></i>
            나의 활동
        </h1>
        <p class="page-guide">나의 활동내역을 조회할 수 있습니다.</p>


        <div class="prfl-box">
            <span class="profile-img">

                <?
                $fileUrl = '/assets/file/images/memberImgs/';
                $profileImagePath = ($member->getMemberFileName() === 'default.png') ? 'defaultImg/default.png' : $member->getMemberFileName();
                ?>
                <img class="my-page-image-preview" src="<?= $fileUrl . $profileImagePath; ?>" alt="프로필사진">
                <div data-member-img-src="<?= $fileUrl . $profileImagePath; ?>"></div>
            </span>
            <span class="nick-and-info">
                <div class="nick-area">
                    <span class="nick-name-represent">
                        <?= $member->getNickName(); ?>
                    </span>
                    <?
                    $email = $member->getUserName();
                    list($localPart, $domainPart) = explode('@', $email);
                    $maskedLocalPart = substr($localPart, 0, 3) . str_repeat('*', strlen($localPart) - 3);
                    $domainParts = explode('.', $domainPart);
                    $domainFirstPart = $domainParts[0];
                    $maskedDomainFirstPart = $domainFirstPart[0] . str_repeat('*', strlen($domainFirstPart) - 1);
                    $maskedDomainPart = $maskedDomainFirstPart . '.' . implode('.', array_slice($domainParts, 1));
                    $maskedEmail = $maskedLocalPart . '@' . $maskedDomainPart;
                    ?>
                    <span>
                        <?= '(' . $maskedEmail . ')' ?>
                    </span>
                </div>
                <div class="info-area">
                    <span>방문</span> <em class="count-num"><?= $member->getVisit(); ?></em> <span class="ml-17">작성글</span> <em class="count-num"><?= $articleCount; ?></em>
                </div>
            </span>
        </div>

        <? if (!empty($errors)) : ?>
            <div class="error-messages mb-0">
                <p class="error-alert"><strong>⚠️ 문제가 발생했습니다!</strong></p>
                <? foreach ($errors as $field => $error) : ?>
                    <p class="error-alert"><strong><?= htmlspecialchars($field) ?>:</strong> <?= htmlspecialchars($error) ?></p>
                <? endforeach; ?>
            </div>
        <? endif; ?>

        <div class="content-box">
            <div class="my-activity-list-style">
                <div class="my_activity_sort_area-left">
                    <a href="javascript:void(0);" class="link_sort on underline" id="my_activity_my_articles_area"><span>작성글</span></a>
                    <a href="javascript:void(0);" class="link_sort" id="my_activity_my_comments_area"><span>작성댓글</span></a>
                    <a href="javascript:void(0);" class="link_sort" id="my_activity_my_commented_articles_area"><span>댓글단 글</span></a>
                    <a href="javascript:void(0);" class="link_sort" id="my_activity_my_liked_articles_area"><span>좋아요한 글</span></a>
                </div>
                <div class="my_activity_sort_area-right">
                    <a href="javascript:void(0);" class="link_sort" id="my_activity_my_deleted_articles_area"><span>삭제한 게시글</span></a>
                </div>
            </div>
            <div id="tabContentArea">
                <!-- 작성글 -->
                <!-- my_activity_my_articles_area.php -->
                <div id="myArticlesArea">

                    <div id="myarticles-col-table">
                        <table>
                            <colgroup>
                                <col>
                                <col style="width:118px">
                                <col style="width:80px">
                            </colgroup>
                            <thead id="myArticlesThead">
                                <tr class="normalTableTitleCol">
                                    <th scope="col">
                                        <span class="article-title-col">제목</span>
                                    </th>
                                    <th scope="col">
                                        <span class="article-create-date-col">작성일</span>
                                    </th>
                                    <th scope="col">
                                        <span class="article-hit-col">조회수</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="myarticles-tbody">
                                <? if ($articlesByPage) : ?>
                                    <? foreach ($articlesByPage as $article) : ?>
                                        <?
                                        $parentArticleDeleted = '';
                                        if ($article->getDepth() > 0 && $parentArticlesExist[$article->getId()]) {
                                            $parentArticleDeleted = '';
                                        } else if (!$parentArticlesExist[$article->getId()]) {
                                            $parentArticleDeleted = '[원글이 삭제된 답글]';
                                        } else {
                                            $parentArticleDeleted = '';
                                        }
                                        ?>
                                        <tr class="normalTableTitleRow">

                                            <td scope="col" class="td-article">
                                                <div class="check-box-cell">
                                                    <input id="check-article-<?= $article->getId() ?>" type="checkbox" class="input_check_article">
                                                </div>
                                                <div class="article-num-cell">
                                                    <span>
                                                        <?= $article->getId(); ?>
                                                    </span>
                                                </div>
                                                <div class="title-list">
                                                    <div class="inner-title-name">
                                                        <a href="/article/articledetailcontroller/index/<?= $article->getId(); ?>" target="_blank" class="article-title-link">
                                                            <span class="parent-article-is-deleted">
                                                                <?= $parentArticleDeleted ?>
                                                            </span>
                                                            <? if (!empty($article->getPrefix())) : ?>
                                                                <span class="prefix">[<?= htmlspecialchars($article->getPrefix(), ENT_QUOTES, 'UTF-8'); ?>]</span>
                                                            <? endif; ?>
                                                            <?= $article->getTitle() ? htmlspecialchars($article->getTitle(), ENT_QUOTES, 'UTF-8') : '제목을 찾을 수 없음'; ?>
                                                            <? $commentCountByArticleNum = $commentCountByArticle[$article->getId()] ?? 0; ?>
                                                            <? if ($commentCountByArticleNum !== 0) : ?>
                                                                <span class="articles-comment-count">
                                                                    <?= '[' . $commentCountByArticleNum . ']' ?>
                                                                </span>
                                                            <? endif; ?>
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>

                                            <td scope="col" class="td-create-date">
                                                <span class="article-create-date-row">
                                                    <?= $article->getCreateDate() ? $article->getCreateDate()->format('Y.m.d H:i') : ''; ?>
                                                </span>
                                            </td>

                                            <td scope="col" class="td-hit">
                                                <span class="article-hit-row"><?= $article->getHit() ? htmlspecialchars($article->getHit()) : '조회수 없음'; ?></span>
                                            </td>

                                        </tr>
                                    <? endforeach; ?>
                                <? else : ?>
                                    <tr>
                                        <td colspan="3" class="article-absence">
                                            <span>작성하신 글이 없습니다.</span>
                                        </td>
                                    </tr>
                                <? endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="my-articles-control-box">
                        <div class="all-check-box-btn">
                            <input id="check-article-all-this-page" type="checkbox" class="input-checked-all">
                            <label for="check-article-all-this-page" class="input-checked-all-label">
                                전체 선택
                            </label>
                        </div>
                        <div class="my-articles-delete-and-write-btn">
                            <a href="javascript:void(0);" class="my-articles-delete-btn">삭제</a>
                            <a href="/article/articleEditController" target="_blank" class="my-articles-write-btn">글쓰기</a>
                        </div>
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
            </div>
        </div>
    </div>
</section>