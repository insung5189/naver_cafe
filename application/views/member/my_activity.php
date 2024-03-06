<?
$GLOBALS['pageResources'] = [
    'css' => ['/assets/css/member/myActivity.css'],
    'js' => ['/assets/js/member/myActivity.js']
];
?>

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
                    <?= $member->getNickName(); ?>
                    (
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
                    <?= $maskedEmail ?>
                    )
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
                    <a href="javascript:void(0);" class="link_sort on underline" id="linkToMyArticles"><span>작성글</span></a>
                    <a href="javascript:void(0);" class="link_sort" id="linkToMyComments"><span>작성댓글</span></a>
                    <a href="javascript:void(0);" class="link_sort" id="linkToMyCommentedArticles"><span>댓글단 글</span></a>
                    <a href="javascript:void(0);" class="link_sort" id="linkToMyLikedArticles"><span>좋아요한 글</span></a>
                </div>
                <div class="my_activity_sort_area-right">
                    <a href="javascript:void(0);" class="link_sort" id="linkToDeletedArticles"><span>삭제한 게시글</span></a>
                </div>
            </div>
            <div id="myArticlesArea">
                <!-- <h1>작성글영역</h1> -->

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
                            <? foreach ($articles as $article) : ?>
                                <tr class="normalTableTitleRow">

                                    <td scope="col" class="td-article">
                                        <div class="check-box-cell">
                                            <input id="check-article-<?= $article->getId() ?>" type="checkbox" class="input_check">
                                        </div>
                                        <div class="article-num-cell">
                                            <span>
                                                <?= $article->getId(); ?>
                                            </span>
                                        </div>
                                        <div class="title-list">
                                            <div class="inner-title-name">
                                                <a href="/article/articledetailcontroller/index/<?= $article->getId(); ?>" class="article-title-link">
                                                    <? if (!empty($article->getPrefix())) : ?>
                                                        <span class="prefix">[<?= htmlspecialchars($article->getPrefix(), ENT_QUOTES, 'UTF-8'); ?>]</span>
                                                    <? endif; ?>
                                                    <?= $article->getTitle() ? htmlspecialchars($article->getTitle(), ENT_QUOTES, 'UTF-8') : '제목을 찾을 수 없음'; ?>
                                                </a>
                                            </div>
                                        </div>
                                    </td>

                                    <td scope="col" class="td-create-date">
                                        <span class="article-create-date-row"><?= $article->getCreateDate()->format('Y-m-d'); ?></span>
                                    </td>

                                    <td scope="col" class="td-hit">
                                        <span class="article-hit-row"><?= $article->getHit() ? htmlspecialchars($article->getHit()) : '조회수 없음'; ?></span>
                                    </td>

                                </tr>
                            <? endforeach; ?>
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
                        삭제, 글쓰기 버튼
                    </div>
                </div>
                <div class="pagination-box">
                    <?= '<div class="pagination">';
                    for ($page = 1; $page <= $totalPages; $page++) {
                        $isActive = ($page == $currentPage) ? 'active' : '';
                        $link = "/member/myactivitycontroller?"
                            . "&page=$page"
                            . "&articlesPerPage=$articlesPerPage";

                        echo '<a href="' . $link . '" class="' . $isActive . '">' . $page . '</a> ';
                    }
                    echo '</div>';
                    ?>
                </div>
            </div>



















            <div id="myCommentsArea" style="display:none;">
                작성댓글영역
            </div>
            <div id="myCommentedArticlesArea" style="display:none;">
                댓글단글 영역
            </div>
            <div id="myLikedArticlesArea" style="display:none;">
                좋아요한글 영역
            </div>
            <div id="myDeletedArticlesArea" style="display:none;">
                삭제한 게시글 영역
            </div>
        </div>

    </div>
</section>