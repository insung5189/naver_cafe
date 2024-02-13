<h1>Main 화면</h1>

<?php 
// 세션에서 'user_data' 배열 전체를 불러옵니다.
$userData = $this->session->userdata('user_data');

// 'user_data' 배열이 존재하고, 그 안에 'user_id'가 있는지 확인합니다.
if (!empty($userData) && isset($userData['user_id'])): ?>
    <!-- 로그인한 사용자에게 보여질 내용 -->
    <p>안녕하세요, <?php echo htmlspecialchars($userData['nickName']); ?>님</p>
    <a href="member/myPageController">마이페이지</a>
    <a href="member/logincontroller/processLogout">로그아웃</a>
<?php else: ?>
    <!-- 로그인하지 않은 사용자에게 보여질 내용 -->
    <a href="member/signupcontroller">회원가입</a>
    <a href="member/logincontroller">로그인</a>
<?php endif; ?>
