<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" type="text/css" href="/assets/css/reset.css">
    <link rel="stylesheet" type="text/css" href="/assets/css/errors.css">
    <link rel="stylesheet" type="text/css" href="/assets/css/home/layout.css">
    <!-- 폰트어썸 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- 제이쿼리 -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- 다음카카오 주소검색API -->
    <script src="//t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>
    <script src="/assets/js/member/address.js"></script>

    <title><?php echo isset($title) ? $title : '비드카페'; ?> | 비드카페</title>
    <link rel="icon" href="data:;base64,iVBORw0KGgo=">

    <script src="/assets/js/home/layout.js"></script>
</head>

<body>

    <header>
        <a href="/">
            <div class="cafe-banner"></div>
        </a>
    </header>

    <main>
        <div class="content-wrap">
            <div class="menu-bar">

                <!-- 카페정보, 나의활동 ~ 카페로그인버튼 영역 -->
                <div class="cafe-info-action">
                    <div class="cafe-details">

                        <ul class="cafe-action-tab">
                            <li class="cafe-info-tab">
                                <button type="button" style="color: #000; font-weight: bold;">카페정보</button>
                            </li>
                            <li class="user-activity-tab">
                                <button type="button" id="userActivityBtn">나의활동</button>
                            </li>
                        </ul>

                        <div class="cafe-info-container">

                            <div class="cafe-info-content">
                                <ul>
                                    <li class="cafe-info-content-ls">
                                        <a class="info-contents-ls-a" href="/">
                                            <img src="https://ssl.pstatic.net/static/cafe/cafe_pc/default/cafe_thumb_noimg_55.png" width="58" height="58" alt="카페아이콘">
                                        </a>
                                    </li>
                                    <li class="cafe-manager">
                                        <a href="/카페매니저의_카페활동내역">
                                            <div class="manager-info">
                                                <div class="manager-name"><?php echo htmlspecialchars($masterNickName, ENT_QUOTES, 'UTF-8'); ?></div>
                                            </div>
                                        </a>
                                        <em class="ico-manager">매니저</em>
                                        <div class="cafe-open-date">
                                            <a href="/카페연혁페이지_선택사항">2024.01.08. 개설</a>
                                        </div>
                                        <div class="cafe-description-link">
                                            <a href="/카페소개페이지_필수사항">카페소개</a>
                                        </div>
                                    </li>
                                </ul>
                            </div>

                            <div class="member-count">
                                <ul>
                                    <li>
                                        <strong>카페멤버수</strong>
                                        <a href="/가입한_회원목록">
                                            <img src="https://ssl.pstatic.net/static/cafe/cafe_pc/svg/ico_member.svg" alt="멤버수">
                                            <em class="cafe-mem-numb">멤버 숫자 표시자리
                                                <!-- 자물쇠 svg아이콘(비공개처리시 사용) -->
                                                <!-- <span>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="19" viewBox="-4 -4 17 19" x="115" y="20"><title>3747584E-313D-42C0-80EB-7145691BB49A</title><defs><path id="o" d="M0 6.324h8.757V0H0v6.324z"/></defs><g fill="none" fill-rule="evenodd" opacity=".6"><g transform="translate(0 3.892)"><mask id="p" fill="#fff"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#o"/></mask><path d="M4.865 3.892h-.973V2.189h.973v1.703zM8.749 0H.009A.008.008 0 0 0 0 .008v6.308c0 .005.004.008.008.008h8.74a.008.008 0 0 0 .009-.008V.008A.008.008 0 0 0 8.749 0z" fill="#575756" mask="url(#p)"/></g><path d="M7.734 4.096h-.73v-.632C7.004 1.979 5.845.753 4.421.73a2.622 2.622 0 0 0-2.668 2.625v.74h-.73v-.74A3.354 3.354 0 0 1 4.433 0c1.82.03 3.3 1.583 3.3 3.464v.632z" fill="#575756"/></g></svg>
                                                </span> -->
                                            </em>
                                        </a>
                                        <a href="/카페초대페이지">카페 초대하기</a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <?php if (isset($_SESSION['user_data']) && $_SESSION['user_data']) : ?>
                            <!-- 로그인 상태일 때 -->
                            <div class="write-cafe">
                                <a href="/article/articlecontroller/write">카페 글쓰기</a>
                            </div>
                            <div class="cafe-logout">
                                <ul>
                                    <li><a href="/member/logincontroller/processLogout">카페 로그아웃</a></li>
                                </ul>
                            </div>
                        <?php else : ?>
                            <!-- 비로그인 상태일 때 -->
                            <div class="join-cafe">
                                <a href="/member/signupcontroller">카페 가입하기</a>
                            </div>
                            <div class="cafe-login">
                                <ul>
                                    <li><a href="/member/logincontroller">카페 로그인</a></li>
                                </ul>
                            </div>
                        <?php endif; ?>

                    </div>

                    <div class="user-activity" style="display:none;">

                        <?php if (isset($_SESSION['user_data'])) : ?>
                            <?php $user = $_SESSION['user_data']; ?>
                            <div class="user-activity">

                                <ul class="cafe-action-tab">
                                    <li class="cafe-info-tab">
                                        <button type="button">카페정보</button>
                                    </li>
                                    <li class="user-activity-tab">
                                        <button type="button" id="userActivityBtn">나의활동</button>
                                    </li>
                                </ul>

                                <div class="activity-summary">

                                    <div class="profile-change">
                                        <ul>
                                            <li class="profile-info">
                                                <div class="profile-thumb">
                                                    <!-- 사용자 프로필 이미지 -->
                                                    <img src="<?php echo $user['memberFilePath']; ?>" width="58" height="58" alt="프로필사진">
                                                </div>
                                                <div class="activity-info">
                                                    <!-- 사용자 닉네임 -->
                                                    <a href="/내_카페_활동내용"><?php echo htmlspecialchars($user['nickName'], ENT_QUOTES, 'UTF-8'); ?></a>
                                                </div>
                                            </li>
                                            <li class="membership-date">
                                                <!-- 사용자 가입 날짜 -->
                                                <em><?php echo $user['create_date']; ?></em> 가입
                                            </li>
                                            <li>
                                                <a href="/마이페이지(내_정보_조회_및_수정)" class="edit-thumb">프로필 변경</a>
                                            </li>
                                        </ul>
                                    </div>

                                    <div class="activity-details">
                                        <ul>
                                            <li class="cafe-member-title">
                                                <strong>회원 등급 :</strong>
                                                <em class="cafe-role">
                                                    <?php if ($user['role'] === 'ROLE_MEMBER') {
                                                        echo '카페멤버';
                                                    } else if ($user['role'] === 'ROLE_ADMIN' || $user['role'] === 'ROLE_MASTER') {
                                                        echo '관리자';
                                                    } ?></em>
                                            </li>
                                            <li class="visit-count">
                                                <strong>
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="20" viewBox="-4 -4 18 20" x="115">
                                                        <path fill="#ADB2B2" fill-rule="evenodd" d="M6.567 1.111A1.672 1.672 0 0 0 5 0c-.722 0-1.333.467-1.567 1.111H0v10h10v-10H6.567zM5 1.111c.306 0 .556.25.556.556 0 .305-.25.555-.556.555a.557.557 0 0 1-.556-.555c0-.306.25-.556.556-.556zm0 2.222c.922 0 1.667.745 1.667 1.667S5.922 6.667 5 6.667A1.664 1.664 0 0 1 3.333 5c0-.922.745-1.667 1.667-1.667zM8.333 10H1.667v-.778C1.667 8.112 3.889 7.5 5 7.5c1.111 0 3.333.611 3.333 1.722V10z" />
                                                    </svg>
                                                    방문</strong>
                                                <em><?php echo $user['visit']; ?>회</em>
                                            </li>
                                            <li class="articles-count">
                                                <strong>
                                                    <a href="/내가_쓴_게시글">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="-4 -4 18 18" x="55" y="108">
                                                            <path fill="#A3A9A9" fill-rule="evenodd" d="M2 2h6v1H2V2zm0 2h6v1H2V4zm0 2h3v1H2V6zm-2 4h10V0H0v10z" />
                                                        </svg>
                                                        내가 쓴 게시글</a>
                                                </strong>
                                                <em><?php echo $articleCount; ?>개</em>
                                            </li>
                                            <li class="comments-count">
                                                <strong>
                                                    <a href="/내가_쓴_댓글">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="-4 -4 18 18" x="115" y="39">
                                                            <defs>
                                                                <path id="c" d="M10 10V0H0v10h10z" />
                                                            </defs>
                                                            <g fill="none" fill-rule="evenodd" opacity=".9">
                                                                <mask id="d" fill="#fff">
                                                                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#c" />
                                                                </mask>
                                                                <path fill="#A3A9A9" d="M0 7V0h10v7H5.023L2.01 10.086 2.024 7H0zm2-5v1h6V2H2zm0 2v1h3V4H2z" mask="url(#d)" />
                                                            </g>
                                                        </svg>
                                                        내가 쓴 댓글</a>
                                                </strong>
                                                <em><?php echo $commentCount; ?>개</em>
                                            </li>
                                        </ul>
                                    </div>

                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['user_data']) && $_SESSION['user_data']) : ?>
                            <!-- 로그인 상태일 때 -->
                            <div class="write-cafe">
                                <a href="/article/articlecontroller/write">카페 글쓰기</a>
                            </div>
                            <div class="cafe-logout">
                                <ul>
                                    <li><a href="/member/logincontroller/processLogout">카페 로그아웃</a></li>
                                </ul>
                            </div>
                        <?php else : ?>
                            <!-- 비로그인 상태일 때 -->
                            <div class="join-cafe">
                                <a href="/member/signupcontroller">카페 가입하기</a>
                            </div>
                            <div class="cafe-login">
                                <ul>
                                    <li><a href="/member/logincontroller">카페 로그인</a></li>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="board-container">
                    <div class="board-header">
                        <div class="favorite-board">
                            <h3>
                                <a href="javascript:void(0);" class="toggle-favorite-board" title="즐겨찾는 게시판">
                                    <span>⭐</span>
                                    즐겨찾는 게시판
                                </a>
                            </h3>
                            <a href="javascript:void(0);" class="toggle-favorite-board" title="즐겨찾는 게시판">
                                <p class="up-and-down-btn"></p>
                            </a>
                        </div>
                        <ul class="board-instructions" style="display:none;">
                            <li>
                                <span>게시판 상단의 아이콘을 클릭하시면 추가됩니다.</span>
                            </li>
                        </ul>
                        <ul class="board-list">
                            <li>
                                <a href="/전체글_보기">📃전체글보기</a>
                                <span class="article-count">37</span>
                            </li>

                            <li class="popular-articles">
                                <a href="/인기글_보기">🔥인기글</a>
                            </li>
                        </ul>

                        <div class="board-group">
                            <h3>
                                <span class="board-group-name" title="소통">소통</span>
                            </h3>
                        </div>

                        <ul class="board-list">
                            <li>
                                <a href="/출석부게시판" class="board-url">
                                    📓출석부
                                </a>
                            </li>
                            <li>
                                <a href="/자유게시판" class="board-url">
                                    📋자유게시판
                                </a>
                            </li>
                            <li>
                                <a href="/건의게시판" class="board-url">
                                    🙋‍♂️건의게시판
                                </a>
                            </li>
                            <li>
                                <a href="/아무말게시판" class="board-url">
                                    👄아무말게시판
                                </a>
                            </li>
                        </ul>
                        <div class="board-group">
                            <h3>
                                <span class="board-group-name" title="개발">개발</span>
                            </h3>
                        </div>
                        <ul class="board-list">
                            <li>
                                <a href="/지식공유게시판" class="board-url">
                                    💡지식공유
                                </a>
                            </li>
                            <li>
                                <a href="/질문_답변게시판" class="board-url">
                                    ❓질문/답변게시판
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <ul class="comment-banner-tag-list">
                    <li class="comment-item">
                        <div class="comment-content">
                            <div class="comment-header">
                                <h4 class="comment-title">
                                    <span>⏲</span>
                                    최근 댓글ㆍ답글
                                </h4>
                            </div>

                            <!-- 최근 댓글, 답글은 php사용해서 each문 돌릴 것 title부분에 답글/댓글 갯수확인도 가져와서 처리-->
                            <!-- 최근댓,답글 페이지네이션 첫번째 페이지 -->
                            <ul class="comment-list">
                                <li class="comment-detail">
                                    <a href="/최근댓글,답글상세보기" class="comment-link" title="답0/댓0">
                                        <ul>
                                            <li class="comment-text">
                                                최근 댓글/답글제목
                                            </li>
                                        </ul>
                                    </a>
                                </li>
                            </ul>

                            <!-- 최근댓,답글 페이지네이션 두번째 페이지 -->
                            <ul class="comment-list">
                                <li class="comment-detail">
                                    <a href="/최근댓글,답글상세보기" class="comment-link" title="답0/댓0">
                                        <ul>
                                            <li class="comment-text">
                                                최근 댓글/답글제목
                                            </li>
                                        </ul>
                                    </a>
                                </li>
                            </ul>

                            <!-- 최근댓,답글 페이지네이션 페이지 컨트롤러 -->
                            <div class="pagination">
                                <span class="pagination-prev">⏪이전</span>
                                <span class="pagination-separator">ㅣ</span>
                                <span class="pagination-next">다음⏩</span>
                            </div>
                        </div>
                    </li>
                    <!-- 카페 배너 자리 -->
                    <a href="/">
                        <li class="banner-item">
                            <div class="banner-content">
                                <strong class="banner-title">인턴프로젝트 카페</strong>
                            </div>
                        </li>
                    </a>

                    <!-- 카페 인기 태그 -->
                    <li class="tag-item">
                        <div class="tag-content">
                            <div class="tag-header">
                                <h4 class="tag-title">🔖카페 인기 태그</h4>
                                <p><a href="/태그페이지" class="tag-more-link">more</a></p>
                            </div>

                            <div class="tag-list-container">
                                <!-- 최근3일간 태그가 없는 경우에는 아래 메시지 띄움 -->
                                <p class="no-tags" style="display:none;">최근 3일간 태그가 없습니다.</p>

                                <!-- 최근 3개월간 태그가 존재하면 아래와 같이 ol로 표시함 -->
                                <!-- php사용해서 each문 돌릴 것 -->
                                <ol class="tag-list">
                                    <li class="tag-detail">
                                        <span class="tag-rank">1.</span>
                                        <a href="/해당태그가들어간게시글목록" class="tag-link" title="태그이름나오는자리">태그이름나오는자리</a>
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
            <!-- 동적으로 삽입될 페이지의 내용 -->
            <?php echo isset($contents) ? $contents : ''; ?>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 비드코칭연구소(주).</p>
    </footer>

</body>

</html>