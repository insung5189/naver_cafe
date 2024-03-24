<!-- book_mark_area.php -->
<? if (!$favoriteBoards) : ?>
    <li class="book-mark-board-none">
        <span>게시판 상단의 아이콘을 클릭하시면 추가됩니다.</span>
    </li>
<? else : ?>
    <? foreach ($favoriteBoards as $favoriteBoard) : ?>
        <?
        $boardName = '';
        $boardId = '';
        if ($favoriteBoard->getArticleBoard()->getId() == 1) {
            $boardName = '📋자유게시판';
            $boardId = 'freeBoardBookMarked';
        } else if ($favoriteBoard->getArticleBoard()->getId() == 2) {
            $boardName = '🙋‍♂️건의게시판';
            $boardId = 'suggestedBoardBookMarked';
        } else if ($favoriteBoard->getArticleBoard()->getId() == 3) {
            $boardName = '👄아무말게시판';
            $boardId = 'wordVomitBoardBookMarked';
        } else if ($favoriteBoard->getArticleBoard()->getId() == 4) {
            $boardName = '💡지식공유';
            $boardId = 'knowledgeSharingBoardBookMarked';
        } else if ($favoriteBoard->getArticleBoard()->getId() == 5) {
            $boardName = '❓질문/답변게시판';
            $boardId = 'qnaBoardBookMarked';
        }
        ?>
        <li class="book-marked-board">
            <a href="/article/articlelistcontroller/index/<?= $favoriteBoard->getArticleBoard()->getId() ?>" class="board-url" id="<?= $boardId ?>" data-board-id="<?= $favoriteBoard->getArticleBoard()->getId() ?>">
                <?= $boardName ?>
            </a>
        </li>
    <? endforeach; ?>
<? endif; ?>