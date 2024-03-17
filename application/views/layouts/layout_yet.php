<li class="popular-articles">
    <a href="/인기글_보기">🔥인기글</a>
</li>



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
            <div class="latest-article-pagination">
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