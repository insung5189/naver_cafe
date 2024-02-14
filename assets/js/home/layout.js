//<![CDATA[
    var nsc = "cafe.mycafe";
    //]]>

// <li class="level-info border-sub">
    checkHideRankingNotice();

    function checkHideRankingNotice() {
        var rankCookie = document.cookie;
        var startPoint = rankCookie.indexOf("rankingHidden=");
        if (startPoint != -1) {
            var start = startPoint + 14;
            var end = rankCookie.indexOf(";", start);
            if (end == -1) end = rankCookie.length;
            var value = rankCookie.substring(start, end);
            value = decodeURIComponent(value);
            if (value == '31126391') {
                hideRankingNotice();
            }
        }
    }

    function hideRankingNotice() {
        Element.hide("rankingNotice");
        setHideCookie();
    }

    function setHideCookie() {
        var expireDate = new Date();
        expireDate.setDate(expireDate.getDate() + 2);
        document.cookie = "rankingHidden=31126391; expires=" + expireDate.toGMTString();
    }

    function goRankingNoticeDetail(linkUrl) {
        window.open(linkUrl, "rankings-introduction", "width=760,height=1010,scrollbars=no,resizable=yes");
    }

    // <div class="cafe-info-action" id="cafe-info-action">

    function writeBoard() {



        var oMenu = oMenuMoveUpdater.current();
        var bSupportTablet = Tablet.editing.JustAlert()
            .check({
                bAccessTabletPC: false,
                bReadOnlyStatus: false,
                bMarketBoard: oMenu.isMarketBoard(),
                bStaffBoard: oMenu.isStaffBoard(),
                bHasArticleForm: oMenu.hasArticleForm(),
                bHasTemplate: oMenu.hasTemplate()
            });

        if (!bSupportTablet) {
            return;
        }
        var boardType = "L";
        if (oMenu.isStaffBoard()) {
            $("cafe_main").src = '/StaffArticleWrite.nhn?clubid=31126391&menuid=' + oMenu.nMenuId + '&boardtype=L&m=write';
        } else if (oMenu.isSimpleBoard() || oMenu.isMemoBoard() || oMenu.isAttendanceBoard() || oMenu.isLevelUpBoard()) {
            var targetUrl = "/ca-fe/cafes/31126391/articles/write?boardType=" + boardType;
            window.open(targetUrl, "_blank");
        } else {


            var targetUrl = "/ca-fe/cafes/31126391/articles/write?boardType=" + boardType;
            if (oMenu.nMenuId > 0) {
                targetUrl = "/ca-fe/cafes/31126391/menus/" + oMenu.nMenuId + "/articles/write?boardType=" + boardType;
            }
            window.open(targetUrl, "_blank");



        }
    }
    var isFolded = false;

    // 카페정보 보기
    function showCafeInfo() {

        Element.show("cafe-info-data");
        Element.hide("member-action-data");

        try {
            var etc = {};
            etc["sti"] = "cafe_infodata";
            lcs_do(etc);
        } catch (e) {}
    }

    function showCafeApplyLayer() {
        Element.show($("hiddenCafeApplyLayer"));
    }

    function hideCafeApplyLayer() {
        Element.hide($("hiddenCafeApplyLayer"));
    }

    // 나의 활동 보기
    function showMyAction() {




        joinCafe();


    }

    // 나의 활동정보 조회 콜백
    function cafeMemberShipCB(res) {
        if (res.status == 200) {
            $("ia-action-data").innerHTML = res.responseText;
        }
    }


    // 카페정보/ 나의 활동영역 접기/펼치기
    function toggleIA() {
        if (isFolded) {
            $("ia-info-up-img").className = $("ia-info-up-img").className.replace("down", "up");
            $("ia-action-down-img").className = $("ia-action-down-img").className.replace("down", "up");
            Element.show("ia-info-data");


            isFolded = false;
        } else {
            $("ia-info-up-img").className = $("ia-info-up-img").className.replace("up", "down");
            $("ia-action-down-img").className = $("ia-action-down-img").className.replace("up", "down");
            Element.hide("ia-info-data");


            isFolded = true;
        }
    }

    function goCafeHistory() {
        var sCafeHistoryUrl = "/ca-fe/cafes/31126391/introduction/history";
        moveIframePage(sCafeHistoryUrl);
    }

    // 카페소개
    function cafeProfile() {
        moveIframePage('/CafeProfileView.nhn?clubid=31126391');
    }

    // 카페 랭킹
    function cafeRanking() {
        moveIframePage('/ca-fe/cafes/31126391/introduction/ranking');
    }

    // 청소년유해매체 카페 공지
    function teenagerHarmfulCafeNotice() {
        window.open('http://terms.naver.com/entry.nhn?docId=72599');
    }

    // 초대
    function inviteMember() {





        joinCafe();


    }

    // 멤버정보 변경
    function cafeMemberInfoEdit(event) {
        open_window("/ca-fe/cafes/31126391/members//profile-setting", "popup", 500, 780, "toolbar=0,menubar=0,scrollbars=yes,resizable=yes");
    }

    // 별명 변경
    function openProfileEdit() {
        window.open("http://admin.blog.naver.com/AdminMain.nhn?blogId=&Redirect=Basicinfo", "profile");
    }

    // 카페 멤버보기
    function showCafeMember() {





        joinCafe();


    }

    /**
     * 권한없는 게시글 페이지에서 가입할 경우 호출. ArticleReadNotMember.jsp 에서만 접근함.
     * @param nArticleId
     * @param fullUrl (FE에서 postMessage로 해당 메서드를 호출할 때 fullUrl을 같이 넘겨준다)
     *
     * @returns {boolean}
     */
    function joinCafeFromArticle(nArticleId, fullUrl) {
        oBAStatSender.send([{
            eventType: cafe.BAStatEventType.PC_843_166,
            extra: {
                pending: false,
                cafe_id: "31126391"
            }
        }]);

        $("cafeApply_check").submit();

        saveJoinTargetUrl(fullUrl);

        return false;
    }

    /**
     * 가입 중 Exception 발생했을 경우 alertAndRunJs 로 호출되는 함수.
     *
     */
    function retryJoinCafe() {
        $("cafeApply_check").submit();
    }

    /**
     * 가입 시도 함수.
     * @param redirectUrl
     * @returns {boolean}
     */
    function joinCafe(redirectUrl) {
        var cafeApplycheckForm = $("cafeApply_check");
        oBAStatSender.send([{
            eventType: cafe.BAStatEventType.PC_758_167,
            extra: {
                cafe_id: '31126391',
                pending: false
            }
        }]);

        if (typeof redirectUrl != "undefined") {
            cafeApplycheckForm.redirectUrl.value = redirectUrl;
        }

        cafeApplycheckForm.submit();

        saveJoinTargetUrl();

        return false;
    }

    /**
     * FE에서 saveJoinTargetUrl()을 사용하는 메서드를 호출 할 때, fullUrl을 넘겨 받으면 fullUrl로 저장하고,
     * 그렇지 않으면 현재 보고 있는 iframe 내부 URL > 가입 완료 후 이동할 URL로 저장한다.
     *
     * - 즉시가입/승인 카페 : 보고 있던 iframe 내부 URL
     * - 비공개 카페 : 카페홈 URL
     */
    function saveJoinTargetUrl(fullUrl) {
        if (fullUrl) {
            oMainLocalStorage.setLocalStorage(oMainLocalStorage.PC_JOIN_TARGET_URL, fullUrl);
        } else {
            var joinTargetUrl = document.getElementById("cafe_main").contentWindow.location.href;
            if (false) {
                joinTargetUrl = document.getElementById("cafe_main").src;
            }
            oMainLocalStorage.setLocalStorage(oMainLocalStorage.PC_JOIN_TARGET_URL, joinTargetUrl);
        }
    }

    /**
     * 가입 승인 취소를 요청한다.
     * @param nAppliedDate
     * @param nFrom
     */
    function cancelApply(nAppliedDate, nFrom) {
        var oBaEventType = cafe.BAStatEventType.PC_758_167; // default 'home'
        if (nFrom == 'article') {
            oBaEventType = cafe.BAStatEventType.PC_843_166;
        }
        oBAStatSender.send([{
            eventType: oBaEventType,
            extra: {
                pending: true,
                cafe_id: "31126391"
            }
        }]);

        if (!confirm('매니저가 승인하면 카페에 가입됩니다.\n가입 신청을 취소하시겠습니까?')) {
            return;
        }

        var ajax = new Ajax("/CafeApplyCancel.nhn?cafeId=31126391&appliedDate=" + nAppliedDate, {
            suspend: "false",
            onLoad: cancelApplyComplete
        });

        ajax.request();
    }

    function cancelApplyComplete(res) {
        if (res.status == 200) {
            alert('가입 신청이 취소되었습니다.')
        } else {
            alert('오류가 발생했습니다.\n다시 시도해주세요.');
        }

        if (false) {
            document.location.href = g_sCafeSectionUrl;
        } else {
            window.location.reload(true);
        }
    }

    // 멤버레벨 정보 팝업
    function viewMyMemberLevel() {
        open_window("/ca-fe/cafes/31126391/member-level", "mylevelview", 740, 498, "");
    }