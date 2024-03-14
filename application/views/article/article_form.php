<?
$GLOBALS['pageResources'] = [
    'css' => ['/assets/css/article/articleEdit.css',],
    'js' => [
        '/assets/js/article/articleEdit.js',
        '/assets/lib/ckeditor5-build-classic/translations/ko.js',
        '/assets/lib/ckeditor5-build-classic/ckeditor.js',
        '/assets/js/article/ckeditor-init.js',
    ]
];
$user = $_SESSION['user_data'];
?>
<section class="section-container">
    <div class="container">

        <? if ($this->session->flashdata('error_messages')) : ?>
            <div class="error-messages">
                <p class="error-alert"><strong>⚠️ 문제가 발생했습니다!</strong></p>
                <? foreach ($this->session->flashdata('error_messages') as $field => $error) : ?>
                    <p class="error-alert"><strong><?= htmlspecialchars($field) ?>:</strong> <?= htmlspecialchars($error) ?></p>
                <? endforeach; ?>
            </div>
        <? endif; ?>
        
        <form action="/article/articleeditcontroller/createArticle" method="POST">
            <div class="writingHeader">
                <h1 class="title">카페 글쓰기</h1>
                <input class="form-btn-box btn-box submit-btn" type="submit" value="등록">
            </div>
            <input type="hidden" name="memberId" value="<?= $user['user_id']; ?>">
            <input type="hidden" name="parentId" value="<?= isset($parentArticle) ? $parentArticle->getId() : ''; ?>">
            <input type="hidden" name="depth" value="<?= isset($parentArticle) ? $parentArticle->getDepth() + 1 : 0; ?>">
            <input type="hidden" name="orderGroup" value="<?= isset($parentArticle) ? $parentArticle->getOrderGroup() : ''; ?>">
            <input type="hidden" id="parentBoardId" value="<?= isset($parentArticle) ? $parentArticle->getArticleBoard()->getId() : ''; ?>">
            <input type="hidden" id="parentPrefix" value="<?= isset($parentArticle) ? $parentArticle->getPrefix() : ''; ?>">
            <div class="editer-box">
                <div class="select-box">
                    <div class="board-select-box">
                        <select id="board-select" name="board" class="custom-input">
                            <option value="">게시판을 선택해 주세요.</option>
                            <? foreach ($boards as $board) : ?>
                                <option value="<?= $board->getId(); ?>"><?= $board->getBoardName(); ?></option>
                            <? endforeach; ?>
                        </select>
                    </div>
                    <div class="prefix-select-box">
                        <select id="prefix-select" name="prefix" class="custom-input">
                            <option value="">말머리 선택</option>
                        </select>
                    </div>
                </div>
                <div class="title-box">
                    <input class="custom-input" id="title" placeholder="제목을 입력해 주세요" required name="title" type="text">
                </div>

                <div class="content-box">
                    <textarea class="article-content-area" name="content" id="contnet" cols="30" rows="10"></textarea>
                </div>

                <div class="public-scope-box">
                    <h3>공개 범위</h3>
                    <label>
                        <input type="radio" name="publicScope" value="public"> 전체공개
                    </label>
                    <label>
                        <input type="radio" name="publicScope" value="members" checked> 멤버공개
                    </label>
                    <?php if ($user['role'] == 'ROLE_ADMIN' || $user['role'] == 'ROLE_MASTER') : ?>
                        <label>
                            <input type="radio" name="publicScope" value="admins"> 관리자공개
                        </label>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>
</section>