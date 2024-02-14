<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="/assets/css/home/layout.css">
    <script src="/assets/js/home/layout.js"></script>
</head>

<body>
    <main>

        <!-- 카페 메인배너 -->
        <div id="front-img">
            <div id="front-cafe">
                <a href="/MyCafeIntro.nhn?clubid=31126391">
                    <span class="cafe_default">
                        <span class="inner_default">
                            <strong class="cafe_name">비드코칭연구소 제품완성 카페</strong>
                            <p class="cafe_url">https://cafe.naver.com/bidco</p>
                        </span>
                    </span>
                </a>
            </div>
        </div>


        <div id="content-area">
            <div id="group-area" class="skin-1080 fl">
                <div class="cafe-info-action" id="cafe-info-action">
                    <div id="cafe-info-data">
                        <ul class="info-action-tab">
                            <li class="tit-info-on">
                                <button class="gm-tcol-t" type="button" onclick="showCafeInfo();clickcr(this, 'cia.cafe', '', '', event);return false;" disabled="">카페정보</button>
                            </li>
                            <li class="tit-action">
                                <button type="button" onclick="showMyAction();clickcr(this, 'cia.my', '', '', event);return false;">나의활동</button>
                            </li>
                        </ul>
                        <div class="box-g">
                            <h4 class="d-none">카페정보</h4>
                            <div class="ia-info-data" id="ia-info-data">
                                <ul>
                                    <li class="gm-tcol-c">
                                        <a href="/MyCafeIntro.nhn?clubid=31126391">
                                            <img src="https://ssl.pstatic.net/static/cafe/cafe_pc/default/cafe_thumb_noimg_55.png" width="58" height="58" alt="카페아이콘" onerror="this.onerror=null;this.src='https://ssl.pstatic.net/static/cafe/cafe_pc/default/cafe_thumb_noimg_55.png';">
                                            <span class="border mask_white"></span>
                                        </a>

                                    </li>
                                    <li class="gm-tcol-c">
                                        <a href="/ca-fe/cafes/31126391/members/yM8-qYmw8v-uUtd_gX9Chw" target="cafe_main" class="id mlink gm-tcol-c" onclick="clickcr(this, 'cia*i.manager', '', '', event);">
                                            <div class="ellipsis gm-tcol-c">
                                                <div class="ellipsis">bid</div>
                                            </div>
                                        </a>
                                        <em class="ico-manager">매니저</em>
                                        <div class="thm"><a href="#" class="gm-tcol-c" onclick="goCafeHistory();clickcr(this, 'cia*i.history', '', '', event);return false;">2024.01.08. 개설</a></div>
                                        <div class="info-view"><a href="#" onclick="cafeProfile();clickcr(this, 'cia*i.intro', '', '', event);return false;" class="u gm-tcol-c">카페소개</a></div>
                                    </li>

                                </ul>
                            </div>

                            <div class="ia-info-data2">
                                <ul>
                                    <li class="level-info border-sub">
                                        <a href="#" class="gm-tcol-c" onclick="cafeRanking();clickcr(this, 'cia*i.grade', '', '', event);return false;">
                                            <strong class="d-none">카페등급</strong>
                                            <span class="ico_rank rank4"></span><em>씨앗4단계</em>
                                        </a>
                                    </li>
                                    <li class="mem-cnt-info" style="cursor:pointer;">
                                        <strong class="d-none">카페멤버수</strong>
                                        <a href="#" onclick="$('hiddenOpenMemberInfoLayer').style.display = '';">
                                            <img src="https://ssl.pstatic.net/static/cafe/cafe_pc/svg/ico_member.svg" alt="멤버수">
                                            <em>4<span class="ico_lock2">비공개</span></em>

                                        </a><a href="#" class="btn_close"><span class="blind">닫기</span></a>
                                        <div class="layer_hint" style="display: none" id="hiddenCafeApplyLayer" onmouseover="showCafeApplyLayer();" onmouseout="hideCafeApplyLayer();">
                                            <p class="txt"><a href="/ManageJoinApplication.nhn?search.clubid=31126391" onblur="hideCafeApplyLayer();">가입대기 0</a></p>
                                            <button onclick="hideCafeApplyLayer();" type="button" class="btn_close"><span class="blind">닫기</span></button>
                                        </div>

                                        <div class="layer_hint" style="display: none;" id="hiddenOpenMemberInfoLayer">
                                            <p class="txt">멤버목록 비공개 카페</p>
                                            <button onclick="$('hiddenOpenMemberInfoLayer').style.display = 'none';" type="button" class="btn_close"><span class="blind">닫기</span></button>
                                        </div>

                                        <a href="#" onclick="inviteMember();clickcr(this, 'cia*i.invite', '', '', event);return false;" class="link_invite">초대</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="cafe-write-btn">
                            <a href="#" class="_rosRestrict" onclick="joinCafe();return false;">카페 가입하기</a>
                        </div>

                        <div class="ia-info-btn">
                            <ul class="ia-info-list">
                                <li><a href="#" onclick="chatting(true);clickcr(this, 'cia*i.chat', '', '', event);return false;" class="link_chat _tabletRestrict(채팅)" target="_blank">카페 채팅</a></li>
                            </ul>
                        </div>
                    </div>

                    <div id="member-action-data" style="display:none">
                        <ul class="info-action-tab" role="tablist">
                            <li class="tit-info" role="presentation">
                                <button onclick="showCafeInfo();clickcr(this, 'cia.cafe', '', '', event);return false;" type="button">카페정보</button>
                            </li>
                            <li class="tit-action-on" role="presentation">
                                <button class="gm-tcol-t" type="button" onclick="showMyAction();clickcr(this, 'cia.my', '', '', event);return false;" disabled="">나의활동</button>
                            </li>
                        </ul>
                        <div class="box-g" role="tabpanel">
                            <h4 class="d-none">나의활동</h4>
                            <div id="ia-action-data"></div>
                        </div>

                        <div class="cafe-write-btn">
                            <a href="#" class="_rosRestrict" onclick="writeBoard();clickcr(this, 'mnu.write', '', '', event);return false;">카페 글쓰기</a>
                        </div>

                        <div class="ia-info-btn">
                            <ul class="ia-info-list">
                                <li><a href="#" onclick="chatting(true);clickcr(this, 'cia*i.chat', '', '', event);return false;" class="link_chat _tabletRestrict(채팅)" target="_blank">카페 채팅</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div id="cafe-menu">
                    <div class="box-g-t"></div>
                    <div class="box-g-m">

                        <ul class="cafe-menu-list">
                            <li>
                                <img src="https://cafe.pstatic.net/cafe4/hidden.gif" width="10" height="11" class="ico-list" alt=""><a href="/ArticleList.nhn?search.clubid=31126391&amp;search.boardtype=L" target="cafe_main" onclick="goMenu('0');clickcr(this, 'mnu.all','','',event);" class="gm-tcol-c" id="menuLink0">전체글보기</a>
                                <span class="gm-tcol-c total">37</span>
                            </li>

                            <li>
                                <img src="https://cafe.pstatic.net/cafe4/hidden.gif" width="10" height="11" class="ico-hot" alt="">
                                <a href="/ca-fe/cafes/31126391/popular" target="cafe_main" onclick="goMenu('-7');clickcr(this, 'mnu.populararticle','','',event);" class="gm-tcol-c" id="menuLink-7">
                                    인기글
                                </a>
                                <div class="tooltip_layer" id="popular_article_tooltip" style="display: none;">
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
                                <a href="/AttendanceView.nhn?search.clubid=31126391&amp;search.menuid=4" target="cafe_main" onclick="goMenu('4');clickcr(this, 'mnu.attend','','',event);" class="gm-tcol-c" id="menuLink4">
                                    출석부
                                </a>
                            </li>
                            <li>
                                <img src="https://cafe.pstatic.net/cafe4/hidden.gif" width="10" height="11" class="ico-list" alt="">
                                <a href="/ArticleList.nhn?search.clubid=31126391&amp;search.menuid=1&amp;search.boardtype=L" target="cafe_main" onclick="goMenu('1');clickcr(this, 'mnu.normal','','',event);" class="gm-tcol-c" id="menuLink1">
                                    자유게시판
                                </a>
                            </li>
                            <li>
                                <img src="https://cafe.pstatic.net/cafe4/hidden.gif" width="10" height="11" class="ico-list" alt="">
                                <a href="/ArticleList.nhn?search.clubid=31126391&amp;search.menuid=6&amp;search.boardtype=L" target="cafe_main" onclick="goMenu('6');clickcr(this, 'mnu.normal','','',event);" class="gm-tcol-c" id="menuLink6">
                                    건의게시판
                                </a>
                            </li>
                            <li>
                                <img src="https://cafe.pstatic.net/cafe4/hidden.gif" width="10" height="11" class="ico-simple" alt="">
                                <a href="/SimpleArticleList.nhn?search.clubid=31126391&amp;search.menuid=5&amp;search.moreDirection=next" target="cafe_main" onclick="goMenu('5');clickcr(this, 'mnu.simple','','',event);" class="gm-tcol-c" id="menuLink5">
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
                                <a href="/ArticleList.nhn?search.clubid=31126391&amp;search.menuid=7&amp;search.boardtype=L" target="cafe_main" onclick="goMenu('7');clickcr(this, 'mnu.normal','','',event);" class="gm-tcol-c" id="menuLink7">
                                    지식공유
                                </a>
                            </li>
                            <li>
                                <img src="https://cafe.pstatic.net/cafe4/hidden.gif" width="10" height="11" class="ico-list" alt="">
                                <a href="/ArticleList.nhn?search.clubid=31126391&amp;search.menuid=8&amp;search.boardtype=L" target="cafe_main" onclick="goMenu('8');clickcr(this, 'mnu.normal','','',event);" class="gm-tcol-c" id="menuLink8">
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
                            <ul class="group-list" id="first-reply-page">
                                <li>
                                    <div class="ball"><img src="https://ssl.pstatic.net/static/cafe/cafe_pc/ico-blank.png" width="3" height="3" alt="" class="tcol-c"></div>
                                    <a href="/ArticleRead.nhn?clubid=31126391&amp;articleid=38" target="cafe_main" onclick="targetCleaner(this);" class="tcol-c tlink" title="답0/댓0">
                                        <div class="ellipsis tcol-c">6</div>
                                    </a>
                                </li>
                                <li>
                                    <div class="ball"><img src="https://ssl.pstatic.net/static/cafe/cafe_pc/ico-blank.png" width="3" height="3" alt="" class="tcol-c"></div>
                                    <a href="/ArticleRead.nhn?clubid=31126391&amp;articleid=37" target="cafe_main" onclick="targetCleaner(this);" class="tcol-c tlink" title="답0/댓0">
                                        <div class="ellipsis tcol-c">5</div>
                                    </a>
                                </li>
                                <li>
                                    <div class="ball"><img src="https://ssl.pstatic.net/static/cafe/cafe_pc/ico-blank.png" width="3" height="3" alt="" class="tcol-c"></div>
                                    <a href="/ArticleRead.nhn?clubid=31126391&amp;articleid=36" target="cafe_main" onclick="targetCleaner(this);" class="tcol-c tlink" title="답0/댓0">
                                        <div class="ellipsis tcol-c">4</div>
                                    </a>
                                </li>
                                <li>
                                    <div class="ball"><img src="https://ssl.pstatic.net/static/cafe/cafe_pc/ico-blank.png" width="3" height="3" alt="" class="tcol-c"></div>
                                    <a href="/ArticleRead.nhn?clubid=31126391&amp;articleid=35" target="cafe_main" onclick="targetCleaner(this);" class="tcol-c tlink" title="답0/댓0">
                                        <div class="ellipsis tcol-c">3</div>
                                    </a>
                                </li>
                                <li>
                                    <div class="ball"><img src="https://ssl.pstatic.net/static/cafe/cafe_pc/ico-blank.png" width="3" height="3" alt="" class="tcol-c"></div>
                                    <a href="/ArticleRead.nhn?clubid=31126391&amp;articleid=34" target="cafe_main" onclick="targetCleaner(this);" class="tcol-c tlink" title="답3/댓0">
                                        <div class="ellipsis tcol-c">2</div>
                                    </a>
                                </li>
                            </ul>

                            <ul class="group-list" id="second-reply-page" style="display:none;">
                                <li>
                                    <div class="ball"><img src="https://ssl.pstatic.net/static/cafe/cafe_pc/ico-blank.png" width="3" height="3" alt="" class="tcol-c"></div>
                                    <a href="/ArticleRead.nhn?clubid=31126391&amp;articleid=2" target="cafe_main" onclick="targetCleaner(this);" class="tcol-c tlink" title="답0/댓1">
                                        <div class="ellipsis tcol-c">제품완성 카페입니다. 많은 이용 부탁드립니다.</div>
                                    </a>
                                </li>
                                <li>
                                    <div class="ball"><img src="https://ssl.pstatic.net/static/cafe/cafe_pc/ico-blank.png" width="3" height="3" alt="" class="tcol-c"></div>
                                    <a href="/ArticleRead.nhn?clubid=31126391&amp;articleid=20" target="cafe_main" onclick="targetCleaner(this);" class="tcol-c tlink" title="답0/댓1">
                                        <div class="ellipsis tcol-c">우리 귀여운 루피를 공유합니다</div>
                                    </a>
                                </li>
                                <li>
                                    <div class="ball"><img src="https://ssl.pstatic.net/static/cafe/cafe_pc/ico-blank.png" width="3" height="3" alt="" class="tcol-c"></div>
                                    <a href="/ArticleRead.nhn?clubid=31126391&amp;articleid=31" target="cafe_main" onclick="targetCleaner(this);" class="tcol-c tlink" title="답0/댓1">
                                        <div class="ellipsis tcol-c">ㅎㅇㅎㅇ</div>
                                    </a>
                                </li>
                                <li>
                                    <div class="ball"><img src="https://ssl.pstatic.net/static/cafe/cafe_pc/ico-blank.png" width="3" height="3" alt="" class="tcol-c"></div>
                                    <a href="/ArticleRead.nhn?clubid=31126391&amp;articleid=29" target="cafe_main" onclick="targetCleaner(this);" class="tcol-c tlink" title="답0/댓0">
                                        <div class="ellipsis tcol-c">안녕하세요 신입인턴 황인성입니다.444</div>
                                    </a>
                                </li>
                                <li>
                                    <div class="ball"><img src="https://ssl.pstatic.net/static/cafe/cafe_pc/ico-blank.png" width="3" height="3" alt="" class="tcol-c"></div>
                                    <a href="/ArticleRead.nhn?clubid=31126391&amp;articleid=28" target="cafe_main" onclick="targetCleaner(this);" class="tcol-c tlink" title="답0/댓0">
                                        <div class="ellipsis tcol-c">안녕하세요 신입인턴 황인성입니다.333</div>
                                    </a>
                                </li>
                            </ul>
                            <div class="pocket_nav p11">
                                <span id="pre-reply-disable" class="tcol-c filter-50 display-inblock"><span class="blind">최근 댓글,답글 목록</span>이전</span>
                                <span id="pre-reply-useable" style="display:none;"><a onclick="toggleFirstReplyList();" class="tcol-c display-inblock"><span class="blind">최근 댓글,답글 목록</span>이전</a></span>
                                <span class="tcol-c filter-25 display-inblock">ㅣ</span>
                                <span id="next-reply-useable"><a onclick="toggleSecondReplyList();" class="tcol-c display-inblock"><span class="blind">최근 댓글,답글 목록</span>다음</a></span>
                                <span id="next-reply-disable" style="display:none;" class="tcol-c filter-50 display-inblock"><span class="blind">최근 댓글,답글 목록</span>다음</span>
                            </div>
                        </div>
                    </li>
                    <li class="box-w">
                        <div id="linked-member">
                            <div class="group-tit">
                                <h4 class="tcol-t" id="linked-member-count">접속멤버</h4>
                                <p>
                                    <a href="#" onclick="toggleLM();return false;"><img src="https://cafe.pstatic.net/cafe4/hidden.gif" id="lmtoggleimg" width="15" height="16" alt="접속멤버 닫기" class="member-up"></a>
                                </p>
                            </div>
                            <ul class="group-list" id="lm-list" style="overflow-x:hidden;">
                                <li><span class="tcol-c">로그인을 하셔야 보실 수 있습니다.</span></li>
                            </ul>
                        </div>
                    </li>
                    <li class="box-ww _wideCafeBanner">
                        <div id="cafe-banner" onclick="moveIframePage('/MyCafeIntro.nhn?clubid=31126391')">
                            <strong class="cafe">비드코칭연구소 제품완성..</strong>
                        </div>
                    </li>
                    <li class="box-w">
                        <!-- widget-tag -->
                        <div id="cafe-pop-tag" class="widget-element">
                            <div class="group-tit">
                                <h4 class="tcol-t">카페 인기 태그</h4>
                                <p><a href="/CafeTagCloudList.nhn?search.clubId=31126391&amp;search.sortType=tagCount" target="cafe_main" class="tcol-t" onclick="window.scrollTo(0,0);targetCleaner(this);">more</a></p>
                            </div>
                            <div id="cafe-pop-tag-list">
                                <p>
                                    <span class="num tcol-c"></span>
                                    <span class="list-l"><span class="tcol-c">최근 3일간 태그가 없습니다.</span></span>
                                </p>
                            </div>
                        </div>
                        <!-- widget-tag -->
                    </li>
                </ul>
                <div class="banner_chatbot">
                    <a href="https://talk.naver.com/ct/w4nd8o" class="chatbot"><img src="https://ssl.pstatic.net/static/cafe/banner_chatbot.png" width="171" height="55" alt="궁금한게 있을 땐 카페 스마트봇"></a>
                </div>
            </div>
        </div>
    </main>
</body>

</html>