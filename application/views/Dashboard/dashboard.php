<h1>Main 화면</h1>

<?php 
$userData = $this->session->userdata('user_data');

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
