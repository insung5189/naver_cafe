<?
defined('BASEPATH') or exit('직접적인 스크립트 접근은 허용되지 않습니다.');

class LoginModel extends CI_Model
{
    public function __construct() {
        parent::__construct();
        $this->load->library('doctrine');
        $this->load->library('session');
        $this->em = $this->doctrine->em;
    }

    public function authenticate($formData) {

        $errorData = ['errors' => []];

        $this->duplicateSession($formData, $errorData);
        if (!empty($errorData['errors'])) {
            return ['success' => false, 'errors' => $errorData['errors']];
        }


        try {
            $userRepo = $this->em->getRepository('Models\Entities\Member');
            $user = $userRepo->findOneBy(['userName' => $formData['userName']]);

            if (!$user) {
                // 사용자가 존재하지 않는 경우
                return ['success' => false, 'errors' => ['username' => '존재하지 않는 사용자입니다.']];
            }

            // 계정 상태 체크
            $activeCheck = $this->isActiveCheck($user);
            $blacklistCheck = $this->blacklistCheck($user);

            if (!password_verify($formData['password'], $user->getPassword())) {
                // 비밀번호 검증 실패 시
                if ($activeCheck) {
                    return $activeCheck;
                } else if ($blacklistCheck) {
                    return $blacklistCheck;
                }

                $this->incrementLoginAttempt($user->getId());
                $attemptCount = $this->getLoginAttemptCount($user->getId());
        
                if ($attemptCount >= 5) {
                    // 실패 횟수가 5회 이상인 경우 사용자 계정 비활성화
                    $user->setIsActive(false);
                    $this->em->flush();
                    return ['success' => false, 'errors' => ['login' => '비밀번호를 5회 이상 잘못 입력하여 계정이 비활성화 되었습니다.']];
                } else {
                    // 실패 횟수가 5회 미만인 경우 경고 메시지
                    return ['success' => false, 'errors' => ['login' => '비밀번호가 잘못되었습니다. ' . (5 - $attemptCount) . '회 더 시도할 수 있습니다.']];
                }
            } else {
                if ($activeCheck) {
                    return $activeCheck;
                } else if ($blacklistCheck) {
                    return $blacklistCheck;
                }

                // 로그인 성공: 로그인 시도 횟수 초기화 및 계정 상태 업데이트
                $this->resetLoginAttempts($user->getId());
                if (!$user->getIsActive() || $user->getBlacklist()) {
                    $user->setIsActive(true);
                    $user->setBlacklist(false);
                    $this->em->flush();
                }
                // 회원의 모든 정보를 배열로 준비
                $userSessData = [
                    'user_id'        => $user->getId(),
                    'create_date'    => $user->getCreateDate() ? $user->getCreateDate()->format('Y-m-d H:i:s') : null,
                    'modify_date'    => $user->getModifyDate() ? $user->getModifyDate()->format('Y-m-d H:i:s') : null,
                    'userName'       => trim($user->getUserName()),
                    'nickName'       => trim($user->getNickName()),
                    'firstName'      => trim($user->getFirstName()),
                    'lastName'       => trim($user->getLastName()),
                    'birth'          => $user->getBirth() ? $user->getBirth()->format('Y-m-d') : null,
                    'detailAddress'  => trim($user->getDetailAddress()),
                    'extraAddress'   => trim($user->getExtraAddress()),
                    'jibunAddress'   => trim($user->getJibunAddress()),
                    'roadAddress'    => trim($user->getRoadAddress()),
                    'postalNum'      => trim($user->getPostalNum()),
                    'gender'         => $user->getGender(),
                    'isActive'       => $user->getIsActive(),
                    'blacklist'      => $user->getBlacklist(),
                    'lastLogin'      => $user->getLastLogin() ? $user->getLastLogin()->format('Y-m-d H:i:s') : null,
                    'phone'          => trim($user->getPhone()),
                    'role'           => trim($user->getRole()),
                    'tmpPassword'    => trim($user->getTmpPassword()),
                    'visit'          => $user->getVisit(),
                    'introduce'      => trim($user->getIntroduce()),
                    'memberFilePath' => trim($user->getMemberFilePath()),
                    'memberFileName' => trim($user->getMemberFileName()),
                ];

                // 세션에 회원 정보 배열 저장
                $this->session->set_userdata('user_data', $userSessData);
        
                return ['success' => true, 'errors' => []];
            }
        } catch (\Exception $e) {
            log_message('error', '로그인 실패: ' . $e->getMessage());
            return ['success' => false, 'errors' => ['general' => '로그인 과정 중 오류가 발생했습니다.']];
        }
    }

    private function duplicateSession($formData, &$errorData) {
        $userSessData = $this->session->userdata('user_data');
        
        if (!empty($userSessData)) {
            if (isset($userSessData['userName']) && $userSessData['userName'] === $formData['userName']) {
                $errorData['errors']['session'] = '이미 로그인된 계정입니다. 다른 계정으로 로그인하려면 먼저 로그아웃 해주세요.';
            } else {
                $errorData['errors']['session'] = '현재 다른 계정이 로그인되어 있습니다. 새로운 계정으로 로그인하려면 로그아웃 후 재접속 해주세요.';
            }
        }
    }

    private function incrementLoginAttempt($memberId) {
        $loginAttempt = $this->em->getRepository('Models\Entities\LoginAttempt')->findOneBy(['member' => $memberId]);
    
        if (!$loginAttempt) {
            // 해당 회원의 로그인 시도 기록이 없으면 새로 생성
            $loginAttempt = new \Models\Entities\LoginAttempt();
            $member = $this->em->getRepository('Models\Entities\Member')->find($memberId);
            $loginAttempt->setMember($member);
            $loginAttempt->setAttemptCount(1);
        } else {
            // 기록이 있으면 시도 횟수를 1 증가
            $loginAttempt->setAttemptCount($loginAttempt->getAttemptCount() + 1);
        }
        $loginAttempt->setLastAttemptAt(new \DateTime());
    
        $this->em->persist($loginAttempt);
        $this->em->flush();
    }

    private function getLoginAttemptCount($memberId) {
        $loginAttempt = $this->em->getRepository('Models\Entities\LoginAttempt')->findOneBy(['member' => $memberId]);
    
        if ($loginAttempt) {
            return $loginAttempt->getAttemptCount();
        } else {
            return 0;
        }
    }

    private function resetLoginAttempts($memberId) {
        $loginAttempt = $this->em->getRepository('Models\Entities\LoginAttempt')->findOneBy(['member' => $memberId]);
        if ($loginAttempt) {
            $loginAttempt->setAttemptCount(0);
            $this->em->flush();
        }
    }

    private function isActiveCheck($user) {
        if (!$user->getIsActive()) {
            return ['success' => false, 'errors' => ['account' => '비활성화된 계정입니다. 관리자에게 문의하세요.']];
        }
        return null;
    }
    
    private function blacklistCheck($user) {
        if ($user->getBlacklist()) {
            return ['success' => false, 'errors' => ['account' => '카페로부터 접속을 거부당했습니다.']];
        }
        return null;
    }
}
