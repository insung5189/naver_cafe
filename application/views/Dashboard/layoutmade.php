<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" type="text/css" href="/assets/css/reset.css">
    <link rel="stylesheet" type="text/css" href="/assets/css/errors.css">
    <link rel="stylesheet" type="text/css" href="/assets/css/home/layoutmade.css">

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

    </header>

    <main>
        <div class="content-wrap">
            <div class="menu-bar">

                <!-- 카페정보, 나의활동 ~ 카페로그인버튼 영역 -->
                <div class="cafe-info-action">
                    <div class="cafe-details">

                        <ul class="cafe-action-tab">
                            <li class="cafe-info-tab">
                                <button type="button">카페정보</button>
                            </li>
                            <li class="user-activity-tab">
                                <button type="button">나의활동</button>
                            </li>
                        </ul>

                        <div class="cafe-info-container">
                            <!-- 현재 여기 작업 중(24-02-15) -->
                            <div class="cafe-info-content">
                                <ul>
                                    <li class="info-content-ls">
                                        <a href="/">
                                            <img src="https://ssl.pstatic.net/static/cafe/cafe_pc/default/cafe_thumb_noimg_55.png" width="58" height="58" alt="카페아이콘">
                                        </a>
                                    </li>
                                    <li class="cafe-manager">
                                        <a href="/카페매니저의_카페활동내역">
                                            <div class="manager-info">
                                                <div class="manager-name">bid</div>
                                            </div>
                                        </a>
                                        <em class="role">매니저</em>
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
                                            <em>4</em>
                                        </a>
                                        <a href="/카페초대페이지">카페 초대하기</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="join-cafe">
                            <a href="/member/signupcontroller">카페 가입하기</a>
                        </div>

                        <div class="cafe-login">
                            <ul>
                                <li><a href="/member/logincontroller">카페 로그인</a></li>
                            </ul>
                        </div>
                    </div>

                    <div class="user-activity">

                        <ul class="cafe-action-tab">
                            <li class="cafe-info-tab">
                                <button type="button">카페정보</button>
                            </li>
                            <li class="user-activity-tab">
                                <button type="button">나의활동</button>
                            </li>
                        </ul>

                        <div class="activity-summary">

                            <div class="profile-change">
                                <ul>
                                    <li class="profile-info">
                                        <a href="/마이페이지(내_정보_조회_및_수정)">프로필 변경하기</a>
                                        <img src="https://cafeptthumb-phinf.pstatic.net/MjAyNDAyMDFfMjUg/MDAxNzA2NzcxMzQyNTQ0._aOpVDxESncEppxtDyzDcgTB5uwIU0-i30IzNtS4GTcg.UwQMbHxxSO1F4xOBoXenWli-XYU8n_56WCahwt_lyZAg.JPEG/237-900x1350.jpg?type=s70" width="58" height="58" alt="프로필사진">
                                    </li>
                                    <li class="membership-date"><em>2024.01.16.</em> 가입</li>
                                </ul>
                            </div>

                            <div class="activity-details">
                                <ul>
                                    <li class="cafe-member-title">카페 멤버</li>
                                    <li class="visit-count">
                                        <strong>방문</strong>
                                        <em>70회</em>
                                    </li>
                                    <li class="posts-count">
                                        <a href="/내가_쓴_게시글">내가 쓴 게시글</a>
                                        <em>13개</em>
                                    </li>
                                    <li class="comments-count">
                                        <a href="/내가_쓴_댓글">내가 쓴 댓글</a>
                                        <em>10개</em>
                                    </li>
                                </ul>
                            </div>

                        </div>

                        <div class="write-post-link">
                            <a href="/카페_글쓰기">카페 글쓰기</a>
                        </div>

                        <div class="cafe-login">
                            <ul>
                                <li><a href="/member/logincontroller">카페 로그인</a></li>
                            </ul>
                        </div>
                    </div>
                </div>



                <div class="board-container">
                    <div class="board-header">
                        <div class="favorite-board">
                            <h3>
                                <img src="https://cafe.pstatic.net/cafe4/hidden.gif" width="12" height="12" alt="즐겨찾는 게시판">
                                <a href="/즐겨찾는_게시판" title="즐겨찾는 게시판">즐겨찾는 게시판</a>
                            </h3>
                            <p class="up-btn">
                                <a href="/즐겨찾는게시판_열기/닫기"><img width="13" height="13" alt="열기/닫기" src="https://cafe.pstatic.net/cafe4/hidden.gif"></a>
                            </p>
                        </div>
                        <ul class="board-instructions">
                            <li>
                                <p>게시판 상단의 <img src="https://cafe.pstatic.net/cafe4/hidden.gif" width="12" height="12" alt="Icon">아이콘을 클릭하시면 추가됩니다.</p>
                            </li>
                        </ul>
                        <ul class="board-list">
                            <li>
                                <img src="https://cafe.pstatic.net/cafe4/hidden.gif" width="10" height="11" alt="전체글보기"><a href="/전체글_보기">전체글보기</a>
                                <span class="post-count">37</span>
                            </li>

                            <li class="popular-posts">
                                <img src="https://cafe.pstatic.net/cafe4/hidden.gif" width="10" height="11" alt="Popular Posts Icon">
                                <a href="/인기글_보기">인기글</a>
                                <div class="popular-posts-tooltip" style="display: none;">
                                    <svg width="9" height="5" viewBox="0 0 9 5" fill="none">
                                        <g>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M6.26323 0.888892L9 5L0 5L2.94212 0.84291C3.57055 -0.0450258 4.81637 -0.266868 5.72476 0.347412C5.93854 0.491982 6.12145 0.675906 6.26323 0.888892Z" fill="currentColor"></path>
                                        </g>
                                    </svg>
                                    <p>우리카페 <strong>인기글</strong>을 확인해보세요!<button type="button" class="btn-close"><img src="https://ssl.pstatic.net/static/cafe/cafe_pc/svg/ico-tooltip-close.svg" alt="닫기"></button></p>
                                </div>
                            </li>
                        </ul>

                        <div class="board-group">
                            <h3><span class="board-group-name" title="소통">소통</span></h3>
                        </div>

                        <ul class="board-list">
                            <li>
                                <img src="https://cafe.pstatic.net/cafe4/hidden.gif" width="10" height="11" class="board-ico" alt="">
                                <a href="/출석부게시판" class="board-url">
                                    출석부
                                </a>
                            </li>
                            <li>
                                <img src="https://cafe.pstatic.net/cafe4/hidden.gif" width="10" height="11" class="board-ico" alt="">
                                <a href="/자유게시판" class="board-url">
                                    자유게시판
                                </a>
                            </li>
                            <li>
                                <img src="https://cafe.pstatic.net/cafe4/hidden.gif" width="10" height="11" class="board-ico" alt="">
                                <a href="/건의게시판" class="board-url">
                                    건의게시판
                                </a>
                            </li>
                            <li>
                                <img src="https://cafe.pstatic.net/cafe4/hidden.gif" width="10" height="11" class="board-ico" alt="">
                                <a href="/아무말게시판" class="board-url">
                                    아무말게시판
                                </a>
                            </li>
                        </ul>
                        <div class="board-group">
                            <h3><span class="board-group-name" title="개발">개발</span></h3>
                        </div>
                        <ul class="board-list">
                            <li>
                                <img src="https://cafe.pstatic.net/cafe4/hidden.gif" width="10" height="11" class="board-ico" alt="">
                                <a href="/지식공유게시판" class="board-url">
                                    지식공유
                                </a>
                            </li>
                            <li>
                                <img src="https://cafe.pstatic.net/cafe4/hidden.gif" width="10" height="11" class="board-ico" alt="">
                                <a href="/질문_답변게시판" class="board-url">
                                    질문/답변게시판
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <ul class="comment-banner-tag-list">
                    <li class="comment-item">
                        <div class="comment-content">
                            <div class="comment-header">
                                <h4 class="comment-title">최근 댓글ㆍ답글</h4>
                            </div>

                            <!-- 최근 댓글, 답글은 php사용해서 each문 돌릴 것 title부분에 답글/댓글 갯수확인도 가져와서 처리-->
                            <!-- 최근댓,답글 페이지네이션 첫번째 페이지 -->
                            <ul class="comment-list">
                                <li class="comment-detail">
                                    <div class="comment-icon"><img src="https://ssl.pstatic.net/static/cafe/cafe_pc/ico-blank.png" width="3" height="3" alt=""></div>
                                    <a href="/최근댓글,답글상세보기" class="comment-link" title="답0/댓0">
                                        <div class="comment-text">최근 댓글/답글제목</div>
                                    </a>
                                </li>
                            </ul>

                            <!-- 최근댓,답글 페이지네이션 두번째 페이지 -->
                            <ul class="comment-list">
                                <li class="comment-detail">
                                    <div class="comment-icon"><img src="https://ssl.pstatic.net/static/cafe/cafe_pc/ico-blank.png" width="3" height="3" alt=""></div>
                                    <a href="/최근댓글,답글상세보기" class="comment-link" title="답0/댓0">
                                        <div class="comment-text">최근 댓글/답글제목</div>
                                    </a>
                                </li>
                            </ul>

                            <!-- 최근댓,답글 페이지네이션 페이지 컨트롤러 -->
                            <div class="pagination">
                                <span class="pagination-prev">이전</span>
                                <span class="pagination-separator">ㅣ</span>
                                <span class="pagination-next">다음</span>
                            </div>
                        </div>
                    </li>
                    <!-- 카페 배너 자리 -->
                    <li class="banner-item">
                        <div class="banner-content">
                            <strong class="banner-title">인턴프로젝트 카페</strong>
                        </div>
                    </li>

                    <!-- 카페 인기 태그 -->
                    <li class="tag-item">
                        <div class="tag-content">
                            <div class="tag-header">
                                <h4 class="tag-title">카페 인기 태그</h4>
                                <p><a href="/태그페이지" class="tag-more-link">more</a></p>
                            </div>

                            <div class="tag-list-container">
                                <!-- 최근3일간 태그가 없는 경우에는 아래 메시지 띄움 -->
                                <p class="no-tags">최근 3일간 태그가 없습니다.</p>

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