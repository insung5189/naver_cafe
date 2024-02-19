<?
$GLOBALS['pageResources'] = [
    'css' => ['/assets/css/home/cafeinfo.css']
];
?>

<section class="section-container">
    <div class="container">
        <h1 class="title">
            <i class="fa-solid fa-circle-info"></i> 카페 소개
        </h1>
        <table class="cafe-info-table">
            <tbody>
                <tr>
                    <th>카페 이름</th>
                    <td>비드코칭연구소 제품완성 미션카페</td>
                </tr>
                <tr>
                    <th>카페 주소</th>
                    <td><a href="http://211.238.132.177/" target="_blank">http://211.238.132.177/</a></td>
                </tr>
                <tr>
                    <th>카페 아이콘</th>
                    <td><img src="https://ssl.pstatic.net/static/cafe/cafe_pc/default/cafe_thumb_noimg_55.png" alt="카페 아이콘"></td>
                </tr>
                <tr>
                    <th>카페 배너</th>
                    <td><a href="/">
                        <li class="banner-item">
                            <div class="banner-content">
                                <strong class="banner-title">인턴프로젝트 카페</strong>
                            </div>
                        </li>
                    </a></td>
                </tr>
                <tr>
                    <th>카페 매니저</th>
                    <td><?php echo htmlspecialchars($masterNickName, ENT_QUOTES, 'UTF-8'); ?></td>
                </tr>
                <tr>
                    <th>카페 설립일</th>
                    <td>Since 2024.02.31</td>
                </tr>
                <tr>
                    <th>주제</th>
                    <td>프로그래밍 언어</td>
                </tr>
                <tr>
                    <th>카페 설명</th>
                    <td>비드코칭연구소 제품완성팀 미션카페입니다.</td>
                </tr>
            </tbody>
        </table>
    </div>
</section>