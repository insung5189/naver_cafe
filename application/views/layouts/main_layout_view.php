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
</head>

<body>

    <header>
        <div class="cafe-banner">
            <a href="/">
                <img src="https://i.imgur.com/ohX8mhO.png" alt="카페 배너">
            </a>
        </div>
    </header>

    <main>
        <div id="content-area">
            <div id="group-area" class="skin-1080 fl">

                <!-- 카페정보, 나의활동 ~ 카페로그인버튼 영역 -->
                <div class="cafe-info-action" id="cafe-info-action">
                    <!-- 카페정보 영역 -->
                    <div id="cafe-info-data">
                        <ul class="info-action-tab">
                            <li class="tit-info-on">
                                <button class="gm-tcol-t" type="button">카페정보</button>
                            </li>
                            <li class="tit-action">
                                <button type="button">나의활동</button>
                            </li>
                        </ul>
                        <div class="box-g">
                            <h4 class="d-none">카페정보</h4>
                            <div class="ia-info-data" id="ia-info-data">
                                <ul>
                                    <li class="gm-tcol-c">
                                        <a href="/">
                                            <img src="https://ssl.pstatic.net/static/cafe/cafe_pc/default/cafe_thumb_noimg_55.png" width="58" height="58" alt="카페아이콘">
                                            <span class="border mask_white"></span>
                                        </a>
                                    </li>
                                    <li class="gm-tcol-c">
                                        <a href="/카페매니저의_카페활동내역" target="cafe_main" class="id mlink gm-tcol-c">
                                            <div class="ellipsis gm-tcol-c">
                                                <div class="ellipsis">bid</div>
                                            </div>
                                        </a>
                                        <em class="ico-manager">매니저</em>
                                        <div class="thm">
                                            <a href="/카페연혁페이지_선택사항" class="gm-tcol-c">2024.01.08. 개설</a>
                                        </div>
                                        <div class="info-view">
                                            <a href="/카페소개페이지_필수사항" class="u gm-tcol-c">카페소개</a>
                                        </div>
                                    </li>
                                </ul>
                            </div>

                            <div class="ia-info-data2">
                                <ul>
                                    <li class="mem-cnt-info" style="cursor:pointer;">
                                        <strong class="d-none">카페멤버수</strong>
                                        <a href="/가입한_회원목록">
                                            <img src="https://ssl.pstatic.net/static/cafe/cafe_pc/svg/ico_member.svg" alt="멤버수">
                                            <em>4<span class="ico_lock2"></span></em>
                                        </a>
                                        <!-- <a href="#" class="btn_close"><span class="blind">닫기</span></a> -->

                                        <!-- 관리자영역(가입회원관리 엘리먼트), 추후 개선예정 -->
                                        <!-- <div class="layer_hint" style="display: none" id="hiddenCafeApplyLayer">
                                            <p class="txt"><a href="#">가입대기 0</a></p>
                                            <button type="button" class="btn_close"><span class="blind">닫기</span></button>
                                        </div> -->

                                        <!-- 관리자영역(멤버목록공개여부 엘리먼트), 추후 개선예정 -->
                                        <!-- <div class="layer_hint" style="display: none;" id="hiddenOpenMemberInfoLayer">
                                            <p class="txt">멤버목록 비공개 카페</p>
                                            <button type="button" class="btn_close"><span class="blind">닫기</span></button>
                                        </div> -->

                                        <a href="/카페초대페이지">카페 초대하기</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="cafe-write-btn">
                            <a href="/member/signupcontroller">카페 가입하기</a>
                        </div>

                        <div class="ia-info-btn">
                            <ul class="ia-info-list">
                                <li><a href="/member/logincontroller" class="login">카페 로그인</a></li>
                            </ul>
                        </div>
                    </div>

                    <!-- 나의활동 영역 -->
                    <div id="member-action-data">
                        <ul class="info-action-tab">
                            <li class="tit-info">
                                <button type="button">카페정보</button>
                            </li>
                            <li class="tit-action-on">
                                <button class="gm-tcol-t" type="button">나의활동</button>
                            </li>
                        </ul>
                        <div class="box-g">
                            <h4 class="d-none">나의활동</h4>
                            <div id="ia-action-data">
                                <div class="ia-action-data">
                                    <ul>
                                        <li title="인성" class="name gm-tcol-c">
                                            <div class="prfl_thmb">
                                                <a href="/마이페이지(내_정보_조회_및_수정)" class="lab_thmb">프로필 변경하기</a>
                                                <img src="https://cafeptthumb-phinf.pstatic.net/MjAyNDAyMDFfMjUg/MDAxNzA2NzcxMzQyNTQ0._aOpVDxESncEppxtDyzDcgTB5uwIU0-i30IzNtS4GTcg.UwQMbHxxSO1F4xOBoXenWli-XYU8n_56WCahwt_lyZAg.JPEG/237-900x1350.jpg?type=s70" width="58" height="58" alt="프로필사진">
                                                <div class="mask"></div>
                                            </div>
                                            <div class="prfl_info"><a href="/내_카페_활동내용" target="cafe_main">인성</a></div>
                                        </li>
                                        <li class="date gm-tcol-c"><em>2024.01.16.</em> 가입</li>
                                    </ul>
                                </div>
                                <div class="ia-info-data3">
                                    <ul class="gm-tcol-c">
                                        <li class="info grade" title="카페 멤버">
                                            <div class="ellipsis">카페 멤버</div>
                                        </li>
                                        <li class="info">
                                            <span class="tit"><span class="ico_vst"></span><strong class="gm-tcol-c">방문</strong></span>
                                            <em class="gm-tcol-c">70<span>회</span></em>
                                        </li>
                                        <li class="info2">
                                            <span class="tit"><span class="ico_wrt"></span><strong class="gm-tcol-c"><a href="/내가_쓴_게시글" target="cafe_main" class="gm-tcol-c">내가 쓴 게시글</a></strong></span>
                                            <em><a href="/내가_쓴_게시글" target="cafe_main" class="gm-tcol-c">13</a><span>개</span></em>
                                        </li>
                                        <li class="info3">
                                            <span class="tit"><span class="ico_cmt"></span><strong class="gm-tcol-c"><a href="/내가_쓴_댓글" target="cafe_main" class="gm-tcol-c">내가 쓴 댓글</a></strong></span>
                                            <em><a href="/내가_쓴_댓글" target="cafe_main" class="gm-tcol-c">10</a><span>개</span></em>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="cafe-write-btn">
                            <a href="/카페_글쓰기">카페 글쓰기</a>
                        </div>

                        <div class="ia-info-btn">
                            <ul class="ia-info-list">
                                <li><a href="/member/logincontroller" class="login">카페 로그인</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div id="cafe-menu">
                    <div class="box-g-t"></div>
                    <div class="box-g-m">
                        <div class="cafe-menu-tit frst">
                            <h3><img src="https://cafe.pstatic.net/cafe4/hidden.gif" width="12" height="12" class="ico-bookmark" alt="">
                                <a href="/즐겨찾는_게시판" title="즐겨찾는 게시판" class="gm-tcol-t">즐겨찾는 게시판</a>
                            </h3>
                            <!-- <p class="down-btn">
                                <a href="/즐겨찾는게시판_열기"><img width="13" height="13" alt="열기/닫기" src="https://cafe.pstatic.net/cafe4/hidden.gif"></a>
                            </p> -->
                            <p class="up-btn">
                                <a href="/즐겨찾는게시판_닫기"><img width="13" height="13" alt="열기/닫기" src="https://cafe.pstatic.net/cafe4/hidden.gif"></a>
                            </p>
                        </div>
                        <ul id="favoriteMenuGroup" style="display: block;" class="cafe-menu-list">
                            <li>
                                <p class="bmk_noti gm-tcol-c">게시판 상단의 <img src="https://cafe.pstatic.net/cafe4/hidden.gif" width="12" height="12" class="ico-bookmark2">아이콘을<br> 클릭하시면 추가됩니다.</p>
                            </li>
                        </ul>
                        <div class="cafe-menu-space"></div>
                        <ul class="cafe-menu-list">
                            <li>
                                <img src="https://cafe.pstatic.net/cafe4/hidden.gif" width="10" height="11" class="ico-list" alt=""><a href="/전체글_보기" target="cafe_main" class="gm-tcol-c" id="menuLink0">전체글보기</a>
                                <span class="gm-tcol-c total">37</span>
                            </li>

                            <li>
                                <img src="https://cafe.pstatic.net/cafe4/hidden.gif" width="10" height="11" class="ico-hot" alt="">
                                <a href="/인기글_보기" target="cafe_main" class="gm-tcol-c" id="menuLink-7">
                                    인기글
                                </a>
                                <div class="tooltip_layer" style="display: none;">
                                    <svg width="9" height="5" viewBox="0 0 9 5" fill="none" xmlns="http://www.w3.org/2000/svg" class="coachmark_arrow_up">
                                        <g id="coachmark_arrow_up">
                                            <path id="Vector" fill-rule="evenodd" clip-rule="evenodd" d="M6.26323 0.888892L9 5L0 5L2.94212 0.84291C3.57055 -0.0450258 4.81637 -0.266868 5.72476 0.347412C5.93854 0.491982 6.12145 0.675906 6.26323 0.888892Z" fill="currentColor"></path>
                                        </g>
                                    </svg>
                                    <p class="txt">
                                        <img src="https://ssl.pstatic.net/static/cafe/cafe_pc/svg/ico-tooltip-hot.svg" class="ico_tooltip_hot" alt="">우리카페 <strong>인기글</strong>을 확인해보세요!<button type="button" class="btn_close" onclick="closePopularArticleTooltip()"><img src="https://ssl.pstatic.net/static/cafe/cafe_pc/svg/ico-tooltip-close.svg" alt="닫기"></button>
                                    </p>
                                </div>
                            </li>
                        </ul>

                        <div class="cafe-menu-tit">
                            <h3><span class="gm-tcol-t ellipsis" title="소통">소통</span></h3>
                        </div>

                        <ul class="cafe-menu-list" id="group2">
                            <li>
                                <img src="https://cafe.pstatic.net/cafe4/hidden.gif" width="10" height="11" class="ico-attendance" alt="">
                                <a href="/출석부게시판" target="cafe_main" class="gm-tcol-c" id="menuLink4">
                                    출석부
                                </a>
                            </li>
                            <li>
                                <img src="https://cafe.pstatic.net/cafe4/hidden.gif" width="10" height="11" class="ico-list" alt="">
                                <a href="/자유게시판" target="cafe_main" class="gm-tcol-c" id="menuLink1">
                                    자유게시판
                                </a>
                            </li>
                            <li>
                                <img src="https://cafe.pstatic.net/cafe4/hidden.gif" width="10" height="11" class="ico-list" alt="">
                                <a href="/건의게시판" target="cafe_main" class="gm-tcol-c" id="menuLink6">
                                    건의게시판
                                </a>
                            </li>
                            <li>
                                <img src="https://cafe.pstatic.net/cafe4/hidden.gif" width="10" height="11" class="ico-simple" alt="">
                                <a href="/아무말게시판" target="cafe_main" class="gm-tcol-c" id="menuLink5">
                                    아무말게시판
                                </a>
                            </li>
                        </ul>
                        <div class="cafe-menu-tit">
                            <h3><span class="gm-tcol-t ellipsis" title="개발">개발</span></h3>
                        </div>
                        <ul class="cafe-menu-list" id="group3">
                            <li>
                                <img src="https://cafe.pstatic.net/cafe4/hidden.gif" width="10" height="11" class="ico-list" alt="">
                                <a href="/지식공유게시판" target="cafe_main" class="gm-tcol-c" id="menuLink7">
                                    지식공유
                                </a>
                            </li>
                            <li>
                                <img src="https://cafe.pstatic.net/cafe4/hidden.gif" width="10" height="11" class="ico-list" alt="">
                                <a href="/질문_답변게시판" target="cafe_main" class="gm-tcol-c" id="menuLink8">
                                    질문/답변게시판
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="box-g-b"></div>
                </div>
                <ul class="com">
                    <li class="box-w">
                        <div id="recent-reply">
                            <div class="group-tit">
                                <h4 class="tcol-t">최근 댓글ㆍ답글</h4>
                            </div>

                            <!-- 최근 댓글, 답글은 php사용해서 each문 돌릴 것 title부분에 답글/댓글 갯수확인도 가져와서 처리-->
                            <!-- 최근댓,답글 페이지네이션 첫번째 페이지 -->
                            <ul class="group-list" id="first-reply-page">
                                <li>
                                    <div class="ball"><img src="https://ssl.pstatic.net/static/cafe/cafe_pc/ico-blank.png" width="3" height="3" alt="" class="tcol-c"></div>
                                    <a href="/최근댓글,답글상세보기" target="cafe_main" onclick="targetCleaner(this);" class="tcol-c tlink" title="답0/댓0">
                                        <div class="ellipsis tcol-c">최근 댓글/답글제목</div>
                                    </a>
                                </li>
                            </ul>

                            <!-- 최근댓,답글 페이지네이션 두번째 페이지 -->
                            <ul class="group-list" id="second-reply-page" style="display:none;">
                                <li>
                                    <div class="ball"><img src="https://ssl.pstatic.net/static/cafe/cafe_pc/ico-blank.png" width="3" height="3" alt="" class="tcol-c"></div>
                                    <a href="/최근댓글,답글상세보기" target="cafe_main" onclick="targetCleaner(this);" class="tcol-c tlink" title="답0/댓0">
                                        <div class="ellipsis tcol-c">최근 댓글/답글제목</div>
                                    </a>
                                </li>
                            </ul>

                            <!-- 최근댓,답글 페이지네이션 페이지 컨트롤러 -->
                            <div class="pocket_nav p11">
                                <span id="pre-reply-disable" class="tcol-c filter-50 display-inblock"><span class="blind">최근 댓글,답글 목록</span>이전</span>
                                <span id="pre-reply-useable" style="display:none;"><a class="tcol-c display-inblock"><span class="blind">최근 댓글,답글 목록</span>이전</a></span>
                                <span class="tcol-c filter-25 display-inblock">ㅣ</span>
                                <span id="next-reply-useable"><a class="tcol-c display-inblock"><span class="blind">최근 댓글,답글 목록</span>다음</a></span>
                                <span id="next-reply-disable" style="display:none;" class="tcol-c filter-50 display-inblock"><span class="blind">최근 댓글,답글 목록</span>다음</span>
                            </div>
                        </div>
                    </li>
                    <!-- 카페 배너 자리 -->
                    <li class="box-ww _wideCafeBanner">
                        <div id="cafe-banner">
                            <strong class="cafe">인턴프로젝트 카페</strong>
                        </div>
                    </li>

                    <!-- 카페 인기 태그 -->
                    <li class="box-w">
                        <div id="cafe-pop-tag" class="widget-element">
                            <div class="group-tit">
                                <h4 class="tcol-t">카페 인기 태그</h4>
                                <p><a href="/태그페이지" target="cafe_main" class="tcol-t">more</a></p>
                            </div>

                            <div id="cafe-pop-tag-list">
                                <!-- 최근3일간 태그가 없는 경우에는 아래 메시지 띄움 -->
                                <p>
                                    <span class="num tcol-c"></span>
                                    <span class="list-l"><span class="tcol-c">최근 3일간 태그가 없습니다.</span></span>
                                </p>

                                <!-- 최근 3개월간 태그가 존재하면 아래와 같이 ol로 표시함 -->
                                <!-- php사용해서 each문 돌릴 것 -->
                                <ol class="group-list">
                                    <li>
                                        <span class="num tcol-c">1.</span>
                                        <span class="list-l"><a href="/해당태그가들어간게시글목록" target="cafe_main" class="tcol-c" title="태그이름나오는자리">태그이름나오는자리</a></span>
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