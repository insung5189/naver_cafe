<?
$GLOBALS['pageResources'] = [
    'css' => ['/assets/css/article/articleListAll.css'],
    'js' => ['/assets/js/article/articleListAll.js']
];
?>

<!-- article_list_all.php -->
<section class="section-container" id="articleContent">
    <!-- article_list_all_content.php -->
    <div class="container">
        <div id="articleIds" data-articles='<?= json_encode($articleIndexIds); ?>' style="display:none;"></div>
        <h1 class="title"><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></h1>

        <? if (!empty($errors)) : ?>
            <div class="error-messages">
                <p class="error-alert"><strong>⚠️ 문제가 발생했습니다!</strong></p>
                <? foreach ($errors as $field => $error) : ?>
                    <p class="error-alert"><strong><?= htmlspecialchars($field) ?>:</strong> <?= htmlspecialchars($error) ?></p>
                <? endforeach; ?>
            </div>
        <? endif; ?>

        <!-- 검색 요약 메시지 표시 -->
        <? if (!empty($keyword) && !empty($period) || !empty($startDate) || !empty($endDate)) : ?>
            <div class="search-summary">
                <?php
                $searchSummary = '검색 조건: ';
                $conditions = [];

                if (!empty($keyword)) {
                    $conditions[] = "'{$keyword}' 키워드로 ";
                }

                if (!empty($element) && $element !== 'all') {
                    switch ($element) {
                        case 'title':
                            $elementText = '제목';
                            break;
                        case 'author':
                            $elementText = '글작성자';
                            break;
                        case 'comment':
                            $elementText = '댓글내용';
                            break;
                        case 'commentAuthor':
                            $elementText = '댓글작성자';
                            break;
                        case 'article-comment':
                            $elementText = '게시글 + 댓글';
                            break;
                        default:
                            $elementText = $element;
                    }
                    $conditions[] = "검색 범위: {$elementText}";
                }

                if (!empty($period) && $period !== 'all') {
                    switch ($period) {
                        case '1day':
                            $periodText = '최근 1일';
                            break;
                        case '1week':
                            $periodText = '최근 1주';
                            break;
                        case '1month':
                            $periodText = '최근 1개월';
                            break;
                        case '6months':
                            $periodText = '최근 6개월';
                            break;
                        case '1year':
                            $periodText = '최근 1년';
                            break;
                        case 'custom':
                            $today = new DateTime();
                            if (empty($startDate) && empty($endDate)) {
                                $periodText = "{$today->format('Y-m-d')}(사용자 지정 기간이 지정되지 않았습니다.)";
                            } else {
                                $startDateText = !empty($startDate) ? $startDate : '오늘';
                                $endDateText = !empty($endDate) ? $endDate : '오늘';
                                $periodText = "{$startDateText}부터 {$endDateText}까지";
                            }
                            break;
                        default:
                            $periodText = $period;
                    }
                    $conditions[] = "기간: {$periodText}";
                }

                $searchSummary .= implode(', ', $conditions);
                echo htmlspecialchars($searchSummary);
                ?>
            </div>
        <? endif; ?>

        <div class="list-style">
            <span class="total-article"><span class="total-article-num"><?= isset($totalArticleCountAll) ? htmlspecialchars($totalArticleCountAll, ENT_QUOTES, 'UTF-8') : '0'; ?></span>개의 글</span>
            <div class="sort_area">
                <select class="custom-input" name="articlesPerPage" id="articlesPerPage">
                    <option value="5" <?= $articlesPerPage == 5 ? 'selected' : ''; ?>>5개씩</option>
                    <option value="10" <?= $articlesPerPage == 10 ? 'selected' : ''; ?>>10개씩</option>
                    <option value="15" <?= $articlesPerPage == 15 ? 'selected' : ''; ?>>15개씩</option>
                    <option value="20" <?= $articlesPerPage == 20 ? 'selected' : ''; ?>>20개씩</option>
                    <option value="25" <?= $articlesPerPage == 25 ? 'selected' : ''; ?>>25개씩</option>
                    <option value="30" <?= $articlesPerPage == 30 ? 'selected' : ''; ?>>30개씩</option>
                    <option value="35" <?= $articlesPerPage == 35 ? 'selected' : ''; ?>>35개씩</option>
                </select>
            </div>
        </div>
        <div id="article-col-table">
            <table>
                <colgroup>
                    <col style="width:88px">
                    <col>
                    <col style="width:132px">
                    <col style="width:96px">
                    <col style="width:71px">
                </colgroup>
                <thead>
                    <tr class="normalTableTitleCol">
                        <th></th>
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
            </table>
        </div>
        <div id="article-row-table">
            <table>
                <colgroup>
                    <col style="width:88px">
                    <col>
                    <col style="width:128px">
                    <col style="width:94px">
                    <col style="width:70px">
                </colgroup>
                <tbody>
                    <? foreach ($articles as $article) : ?>

                        <tr class="normalTableTitleRow">
                            <td colspan="2" class="td-article">

                                <div class="board-name">
                                    <div class="inner-board-name">
                                        <a href="/article/articlelistcontroller/index/<?= $article->getArticleBoard()->getId() ?>" class="board-link"><?= $article->getArticleBoard() ? htmlspecialchars($article->getArticleBoard()->getBoardName(), ENT_QUOTES, 'UTF-8') : '게시판 없음'; ?></a>
                                    </div>
                                </div>

                                <div class="title-list">
                                    <div class="inner-title-name">
                                        <a href="/article/articledetailcontroller/index/<?= $article->getId(); ?>" class="article-title-link">
                                            <? if (!empty($article->getPrefix())) : ?>
                                                <span class="prefix">[<?= htmlspecialchars($article->getPrefix(), ENT_QUOTES, 'UTF-8'); ?>]</span>
                                            <? endif; ?>
                                            <?= $article->getTitle() ? htmlspecialchars($article->getTitle(), ENT_QUOTES, 'UTF-8') : '제목을 찾을 수 없음'; ?>
                                            <? $commentCount = $commentCounts[$article->getId()] ?? 0; ?>
                                            <? if ($commentCount !== 0) : ?>
                                                <span class="articles-comment-count">
                                                    <?= '[' . $commentCount . ']' ?>
                                                </span>
                                            <? endif; ?>
                                        </a>
                                        <? if (isset($childArticles[$article->getOrderGroup()])) : ?>
                                            <a href="javascript:void(0);" class="show-reply" data-article-id="<?= $article->getId(); ?>">
                                                답글 <?= count($childArticles[$article->getOrderGroup()]) ?> <i class="fa-reply-toggle-arrow-<?= $article->getId(); ?> fa fa-caret-down"></i>
                                            </a>
                                        <? endif; ?>
                                    </div>
                                </div>
                            </td>

                            <td scope="col" class="td-author">
                                <div class="author-name">
                                    <?
                                    $rquserId = '';
                                    if ($article->getMember()->getId() === "58") {
                                        $rquserId = 'manager';
                                    } else {
                                        $rquserId = $article->getMember()->getId();
                                    }
                                    ?>
                                    <a href="/member/userActivityController/index/<?= $rquserId ?>" class="author-name-link">
                                        <span class="article-author-row"><?= $article->getMember() ? htmlspecialchars($article->getMember()->getNickName(), ENT_QUOTES, 'UTF-8') : '작성자미상'; ?></span>
                                    </a>
                                </div>
                            </td>

                            <td scope="col" class="td-create-date">
                                <span class="article-create-date-row"><?= $article->getCreateDate()->format('Y-m-d'); ?></span>
                            </td>

                            <td scope="col" class="td-hit">
                                <span class="article-hit-row"><?= $article->getHit() ? htmlspecialchars($article->getHit()) : '조회수 없음'; ?></span>
                            </td>

                        </tr>

                        <? if (isset($childArticles[$article->getOrderGroup()])) : ?>
                            <? foreach ($childArticles[$article->getOrderGroup()] as $childArticle) : ?>
                                <?
                                $styleAttributes = '';
                                $parentArticleDeleted = '';
                                $leftBottomEdge = '';
                                $paddingVal = 0;
                                if ($childArticle->getDepth() > 0 && $parentArticlesExist[$childArticle->getId()]) {
                                    $leftBottomEdge = '┗';
                                    $parentArticleDeleted = '';
                                    $paddingVal = $childArticle->getDepth() * 12;
                                    $styleAttributes = 'style="padding-left:' . $paddingVal . 'px;"';
                                } else if (!$parentArticlesExist[$childArticle->getId()]) {
                                    $leftBottomEdge = '';
                                    $parentArticleDeleted = '[원글이 삭제된 답글]';
                                    $paddingVal = 0;
                                    $styleAttributes = 'style="padding-left:' . $paddingVal . 'px;"';
                                } else {
                                    $parentArticleDeleted = '';
                                    $leftBottomEdge = '';
                                    $paddingVal = 0;
                                    $styleAttributes = '';
                                }
                                ?>
                                <tr class="normalTableTitleRow childTableTitleRow replyToggle-<?= $article->getId(); ?>" style="display:none;">
                                    <td colspan="2" class="td-article">

                                        <div class="board-name">
                                            <div class="inner-board-name">
                                            </div>
                                        </div>

                                        <div class="title-list">
                                            <div class="inner-title-name">
                                                <a href="/article/articledetailcontroller/index/<?= $childArticle->getId(); ?>" class="article-title-link" <?= $styleAttributes ?>>
                                                    <span class="left-bottom-edge"><?= $leftBottomEdge ?></span>
                                                    <span class="parent-article-is-deleted">
                                                        <?= $parentArticleDeleted ?>
                                                    </span>
                                                    <? if (!empty($childArticle->getPrefix())) : ?>
                                                        <span class="prefix">[<?= htmlspecialchars($childArticle->getPrefix(), ENT_QUOTES, 'UTF-8'); ?>]</span>
                                                    <? endif; ?>
                                                    <?= $childArticle->getTitle() ? htmlspecialchars($childArticle->getTitle(), ENT_QUOTES, 'UTF-8') : '제목을 찾을 수 없음'; ?>
                                                    <? $commentCount = $commentCounts[$childArticle->getId()] ?? 0; ?>
                                                    <? if ($commentCount !== 0) : ?>
                                                        <span class="articles-comment-count">
                                                            <?= '[' . $commentCount . ']' ?>
                                                        </span>
                                                    <? endif; ?>
                                                </a>
                                            </div>
                                        </div>
                                    </td>

                                    <td scope="col" class="td-author">
                                        <div class="author-name">
                                            <a href="/member/userActivityController/index/<?= $rquserId ?>" class="author-name-link">
                                                <span class="article-author-row"><?= $childArticle->getMember() ? htmlspecialchars($childArticle->getMember()->getNickName(), ENT_QUOTES, 'UTF-8') : '작성자미상'; ?></span>
                                            </a>
                                        </div>
                                    </td>

                                    <td scope="col" class="td-create-date">
                                        <span class="article-create-date-row"><?= $childArticle->getCreateDate()->format('Y-m-d'); ?></span>
                                    </td>

                                    <td scope="col" class="td-hit">
                                        <span class="article-hit-row"><?= $childArticle->getHit() ? htmlspecialchars($childArticle->getHit()) : '조회수 없음'; ?></span>
                                    </td>

                                </tr>
                            <? endforeach; ?>
                        <? endif; ?>
                    <? endforeach; ?>
                </tbody>
            </table>
        </div>
        <input type="hidden" id="currentPage" name="currentPage" value="<?= $currentPage ?? 1; ?>">
        <div class="pagination-box">
            <div class="pagination">
                <?
                $startPage = max(1, $currentPage - 2);
                $endPage = min($totalPages, $currentPage + 2);

                // 첫 페이지로 가기 버튼
                if ($currentPage > 1) {
                    echo '<a href="javascript:void(0);" class="article-list-all-page-btn page-btn page-start-btn" data-page="1"><i class="fa-solid fa-angles-left"></i></a>';
                }

                // 이전 페이지로 가는 버튼 (현재 페이지가 1페이지가 아닐 경우 항상 표시)
                if ($currentPage > 1) {
                    echo '<a href="javascript:void(0);" class="article-list-all-page-btn page-btn page-prev-btn" data-page="' . ($currentPage - 1) . '"><i class="fa-solid fa-angle-left"></i></a>';
                }

                for ($page = $startPage; $page <= $endPage; $page++) {
                    $isActive = ($page == $currentPage) ? 'active' : '';
                    echo '<a href="javascript:void(0);" class="article-list-all-page-btn page-btn ' . $isActive . '" data-page="' . $page . '">' . $page . '</a>';
                }

                // 다음 페이지로 가는 버튼 (현재 페이지가 마지막 페이지가 아닐 경우 항상 표시)
                if ($currentPage < $totalPages) {
                    echo '<a href="javascript:void(0);" class="article-list-all-page-btn page-btn page-next-btn" data-page="' . ($currentPage + 1) . '"><i class="fa-solid fa-angle-right"></i></a>';
                }

                // 마지막 페이지로 가기 버튼
                if ($currentPage < $totalPages) {
                    echo '<a href="javascript:void(0);" class="article-list-all-page-btn page-btn page-end-btn" data-page="' . $totalPages . '"><i class="fa-solid fa-angles-right"></i></a>';
                }
                ?>
            </div>
        </div>
        <div class="search-box">
            <form id="searchForm" class="search-form">
                <div class="search-criteria">

                    <select name="period" class="custom-input" id="select-period">
                        <option value="all" <?= ($period === 'all') ? 'selected' : ''; ?>>전체 기간</option>
                        <option value="1day" <?= ($period === '1day') ? 'selected' : ''; ?>>1일</option>
                        <option value="1week" <?= ($period === '1week') ? 'selected' : ''; ?>>1주</option>
                        <option value="6months" <?= ($period === '6months') ? 'selected' : ''; ?>>6개월</option>
                        <option value="1year" <?= ($period === '1year') ? 'selected' : ''; ?>>1년</option>
                        <option value="3years" <?= ($period === '3years') ? 'selected' : ''; ?>>3년</option>
                        <option value="5years" <?= ($period === '5years') ? 'selected' : ''; ?>>5년</option>
                        <option value="custom" <?= ($period === 'custom') ? 'selected' : ''; ?>>사용자 지정 기간</option>
                    </select>

                    <div class="select-date" style="display:none;">
                        <input type="date" name="startDate" class="date-input" placeholder="시작 날짜" id="start-date" value="<?= htmlspecialchars($startDate); ?>">
                        <input type="date" name="endDate" class="date-input" placeholder="종료 날짜" id="end-date" value="<?= htmlspecialchars($endDate); ?>">
                    </div>

                    <select name="element" class="custom-input" id="element">
                        <option value="article-comment" <?= ($element === 'article-comment') ? 'selected' : ''; ?>>게시글 + 댓글</option>
                        <option value="title" <?= ($element === 'title') ? 'selected' : ''; ?>>제목</option>
                        <option value="author" <?= ($element === 'author') ? 'selected' : ''; ?>>글작성자</option>
                        <option value="comment" <?= ($element === 'comment') ? 'selected' : ''; ?>>댓글내용</option>
                        <option value="commentAuthor" <?= ($element === 'commentAuthor') ? 'selected' : ''; ?>>댓글작성자</option>
                    </select>

                </div>

                <div class="search-keyword">
                    <input type="text" name="keyword" id="keyword" placeholder="검색어를 입력하세요" class="custom-input" value="<?= htmlspecialchars($keyword); ?>" required>
                    <button type="submit" class="search-btn">검색</button>
                </div>

            </form>
        </div>
    </div>
</section>