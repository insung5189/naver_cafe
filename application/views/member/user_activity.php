<?
$GLOBALS['pageResources'] = [
    'css' => ['/assets/css/member/userActivity.css'],
    'js' => ['/assets/js/member/userActivity.js']
];
?>
<!-- user_activity.php -->
<section class="section-container">
    <div class="container">
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
                <div class="my_activity_sort_area-left" data-member-id="<?= $member->getId() ?>">
                    <a href="javascript:void(0);" class="link_sort on underline" id="user_activity_user_articles_area"><span>작성글</span></a>
                    <a href="javascript:void(0);" class="link_sort" id="user_activity_commented_articles_area"><span>댓글단 글</span></a>
                </div>
            </div>
            <div id="tabContentArea">
            </div>
        </div>
    </div>
</section>