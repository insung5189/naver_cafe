<?
$GLOBALS['pageResources'] = [
    'css' => [
        '/assets/css/article/articleListByBoard.css',
        '/assets/css/errorPage.css'
    ],
    'js' => ['/assets/js/article/articleListByBoard.js']
];
?>

<!-- article_list_by_board.php -->
<? if ($boardId == "1" || $boardId == "2" || $boardId == "3" || $boardId == "4" || $boardId == "5") : ?>
    <section class="section-container" id="articleContent" data-article-board-id="<?= $boardId ?>">
    </section>
<? else : ?>
    <section class="section-container">
        <div class="container">
            <h1 class="title">
                <i class="fa-solid fa-triangle-exclamation"></i> 잘못된 접근입니다.
            </h1>
            <p class="page-guide">죄송합니다. 존재하지 않는 게시판입니다.</p>
            <div class="btn-box flex-end">
                <a href="/" class="btn btn-secondary">메인 페이지로 이동</a>
                <button onclick="window.history.back();" class="btn find-account-btn">뒤로 가기</button>
            </div>
        </div>
    </section>
<? endif; ?>