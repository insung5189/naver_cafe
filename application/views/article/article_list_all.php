<?
$GLOBALS['pageResources'] = [
    'css' => ['/assets/css/article/articleListAll.css'],
    'js' => ['/assets/js/article/articleListAll.js']
];
?>

<section class="section-container">
    <div class="container">
        <h1 class="title">
            전체글보기
        </h1>


        <div class="list-style">
            <span class="total-article"><span class="total-article-num"><?= isset($totalArticleCount) ? htmlspecialchars($totalArticleCount, ENT_QUOTES, 'UTF-8') : '0'; ?></span>개의 글</span>
            <div class="sort_area">
                <form id="articlesPerPageForm" method="GET">
                    <select class="custom-input" name="articlesPerPage" id="articlesPerPage">
                        <option value="5" <?php echo $articlesPerPage === 5 ? 'selected' : ''; ?>>5개씩</option>
                        <option value="10" <?php echo $articlesPerPage === 10 ? 'selected' : ''; ?>>10개씩</option>
                        <option value="15" <?php echo $articlesPerPage === 15 ? 'selected' : ''; ?>>15개씩</option>
                        <option value="20" <?php echo $articlesPerPage === 20 ? 'selected' : ''; ?>>20개씩</option>
                        <option value="25" <?php echo $articlesPerPage === 25 ? 'selected' : ''; ?>>25개씩</option>
                        <option value="30" <?php echo $articlesPerPage === 30 ? 'selected' : ''; ?>>30개씩</option>
                        <option value="35" <?php echo $articlesPerPage === 35 ? 'selected' : ''; ?>>35개씩</option>
                    </select>
                </form>
            </div>
        </div>
        <div id="article-col-table">
            <table>
                <colgroup>
                    <col style="width:88px">
                    <col>
                    <col style="width:118px">
                    <col style="width:80px">
                    <col style="width:68px">
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
                    <col style="width:118px">
                    <col style="width:80px">
                    <col style="width:68px">
                </colgroup>
                <tbody>
                    <?php foreach ($articles as $article) : ?>
                        <tr class="normalTableTitleRow">

                            <td colspan="2" class="td-article">
                                <div class="board-name">
                                    <div class="inner-board-name">
                                        <a href="/해당게시판 링크" class="board-link"><?= $article->getArticleBoard() ? htmlspecialchars($article->getArticleBoard()->getBoardName(), ENT_QUOTES, 'UTF-8') : '게시판 없음'; ?></a>
                                    </div>
                                </div>

                                <div class="title-list">
                                    <div class="inner-title-name">
                                        <a href="/해당 게시물 링크" class="article-title-link">
                                            <? if (!empty($article->getPrefix())) : ?>
                                                <span class="prefix">[<?= htmlspecialchars($article->getPrefix(), ENT_QUOTES, 'UTF-8'); ?>]</span>
                                            <? endif; ?>
                                            <?= $article->getTitle() ? htmlspecialchars($article->getTitle(), ENT_QUOTES, 'UTF-8') : '제목을 찾을 수 없음'; ?>
                                        </a>
                                    </div>
                                </div>
                            </td>

                            <td scope="col" class="td-author">
                                <div class="author-name">
                                    <a href="/해당 작성자 활동내역" class="author-name-link">
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
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="pagination-box">
            <?
            // 페이지네이션 시작
            echo '<div class="pagination">';

            // 페이지 버튼 생성
            for ($page = 1; $page <= $totalPages; $page++) {
                $isActive = ($page == $currentPage) ? 'active' : '';
                echo '<a href="?page=' . $page . '&articlesPerPage=' . $articlesPerPage . '" class="' . $isActive . '">' . $page . '</a> ';
            }

            // 페이지네이션 종료
            echo '</div>';
            ?>
        </div>
        <div class="search-box">
            <form action="/article/articlelistcontroller/search" method="GET" class="search-form">
                <div class="search-criteria">

                    <select name="period" class="custom-input" id="select-period">
                        <option value="all">전체 기간</option>
                        <option value="1day">1일</option>
                        <option value="1week">1주</option>
                        <option value="6months">6개월</option>
                        <option value="1year">1년</option>
                        <option value="custom">사용자 지정 기간</option>
                    </select>

                    <div class="select-date" style="display:none;">
                        <input type="date" name="startDate" class="date-input" placeholder="시작 날짜" id="start-date">
                        <input type="date" name="endDate" class="date-input" placeholder="종료 날짜" id="end-date">
                    </div>

                    <select name="element" class="custom-input">
                        <option value="all">게시글 + 댓글</option>
                        <option value="title">제목</option>
                        <option value="author">글작성자</option>
                        <option value="comment">댓글내용</option>
                        <option value="commentAuthor">댓글작성자</option>
                    </select>

                </div>

                <div class="search-keyword">
                    <input type="text" name="keyword" placeholder="검색어를 입력하세요" class="custom-input">
                    <button type="submit" class="search-btn">검색</button>
                </div>
            </form>
        </div>
    </div>
</section>